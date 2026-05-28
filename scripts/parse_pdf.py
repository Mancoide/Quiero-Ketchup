#!/usr/bin/env python3
"""
Parse PDF bank statements and reconciliation documents
Supports: Bank statements, internal system exports, reconciliation reports
"""

import sys
import json
import re
from collections import Counter
from datetime import datetime
from pathlib import Path

# pip install pdfplumber
import pdfplumber


def extract_section_name(text):
    """Detecta el nombre de la sección de conciliación"""
    text_upper = text.upper()
    
    sections = [
        'DEPOSITOS (CREDITOS) NO REG. X BANCO',
        'CHEQUES ADELANTADOS',
        'CHEQUES (DEBITOS) NO REG. X BANCO',
        'CHEQUES (DEBITOS) NO CONTABILIZADOS',
        'DEPOSITOS (CREDITOS) NO CONTABILIZADOS',
        'DEPOSITOS/CREDITOS NO REG',
        'CHEQUES NO REG',
        'CHEQUES DEBITOS',
    ]
    
    for section in sections:
        if section.upper() in text_upper:
            return section
    
    return None


def is_transaction_table_header(headers):
    """Detecta si una fila es encabezado de tabla de transacciones"""
    header_text = ' '.join(str(h or '').lower() for h in headers)
    
    # Buscar palabras clave que identifiquen una tabla de transacciones
    transaction_keywords = [
        ('dia' in header_text or 'day' in header_text) and ('hora' in header_text or 'hour' in header_text),
        'movimiento' in header_text and 'descripción' in header_text,
        'debe' in header_text and 'haber' in header_text,
        'debit' in header_text and 'credit' in header_text,
        'fecha' in header_text and ('descripción' in header_text or 'monto' in header_text),
    ]
    
    return any(transaction_keywords)


def is_header_or_summary_row(row):
    """Detecta si una fila es encabezado o sumario, no transacción real"""
    if not row:
        return True
    
    # Convertir a strings
    row_text = ' '.join(str(x or '').lower() for x in row)
    
    # Si contiene palabras clave de encabezado/resumen
    summary_keywords = [
        'banco', 'ruc', 'cuenta', 'saldo', 'telefono', 'desde', 'hasta',
        'retenido', 'disponible', 'bloqueado', 'sobregiro', 'total',
        'movimientos de cuenta', 'resumen', 'contable', 'anterior',
    ]
    
    return any(keyword in row_text for keyword in summary_keywords)


def infer_statement_period(text, pdf_path=None):
    """Detecta el rango del extracto para completar líneas que solo traen día."""
    text = str(text or "")

    range_match = re.search(
        r"desde\s+el\s+(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})\s+hasta\s+el\s+(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})",
        text,
        flags=re.IGNORECASE,
    )
    if range_match:
        start = parse_date_obj(range_match.group(1))
        end = parse_date_obj(range_match.group(2))
        if start and end:
            return start, end

    dates = []
    for date_text in re.findall(r"\d{1,2}[/-]\d{1,2}[/-]\d{2,4}", text):
        parsed = parse_date_obj(date_text)
        if parsed:
            dates.append(parsed)

    if dates:
        (year, month), _count = Counter((date.year, date.month) for date in dates).most_common(1)[0]
        start = datetime(year, month, 1)
        return start, start

    if pdf_path:
        name = Path(pdf_path).stem
        match = re.search(r"(?:^|[_\s-])(\d{1,2})(?:[_\s-]|$)", name)
        if match:
            month = int(match.group(1))
            if 1 <= month <= 12:
                year_match = re.search(r"(20\d{2})", name)
                year = int(year_match.group(1)) if year_match else datetime.now().year
                start = datetime(year, month, 1)
                return start, start

    today = datetime.now()
    return datetime(today.year, today.month, 1), datetime(today.year, today.month, 1)


def parse_date_obj(date_str):
    for fmt in ("%d/%m/%Y", "%d-%m-%Y", "%d/%m/%y", "%d-%m-%y", "%Y-%m-%d", "%Y/%m/%d"):
        try:
            return datetime.strptime(str(date_str).strip(), fmt)
        except ValueError:
            continue
    return None


def date_from_day(day, period):
    """Completa una fecha usando el rango del extracto."""
    day = int(day)
    start, end = period
    chosen = start

    if (start.year, start.month) != (end.year, end.month) and day < start.day:
        chosen = end

    return f"{day:02d}/{chosen.month:02d}/{chosen.year}"



def parse_bank_statement(pdf_path):
    """
    Parse bank statement PDF and extract transactions
    Formato esperado:
    - Fecha | Descripción | Referencia | Débito | Crédito | Saldo
    
    Intenta múltiples estrategias:
    1. Extracción de tablas (filtrando encabezados falsos)
    2. Parsing de líneas del texto
    3. Extracción general del texto
    """
    transactions = []
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            print(f"[PDF Parser] Abriendo PDF: {pdf_path}", file=sys.stderr)
            print(f"[PDF Parser] Total de páginas: {len(pdf.pages)}", file=sys.stderr)
            page_texts = [page.extract_text() or "" for page in pdf.pages]
            period = infer_statement_period("\n".join(page_texts), pdf_path)
            print(f"[PDF Parser] Período detectado: {period[0].strftime('%d/%m/%Y')} - {period[1].strftime('%d/%m/%Y')}", file=sys.stderr)
            
            # ESTRATEGIA 1: Intentar extracción de tablas
            transactions = parse_from_table(pdf_path, period)
            
            if transactions:
                print(f"[PDF Parser] ✓ Éxito: {len(transactions)} transacciones de tablas", file=sys.stderr)
                return transactions
            
            print(f"[PDF Parser] ⚠ No se extranjeron transacciones de tablas, intentando texto...", file=sys.stderr)
            
            # ESTRATEGIA 2: Parsing de líneas con regex
            for page_idx, text in enumerate(page_texts):
                print(f"[PDF Parser] Página {page_idx + 1}: Analizando texto ({len(text) if text else 0} caracteres)...", file=sys.stderr)
                
                if not text:
                    continue
                
                lines = text.split('\n')
                for line in lines:
                    if re.search(r'^\s*\d{1,2}\s+\d{1,2}:\d{2}(?::\d{2})?', line):
                        transaction = parse_bank_transaction_line(line, period)
                        if transaction:
                            transactions.append(transaction)
                    elif re.search(r'\d{1,2}[/-]\d{1,2}[/-]\d{2,4}', line):
                        transaction = parse_bank_two_date_transaction_line(line) or parse_ledger_transaction_line(line) or parse_transaction_line(line)
                        if transaction:
                            transactions.append(transaction)
            
            if transactions:
                print(f"[PDF Parser] ✓ Éxito: {len(transactions)} transacciones de parsing de líneas", file=sys.stderr)
                return transactions
            
            # ESTRATEGIA 3: Extracción general del texto (fallback)
            print(f"[PDF Parser] ⚠ Intentando extracción general del texto...", file=sys.stderr)
            transactions = extract_text_transactions(pdf_path, period)
            
            if transactions:
                print(f"[PDF Parser] ✓ Éxito: {len(transactions)} transacciones de texto general", file=sys.stderr)
            else:
                print(f"[PDF Parser] ✗ No se encontraron transacciones con ninguna estrategia", file=sys.stderr)
                
    except Exception as e:
        print(f"[PDF Parser] ERROR: {str(e)}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
        return []
    
    return transactions


def parse_from_table(pdf_path, period=None):
    """
    Extract transactions from PDF tables
    Estrategia: Buscar la tabla real de transacciones, ignorando tablas de encabezado
    """
    transactions = []
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            current_section = None
            last_transaction_headers = None
            
            for page_idx, page in enumerate(pdf.pages):
                tables = page.extract_tables()
                text = page.extract_text()
                print(f"[PDF Parser] Página {page_idx + 1}: {len(tables) if tables else 0} tablas encontradas", file=sys.stderr)
                
                if text:
                    current_section = extract_section_name(text) or current_section
                    if current_section:
                        print(f"[PDF Parser] Sección: {current_section}", file=sys.stderr)
                
                if not tables:
                    continue
                
                # Buscar la tabla real de transacciones.
                # Algunos bancos generan muchas tablas pequeñas: una con el
                # encabezado y luego pares de filas con movimientos. Por eso
                # conservamos el último encabezado válido y también aceptamos
                # filas que tienen formato DIA HORA aunque la tabla no traiga
                # encabezado propio.
                for table_idx, table in enumerate(tables):
                    if len(table) <= 1:
                        raw_extracted = extract_transactions_from_raw_bank_rows(table, last_transaction_headers, current_section, period)
                        if raw_extracted:
                            transactions.extend(raw_extracted)
                            print(f"[PDF Parser]   Tabla {table_idx + 1}: PROCESANDO filas bancarias ({len(table)} filas)", file=sys.stderr)
                            print(f"[PDF Parser]     Transacciones extraídas: {len(raw_extracted)}", file=sys.stderr)
                            continue

                        print(f"[PDF Parser]   Tabla {table_idx + 1}: Saltada (< 2 filas)", file=sys.stderr)
                        continue
                    
                    # Obtener encabezado
                    headers = [str(h or '').lower().strip() for h in table[0]]
                    header_text = ' '.join(headers)
                    
                    # PASO 1: Filtrar tablas falsas (encabezados del banco)
                    # Si la primera fila es muy larga y tiene palabras clave de encabezado, saltarla
                    if len(header_text) > 200 and is_header_or_summary_row(table[0]):
                        print(f"[PDF Parser]   Tabla {table_idx + 1}: Saltada (es encabezado del banco, {len(header_text)} caracteres)", file=sys.stderr)
                        continue
                    
                    # PASO 2: Verificar si es tabla de transacciones
                    is_transaction_table = is_transaction_table_header(headers)
                    
                    if not is_transaction_table:
                        raw_extracted = extract_transactions_from_raw_bank_rows(table, last_transaction_headers, current_section, period)
                        if raw_extracted:
                            transactions.extend(raw_extracted)
                            print(f"[PDF Parser]   Tabla {table_idx + 1}: PROCESANDO filas bancarias ({len(table)} filas)", file=sys.stderr)
                            print(f"[PDF Parser]     Transacciones extraídas: {len(raw_extracted)}", file=sys.stderr)
                            continue

                        # Intentar buscar columnas estándar como fallback
                        date_col = find_column_index(headers, ['fecha', 'date', 'día', 'data', 'dia'])
                        desc_col = find_column_index(headers, ['descripción', 'concepto', 'description', 'descr'])
                        
                        if date_col is None and desc_col is None:
                            print(f"[PDF Parser]   Tabla {table_idx + 1}: Saltada (sin columnas de transacción)", file=sys.stderr)
                            continue
                    
                    print(f"[PDF Parser]   Tabla {table_idx + 1}: PROCESANDO ({len(table)} filas)", file=sys.stderr)
                    print(f"[PDF Parser]     Encabezado: {headers[:6]}", file=sys.stderr)
                    
                    # PASO 3: Procesar tabla de transacciones
                    last_transaction_headers = headers
                    extracted = extract_transactions_from_bank_table(table, headers, current_section, period)
                    transactions.extend(extracted)
                    print(f"[PDF Parser]     Transacciones extraídas: {len(extracted)}", file=sys.stderr)
                    
    except Exception as e:
        print(f"[PDF Parser] Error parsing table: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
    
    return transactions


def extract_transactions_from_raw_bank_rows(table, headers=None, section=None, period=None):
    """Extrae movimientos desde tablas fragmentadas sin encabezado confiable."""
    transactions = []

    for row in table:
        transaction = parse_bank_transaction_cells(row, headers, section, period)
        if transaction:
            transactions.append(transaction)

    return transactions


def parse_bank_transaction_cells(row, headers=None, section=None, period=None):
    """
    Parsea una fila tabular de banco con formato:
    DIA | HORA | MOVIMIENTO | ... | DESCRIPCION | DEBE | HABER | SALDO
    """
    if not row:
        return None

    cells = [str(cell or '').strip() for cell in row]
    if len(cells) < 4:
        return None

    day_idx = None
    for idx, cell in enumerate(cells[:3]):
        if re.fullmatch(r'\d{1,2}', cell):
            day_idx = idx
            break

    if day_idx is None:
        return None

    time_idx = None
    for idx in range(day_idx + 1, min(day_idx + 3, len(cells))):
        if re.fullmatch(r'\d{1,2}:\d{2}(?::\d{2})?', cells[idx]):
            time_idx = idx
            break

    if time_idx is None:
        return None

    date_str = date_from_day(cells[day_idx], period or infer_statement_period(""))
    parsed_date = parse_date(date_str)

    normalized_headers = [str(header or '').lower().strip() for header in (headers or [])]
    debit_col = find_column_index(normalized_headers, ['débito', 'debito', 'gasto', 'salida', 'debe'])
    credit_col = find_column_index(normalized_headers, ['crédito', 'credito', 'ingreso', 'entrada', 'haber'])
    balance_col = find_column_index(normalized_headers, ['saldo', 'balance', 'sal'])
    desc_col = find_column_index(normalized_headers, ['descripción', 'concepto', 'description', 'descr', 'descripcion'])
    ref_col = find_column_index(normalized_headers, ['referencia', 'reference', 'comprobante', 'nro', 'numero', 'movimiento'])

    debit_amount = amount_from_cell(cells, debit_col)
    credit_amount = amount_from_cell(cells, credit_col)
    balance = amount_from_cell(cells, balance_col)

    amount_cells = []
    for idx, cell in enumerate(cells):
        if idx <= time_idx:
            continue
        parsed_amount = parse_amount_like_cell(cell)
        if parsed_amount > 0:
            amount_cells.append((idx, parsed_amount))

    if debit_amount == 0 and credit_amount == 0 and amount_cells:
        amount_idx, amount_value = amount_cells[-2] if len(amount_cells) >= 2 else amount_cells[-1]
        amount = amount_value
        if balance in (None, 0) and len(amount_cells) >= 2:
            balance = amount_cells[-1][1]
    else:
        amount_idx = None
        amount = debit_amount if debit_amount > 0 else credit_amount

    if amount <= 0:
        return None

    if balance is not None and balance <= 0:
        balance = None

    reference = ''
    if ref_col is not None and ref_col < len(cells):
        reference = cells[ref_col]
    for cell in cells[time_idx + 1:]:
        if re.fullmatch(r'\d{4,}', cell):
            if not reference or len(cell) > len(reference):
                reference = cell
            break

    description = ''
    if desc_col is not None and desc_col < len(cells):
        description = cells[desc_col]
    if not description:
        stop_idx = amount_idx if amount_idx is not None else len(cells)
        desc_parts = []
        for idx, cell in enumerate(cells[time_idx + 1:stop_idx], time_idx + 1):
            if not cell or cell == reference:
                continue
            if re.fullmatch(r'\d{1,3}', cell):
                continue
            if parse_amount_like_cell(cell) > 0:
                continue
            desc_parts.append(cell)
        description = ' '.join(desc_parts).strip()

    if not description and not reference:
        return None

    tx_type = 'unknown'
    if debit_amount > 0:
        tx_type = 'debit'
    elif credit_amount > 0:
        tx_type = 'credit'
    else:
        tx_type = infer_transaction_type(description)

    return {
        'date': parsed_date,
        'description': description,
        'reference': reference,
        'amount': amount,
        'type': tx_type,
        'balance': balance,
        'debit': amount if tx_type == 'debit' else None,
        'credit': amount if tx_type == 'credit' else None,
        'section': section,
    }


def amount_from_cell(cells, idx):
    if idx is None or idx >= len(cells):
        return 0
    return parse_amount_like_cell(cells[idx])


def parse_amount_like_cell(cell):
    cell = str(cell or '').strip()
    if not cell or not re.search(r'\d', cell):
        return 0
    if not any(separator in cell for separator in ['.', ',']):
        return 0
    if not re.fullmatch(r'(?:Gs\.?\s*)?-?\d[\d.,]*', cell):
        return 0
    return parse_amount(cell)


def infer_transaction_type(description):
    desc_upper = str(description or '').upper()
    if any(word in desc_upper for word in ['HABER', 'CREDITO', 'CRÉDITO', 'DEPOSITO', 'DEPÓSITO', 'TRF', 'INTRBN', 'SIPAP']):
        return 'credit'
    if any(word in desc_upper for word in ['DEBE', 'DEBITO', 'DÉBITO', 'CHEQUE', 'PAGO']):
        return 'debit'
    return 'unknown'


def extract_transactions_from_bank_table(table, headers, section=None, period=None):
    """
    Extrae transacciones de una tabla verificada de transacciones.
    Maneja múltiples formatos:
    - Formato 1: FECHA | DESCRIPCIÓN | DÉBITO | CRÉDITO | SALDO
    - Formato 2: DIA HORA MOVIMIENTO DESCRIPCIÓN DEBE HABER SALDO
    """
    transactions = []
    
    # Encontrar índices de columnas
    date_col = find_column_index(headers, ['fecha', 'date', 'día', 'data', 'dia'])
    desc_col = find_column_index(headers, ['descripción', 'concepto', 'description', 'descr', 'descripcion'])
    debit_col = find_column_index(headers, ['débito', 'debito', 'gasto', 'salida', 'debe'])
    credit_col = find_column_index(headers, ['crédito', 'credito', 'ingreso', 'entrada', 'haber'])
    ref_col = find_column_index(headers, ['referencia', 'reference', 'comprobante', 'nro', 'numero', 'movimiento'])
    balance_col = find_column_index(headers, ['saldo', 'balance', 'sal'])
    
    # Si no hay columna de descripción, intentar combinar múltiples columnas
    if desc_col is None and len(headers) > 2:
        # En formato tipo: DIA HORA MOVIMIENTO DESCRIPCIÓN...
        # Las columnas 3+ podrían ser descripción
        desc_col_start = 2
    else:
        desc_col_start = None
    
    for row_idx, row in enumerate(table[1:], 1):
        if not row or not any(str(x or '').strip() for x in row):
            continue
        
        try:
            # Extraer fecha
            date_str = ''
            if date_col is not None and date_col < len(row):
                date_str = str(row[date_col] or '').strip()
            
            # Si no hay fecha, intentar de las primeras columnas
            if not date_str and len(row) > 0:
                for i in range(min(3, len(row))):
                    cell = str(row[i] or '').strip()
                    if re.search(r'\d{1,2}[/-]\d{1,2}', cell) or (i == 0 and re.search(r'^\d{1,2}$', cell)):
                        date_str = cell
                        break
            
            if not date_str or not re.search(r'\d', date_str):
                continue
            
            # Construir fecha completa si solo tenemos el día
            if len(date_str) <= 2 and date_str.isdigit():
                date_str = date_from_day(date_str, period or infer_statement_period(""))
            
            parsed_date = parse_date(date_str)
            
            # Extraer descripción
            description = ''
            if desc_col is not None and desc_col < len(row):
                description = str(row[desc_col] or '').strip()
            elif desc_col_start is not None:
                # Combinar múltiples columnas para descripción
                desc_parts = []
                for i in range(desc_col_start, min(desc_col_start + 3, len(row))):
                    part = str(row[i] or '').strip()
                    if part:
                        desc_parts.append(part)
                description = ' '.join(desc_parts)
            
            # Extraer referencia
            reference = ''
            if ref_col is not None and ref_col < len(row):
                reference = str(row[ref_col] or '').strip()
            
            # Si tampoco hay descripción, es probablemente basura
            if not description and not reference:
                continue
            
            # Extraer montos
            debit_amount = 0
            credit_amount = 0
            tx_type = 'unknown'
            
            if debit_col is not None and debit_col < len(row) and row[debit_col]:
                debit_str = str(row[debit_col]).strip()
                debit_amount = parse_amount(debit_str)
                if debit_amount > 0:
                    tx_type = 'debit'
            
            if credit_col is not None and credit_col < len(row) and row[credit_col]:
                credit_str = str(row[credit_col]).strip()
                credit_amount = parse_amount(credit_str)
                if credit_amount > 0:
                    tx_type = 'credit'
            
            amount = debit_amount if debit_amount > 0 else credit_amount
            
            if amount == 0:
                continue
            
            # Extraer saldo
            balance = None
            if balance_col is not None and balance_col < len(row) and row[balance_col]:
                balance = parse_amount(str(row[balance_col]).strip())
                if balance <= 0:
                    balance = None
            
            transaction = {
                'date': parsed_date,
                'description': description,
                'reference': reference,
                'amount': amount,
                'type': tx_type,
                'balance': balance,
                'debit': debit_amount if debit_amount > 0 else None,
                'credit': credit_amount if credit_amount > 0 else None,
                'section': section,
            }
            
            transactions.append(transaction)
            
        except (ValueError, IndexError, TypeError) as e:
            continue
    
    return transactions



def parse_transaction_line(line):
    """
    Parse a single transaction line
    Soporta múltiples formatos
    """
    # Patrón flexible para transacciones con fecha
    # Ejemplos: 01/09/2025 DEPOSITO ... 1.000.000
    pattern = r'(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})\s+(.+?)\s+(\d[\d.,]*[.,]\d+)(?:\s+|$)'
    
    match = re.search(pattern, line)
    if not match:
        return None
    
    try:
        date_str, description, amount_str = match.groups()
        amount = parse_amount(amount_str)
        
        # Detectar tipo de transacción
        desc_upper = description.upper()
        tx_type = 'unknown'
        if any(word in desc_upper for word in ['CREDIT', 'CREDITO', 'CRÉDITO', 'HABER', 'DEPOSITO', 'DEPÓSITO', 'INTRBN', 'TRF']):
            tx_type = 'credit'
        elif any(word in desc_upper for word in ['DEBIT', 'DEBITO', 'DÉBITO', 'DEBE', 'CHEQUE', 'PAGO']):
            tx_type = 'debit'
        
        return {
            'date': parse_date(date_str),
            'description': description.strip(),
            'reference': '',
            'amount': amount,
            'type': tx_type,
            'balance': None
        }
    except (ValueError, AttributeError):
        return None


def parse_bank_two_date_transaction_line(line):
    """
    Parsea extractos con formato:
    Fecha Conf. | Fecha Mov. | Comprobante/Transacción | Débito | Crédito | Saldo
    """
    match = re.search(
        r'^\s*(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})\s+(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})\s+(.+)$',
        line,
    )
    if not match:
        return None

    date_str, _movement_date, rest = match.groups()
    tokens = rest.split()
    if len(tokens) < 4:
        return None

    numeric_positions = []
    for idx, token in enumerate(tokens):
        if re.fullmatch(r'-?\d[\d.,]*', token):
            numeric_positions.append(idx)

    if len(numeric_positions) < 3:
        return None

    debit_idx, credit_idx, balance_idx = numeric_positions[-3:]
    if balance_idx != len(tokens) - 1:
        return None

    debit_amount = parse_amount(tokens[debit_idx])
    credit_amount = parse_amount(tokens[credit_idx])
    balance = parse_amount(tokens[balance_idx])
    tx_amount = debit_amount if debit_amount > 0 else credit_amount

    if tx_amount <= 0 or balance == 0:
        return None

    description_tokens = tokens[:debit_idx]
    reference = ''
    if description_tokens and re.match(r'\d+', description_tokens[0]):
        ref_match = re.match(r'(\d+)', description_tokens[0])
        reference = ref_match.group(1) if ref_match else ''

    return {
        'date': parse_date(date_str),
        'description': ' '.join(description_tokens).strip(),
        'reference': reference,
        'amount': tx_amount,
        'type': 'debit' if debit_amount > 0 else 'credit',
        'balance': balance,
        'debit': debit_amount if debit_amount > 0 else None,
        'credit': credit_amount if credit_amount > 0 else None,
    }


def parse_ledger_transaction_line(line):
    """
    Parsea líneas de libro mayor:
    ASIENTO FECHA DETALLE DEBE HABER SALDO IMPORTE_ME ...
    """
    match = re.search(r'^\s*\d+\s+(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})\s+(.+)$', line)
    if not match:
        return None

    date_str, rest = match.groups()
    tokens = rest.split()
    amount_positions = []

    for idx, token in enumerate(tokens):
        parsed = parse_amount_like_cell(token)
        if parsed != 0 or re.fullmatch(r'0+[.,]0+', token):
            amount_positions.append((idx, parsed))

    if len(amount_positions) < 4:
        return None

    column_amounts = amount_positions[-4:]

    if len(amount_positions) >= 4 and column_amounts[1][1] == 0 and column_amounts[3][1] == 0:
        amount_idx, tx_amount = column_amounts[0]
        balance = column_amounts[2][1]
        description = ' '.join(tokens[:amount_idx + 1]).strip()
        tx_type = 'debit'
    else:
        debit_idx, debit_amount = column_amounts[0]
        credit_idx, credit_amount = column_amounts[1]
        balance = column_amounts[2][1]
        description = ' '.join(tokens[:debit_idx]).strip()
        tx_amount = debit_amount if debit_amount > 0 else credit_amount
        tx_type = 'debit' if debit_amount > 0 else 'credit'

    if tx_amount <= 0:
        return None
    if tx_type == 'unknown':
        tx_type = 'credit'
    reference = ''
    ref_match = re.search(r'(?:NRO[.:\s]*|NC|N/C|SIPAP[-\s]*|SPI[-\s]*)(\d+)', description.upper())
    if ref_match:
        reference = ref_match.group(1)

    return {
        'date': parse_date(date_str),
        'description': description,
        'reference': reference,
        'amount': tx_amount,
        'type': tx_type,
        'balance': balance if balance > 0 else None,
        'debit': tx_amount if tx_type == 'debit' else None,
        'credit': tx_amount if tx_type == 'credit' else None,
    }


def extract_text_transactions(pdf_path, period=None):
    """
    Extrae transacciones analizando el texto completo del PDF.
    Busca líneas con patrón: DIA/FECHA ... MONTO ... SALDO
    Útil como fallback cuando las tablas no se extraen bien
    """
    transactions = []
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            for page_idx, page in enumerate(pdf.pages):
                text = page.extract_text()
                print(f"[PDF Parser] Página {page_idx + 1}: Extrayendo texto ({len(text) if text else 0} caracteres)", file=sys.stderr)
                
                if not text:
                    continue
                
                # Buscar sección de transacciones (empieza con DIA HORA)
                # Patrón: DIA HORA MOVIMIENTO DESCRIPCIÓN DEBE HABER SALDO
                lines = text.split('\n')
                
                in_transactions_section = False
                for line_idx, line in enumerate(lines):
                    line_stripped = line.strip()
                    
                    # Detectar inicio de sección de transacciones
                    if 'DIA' in line and 'HORA' in line and ('DEBE' in line or 'HABER' in line):
                        in_transactions_section = True
                        print(f"[PDF Parser] Sección de transacciones encontrada en línea {line_idx}", file=sys.stderr)
                        continue
                    
                    if not in_transactions_section:
                        continue
                    
                    # Si encontramos línea vacía o resumen, salir de sección
                    if not line_stripped or 'TOTAL' in line.upper() or 'SALDO' in line.upper():
                        if 'TOTAL' in line.upper():
                            in_transactions_section = False
                    
                    # Buscar líneas con fecha y monto
                    # Patrón: 01 09:44:01 56 556178 95-ea-sys trf.intrbn... 1.000.000 60.138.620
                    if re.search(r'^\s*\d{1,2}\s+\d{1,2}:\d{2}:\d{2}', line_stripped):
                        transaction = parse_bank_transaction_line(line_stripped, period)
                        if transaction:
                            transactions.append(transaction)
    
    except Exception as e:
        print(f"[PDF Parser] Error al extraer texto: {e}", file=sys.stderr)
    
    return transactions


def parse_bank_transaction_line(line, period=None):
    """
    Parsea una línea de transacción del banco.
    Formato: 01 09:44:01 56 556178 95-ea-sys descripción 1.000.000 60.138.620
    Donde: DIA HORA CAMPO MOVIMIENTO CODIGO DESCRIPCIÓN MONTO SALDO
    """
    try:
        # Patrón flexible:
        # DIA HORA [campos] MOVIMIENTO DESCRIPCION MONTO [SALDO]
        match = re.search(r'^\s*(\d{1,2})\s+(\d{1,2}:\d{2}(?::\d{2})?)\s+(.+?)\s*$', line)
        if not match:
            return None
        
        day, time, rest = match.groups()
        tokens = rest.split()
        amount_tokens = []
        for idx, token in enumerate(tokens):
            parsed_amount = parse_amount_like_cell(token)
            if parsed_amount > 0:
                amount_tokens.append((idx, parsed_amount))

        if not amount_tokens:
            return None

        amount_idx, amount = amount_tokens[-2] if len(amount_tokens) >= 2 else amount_tokens[-1]
        saldo = amount_tokens[-1][1] if len(amount_tokens) >= 2 else None
        
        date_str = date_from_day(day, period or infer_statement_period(""))

        if amount <= 0:
            return None

        movimiento = ''
        movement_idx = None
        for idx, token in enumerate(tokens[:amount_idx]):
            if re.fullmatch(r'\d{4,}', token):
                movimiento = token
                movement_idx = idx
                break

        description_tokens = tokens[(movement_idx + 1) if movement_idx is not None else 0:amount_idx]
        description = ' '.join(description_tokens).strip()
        
        # Detectar tipo
        tx_type = infer_transaction_type(description)
        
        # Si el monto aparece solo en la columna de debe/haber, inferir tipo
        if tx_type == 'unknown':
            # Por defecto, asumir crédito si no hay indicación
            tx_type = 'debit'
        
        return {
            'date': date_str,
            'description': description.strip(),
            'reference': movimiento,
            'amount': amount,
            'type': tx_type,
            'balance': saldo if saldo and saldo > 0 else None,
        }
    
    except (ValueError, AttributeError, IndexError):
        return None


def parse_date(date_str):
    """
    Parse various date formats
    """
    date_str = str(date_str).strip()
    
    formats = [
        '%d/%m/%Y',
        '%d-%m-%Y',
        '%d/%m/%y',
        '%d-%m-%y',
        '%Y-%m-%d',
        '%m/%d/%Y',
        '%Y/%m/%d',
    ]
    
    for fmt in formats:
        try:
            date_obj = datetime.strptime(date_str, fmt)
            return date_obj.strftime('%Y-%m-%d')
        except ValueError:
            continue
    
    # Retornar hoy si no se puede parsear
    return datetime.now().strftime('%Y-%m-%d')


def parse_amount(value):
    """
    Parse amount in various formats (Paraguayan, European, US)
    Handles: 1.000.000,50 | 1,000,000.50 | 1000000.50 | 1.000,50
    """
    if not value or value == '':
        return 0.0
    
    value = str(value).strip().replace('Gs.', '').replace('$', '').strip()
    
    if not value:
        return 0.0
    
    # Detectar el patrón de separadores
    if ',' in value and '.' in value:
        # Podría ser 1.000.000,50 (Paraguayan) o 1,000,000.50 (US)
        last_comma_idx = value.rfind(',')
        last_dot_idx = value.rfind('.')
        
        if last_comma_idx > last_dot_idx:
            # 1.000.000,50 (Paraguayan - coma es decimal)
            value = value.replace('.', '').replace(',', '.')
        else:
            # 1,000,000.50 (US - punto es decimal)
            value = value.replace(',', '')
    elif value.count('.') > 1:
        # 1.000.000 (separadores de miles, sin decimales)
        value = value.replace('.', '')
    elif value.count(',') > 1:
        # 1,000,000 (separadores de miles, sin decimales)
        value = value.replace(',', '')
    else:
        # Un solo separador, decidir basado en patrón
        if ',' in value and len(value.split(',')[1]) <= 2:
            # 1.000,50 o 1000,50 (coma es decimal)
            value = value.replace(',', '.')
        elif '.' in value and len(value.split('.')[-1]) > 2:
            # Punto es separador de miles
            value = value.replace('.', '')
        elif '.' in value and len(value.split('.')[-1]) <= 2:
            # Punto es decimal
            pass
        elif ',' in value and len(value.split(',')[-1]) > 2:
            # Coma es separador de miles
            value = value.replace(',', '')
    
    try:
        return float(value)
    except ValueError:
        return 0.0


def find_column_index(headers, keywords):
    """
    Find column index by keywords (case-insensitive)
    """
    for i, header in enumerate(headers):
        if header is None:
            continue
        
        header_lower = str(header).lower().strip()
        for keyword in keywords:
            if keyword.lower() in header_lower:
                return i
    
    # Si no encontramos por keywords, intentar por posición esperada
    # (primera columna podría ser fecha, segunda descripción, etc)
    return None


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: parse_pdf.py <pdf_path> [type]"}), file=sys.stderr)
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    pdf_type = sys.argv[2] if len(sys.argv) > 2 else 'bank'
    
    if not Path(pdf_path).exists():
        print(json.dumps({"error": f"File not found: {pdf_path}"}), file=sys.stderr)
        sys.exit(1)
    
    transactions = parse_bank_statement(pdf_path)
    
    # Output JSON
    print(json.dumps(transactions, ensure_ascii=False, default=str))
