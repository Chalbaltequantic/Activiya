import re
from datetime import datetime
from .base_parser import BasePOParser


class AmazonParser(BasePOParser):
    client_code = 'amazon'
    client_name = 'Amazon Retail India Private Limited'

    def parse(self, text, tables):
        text = text or ''
        po_no = self.first_match(text, [r'\bPO\s*#\s*([A-Z0-9]+)'])
        po_date = self.first_match(text, [r'Order\s+Placed\s+(\d{1,2}/\d{1,2}/\d{4})'])
        window = re.search(r'Ship\s+window\s+(\d{1,2}/\d{1,2}/\d{4})\s*-\s*(\d{1,2}/\d{1,2}/\d{4})', text, re.I)
        ship_start = window.group(1) if window else None
        ship_end = window.group(2) if window else None
        # The ship-window endpoint 30/9/2026 confirms day/month/year.
        def us_date(value):
            return datetime.strptime(value, '%d/%m/%Y').strftime('%Y-%m-%d') if value else None

        shipping, billing = self._extract_addresses(text)
        # PyMuPDF text extraction can interleave two address columns; use
        # table cells when available, and retain raw sections otherwise.
        items = self._items(tables)
        totals = self._totals(tables)
        requested = sum(int(item['quantity_requested']) for item in items)
        accepted = sum(int(item['accepted_quantity']) for item in items)
        warnings = []
        if not items:
            warnings.append('No item rows extracted')
        if totals and (requested != totals['requested'] or accepted != totals['accepted']):
            warnings.append('Extracted quantities do not match document totals')
        if len(items) != 36:
            warnings.append('Document specifies 36 PO items; extracted %d' % len(items))
        for item in items:
            if item['accepted_quantity'] > item['quantity_requested']:
                warnings.append('Accepted quantity exceeds requested for item %s' % item['item_code'])
        result = self.build_result(
            po_no=po_no, po_date=us_date(po_date),
            delivery_date=us_date(ship_start), expiry_date=us_date(ship_end),
            buyer_name=self.client_name, buyer_address=billing,
            bill_to=billing, ship_to=shipping,
            currency='INR', items=items,
            extra={
                'document_type': 'PURCHASE ORDER',
                'parser': 'amazon', 'ship_window_start': us_date(ship_start),
                'ship_window_end': us_date(ship_end),
                'amazon_account_code': self.first_match(text, [r'Amazon Retail India Private Limited\s*\(([A-Z0-9]+)\)']),
                'document_totals': totals,
                'extracted_requested_quantity': requested,
                'extracted_accepted_quantity': accepted,
                'review_required': bool(warnings),
                'validation_warnings': warnings,
                'note': 'Unit prices, taxes and total amount are not present in this PO.'
            }
        )
        return result

    def _extract_addresses(self, text):
        """Handle Amazon's side-by-side address columns in PyMuPDF reading order."""
        match = re.search(
            r"Shipping\s+Address\s+Billing\s+Address\s*(.*?)\s*PO\s+items\s*\(\d+\)",
            text, re.I | re.S
        )
        if match:
            body = match.group(1)
            # PyMuPDF reads these two columns one after the other. The
            # company line starts each address, even if both are identical.
            starts = list(re.finditer(
                r"Amazon\s+Retail\s+India\s+Private\s+Limited", body, re.I
            ))
            if len(starts) >= 2:
                shipping = self.clean_multiline(body[starts[0].start():starts[1].start()])
                billing = self.clean_multiline(body[starts[1].start():])
                return shipping, billing
        # Do not misassign both addresses to Bill To if the format changes.
        return None, None

    def _items(self, tables):
        result = []
        for table in tables or []:
            rows = table.get('rows', [])
            for row in rows:
                if len(row) < 5:
                    continue
                external, model, title, requested, accepted = [str(x or '').strip() for x in row[:5]]
                external = re.sub(r'\s+', '', external)
                if not external or external.lower() in ('externalid', 'total:'):
                    continue
                if not requested.replace(',', '').isdigit() or not accepted.replace(',', '').isdigit():
                    continue
                result.append({
                    'item_code': external,
                    'external_id': external,
                    'model_number': self.clean(model),
                    'description': self.clean(title),
                    'quantity': int(requested.replace(',', '')),
                    'quantity_requested': int(requested.replace(',', '')),
                    'accepted_quantity': int(accepted.replace(',', '')),
                    'uom': None, 'hsn_code': None, 'ean': external if external.isdigit() and len(external) == 13 else None,
                    'mrp': None, 'unit_cost': None, 'total_amount': None,
                    'raw_row': row,
                })
        return result

    def _totals(self, tables):
        for table in tables or []:
            for row in table.get('rows', []):
                if len(row) >= 5 and str(row[0] or '').strip().lower().startswith('total:'):
                    try:
                        return {'requested': int(str(row[3]).replace(',', '')), 'accepted': int(str(row[4]).replace(',', ''))}
                    except (ValueError, TypeError):
                        return None
        return None
