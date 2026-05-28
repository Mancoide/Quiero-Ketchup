#!/usr/bin/env python3
"""
Parse spreadsheet files (CSV, XLSX) with transactions
"""

import sys
import json
import csv
from datetime import datetime
from pathlib import Path

# pip install openpyxl pandas
import pandas as pd


def parse_spreadsheet(file_path):
    """
    Parse CSV or XLSX file and extract transactions
    """
    transactions = []
    
    try:
        file_ext = Path(file_path).suffix.lower()
        
        if file_ext == '.csv':
            df = pd.read_csv(file_path)
        elif file_ext in ['.xlsx', '.xls']:
            df = pd.read_excel(file_path)
        else:
            raise ValueError(f"Unsupported file format: {file_ext}")
        
        # Detectar columnas
        df.columns = df.columns.str.lower().str.strip()
        
        date_col = find_column(df.columns, ['fecha', 'date', 'día', 'day'])
        desc_col = find_column(df.columns, ['descripción', 'concepto', 'description', 'concept'])
        amount_col = find_column(df.columns, ['monto', 'amount', 'valor', 'value'])
        debit_col = find_column(df.columns, ['débito', 'debito', 'gasto', 'expense'])
        credit_col = find_column(df.columns, ['crédito', 'credito', 'ingreso', 'income'])
        ref_col = find_column(df.columns, ['referencia', 'reference', 'comprobante', 'receipt'])
        balance_col = find_column(df.columns, ['saldo', 'balance'])
        type_col = find_column(df.columns, ['tipo', 'type'])
        
        for idx, row in df.iterrows():
            try:
                # Obtener fecha
                if date_col and pd.notna(row[date_col]):
                    date_str = str(row[date_col])
                    date = parse_date(date_str)
                else:
                    continue
                
                # Obtener monto
                amount = 0
                tx_type = 'debit'
                
                if amount_col and pd.notna(row[amount_col]):
                    amount = parse_amount(row[amount_col])
                    if type_col and pd.notna(row[type_col]):
                        tx_type = str(row[type_col]).strip().lower()
                elif debit_col and pd.notna(row[debit_col]):
                    amount = parse_amount(row[debit_col])
                    tx_type = 'debit'
                elif credit_col and pd.notna(row[credit_col]):
                    amount = parse_amount(row[credit_col])
                    tx_type = 'credit'
                
                if amount == 0:
                    continue
                
                # Obtener descripción
                description = ''
                if desc_col and pd.notna(row[desc_col]):
                    description = str(row[desc_col]).strip()
                
                # Obtener referencia
                reference = ''
                if ref_col and pd.notna(row[ref_col]):
                    reference = str(row[ref_col]).strip()
                
                transaction = {
                    'date': date,
                    'description': description,
                    'reference': reference,
                    'amount': amount,
                    'type': tx_type,
                }

                if balance_col and pd.notna(row[balance_col]):
                    transaction['balance'] = parse_amount(row[balance_col])
                
                transactions.append(transaction)
                
            except (ValueError, TypeError, KeyError):
                continue
        
    except Exception as e:
        print(json.dumps({"error": str(e)}), file=sys.stderr)
        return []
    
    return transactions


def parse_amount(value):
    if value is None or pd.isna(value):
        return 0.0

    if isinstance(value, (int, float)):
        return float(value)

    text = str(value).strip().replace('Gs.', '').replace('$', '').replace(' ', '')
    if not text:
        return 0.0

    if ',' in text and '.' in text:
        text = text.replace('.', '').replace(',', '.')
    elif text.count('.') > 1:
        text = text.replace('.', '')
    elif '.' in text and len(text.rsplit('.', 1)[1]) == 3:
        text = text.replace('.', '')
    elif ',' in text:
        text = text.replace(',', '.')

    return float(text)


def find_column(columns, keywords):
    """
    Find column name by keywords
    """
    for col in columns:
        col_lower = str(col).lower()
        for keyword in keywords:
            if keyword.lower() in col_lower:
                return col
    
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
        '%d %B %Y',
        '%d %b %Y',
    ]
    
    # Try parsing with different formats
    for fmt in formats:
        try:
            date_obj = datetime.strptime(date_str, fmt)
            return date_obj.strftime('%Y-%m-%d')
        except ValueError:
            continue
    
    # Try pandas to_datetime
    try:
        date_obj = pd.to_datetime(date_str)
        return date_obj.strftime('%Y-%m-%d')
    except:
        pass
    
    # Return today if can't parse
    return datetime.now().strftime('%Y-%m-%d')


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: parse_spreadsheet.py <file_path>"}), file=sys.stderr)
        sys.exit(1)
    
    file_path = sys.argv[1]
    
    if not Path(file_path).exists():
        print(json.dumps({"error": f"File not found: {file_path}"}), file=sys.stderr)
        sys.exit(1)
    
    transactions = parse_spreadsheet(file_path)
    
    # Output JSON
    print(json.dumps(transactions, ensure_ascii=False, default=str))
