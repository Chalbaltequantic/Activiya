"""Reliance Retail purchase-order parser (text-based PDFs).

Standalone API: RelianceParser().parse(text), .parse_pdf(path), .can_parse(text).
Use an adapter if your existing BaseParser requires a different signature.
"""
import re
from datetime import datetime
from pathlib import Path
from parsers.base_parser import BasePOParser


class RelianceParser(BasePOParser):
    name = 'reliance'
    client_code = "reliance"
    client_name = "Reliance Retail Limited"

    @staticmethod
    def can_parse(text):
        return bool(re.search(r'Reliance\s+Retail\s+Limited', text, re.I) and
                    re.search(r'PURCHASE\s+ORDER', text, re.I) and
                    re.search(r'(?:PO\s*NO\.?|Number)\s*:', text, re.I))

    @staticmethod
    def _date(value):
        return datetime.strptime(value, '%d.%m.%Y').date().isoformat() if value else None

    @staticmethod
    def _money(value):
        return float(value.replace(',', '')) if value else None

    def parse_pdf(self, path):
        try:
            import fitz
        except ImportError as exc:
            raise RuntimeError('Install PyMuPDF: pip install pymupdf') from exc
        with fitz.open(str(path)) as doc:
            return self.parse('\n'.join(page.get_text(sort=True) for page in doc))

    def parse(self, text, tables=None):
        if not self.can_parse(text):
            raise ValueError('Not a recognized Reliance Retail purchase order')

        def capture(pattern, source=text, flags=re.I | re.M):
            m = re.search(pattern, source, flags)
            return m.group(1).strip() if m else None

        po_no = capture(r'PO\s*NO\.?\s*:\s*(\d{8,15})') or capture(r'PURCHASE\s+ORDER\s+Number\s*:\s*(\d{8,15})')
        po_date = capture(r'PO\s*Date\s*:\s*(\d{2}\.\d{2}\.\d{4})')
        delivery_date = capture(r'DELIVERY\s+DATE\s*:\s*(\d{2}\.\d{2}\.\d{4})')
        vendor_code = capture(r'Vendor\s+Code\s*:\s*(\d+)')
        seller = capture(r'Vendor\s+Code\s*:\s*\d+[^\n]*\n\s*([^\n]+)')
        gstin = capture(r'GSTN\s+No\s*:\s*([A-Z0-9]{15})')
        total = capture(r'Total\s+Order\s+Value\s*:\s*(?:INR\s*)?([\d,]+\.\d{2})')
        basic = capture(r'TOTAL\s+BASIC\s+VALUE\s*(?:INR\s*)?([\d,]+\.\d{2})')
        site = capture(r'PO\s*NO\.?\s*:\s*\d+\s+Site\s*:\s*(\w+)')
        # Parse only the item table; avoid legal terms pages and their numbered clauses.
        start = re.search(r'Sr\.No\s+Article\s+No\.', text, re.I)
        end = re.search(r'Grand\s+Total\s+of\s+Qty', text[start.end():], re.I) if start else None
        if not start or not end:
            raise ValueError('Reliance item table not found; document may require OCR')
        table = text[start.end():start.end() + end.start()]
        lines = table.splitlines()
        # Primary product row: line, article code, EAN, description, carton qty, CAR, carton MRP, base cost, tax, tax value, total.
        primary = re.compile(
            r'^\s*(\d{1,3})\s+(\d{7,12})\s+(\d{8,14})\s+(.+?)\s+'
            r'([\d,]+\.\d{3})\s+(CAR|CTN|BOX)\s+'
            r'([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+'
            r'([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})\s*$', re.I)
        ea_row = re.compile(r'\b([\d,]+\.\d{3})\s+(EA|PCS|PC)\s+([\d,]+\.\d{2})\b', re.I)
        items = []
        current = None
        for line in lines:
            m = primary.match(line)
            if m:
                if current:
                    items.append(current)
                line_no, code, ean, desc, qty, uom, mrp, base_cost, tax_pct, tax_amount, value = m.groups()
                current = {
                    'line_no': int(line_no), 'item_code': code, 'ean': ean,
                    'description': re.sub(r'\s+', ' ', desc).strip(),
                    'quantity': self._money(qty), 'uom': uom.upper(),
                    'carton_qty': self._money(qty), 'carton_uom': uom.upper(),
                    'carton_mrp': self._money(mrp), 'base_cost': self._money(base_cost),
                    'cgst_percent': self._money(tax_pct), 'cgst_amount': self._money(tax_amount),
                    'total_base_value': self._money(value),
                    'each_qty': None, 'each_uom': None, 'each_mrp': None,
                }
                continue
            if current is None:
                continue
            # The product's description may wrap to a short next line.
            if re.match(r'^\s*\d{8}\s+', line):
                continue  # HSN line
            m = ea_row.search(line)
            if m:
                current['each_qty'] = self._money(m.group(1))
                current['each_uom'] = m.group(2).upper()
                current['each_mrp'] = self._money(m.group(3))
                # Compare with GRNs in EA, not cartons.
                current['quantity'] = current['each_qty']
                current['uom'] = current['each_uom']
                current['mrp'] = current['each_mrp']
                continue
            stripped = line.strip()
            if stripped in {'CBD', 'TUB'}:
                current['description'] += ' ' + stripped
        if current:
            items.append(current)
        if not po_no or not items:
            raise ValueError('PO number or item rows not extracted; do not import an empty PO')
        missing_ea = [i['line_no'] for i in items if i['each_qty'] is None]
        if missing_ea:
            raise ValueError(f'Missing EA quantity on PO lines {missing_ea}; refusing unsafe GRN reconciliation')
        result = self.build_result(
            po_no=po_no, po_date=po_date, delivery_date=delivery_date,
            vendor_code=vendor_code, vendor_name=seller, vendor_gstin=gstin,
            buyer_name='Reliance Retail Limited', site_code=site,
            basic_amount=basic, total_amount=total, items=items,
            extra={'carton_total': sum(item['carton_qty'] for item in items),
                   'each_total': sum(item['each_qty'] for item in items)},
        )
        result['detected_template'] = 'reliance'
        result['item_count'] = len(items)
        return result
