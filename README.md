# Contabot

**Sistema Automatizado de Reconciliación Bancaria y Contabilidad**

Contabot es una plataforma inteligente que automatiza la comparación entre extractos bancarios y archivos de conciliación de sistemas internos. Utiliza procesamiento de PDF con inteligencia artificial para extraer transacciones y realizar matching automático.

## Características Principales

- 📄 **Procesamiento de PDFs**: Extrae automáticamente transacciones de extractos bancarios
- 📊 **Análisis de Hojas de Cálculo**: Soporta CSV y XLSX para archivos de conciliación
- 🤖 **Matching Automático**: Algoritmo inteligente de matching basado en fecha, monto y descripción
- 📈 **Reportes Detallados**: Genera reportes con transacciones coincididas y discrepancias
- ⚡ **API REST**: Interfaz API completa para integración
- 🐳 **Docker Ready**: Configuración Docker completa para desplegar fácilmente

## Stack Tecnológico

- **Backend**: Laravel 12 + PHP 8.3
- **Base de Datos**: PostgreSQL
- **Caché**: Redis
- **Procesamiento**: Python 3 (pdfplumber, pandas)
- **Frontend**: Vue 3 + Tailwind CSS
- **Contenedorización**: Docker & Docker Compose

## Requisitos Previos

- Docker & Docker Compose
- Git

## Instalación Rápida

### 1. Clonar el repositorio
```bash
git clone <tu-repo>
cd contabot
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
```

### 3. Levantar Docker
```bash
docker compose up -d
```

### 4. Preparar la base de datos
```bash
docker compose exec app php artisan migrate
```

### 5. Verificar que está funcionando
```bash
curl http://localhost:8000/api/health
```

## Uso de la API

### Subir Extracto Bancario
```bash
curl -X POST http://localhost:8000/api/files/upload-bank-statement \
  -F "file=@extracto.pdf" \
  -F "bank_name=Banco XYZ" \
  -F "account_number=123456789"
```

### Subir Archivo de Conciliación
```bash
curl -X POST http://localhost:8000/api/files/upload-reconciliation \
  -F "file=@reconciliacion.csv" \
  -F "file_type=internal_system_export"
```

### Procesar Reconciliación
```bash
curl -X POST http://localhost:8000/api/reconciliation/reconcile \
  -H "Content-Type: application/json" \
  -d '{
    "bank_statement_id": 1,
    "reconciliation_file_id": 1
  }'
```

### Obtener Resultado
```bash
curl http://localhost:8000/api/reconciliation/result/1
```

## Comandos Útiles

### Entrar al contenedor
```bash
docker compose exec app bash
```

### Ver logs
```bash
docker compose logs -f app
```

### Limpiar Docker
```bash
docker compose down -v
```

## Algoritmo de Matching

- **Fecha**: ±2 días = 30 puntos
- **Monto**: Exacto = 50 puntos, ±5% = 30 puntos
- **Descripción**: >70% similitud = 20 puntos

Score mínimo: 75 puntos

## Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/health` | Health check |
| POST | `/api/files/upload-bank-statement` | Subir extracto |
| POST | `/api/files/upload-reconciliation` | Subir reconciliación |
| POST | `/api/reconciliation/reconcile` | Procesar |
| GET | `/api/reconciliation/result/{id}` | Obtener resultado |

## Licencia

MIT License

---

**Contabot** - Automatizando la reconciliación bancaria ⚡
