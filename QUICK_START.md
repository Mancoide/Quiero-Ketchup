# QUICK START - Contabot

Guía rápida para poner Contabot en funcionamiento en 5 minutos.

## Opción 1: Inicio Automatizado (Recomendado)

```bash
# 1. Navegar a la carpeta del proyecto
cd /Users/guillermoleguizamon/Desktop/CONTABILIDAD

# 2. Ejecutar script de setup
./setup.sh
```

El script hará automáticamente:
- ✅ Crear archivo .env
- ✅ Levantar Docker
- ✅ Instalar dependencias (Composer, npm)
- ✅ Generar APP_KEY
- ✅ Ejecutar migraciones
- ✅ Compilar assets

## Opción 2: Inicio Manual

### Paso 1: Configurar variables de entorno
```bash
cp .env.example .env
```

### Paso 2: Levantar Docker
```bash
docker compose up -d
```

### Paso 3: Esperar servicios (30 segundos)
```bash
sleep 30
```

### Paso 4: Instalar dependencias
```bash
docker compose exec app composer install
docker compose exec app npm install
```

### Paso 5: Generar APP_KEY
```bash
docker compose exec app php artisan key:generate
```

### Paso 6: Ejecutar migraciones
```bash
docker compose exec app php artisan migrate
```

### Paso 7: Compilar assets
```bash
docker compose exec app npm run build
```

## Verificar que Funciona

```bash
curl http://localhost:8000/api/health
```

Deberías ver:
```json
{
  "status": "ok",
  "service": "Contabot API",
  "version": "1.0.0",
  "timestamp": "2024-04-27T10:30:00Z"
}
```

## Primeras Pruebas

### 1. Crear archivo de prueba (CSV)
```bash
cat > /tmp/test_reconciliation.csv << 'EOF'
Fecha,Descripción,Monto,Tipo
27/04/2024,Pago a proveedor,1000.00,Débito
26/04/2024,Ingreso ventas,2500.00,Crédito
25/04/2024,Comisión bancaria,50.00,Débito
EOF
```

### 2. Subir archivo
```bash
curl -X POST http://localhost:8000/api/files/upload-reconciliation \
  -F "file=@/tmp/test_reconciliation.csv" \
  -F "file_type=internal_system_export"
```

Respuesta esperada:
```json
{
  "success": true,
  "message": "Archivo de conciliación cargado correctamente",
  "file_id": 1
}
```

## Acceso a Servicios

| Servicio | URL | Usuario | Contraseña |
|----------|-----|---------|-----------|
| API | http://localhost:8000 | N/A | N/A |
| Database (PostgreSQL) | localhost:5432 | contabot | contabot123 |
| Redis | localhost:6379 | N/A | N/A |

## Comandos Docker Útiles

```bash
# Ver logs en tiempo real
docker compose logs -f app

# Entrar al contenedor
docker compose exec app bash

# Ejecutar comando en el contenedor
docker compose exec app php artisan tinker

# Ver estado de contenedores
docker compose ps

# Detener todos los servicios
docker compose stop

# Eliminar todo (CUIDADO)
docker compose down -v
```

## Solucionar Problemas

### "Connection refused" en la API
**Solución**: Esperar 30 segundos a que los contenedores estén completamente listos.

### Error de permisos en storage
```bash
docker compose exec app chown -R www-data:www-data /var/www/storage
```

### Base de datos vacía
```bash
docker compose exec app php artisan migrate --fresh
```

### Limpiar caché
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

## Estructura de Carpetas Importante

```
contabot/
├── app/
│   ├── Models/              # Modelos de base de datos
│   ├── Http/Controllers/    # Controladores de API
│   └── Services/            # Servicios (lógica de negocio)
├── routes/
│   └── api.php              # Rutas de la API
├── database/
│   └── migrations/          # Migraciones de base de datos
├── scripts/
│   ├── parse_pdf.py         # Procesar PDFs
│   └── parse_spreadsheet.py # Procesar hojas de cálculo
├── storage/
│   ├── bank-statements/     # PDFs de extractos bancarios
│   └── reconciliation-files/ # Archivos de reconciliación
├── docker-compose.yml       # Configuración de Docker
├── Dockerfile               # Imagen Docker personalizada
└── .env                     # Variables de entorno
```

## Próximos Pasos

1. **Crear usuario**: `docker compose exec app php artisan tinker`
2. **Cargar datos de prueba**: Usa los ejemplos de la API
3. **Consultar resultados**: GET `/api/reconciliation/result/{id}`

## Documentación Completa

Ver [README.md](README.md) para documentación detallada.

---

¿Necesitas ayuda? Revisa los logs:
```bash
docker compose logs app
```
