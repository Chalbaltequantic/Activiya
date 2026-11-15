"""Freight invoice parser; never use a table heading as an invoice field."""
import re
from .common import extract_gstins, normalize_date

NUMBER = r'([A-Z0-9][A-Z0-9/._-]{2,45})'
DATE = r'(\d{1,2}[./-]\d{1,2}[./-]\d{2,4})'


def capture(text, patterns):
    for p in patterns:
        m = re.search(p, text, re.I | re.M)
        if m:
            return m.group(1).strip()
    return None


def parse(text):
    gstins = extract_gstins(text)
    invoice_no = capture(text, [
        r'\b(?:TAX\s+)?INVOICE\s*(?:NO\.?|NUMBER|#)\s*[:\-]\s*' + NUMBER,
        r'\bBILL\s*(?:NO\.?|NUMBER)\s*[:\-]\s*' + NUMBER,
    ])
    invoice_date = normalize_date(capture(text, [
        r'\bINVOICE\s*(?:DATED?|DATE)\s*[:\-]?\s*' + DATE,
        r'\bDATED\s*[:\-]\s*' + DATE,
    ]))
    po_no = capture(text, [
        r'\bP\.?O\.?\s*(?:NO\.?|NUMBER|#)\s*[:\-]\s*' + NUMBER,
        r'\bPURCHASE\s+ORDER\s*(?:NO\.?|NUMBER)\s*[:\-]\s*' + NUMBER,
    ])
    # The LR number in the invoice table is in a separate cell/line below the header.
    lr_no = capture(text, [
        r'\bL\.?R\.?\s*(?:NO\.?|NUMBER)\s*[:\-]\s*' + NUMBER,
        r'\bL\.?R\.?\s*(?:NO\.?|NUMBER)\s*\n(?:[^\n]*\n){0,16}?\s*\d+\s*\n(\d{3,12})\b',
    ])
    # Prefer invoice table row: Sr No, customer invoice no, LR no, loading date.
    table_row = re.search(r'\b1\s*\n\s*\d{7,15}\s*\n\s*(\d{3,12})\s*\n\s*' + DATE, text)
    if table_row:
        lr_no = table_row.group(1)
    loading_date = normalize_date(capture(text, [
        r'\bLOADING\s+DATE\s*[:\-]?\s*' + DATE,
        r'\bL\.?R\.?\s*(?:NO\.?|NUMBER)\s*\n(?:[^\n]*\n){0,18}?\s*\d+\s*\n\d{3,12}\s*\n' + DATE,
    ]))
    # Accept only a labelled total or the final amount in a recognized invoice table.
    amount = capture(text, [
        r'\b(?:GRAND\s+TOTAL|NET\s+AMOUNT|TOTAL\s+AMOUNT|AMOUNT\s+PAYABLE)\s*[:\-]?\s*(?:RS\.?|INR|₹)?\s*([\d,]+\.\d{2})',
        r'\bTOTAL\s*\n\s*([\d,]+\.\d{2})',
    ])
    if amount is None and re.search(r'\b(?:RATE\s+OF\s+FIEXED|RATE\s+OF\s+FIXED)\b', text, re.I):
        candidates = re.findall(r'\b\d{1,3}(?:,\d{3})+\.\d{2}\b', text)
        if candidates and len(set(candidates)) == 1:
            amount = candidates[0]
    if amount:
        amount = f'{float(amount.replace(",", "")):.2f}'
    # Only assign GSTIN roles when there is explicit context; otherwise compare to masters in Laravel.
    vendor = capture(text, [r'\b(?:ENORL\.?\s*NO\.?|GSTIN\s*OF\s*(?:TRANSPORTER|VENDOR))\s*[:.]?\s*(\d{2}[A-Z0-9]{13})'])
    customer = capture(text, [r'\bGSTN\.?\s*(\d{2}[A-Z0-9]{13})'])
    return {
        'invoice_number': invoice_no, 'invoice_date': invoice_date,
        'po_number': po_no, 'lr_number': lr_no, 'lr_date': loading_date,
        'amount': amount, 'gstins_found': gstins,
        'vendor_gstin': vendor if vendor in gstins else None,
        'customer_gstin': customer if customer in gstins else None,
    }
