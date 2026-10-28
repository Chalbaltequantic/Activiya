import re
from decimal import Decimal
from .base_parser import BasePOParser

class ZeptoParser(BasePOParser):
    client_code = 'zepto'
    client_name = 'Zepto Limited'

    def parse(self, text, tables):
        text = text or ''
        def match(pattern):
            m = re.search(pattern, text, re.I)
            return m.group(1).strip() if m else None
        def val(row, index):
            return str(row[index]).strip() if index < len(row) and row[index] is not None else ''
        def amount(pattern):
            value = match(pattern)
            return self.money(value) if value else None

        items = []
        vendor_name = vendor_address = vendor_gstin = vendor_pan = None
        buyer_name = buyer_gstin = buyer_pan = bill_to = ship_to = None
        for table in tables or []:
            rows = table.get('rows', [])
            for idx, row in enumerate(rows):
                if val(row, 0).lower() == 'name:' and val(row, 3) and vendor_name is None:
                    vendor_name = val(row, 3)
                if val(row, 0).lower() == 'address:' and val(row, 3) and vendor_address is None:
                    vendor_address = val(row, 3)
                if val(row, 0).lower() == 'gstin:' and val(row, 3) and vendor_gstin is None:
                    vendor_gstin = val(row, 3)
                if val(row, 0).lower() == 'pan:' and val(row, 3) and vendor_pan is None:
                    vendor_pan = val(row, 3)
                if val(row, 0).strip().lower() == 'billing address' and idx + 1 < len(rows):
                    bill_to = val(rows[idx + 1], 3)
                    ship_to = val(rows[idx + 1], 16)
                    buyer_name = 'ZEPTO LIMITED' if 'ZEPTO' in (bill_to or '').upper() else None
                    if idx + 2 < len(rows):
                        buyer_gstin = val(rows[idx + 2], 3)
                    if idx + 3 < len(rows):
                        buyer_pan = val(rows[idx + 3], 3)
            for idx, row in enumerate(rows):
                if 'MATERIAL CODE' not in ' '.join(str(c or '') for c in row).upper():
                    continue
                if 'ITEM DESCRIPTION' not in ' '.join(str(c or '') for c in row).upper():
                    continue
                for data_row in rows[idx + 1:]:
                    if not val(data_row, 0).isdigit() or not val(data_row, 1).isdigit():
                        continue
                    def num(i):
                        return self.number(val(data_row, i))
                    def pct(i):
                        return self.percentage(val(data_row, i))
                    item = {
                        'line_no': int(val(data_row, 0)),
                        'item_code': val(data_row, 1),
                        'description': self.clean(val(data_row, 2)),
                        'sku_code': self.clean(val(data_row, 4)),
                        'hsn_code': val(data_row, 5),
                        'ean': val(data_row, 7),
                        'quantity': num(8),
                        'mrp': num(9),
                        'unit_cost': num(11),
                        'taxable_amount': num(12),
                        'cgst_percent': pct(13),
                        'cgst_amount': num(15),
                        'sgst_percent': pct(17),
                        'sgst_amount': num(18),
                        'igst_percent': pct(19),
                        'igst_amount': num(20),
                        'cess_percent': pct(21),
                        'cess_amount': num(22),
                        'additional_cess': num(23),
                        'total_amount': num(24),
                    }
                    if item['quantity'] is None or item['total_amount'] is None:
                        continue
                    items.append(item)
                break
        po_no = match(r'PO\s*No\s*:\s*([A-Z0-9/-]+)')
        po_date = match(r'PO\s*Date\s*:\s*(\d{4}-\d{2}-\d{2})')
        delivery_date = match(r'Expected\s+Delivery\s+Date\s*:\s*(\d{4}-\d{2}-\d{2})')
        expiry_date = match(r'PO\s+Expiry\s+Date\s*:\s*(\d{4}-\d{2}-\d{2})')
        basic = amount(r'Total\s+Taxable\s+Amount\s*\(INR\)\s*([\d,.]+)')
        tax = amount(r'Total\s+Tax\s*\(INR\)\s*([\d,.]+)')
        total = amount(r'Grand\s+Total\s+Amount\s*\(INR\)\s*([\d,.]+)')
        if basic is None or tax is None or total is None:
            raise ValueError('Zepto PO summary amounts could not be read')
        if not items:
            raise ValueError('Zepto PO item table could not be read')
        if abs(Decimal(str(basic)) + Decimal(str(tax)) - Decimal(str(total))) > Decimal('0.02'):
            raise ValueError('Zepto PO summary amounts do not reconcile')
        if abs(sum(Decimal(str(i['total_amount'])) for i in items) - Decimal(str(total))) > Decimal('0.02'):
            raise ValueError('Zepto PO item totals do not reconcile')
        igst = sum(Decimal(str(i['igst_amount'] or 0)) for i in items)
        cgst = sum(Decimal(str(i['cgst_amount'] or 0)) for i in items)
        sgst = sum(Decimal(str(i['sgst_amount'] or 0)) for i in items)
        cess = sum(Decimal(str(i['cess_amount'] or 0)) for i in items)
        result = self.build_result(
            po_no=po_no, po_date=po_date, delivery_date=delivery_date, expiry_date=expiry_date,
            vendor_name=vendor_name, vendor_pan=vendor_pan, vendor_gstin=vendor_gstin, vendor_address=vendor_address,
            buyer_name=buyer_name or 'ZEPTO LIMITED', buyer_pan=buyer_pan, buyer_gstin=buyer_gstin,
            buyer_address=bill_to, bill_to=bill_to, ship_to=ship_to,
            basic_amount=basic, cgst_amount=float(cgst), sgst_amount=float(sgst),
            igst_amount=float(igst), cess_amount=float(cess), tax_amount=tax, total_amount=total,
            items=items, currency='INR',
            extra={'parser':'zepto','document_type':'PURCHASE ORDER','review_required':True}
        )
        return result
