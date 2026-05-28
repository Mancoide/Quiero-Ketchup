#!/usr/bin/env python3
"""
PDF Diagnostic Tool
Ayuda a diagnosticar problemas con PDFs de conciliación
"""

import sys
import json
import pdfplumber
from pathlib import Path

def diagnose_pdf(pdf_path):
    """Diagnóstico completo de un PDF"""
    
    if not Path(pdf_path).exists():
        print(f"❌ Archivo no encontrado: {pdf_path}")
        return
    
    print(f"\n📄 Diagnosticando: {Path(pdf_path).name}")
    print("=" * 80)
    
    try:
        with pdfplumber.open(pdf_path) as pdf:
            print(f"\n📊 Información general:")
            print(f"  Total de páginas: {len(pdf.pages)}")
            
            for page_idx, page in enumerate(pdf.pages, 1):
                print(f"\n🔍 Página {page_idx}:")
                print(f"  Ancho: {page.width}pt, Alto: {page.height}pt")
                
                # Intentar extraer tablas
                tables = page.extract_tables()
                if tables:
                    print(f"  📋 Tablas encontradas: {len(tables)}")
                    for table_idx, table in enumerate(tables, 1):
                        print(f"\n    Tabla {table_idx}:")
                        print(f"      Filas: {len(table)}")
                        if table:
                            print(f"      Columnas en encabezado: {len(table[0])}")
                            print(f"      Encabezado: {table[0]}")
                            print(f"      Primeras 3 filas de datos:")
                            for row_idx, row in enumerate(table[1:4], 1):
                                print(f"        {row_idx}. {row}")
                else:
                    print(f"  ℹ️ Sin tablas detectadas")
                
                # Extraer texto
                text = page.extract_text()
                print(f"\n  📝 Contenido de texto: {len(text) if text else 0} caracteres")
                
                if text:
                    # Mostrar primeras líneas
                    lines = text.split('\n')[:20]
                    print(f"  Primeras líneas:")
                    for line_idx, line in enumerate(lines, 1):
                        if line.strip():
                            print(f"    {line_idx:2d}. {line[:80]}")
                
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Uso: python diagnose_pdf.py <archivo.pdf> [archivo2.pdf ...]")
        sys.exit(1)
    
    for pdf_path in sys.argv[1:]:
        diagnose_pdf(pdf_path)
