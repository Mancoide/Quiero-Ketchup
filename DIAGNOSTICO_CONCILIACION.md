# Guía de Diagnóstico y Mejoras - Sistema de Conciliación

## ¿Qué se mejoró?

He realizado mejoras significativas al sistema de conciliación bancaria para manejar mejor los PDFs:

### 1. **Parser de PDF Mejorado** (`parse_pdf.py`)
- ✅ **Múltiples estrategias de extracción:**
  - Primero intenta extraer tablas (más preciso)
  - Si no hay tablas, busca líneas con fechas
  - Como fallback final, extrae todo el texto

- ✅ **Detección de secciones de conciliación:**
  - Identifica automáticamente secciones como:
    - DEPOSITOS (CREDITOS) NO REG. X BANCO
    - CHEQUES ADELANTADOS
    - CHEQUES (DEBITOS) NO REG. X BANCO
    - CHEQUES (DEBITOS) NO CONTABILIZADOS
    - DEPOSITOS (CREDITOS) NO CONTABILIZADOS

- ✅ **Mejor manejo de formatos numéricos:**
  - Formato paraguayo: 1.000.000,50 ✓
  - Formato europeo: 1.000.000,50 ✓
  - Formato US: 1,000,000.50 ✓

- ✅ **Logging mejorado:** Muestra exactamente qué se está procesando

### 2. **Script de Diagnóstico** (`diagnose_pdf.py`)
Herramienta para analizar la estructura de tus PDFs sin procesarlos.

## ¿Cómo usar?

### Opción 1: Diagnóstico (RECOMENDADO SI FALLA)

```bash
cd scripts
python diagnose_pdf.py "20260526_114242_bank_CTA CTE 09.pdf"
python diagnose_pdf.py "20260526_114242_company_libro mayor setiembre.pdf"
```

Esto mostrará:
- Número de páginas
- Tablas encontradas
- Encabezados de columnas
- Primeras líneas de datos
- Estructura del texto

**Salida ejemplo:**
```
📄 Diagnosticando: bank_CTA CTE 09.pdf
📊 Información general:
  Total de páginas: 1

🔍 Página 1:
  📋 Tablas encontradas: 3
    Tabla 1:
      Filas: 15
      Columnas en encabezado: 6
      Encabezado: ['FECHA', 'DESCRIPCION', 'REFERENCIA', 'DEBITO', 'CREDITO', 'SALDO']
```

### Opción 2: Procesar con Conciliación

```bash
cd scripts
python reconcile_transactions.py \
  "20260526_114242_bank_CTA CTE 09.pdf" \
  "20260526_114242_company_libro mayor setiembre.pdf" \
  "reconciliation_21_20260526_114246.xlsx"
```

## Qué Hacer Si Sigue Fallando

### Paso 1: Ejecutar Diagnóstico
```bash
python diagnose_pdf.py tu_archivo.pdf 2>&1 | tee diagnostico.txt
```

### Paso 2: Compartir Salida
Si sigue sin funcionar, comparte:
1. La salida del diagnóstico
2. Una foto o pantalla del PDF original

### Paso 3: Áreas Clave a Revisar

**Si no encontró tablas:**
- El PDF podría tener un formato muy especial
- El contenido podría estar como imágenes (necesitaría OCR)

**Si encontró tablas pero sin datos:**
- Los encabezados podrían ser diferentes a lo esperado
- El PDF podría estar vacío o tener estructura anómala

**Si extrae transacciones pero incorrectamente:**
- Los separadores de miles/decimales podrían ser diferentes
- Las columnas podrían estar en orden diferente

## Estructura Esperada del PDF

Basado en tu descripción y la imagen compartida:

### Banco (CTA CTE 09.pdf)
```
ENCABEZADO: [Fecha, Descripción, Referencia, Débito, Crédito, Saldo]
FILA: [01/09/2025, DEPOSITO NRO 839911, 839911, , 1.000.000, 84.354.886]
```

### Mayor (libro mayor setiembre.pdf)
```
Secciones:
- DEPOSITOS (CREDITOS) NO REG. X BANCO (están en mayor, no en banco)
- CHEQUES (DEBITOS) NO REG. X BANCO (para salarios, sin movimiento en banco)
- CHEQUES (DEBITOS) NO CONTABILIZADOS (diferencia en monto)
- DEPOSITOS (CREDITOS) NO CONTABILIZADOS (en banco pero no en mayor)
```

## Variables Que Busca el Parser

### Encabezados de Columnas (cualquier variación)
```
Fecha: fecha, date, día, data, fecha_mov
Descripción: descripción, description, concepto, descr, descripcion
Débito: débito, debito, gasto, salida, debe
Crédito: crédito, credito, ingreso, entrada, haber
Referencia: referencia, reference, comprobante, nro, numero
Saldo: saldo, balance, sal
```

### Tipos de Movimiento Detectados
- **Débito**: cheque, pago, cargo, débito, faltante
- **Crédito**: depósito, crédito, haber, transferencia, abono

## Próximos Pasos Recomendados

1. ✅ Ejecuta el diagnóstico en tus PDFs
2. ✅ Verifica que el parser lea datos correctamente
3. ✅ Si hay problemas, ajusta columnas según necesidad
4. ✅ Prueba la conciliación completa

## Soporte Adicional

Si necesitas ajustar algo específico, puedo:
- Cambiar detección de columnas
- Ajustar formatos de números
- Crear parser especializado para tu formato
- Agregar detección de más secciones de conciliación
