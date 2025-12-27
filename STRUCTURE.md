- addresses 
    id UUID PRIMARY KEY
    user_id UUID REFERENCES users(id) ON DELETE CASCADE
    label TEXT -- "Casa", "Trabajo"
    street TEXT
    city TEXT
    postal_code TEXT
    country TEXT
    lat DOUBLE PRECISION
    lng DOUBLE PRECISION
    geo GEOGRAPHY(POINT, 4326) -- indexar
    is_default BOOLEAN
    created_at, 
    updated_at

- restaurants
    id UUID PRIMARY KEY
    name TEXT
    description TEXT
    owner_user_id UUID REFERENCES users(id)
    is_active BOOLEAN
    created_at, updated_at

- locations (sucursales)
    id UUID PRIMARY KEY
    restaurant_id UUID REFERENCES restaurants(id) ON DELETE CASCADE
    address_id UUID REFERENCES addresses(id) -- dirección de la sucursal
    lat DOUBLE PRECISION
    lng DOUBLE PRECISION
    geo GEOGRAPHY(POINT,4326)
    opening_hours JSONB -- estructura flexible
    is_open BOOLEAN
    created_at, updated_at

- categories
    id UUID PRIMARY KEY
    restaurant_id UUID REFERENCES restaurants(id) -- o NULL si global
    name TEXT
    parent_id UUID NULL REFERENCES categories(id) -- para subcategorias
    position INT

- products
    id UUID PRIMARY KEY
    restaurant_id UUID REFERENCES restaurants(id)
    category_id UUID REFERENCES categories(id)
    name TEXT
    description TEXT
    price_cents INT
    sku TEXT
    active BOOLEAN
    inventory INT NULL
    metadata JSONB
    created_at, updated_at

- product_options
    id UUID PRIMARY KEY
    product_id UUID REFERENCES products(id) ON DELETE CASCADE
    name TEXT -- "Agregar carne" / "Aderezo"
    type TEXT -- enum: 'single'|'multiple' (o usar check)
    required BOOLEAN
    min_select INT
    max_select INT

- product_option_items
    id UUID PRIMARY KEY
    product_option_id UUID REFERENCES product_options(id) ON DELETE CASCADE
    name TEXT
    price_delta_cents INT DEFAULT 0
    is_default BOOLEAN

- orders
    id UUID PRIMARY KEY
    user_id UUID REFERENCES users(id)
    restaurant_id UUID REFERENCES restaurants(id)
    origin_location_id UUID REFERENCES locations(id) -- sucursal
    address_id UUID REFERENCES addresses(id) -- entrega
    status TEXT -- enum: pending, preparing, assigned, picked, delivered, cancelled
    subtotal_cents INT
    shipping_cents INT
    discount_cents INT
    distance_m INT
    delivery_estimated_minutes INT
    total_cents INT
    payment_method TEXT
    payment_status TEXT
    assigned_rider_id UUID NULL REFERENCES users(id)
    metadata JSONB
    created_at, updated_at

- order_items
    id UUID PRIMARY KEY
    order_id UUID REFERENCES orders(id) ON DELETE CASCADE
    product_id UUID REFERENCES products(id)
    name TEXT
    qty INT
    unit_price_cents INT
    total_price_cents INT

- order_item_options
    id UUID PRIMARY KEY
    order_item_id UUID REFERENCES order_items(id) ON DELETE CASCADE
    product_option_item_id UUID REFERENCES product_option_items(id)
    name TEXT
    price_delta_cents INT

- assignments (historial de asignaciones)
    id UUID PRIMARY KEY
    order_id UUID REFERENCES orders(id) ON DELETE CASCADE
    rider_id UUID REFERENCES riders(id)
    assigned_at TIMESTAMP
    accepted_at TIMESTAMP NULL
    picked_at TIMESTAMP NULL
    delivered_at TIMESTAMP NULL
    status TEXT

- promotions / coupons
    id UUID PK
    code TEXT UNIQUE
    type TEXT -- 'amount'|'percent'
    value INT
    restaurant_id UUID NULL -- si aplica a un restaurant
    valid_from TIMESTAMP
    valid_to TIMESTAMP
    usage_limit INT
    metadata JSONB

- loyalty_points / point_transactions
    id UUID PK
    user_id UUID REFERENCES users(id)
    points INT
    reason TEXT
    order_id UUID NULL
    created_at TIMESTAMP

- reviews 
    id UUID PK
    user_id UUID REFERENCES users(id)
    restaurant_id UUID REFERENCES restaurants(id)
    order_id UUID NULL
    rating INT -- 1..5
    comment TEXT
    created_at, updated_at

- cms_pages
    id (PK)
    slug
    title
    body
    seo_title
    seo_description
    is_published
    published_at
    created_at
    updated_at

- banner_types
    id (PK)
    name
    code
    description
    created_at
    updated_at

Relaciones:
    Un banner_type puede tener muchos banners.

- banners
    id (PK)
    banner_type_id (FK → banner_types.id)
    media_id (FK → media.id)
    title
    alt
    target_url
    placement
    start_at
    end_at
    weight
    is_active
    metadata (JSON)
    created_at
    updated_at

Relaciones:
Un banner pertenece a un banner_type.



Índices y optimizaciones recomendadas

Índice espacial en locations.geo y en addresses.geo: CREATE INDEX ON locations USING GIST (geo);

Índice en orders(status), orders(restaurant_id), orders(assigned_rider_id) para búsquedas rápidas.

Índices en products(restaurant_id, active) y categories(restaurant_id).

Índice compuesto para búsquedas de menú por restaurante + categoría.

Utilizar GIN sobre campos metadata JSONB si vas a consultar por claves dentro del JSON.
