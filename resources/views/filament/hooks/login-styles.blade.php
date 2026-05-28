<style>
    :root {
        --panel-black: #090909;
        --panel-graphite: #171717;
        --panel-charcoal: #242424;
        --panel-steel: #4b4b4f;
        --panel-silver: #b9bbc2;
        --panel-cloud: #d8d8de;
        --panel-white: #f2f1f3;
        --panel-surface: #d8d2d6;
        --panel-surface-soft: #cfc7cd;
        --panel-surface-muted: #bdb4bc;
        --panel-red: #b3132b;
        --panel-burgundy: #5f0f1c;
        --panel-burgundy-deep: #3e0912;
        --panel-border-dark: rgba(255, 255, 255, 0.08);
        --panel-border-light: rgba(95, 15, 28, 0.14);
        --panel-shadow-soft: 0 24px 60px rgba(15, 15, 15, 0.14);
        --panel-shadow-strong: 0 22px 46px rgba(40, 8, 14, 0.24);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(179, 19, 43, 0.18), transparent 22%),
            radial-gradient(circle at bottom center, rgba(95, 15, 28, 0.34), transparent 30%),
            linear-gradient(180deg, #111113 0%, #1a1a1d 48%, #300b14 78%, #5f0f1c 100%) !important;
        min-height: 100vh !important;
    }

    body:has(.fi-simple-layout) {
        background:
            radial-gradient(circle at top left, rgba(179, 19, 43, 0.2), transparent 24%),
            radial-gradient(circle at bottom center, rgba(95, 15, 28, 0.4), transparent 34%),
            linear-gradient(180deg, #111113 0%, #1a1a1d 48%, #300b14 78%, #5f0f1c 100%) !important;
        min-height: 100vh !important;
    }

    .fi-simple-layout {
        position: relative;
    }

    .fi-main-ctn {
        background:
            linear-gradient(180deg, rgba(9, 9, 10, 0.22), rgba(95, 15, 28, 0.1)) !important;
    }

    .fi-simple-main,
    .fi-main,
    .fi-ta,
    .fi-section,
    .fi-wi-widget,
    .fi-modal-window {
        border: 1px solid var(--panel-border-light) !important;
        box-shadow: var(--panel-shadow-soft) !important;
    }

    .fi-simple-main,
    .fi-ta-ctn,
    .fi-section-content,
    .fi-wi-widget,
    .fi-modal-window,
    .fi-dropdown-panel,
    .fi-ta-filters,
    .fi-in-entry-wrp,
    .fi-ta-content,
    .fi-form,
    .fi-fo-component-ctn {
        background: color-mix(in srgb, var(--panel-surface) 98%, white 2%) !important;
        backdrop-filter: blur(14px) !important;
        border-radius: 22px !important;
    }

    .fi-simple-main {
        padding: 1.75rem !important;
    }

    .fi-logo img {
        border-radius: 18px !important;
        background: #ffffff !important;
        padding: 0.45rem !important;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.22) !important;
        object-fit: contain !important;
    }

    .fi-sidebar-header .fi-logo img,
    .fi-topbar .fi-logo img {
        border-radius: 14px !important;
    }

    .fi-topbar {
        background:
            linear-gradient(180deg, rgba(9, 9, 9, 0.96), rgba(20, 20, 22, 0.94)) !important;
        backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22) !important;
    }

    .fi-page {
        color: #f8f8f8 !important;
    }

    .fi-sidebar {
        background:
            linear-gradient(180deg, rgba(10, 10, 10, 0.98) 0%, rgba(26, 26, 28, 0.98) 42%, rgba(63, 9, 18, 0.98) 100%) !important;
        border-right: 1px solid var(--panel-border-dark) !important;
        box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.04) !important;
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    }

    .fi-sidebar-nav,
    .fi-sidebar-header,
    .fi-sidebar-group-label,
    .fi-sidebar-group-button,
    .fi-sidebar-item-button {
        color: rgba(255, 255, 255, 0.92) !important;
    }

    .fi-sidebar-item-button,
    .fi-sidebar-group-button {
        border-radius: 14px !important;
        transition: all 160ms ease !important;
    }

    .fi-sidebar-item-button:hover,
    .fi-sidebar-group-button:hover {
        background: linear-gradient(90deg, rgba(179, 19, 43, 0.18), rgba(95, 15, 28, 0.28)) !important;
        color: #ffffff !important;
    }

    .fi-active.fi-sidebar-item-button,
    .fi-sidebar-item-active .fi-sidebar-item-button {
        background:
            linear-gradient(90deg, rgba(179, 19, 43, 0.22), rgba(95, 15, 28, 0.58)) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06), 0 12px 28px rgba(57, 7, 15, 0.28) !important;
    }

    .fi-header,
    .fi-ta-header {
        color: var(--panel-black) !important;
    }

    .fi-simple-header-heading,
    .fi-header-heading,
    .fi-ta-header-heading,
    .fi-section-heading {
        color: #111111 !important;
        letter-spacing: -0.03em;
    }

    .fi-simple-header-subheading,
    .fi-header-subheading,
    .fi-ta-header-description,
    .fi-section-description,
    .fi-breadcrumbs-item-label {
        color: #7c8596 !important;
    }

    .fi-breadcrumbs-item-separator,
    .fi-icon-btn-icon,
    .fi-ta-header-toolbar .fi-icon {
        color: #94a3b8 !important;
    }

    .fi-input-wrp,
    .fi-select-input,
    .fi-textarea,
    .fi-fo-rich-editor-toolbar,
    .fi-fo-date-time-picker-panel {
        background: color-mix(in srgb, var(--panel-surface-soft) 96%, white 4%) !important;
        border-color: rgba(95, 15, 28, 0.14) !important;
        border-radius: 16px !important;
    }

    .fi-input,
    .fi-select-input,
    .fi-textarea,
    .fi-input-wrp input,
    .fi-input-wrp textarea,
    .fi-input-wrp select,
    .fi-fo-field-wrp label,
    .fi-fo-placeholder {
        color: var(--panel-black) !important;
    }

    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp legend,
    .fi-section-content label,
    .fi-fo-field-wrp-label span {
        color: #f4f4f5 !important;
        opacity: 1 !important;
    }

    .fi-input-wrp[data-disabled],
    .fi-input-wrp[disabled],
    .fi-fo-field-wrp:has(input[disabled]),
    .fi-fo-field-wrp:has(textarea[disabled]),
    .fi-fo-field-wrp:has(select[disabled]) {
        opacity: 1 !important;
    }

    .fi-input:disabled,
    .fi-select-input:disabled,
    .fi-textarea:disabled,
    .fi-input-wrp input:disabled,
    .fi-input-wrp textarea:disabled,
    .fi-input-wrp select:disabled,
    .fi-fo-placeholder {
        color: #7e97bb !important;
        -webkit-text-fill-color: #7e97bb !important;
        opacity: 1 !important;
    }

    .fi-input-wrp .fi-input-suffix,
    .fi-input-wrp .fi-input-prefix,
    .fi-select-input + .fi-input-suffix,
    .fi-select-input + .fi-input-prefix {
        color: #6e7890 !important;
        opacity: 1 !important;
    }

    .fi-input::placeholder,
    .fi-textarea::placeholder,
    .fi-input-wrp input::placeholder,
    .fi-input-wrp textarea::placeholder {
        color: #7a7a84 !important;
        opacity: 1 !important;
    }

    .fi-input-wrp input:-webkit-autofill,
    .fi-input-wrp input:-webkit-autofill:hover,
    .fi-input-wrp input:-webkit-autofill:focus {
        -webkit-text-fill-color: #111111 !important;
        box-shadow: 0 0 0 1000px var(--panel-surface-soft) inset !important;
    }

    .fi-input:focus,
    .fi-select-input:focus,
    .fi-textarea:focus {
        border-color: rgba(179, 19, 43, 0.46) !important;
        box-shadow: 0 0 0 4px rgba(179, 19, 43, 0.12) !important;
    }

    .fi-btn {
        border-radius: 14px !important;
        font-weight: 600 !important;
    }

    .fi-btn-color-primary,
    .fi-btn-primary {
        background: linear-gradient(135deg, var(--panel-red) 0%, var(--panel-burgundy) 100%) !important;
        border-color: var(--panel-burgundy) !important;
        color: var(--panel-white) !important;
        box-shadow: var(--panel-shadow-strong) !important;
    }

    .fi-btn-color-primary:hover,
    .fi-btn-primary:hover {
        background: linear-gradient(135deg, #c61733 0%, #741223 100%) !important;
        border-color: #741223 !important;
    }

    .fi-btn-color-gray {
        background: linear-gradient(180deg, #2d2d31 0%, #1a1a1d 100%) !important;
        border-color: #1a1a1d !important;
        color: #ffffff !important;
    }

    .fi-link,
    .fi-ac-btn-link,
    .fi-breadcrumbs-item-active .fi-breadcrumbs-item-label {
        color: var(--panel-burgundy) !important;
    }

    .fi-global-search-field input,
    .fi-topbar .fi-input-wrp input {
        color: #111111 !important;
    }

    .fi-global-search-field input::placeholder,
    .fi-topbar .fi-input-wrp input::placeholder {
        color: #7a7a84 !important;
    }

    .fi-topbar .fi-icon-btn-icon,
    .fi-topbar .fi-theme-switcher-btn,
    .fi-topbar .fi-user-menu-trigger,
    .fi-topbar .fi-breadcrumbs-item-label {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .fi-badge,
    .fi-status-badge {
        border-radius: 999px !important;
        font-weight: 700 !important;
        letter-spacing: 0.01em;
    }

    .fi-color-success {
        --c-50: 237 243 239 !important;
        --c-100: 221 232 224 !important;
        --c-400: 82 116 92 !important;
        --c-500: 58 93 68 !important;
        --c-600: 43 74 52 !important;
        --c-700: 31 57 40 !important;
    }

    .fi-color-danger {
        --c-50: 248 238 239 !important;
        --c-100: 241 221 224 !important;
        --c-400: 176 88 98 !important;
        --c-500: 146 33 49 !important;
        --c-600: 111 20 34 !important;
        --c-700: 87 14 27 !important;
    }

    .fi-badge.fi-color-success,
    .fi-status-badge.fi-color-success,
    [data-color="success"].fi-badge,
    [data-color="success"].fi-status-badge {
        background: rgba(43, 74, 52, 0.12) !important;
        color: #315540 !important;
        border: 1px solid rgba(43, 74, 52, 0.18) !important;
        box-shadow: none !important;
    }

    .fi-badge.fi-color-danger,
    .fi-status-badge.fi-color-danger,
    [data-color="danger"].fi-badge,
    [data-color="danger"].fi-status-badge {
        background: rgba(111, 20, 34, 0.1) !important;
        color: #8b2636 !important;
        border: 1px solid rgba(111, 20, 34, 0.16) !important;
        box-shadow: none !important;
    }

    .fi-ta-table tbody tr {
        transition: background-color 140ms ease, transform 140ms ease !important;
    }

    .fi-ta-table tbody tr:hover {
        background: rgba(95, 15, 28, 0.04) !important;
    }

    .fi-ta-table thead th,
    .fi-ta-text,
    .fi-ta-cell,
    .fi-in-text,
    .fi-fo-text,
    .fi-section-content,
    .fi-modal-window {
        color: #151515 !important;
    }

    .fi-tabs-item {
        border-radius: 999px !important;
    }

    .fi-tabs-item.fi-active {
        background: linear-gradient(135deg, rgba(179, 19, 43, 0.16), rgba(95, 15, 28, 0.22)) !important;
        color: var(--panel-burgundy) !important;
    }

    .fi-dropdown-list-item:hover,
    .fi-global-search-result:hover {
        background: rgba(95, 15, 28, 0.06) !important;
    }

    .fi-pagination-records-per-page-select {
        border-radius: 14px !important;
    }

    .fi-ta-pagination,
    .fi-pagination,
    .fi-pagination p,
    .fi-pagination span,
    .fi-pagination label,
    .fi-pagination-records-per-page-label,
    .fi-ta-pagination p,
    .fi-ta-pagination span {
        color: #5d616d !important;
        opacity: 1 !important;
    }

    .fi-section-content,
    .fi-in-entry-wrp,
    .fi-fo-placeholder,
    .fi-fo-text,
    .fi-in-text {
        color: #2a2a30 !important;
    }

    .fi-section-content .fi-input-wrp,
    .fi-section-content .fi-textarea,
    .fi-section-content .fi-select-input,
    .fi-in-entry-wrp .fi-input-wrp {
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18), 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .fi-body,
    .fi-layout,
    .fi-page-ctn,
    .fi-page-header,
    .fi-ta,
    .fi-section,
    .fi-wi-widget,
    .fi-modal-window,
    .fi-dropdown-panel,
    .fi-fo-builder-item,
    .fi-fo-repeater-item,
    .fi-tabs,
    .fi-ta-filters,
    .fi-pagination {
        background-color: transparent !important;
    }

    .fi-ta-ctn,
    .fi-section-content,
    .fi-wi-widget,
    .fi-modal-window,
    .fi-dropdown-panel,
    .fi-in-entry-wrp,
    .fi-fo-builder-item,
    .fi-fo-repeater-item {
        border-color: rgba(95, 15, 28, 0.12) !important;
    }

    .fi-ta-table thead th {
        background: color-mix(in srgb, var(--panel-surface-muted) 94%, white 6%) !important;
        color: #26252a !important;
    }

    .fi-ta-table tbody tr {
        background: rgba(214, 207, 212, 0.52) !important;
    }

    .fi-ta-table tbody tr:nth-child(even) {
        background: rgba(189, 180, 188, 0.46) !important;
    }

    .fi-pagination,
    .fi-pagination-records-per-page-select,
    .fi-dropdown-list {
        background: color-mix(in srgb, var(--panel-surface-soft) 96%, white 4%) !important;
        border-color: rgba(95, 15, 28, 0.12) !important;
    }

    .fi-global-search-results-ctn,
    .fi-dropdown-list-item,
    .fi-no-notification {
        background: color-mix(in srgb, var(--panel-surface) 96%, white 4%) !important;
    }

    .fi-ta-header,
    .fi-ta-header-toolbar,
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-empty-state,
    .fi-pagination {
        background: color-mix(in srgb, var(--panel-surface) 97%, white 3%) !important;
    }
</style>
