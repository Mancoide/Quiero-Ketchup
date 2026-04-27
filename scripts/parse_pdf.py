#!/usr/bin/env python3
"""
Parse PDF bank statements and reconciliation documents
Supports: Bank statements, internal system exports
"""

import sys
import json
import re
from datetime import datetime
from pathlib import Path

# pip install pdfplumber
import pdfplumber


def parse_bank_statement(pdf_path):
    """
    Parse bank statement PDF and extract transactions
    Formato esperado:
    - Fecha | Descripción | Referencia | Débito | Crédito | Saldo
    """
    transactions = []
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                text = page.extract_text()
                
                # Buscar líneas que contengan transacciones
                # Patrón: FECHA | DESCRIPCION | MONTO
                lines = text.split('\n')
                
                for line in lines:
                    # Detectar línea de transacción
                    if re.search(r'\d{1,2}[-/]\d{1,2}[-/]\d{2,4}', line):
                        transaction = parse_transaction_line(line)
                        if transaction:
                            transactions.append(transaction)
        
        # Si no se encuentran transacciones con ese formato, usar extracción de tabla
        if not transactions:
            transactions = parse_from_table(pdf_path)
                
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
        return []
    
    return transactions


def parse_from_table(pdf_path):
    """
    Extract transactions from PDF tables
    """
    transactions = []
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                tables = page.extract_tables()
                
                if not tables:
                    continue
                
                for table in tables:
                    # Asumir que la primera fila es encabezado
                    if len(table) <= 1:
                        continue
                    
                    headers = table[0]
                    
                    # Encontrar índices de columnas importantes
                    date_col = find_column_index(headers, ['fecha', 'date', 'día'])
                    desc_col = find_column_index(headers, ['descripción', 'concepto', 'description'])
                    debit_col = find_column_index(headers, ['débito', 'debito', 'gasto'])
                    credit_col = find_column_index(headers, ['crédito', 'credito', 'ingreso'])
                    ref_col = find_column_index(headers, ['referencia', 'reference', 'comprobante'])
                    
                    for row in table[1:]:
                        if not any(row):  # Skip empty rows
                            continue
                        
                        try:
                            date_str = str(row[date_col] or '')
                            amount = 0
                            tx_type = 'debit'
                            
                            if debit_col is not None and row[debit_col]:
                                amount = float(str(row[debit_col]).replace(',', '.'))
                                tx_type = 'debit'
                            elif credit_col is not None and row[credit_col]:
                                amount = float(str(row[credit_col]).replace(',', '.'))
                                tx_type = 'credit'
                            
                            if amount == 0:
                                continue
                            
                            transaction = {
                                'date': parse_date(date_str),
                                'description': str(row[desc_col] or ''),
                                'reference': str(row[ref_col] or '') if ref_col is not None else '',
                                'amount': amount,
                                'type': tx_type,
                                'balance': 0
                            }
                            
                            transactions.append(transaction)
                            
                        except (ValueError, IndexError, TypeError):
                            continue
                    
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
    
    return transactions


def parse_transaction_line(line):
    """
    Parse a single transaction line
    """
    # Patrón flexible para transacciones
    pattern = r'(\d{1,2}[-/]\d{1,2}[-/]\d{2,4})\s+(.+?)\s+(\d+[.,]\d{2})'
    
    match = re.search(pattern, line)
    if not match:
        return None
    
    try:
        date_str, description, amount_str = match.groups()
        amount = float(amount_str.replace(',', '.'))
        
        return {
            'date': parse_date(date_str),
            'description': description.strip(),
            'reference': '',
            'amount': amount,
            'type': 'debit' if 'debit' not in description.lower() else 'credit',
            'balance': 0
        }
    except ValueError:
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


def find_column_index(headers, keywords):
    """
    Find column index by keywords
    """
    for i, header in enumerate(headers):
        if header is None:
            continue
        
        header_lower = str(header).lower()
        for keyword in keywords:
            if keyword.lower() in header_lower:
                return i
    
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
