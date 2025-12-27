<style>
    /* Degradado sutil de fondo oscuro con tonos del color primario */
    body:has(.fi-simple-layout) {
        background: radial-gradient(
            circle at top center,
            rgba(214, 28, 3, 0.9),
            #1f2937,
            #111827 100%
        );
        /* background-size: 100% 200% !important; */
        animation: heartbeat 6s ease-in-out infinite !important;
        min-height: 100vh !important;
    }

   @keyframes heartbeat-bg {
        0%   { background-size: 75% 75%; }
        30%  { background-size: 110% 110%; }
        50%  { background-size: 95% 95%; }
        70%  { background-size: 120% 120%; }
        100% { background-size: 75% 75%; }
    }

    /* Contenedor del formulario con fondo oscuro semi-transparente */
    .fi-simple-main {
        background: rgba(42, 42, 58, 0.85) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 1rem !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
        border: 1px solid rgba(214, 28, 3, 0.2) !important;
    }

    /* Sombra en el logo */
    .fi-logo img {
        filter: drop-shadow(0 4px 6px rgba(214, 28, 3, 0.3)) !important;
    }

    /* Título en blanco */
    .fi-simple-header h1,
    .fi-simple-header-heading {
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* Labels en gris claro */
    .fi-simple-layout label {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    /* Inputs con fondo oscuro */
    .fi-simple-layout input[type="email"],
    .fi-simple-layout input[type="password"] {
        background-color: rgba(30, 30, 45, 0.6) !important;
        border-color: rgba(214, 28, 3, 0.3) !important;
        color: #ffffff !important;
    }

    .fi-simple-layout input[type="email"]:focus,
    .fi-simple-layout input[type="password"]:focus {
        border-color: #d61c03 !important;
        ring-color: #d61c03 !important;
        box-shadow: 0 0 0 3px rgba(214, 28, 3, 0.1) !important;
    }

    /* Placeholder en gris */
    .fi-simple-layout input::placeholder {
        color: rgba(255, 255, 255, 0.4) !important;
    }

    /* Checkbox "Recordarme" */
    .fi-simple-layout .fi-fo-checkbox label {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    /* Botón con el color primario */
    .fi-simple-layout .fi-btn-primary {
        background-color: #d61c03 !important;
        border-color: #d61c03 !important;
        color: #ffffff !important;
    }

    .fi-simple-layout .fi-btn-primary:hover {
        background-color: #b01802 !important;
        border-color: #b01802 !important;
    }
</style>
