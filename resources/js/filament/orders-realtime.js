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
    const tables = Array.from(document.querySelectorAll('table'));

    const scoreTable = (table) => {
        const headers = Array.from(table.querySelectorAll('thead th'));
        if (headers.length === 0) return 0;

        const keys = headers.map((th) => normalizeHeader(th.textContent));
        let score = 0;

        // Heurística: la tabla de Orders suele tener estas columnas.
        if (keys.some((k) => k === '#' || k === 'id')) score += 3;
        if (keys.some((k) => k.includes('cliente'))) score += 3;
        if (keys.some((k) => k.includes('sucursal'))) score += 3;
        if (keys.some((k) => k.includes('tipo') && k.includes('entrega'))) score += 2;
        if (keys.some((k) => k.includes('estado'))) score += 2;
        if (keys.some((k) => k.includes('total'))) score += 1;
        if (keys.some((k) => k.includes('creado'))) score += 1;

        // Penaliza tablas muy pequeñas (típicamente no son listados).
        const tbody = table.querySelector('tbody');
        if (!tbody) score -= 2;

        return score;
    };

    const best = tables
        .map((t) => ({ table: t, score: scoreTable(t) }))
        .sort((a, b) => b.score - a.score)[0];

    if (best?.score > 0) {
        return best.table;
    }

    // Último fallback: la primera tabla dentro del main.
    return document.querySelector('main table') ?? null;
}

function buildBadge({ label, color }) {
    const badge = document.createElement('span');
    badge.className = `fi-badge fi-color-${color ?? 'gray'} text-xs`;
    badge.textContent = label ?? '';
    return badge;
}

function setCellText(td, text) {
    const target = td.querySelector('.fi-ta-text-item-label')
        ?? td.querySelector('.fi-ta-text-item')
        ?? td.querySelector('.fi-ta-text')
        ?? td.querySelector('a')
        ?? td.querySelector('span')
        ?? td;

    target.textContent = text ?? '';
}

function setCellBadge(td, { label, color }) {
    const badge = td.querySelector('.fi-badge');
    if (badge) {
        // Remueve cualquier fi-color-* previo.
        badge.className = badge.className
            .split(' ')
            .filter((c) => !c.startsWith('fi-color-'))
            .concat([`fi-color-${color ?? 'gray'}`])
            .join(' ');
        badge.textContent = label ?? '';
        return;
    }

    // Si no existía badge (por tabla vacía o estructura distinta), lo agregamos.
    td.innerHTML = '';
    td.appendChild(buildBadge({ label, color }));
}

function setActionsCell(td, { viewUrl, editUrl }) {
    const links = Array.from(td.querySelectorAll('a'));

    // Intento 1: por texto visible.
    const viewLink = links.find((a) => /\bver\b/i.test(a.textContent ?? ''));
    const editLink = links.find((a) => /\beditar\b/i.test(a.textContent ?? ''));

    if (viewLink && viewUrl) viewLink.href = viewUrl;
    if (editLink && editUrl) editLink.href = editUrl;

    // Intento 2: por orden (cuando son solo íconos o el texto no está).
    if (!viewLink && links[0] && viewUrl) links[0].href = viewUrl;
    if (!editLink && links[1] && editUrl) links[1].href = editUrl;
}

function insertOrderIntoTable(root, order) {
    const table = findOrdersTable(root);
    const tbody = table?.querySelector?.('tbody');
    const debugEnabled = root?.dataset?.ordersRealtimeDebug === '1' || import.meta.env.DEV;
    const debug = (...args) => {
        if (!debugEnabled) return;
        console.debug('[orders-realtime]', ...args);
    };

    if (!table || !tbody) {
        debug('no table/tbody found for insert', { hasTable: !!table, hasTbody: !!tbody });
        return;
    }

    // Evita duplicados si ya existe el registro en la tabla.
    if (tbody.querySelector(`[data-orders-realtime-row="${order.id}"]`)) {
        return;
    }

    // Evita falso positivo: comparamos contra el primer TD (id) en vez de hacer includes() en todo el texto.
    const idText = String(order.id);
    const alreadyInTable = Array.from(tbody.querySelectorAll('tr')).some((tr) => {
        const firstCell = tr.querySelector('td');
        const first = (firstCell?.textContent ?? '').trim();
        const firstNumber = Number.parseInt(first, 10);
        return Number.isFinite(firstNumber) && String(firstNumber) === idText;
    });
    if (alreadyInTable) {
        debug('row already present, skipping', { id: order.id });
        return;
    }

    const headers = Array.from(table.querySelectorAll('thead th'));
    const headerKeys = headers.map((th) => normalizeHeader(th.textContent));

    const templateRow = Array.from(tbody.querySelectorAll('tr'))
        .find((tr) => !tr.dataset?.ordersRealtimeRow);

    if (!templateRow) {
        debug('no template row found, cannot clone styles');
        return;
    }

    const row = templateRow.cloneNode(true);
    row.dataset.ordersRealtimeRow = String(order.id);
    row.classList.add('bg-primary-50/10');

    const tds = Array.from(row.querySelectorAll('td'));

    headerKeys.forEach((key, index) => {
        const td = tds[index];
        if (!td) return;

        if (key === '#' || key === 'id') {
            setCellText(td, String(order.id));
            return;
        }

        if (key.includes('cliente') || key.includes('user')) {
            setCellText(td, order.user ?? '-');
            return;
        }

        if (key.includes('sucursal') || key.includes('restaurant')) {
            setCellText(td, order.restaurant ?? '-');
            return;
        }

        if (key.includes('tipo') && key.includes('entrega')) {
            setCellBadge(td, {
                label: order.fulfillment_label ?? order.fulfillment_type,
                color: order.fulfillment_color ?? 'gray',
            });
            return;
        }

        if (key.includes('estado') || key.includes('status')) {
            setCellBadge(td, {
                label: order.status_label ?? order.status,
                color: order.status_color ?? 'gray',
            });
            return;
        }

        if (key.includes('total')) {
            setCellText(td, order.total_label ?? formatCurrency(order.total_amount, order.currency));
            return;
        }

        if (key.includes('creado') || key.includes('created')) {
            setCellText(td, order.created_at_label ?? order.created_at ?? '');
            return;
        }

        if (key === '') {
            setActionsCell(td, { viewUrl: order.view_url, editUrl: order.edit_url });
        }
    });

    // Si existe una columna extra (acciones) al final, también la actualizamos.
    const lastKey = headerKeys[headerKeys.length - 1];
    if (lastKey !== '' && tds.length > headerKeys.length) {
        setActionsCell(tds[tds.length - 1], { viewUrl: order.view_url, editUrl: order.edit_url });
    }

    tbody.prepend(row);
    debug('row inserted', { id: order.id, cloned: true });
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
