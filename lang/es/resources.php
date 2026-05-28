<?php

return [
    'navigation_groups' => [
        'administration' => 'Administración',
        'authentication' => 'Autenticación',
        'cms' => 'CMS',
        'accounting' => 'Contabilidad',
        'settings' => 'Configuraciones',
    ],

    'reconciliations' => [
        'singular' => 'Conciliación',
        'plural' => 'Conciliaciones',
        'sections' => [
            'files' => 'Archivos',
            'summary' => 'Resumen',
            'logs' => 'Logs',
        ],
        'fields' => [
            'name' => 'Nombre',
            'bank_file' => 'Extracto bancario',
            'company_file' => 'Extracto empresa',
            'status' => 'Estado',
            'total_bank_records' => 'Registros banco',
            'total_company_records' => 'Registros empresa',
            'matched_records' => 'Conciliados',
            'bank_only_records' => 'Solo en banco',
            'company_only_records' => 'Solo en empresa',
            'possible_matches' => 'Posibles coincidencias',
            'total_reconciled_bank' => 'Total conciliado banco',
            'total_reconciled_company' => 'Total conciliado mayor',
            'total_unreconciled_bank' => 'Total no conciliado banco',
            'total_unreconciled_company' => 'Total no conciliado mayor',
            'ledger_balance' => 'Saldo del mayor',
            'outstanding_checks' => 'Cheques pendientes',
            'bank_unregistered_credits' => 'Depositos no reg. x banco',
            'unbooked_debits' => 'Cheques no contabilizados',
            'unbooked_credits' => 'Depositos no contabilizados',
            'reconciled_balance' => 'Saldo s/ conciliacion',
            'bank_statement_balance' => 'Saldo s/ extracto',
            'difference_amount' => 'Diferencia',
            'processed_at' => 'Procesado',
            'processing_log' => 'Salida del proceso',
            'error_message' => 'Error',
        ],
        'actions' => [
            'process' => 'Procesar conciliación',
            'download' => 'Descargar resultado',
        ],
        'notifications' => [
            'process_success' => 'Conciliación procesada correctamente.',
            'process_error' => 'No se pudo procesar la conciliación.',
        ],
    ],

    'cms' => [
        'statuses' => [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ],
    ],

    'cms_sections' => [
        'singular' => 'Sección',
        'plural' => 'Secciones',
        'fields' => [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'status' => 'Estado',
        ],
    ],

    'cms_banners' => [
        'singular' => 'Banner',
        'plural' => 'Banners',
        'sections' => [
            'media' => 'Imagen',
            'links' => 'Enlaces',
            'configuration' => 'Configuración',
        ],
        'fields' => [
            'section' => 'Sección',
            'image' => 'Imagen',
            'button_text' => 'Texto del botón',
            'image_link' => 'Link de la imagen',
            'button_link' => 'Link del botón',
            'status' => 'Estado',
            'sort_order' => 'Orden',
        ],
    ],

    'cms_internal_pages' => [
        'singular' => 'Página interna',
        'plural' => 'Páginas internas',
        'fields' => [
            'section' => 'Sección',
            'title' => 'Título',
            'description' => 'Descripción',
            'status' => 'Estado',
        ],
    ],

    'legal_texts' => [
        'singular' => 'Texto legal',
        'plural' => 'Textos legales',
        'fields' => [
            'type' => 'Tipo',
            'content' => 'Contenido',
            'updated_at' => 'Actualizado',
        ],
        'types' => [
            'privacy_policy' => 'Política de privacidad',
            'terms_and_conditions' => 'Términos y condiciones',
        ],
    ],
    'roles' => [
        'singular' => 'Rol',
        'plural' => 'Roles',
    ],
    'users' => [
        'singular' => 'Usuario',
        'plural' => 'Usuarios',
    ],
    'categories' => [
        'singular' => 'Categoría',
        'plural' => 'Categorías',
        'fields' => [
            'name' => 'Nombre',
            'slug' => 'Slug',
            'subcategories_count' => 'Subcategorías',
            'products_count' => 'Productos',
            'description' => 'Descripción',
            'meta' => 'Meta',
        ],
    ],

    'common' => [
        'fields' => [
            'created_at' => 'Creado',
        ],
    ],

    'key_value' => [
        'key' => 'Clave',
        'value' => 'Valor',
        'add' => 'Agregar dato',
    ],

    'subcategories' => [
        'singular' => 'Sub categoría',
        'plural' => 'Sub categorías',
        'fields' => [
            'name' => 'Nombre',
        ],
    ],

    'products' => [
        'singular' => 'Producto',
        'plural' => 'Productos',
        'sections' => [
            'media' => 'Imágenes',
            'general' => 'Información',
            'pricing' => 'Precio',
            'classification' => 'Clasificación',
        ],
        'fields' => [
            'images' => 'Imágenes',
            'name' => 'Nombre',
            'slug' => 'Slug',
            'description' => 'Descripción',
            'meta' => 'Meta',
            'price' => 'Precio',
            'currency' => 'Moneda',
            'available' => 'Disponible',
            'category' => 'Categoría',
            'subcategory' => 'Subcategoría',
            'restaurants' => 'Sucursales',
        ],
        'helpers' => [
            'meta' => 'Datos extra opcionales en formato clave/valor. Ejemplo: clave "nivel_picante" valor "medio" o clave "tiempo_preparacion_min" valor "10".',
        ],
        'placeholders' => [
            'meta_key' => 'ej: nivel_picante',
            'meta_value' => 'ej: medio',
        ],
    ],

    'restaurants' => [
        'singular' => 'Sucursal',
        'plural' => 'Sucursales',
        'sections' => [
            'media' => 'Imágenes',
            'general' => 'Información',
            'contact' => 'Contacto',
            'status' => 'Estado',
            'advanced' => 'Avanzado',
        ],
        'fields' => [
            'images' => 'Imágenes',
            'name' => 'Nombre',
            'slug' => 'Slug',
            'description' => 'Descripción',
            'address' => 'Dirección',
            'phone' => 'Teléfono',
            'email' => 'Email',
            'status' => 'Estado',
            'settings' => 'Configuración',
            'meta' => 'Meta',
            'products_count' => 'Productos',
            'locations_count' => 'Ubicaciones',
        ],
        'statuses' => [
            'active' => 'Activa',
            'inactive' => 'Inactiva',
            'suspended' => 'Suspendida',
        ],
    ],

    'addresses' => [
        'singular' => 'Dirección',
        'plural' => 'Direcciones',
        'tabs' => [
            'information' => 'Información',
            'location' => 'Ubicación',
            'advanced' => 'Avanzado',
        ],
        'fields' => [
            'user' => 'Usuario',
            'street' => 'Calle',
            'city' => 'Ciudad',
            'state' => 'Estado/Provincia',
            'postal_code' => 'Código postal',
            'country' => 'País',
            'location' => 'Ubicación',
            'meta' => 'Meta',
        ],
    ],

    'product_options' => [
        'singular' => 'Opción',
        'plural' => 'Opciones',
        'fields' => [
            'name' => 'Nombre',
            'type' => 'Tipo',
            'required' => 'Requerido',
            'meta' => 'Meta',
        ],
        'types' => [
            'single' => 'Selección única',
            'multiple' => 'Selección múltiple',
        ],
        'helpers' => [
            'meta' => 'Config extra opcional de la opción. Ejemplo: clave "maximo" valor "3" o clave "nota" valor "sin cebolla".',
        ],
        'placeholders' => [
            'meta_key' => 'ej: maximo',
            'meta_value' => 'ej: 3',
        ],
    ],

    'product_option_items' => [
        'singular' => 'Ítem de opción',
        'plural' => 'Ítems de opción',
        'fields' => [
            'name' => 'Nombre',
            'price' => 'Precio',
            'meta' => 'Meta',
        ],
        'helpers' => [
            'meta' => 'Datos extra opcionales del ítem (clave/valor).',
        ],
        'placeholders' => [
            'meta_key' => 'ej: porcion',
            'meta_value' => 'ej: grande',
        ],
    ],

    'promotions' => [
        'singular' => 'Promoción',
        'plural' => 'Promociones',
        'fields' => [
            'code' => 'Código',
            'type' => 'Tipo',
            'value' => 'Valor',
            'starts_at' => 'Comienza',
            'ends_at' => 'Termina',
            'products' => 'Productos',
            'products_count' => 'Productos',
            'meta' => 'Meta',
        ],
        'types' => [
            'percentage' => 'Porcentaje',
            'fixed' => 'Monto fijo',
            'free_delivery' => 'Envío gratis',
        ],
        'helpers' => [
            'meta' => 'Reglas/datos extra opcionales. Ejemplo: clave "minimo_pedido_total" valor "20" o clave "acumulable" valor "false".',
        ],
        'placeholders' => [
            'meta_key' => 'ej: minimo_pedido_total',
            'meta_value' => 'ej: 20',
        ],
    ],

    'orders' => [
        'singular' => 'Pedido',
        'plural' => 'Pedidos',
        'sections' => [
            'general' => 'Información',
            'timestamps' => 'Fechas',
        ],
        'fields' => [
            'id' => '#',
            'user' => 'Cliente',
            'restaurant' => 'Sucursal',
            'fulfillment_type' => 'Tipo de entrega',
            'status' => 'Estado',
            'total_amount' => 'Total',
            'currency' => 'Moneda',
            'metadata' => 'Meta',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ],
        'fulfillment_types' => [
            'delivery' => 'Delivery',
            'pickup' => 'Retiro en el local',
            'dine_in' => 'En el local',
        ],
        'statuses' => [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'preparing' => 'Preparando',
            'ready' => 'Lista',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        ],
        'realtime' => [
            'new_orders' => 'Nuevos pedidos',
            'helper' => 'Se insertan en tiempo real sin recargar.',
            'clear' => 'Limpiar',
            'view' => 'Ver',
        ],
        'helpers' => [
            'metadata' => 'Datos extra opcionales en formato clave/valor. Ejemplo: clave "tipo_entrega" valor "delivery" o clave "nota_cliente" valor "sin hielo".',
        ],
        'placeholders' => [
            'metadata_key' => 'ej: tipo_entrega',
            'metadata_value' => 'ej: delivery',
        ],
    ],

    'order_items' => [
        'singular' => 'Ítem',
        'plural' => 'Ítems',
        'fields' => [
            'product' => 'Producto',
            'quantity' => 'Cantidad',
            'unit_price' => 'Precio unitario',
            'total_price' => 'Total',
            'meta' => 'Meta',
        ],
        'helpers' => [
            'meta' => 'Datos extra opcionales del ítem. Ejemplo: clave "nota" valor "sin cebolla".',
        ],
        'placeholders' => [
            'meta_key' => 'ej: nota',
            'meta_value' => 'ej: sin cebolla',
        ],
    ],
];
