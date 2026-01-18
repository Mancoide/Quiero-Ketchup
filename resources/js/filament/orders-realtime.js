import '../echo';

function formatCurrency(amount, currency) {
    const numeric = Number(amount);

    if (!Number.isFinite(numeric)) {
        return `${amount ?? ''} ${currency ?? ''}`.trim();
    }

    // Para PYG normalmente no hace falta decimales, pero respetamos lo que llegue.
    return `${numeric.toLocaleString(undefined)} ${currency ?? ''}`.trim();
}

function normalizeHeader(text) {
    return String(text ?? '')
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '');
}

function findOrdersTable(root) {
    // El hook se renderiza justo antes de la tabla. Buscamos en siblings siguientes.
    let node = root;

    for (let i = 0; i < 12; i += 1) {
        node = node?.nextElementSibling;
        if (!node) break;

        if (node.tagName === 'TABLE') return node;

        const table = node.querySelector?.('table');
        if (table) return table;
    }

    // Fallback: primera tabla visible en el contenido principal.
    return document.querySelector('main table') ?? document.querySelector('table');
}

function buildBadge({ label, color }) {
    const badge = document.createElement('span');
    badge.className = `fi-badge fi-color-${color ?? 'gray'} text-xs`;
    badge.textContent = label ?? '';
    return badge;
}

function insertOrderIntoTable(root, order) {
    const table = findOrdersTable(root);
    const tbody = table?.querySelector?.('tbody');
    if (!table || !tbody) return;

    // Evita duplicados si ya existe el registro en la tabla.
    if (tbody.querySelector(`[data-orders-realtime-row="${order.id}"]`)) {
        return;
    }

    // Si ya existe por render Livewire (por ejemplo, el usuario recargó), no insertamos.
    const existingTextMatch = Array.from(tbody.querySelectorAll('tr')).some((tr) =>
        tr.textContent?.includes(String(order.id))
    );
    if (existingTextMatch) return;

    const headers = Array.from(table.querySelectorAll('thead th'));
    const headerKeys = headers.map((th) => normalizeHeader(th.textContent));

    const row = document.createElement('tr');
    row.dataset.ordersRealtimeRow = String(order.id);
    row.className = 'bg-primary-50/10';

    const makeCell = (contentNode) => {
        const td = document.createElement('td');
        td.className = 'fi-ta-cell';
        if (contentNode instanceof Node) {
            td.appendChild(contentNode);
        } else if (contentNode !== undefined && contentNode !== null) {
            td.textContent = String(contentNode);
        }
        return td;
    };

    const actions = document.createElement('div');
    actions.className = 'flex items-center justify-end gap-3 whitespace-nowrap';

    if (order.view_url) {
        const view = document.createElement('a');
        view.className = 'fi-link fi-size-sm';
        view.href = order.view_url;
        view.textContent = root.dataset.ordersRealtimeViewLabel ?? 'Ver';
        actions.appendChild(view);
    }

    if (order.edit_url) {
        const edit = document.createElement('a');
        edit.className = 'fi-link fi-size-sm';
        edit.href = order.edit_url;
        edit.textContent = root.dataset.ordersRealtimeEditLabel ?? 'Editar';
        actions.appendChild(edit);
    }

    const cells = headerKeys.map((key) => {
        if (key === '#' || key === 'id') {
            return makeCell(order.id);
        }

        if (key.includes('cliente') || key.includes('user')) {
            return makeCell(order.user ?? '-');
        }

        if (key.includes('sucursal') || key.includes('restaurant')) {
            return makeCell(order.restaurant ?? '-');
        }

        if (key.includes('tipo') && key.includes('entrega')) {
            return makeCell(buildBadge({
                label: order.fulfillment_label ?? order.fulfillment_type,
                color: 'gray',
            }));
        }

        if (key.includes('estado') || key.includes('status')) {
            return makeCell(buildBadge({
                label: order.status_label ?? order.status,
                color: order.status_color ?? 'gray',
            }));
        }

        if (key.includes('total')) {
            return makeCell(formatCurrency(order.total_amount, order.currency));
        }

        if (key.includes('creado') || key.includes('created')) {
            return makeCell(order.created_at ?? '');
        }

        // Acciones: en Filament a veces el header está vacío.
        if (key === '') {
            return makeCell(actions);
        }

        return makeCell('');
    });

    // Si no detectamos una columna vacía para acciones, las agregamos al final.
    const hasActions = cells.some((td) => td.contains(actions));
    if (!hasActions) {
        cells.push(makeCell(actions));
    }

    cells.forEach((td) => row.appendChild(td));

    tbody.prepend(row);
}

function ensureSubscribed(root) {
    if (!window.Echo) {
        if (root?.dataset?.ordersRealtimeDebug === '1') {
            console.debug('[orders-realtime] Echo no está inicializado.');
        }
        return;
    }

    if (window.__ordersRealtimeSubscribed) {
        return;
    }

    window.__ordersRealtimeSubscribed = true;

    const debugEnabled = root?.dataset?.ordersRealtimeDebug === '1' || import.meta.env.DEV;
    const debug = (...args) => {
        if (!debugEnabled) return;
        console.debug('[orders-realtime]', ...args);
    };

    const pusher = window.Echo?.connector?.pusher;
    if (debugEnabled && pusher && !window.__ordersRealtimePusherDebugBound) {
        window.__ordersRealtimePusherDebugBound = true;

        pusher.connection.bind('state_change', (states) => debug('state_change', states));
        pusher.connection.bind('connected', () => debug('connected'));
        pusher.connection.bind('disconnected', () => debug('disconnected'));
        pusher.connection.bind('error', (err) => debug('connection_error', err));
    }

    debug('subscribing to private-orders');

    const channel = window.Echo.private('orders')
        .subscribed(() => debug('subscribed private-orders'))
        .error((err) => debug('subscription_error', err));

    channel
        .listen('.orders.created', (payload) => {
            debug('event received', payload);
            const root = document.querySelector('[data-orders-realtime-root]');
            if (!root) return;

            const order = payload?.order;
            if (!order?.id) return;

            insertOrderIntoTable(root, order);

            // Actualiza totalizadores si existen.
            const stats = payload?.stats;
            if (stats) {
                const byStat = (key) => document.querySelector(`[data-orders-stats="${key}"] .fi-wi-stats-overview-stat-value`);

                const pending = byStat('pending') ?? document.querySelector('[data-orders-stats-pending]');
                const delivery = byStat('delivery') ?? document.querySelector('[data-orders-stats-delivery]');
                const pickup = byStat('pickup') ?? document.querySelector('[data-orders-stats-pickup]');
                const dineIn = byStat('dine_in') ?? document.querySelector('[data-orders-stats-dine-in]');

                if (pending) pending.textContent = stats.pending ?? pending.textContent;
                if (delivery) delivery.textContent = stats.delivery ?? delivery.textContent;
                if (pickup) pickup.textContent = stats.pickup ?? pickup.textContent;
                if (dineIn) dineIn.textContent = stats.dine_in ?? dineIn.textContent;
            }
        });
}

function init() {
    const root = document.querySelector('[data-orders-realtime-root]');
    if (!root) return;

    ensureSubscribed(root);
}

document.addEventListener('DOMContentLoaded', init);

document.addEventListener('livewire:navigated', () => {
    // Filament navega sin recargar. Re-inicializa si el usuario volvió al index.
    init();
});
