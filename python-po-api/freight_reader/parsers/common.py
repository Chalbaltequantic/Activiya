import re
from datetime import datetime
from decimal import Decimal, InvalidOperation

GSTIN_RE = re.compile(r'\b\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]\b', re.I)


def find_value(text, labels, value_pattern=r'([^\n]{1,70})'):
    for label in labels:
        match = re.search(r'(?:' + label + r')\s*[:#\-–]?\s*' + value_pattern, text, re.I)
        if match:
            value = match.group(1).strip(' :#-\t')
            if value:
                return value
    return None


def normalize_date(value):
    if not value:
        return None
    value = value.strip().replace(' ', '')
    for fmt in ('%d.%m.%Y', '%d/%m/%Y', '%d-%m-%Y', '%Y-%m-%d', '%d.%m.%y', '%d/%m/%y', '%d-%m-%y'):
        try:
            return datetime.strptime(value, fmt).strftime('%Y-%m-%d')
        except ValueError:
            pass
    return None


def extract_date(text, labels):
    value = find_value(text, labels, r'(\d{1,4}[./-]\d{1,2}[./-]\d{2,4})')
    return normalize_date(value)


def extract_gstins(text):
    return list(dict.fromkeys(match.upper() for match in GSTIN_RE.findall(text)))


def extract_amount(text):
    # Conservative: only accept an amount with a clear total/payable label.
    raw = find_value(text, [r'grand\s+total', r'net\s+(?:freight\s+)?(?:amount|payable)',
                            r'total\s+(?:invoice\s+)?amount', r'amount\s+payable',
                            r'freight\s+amount'],
                     r'(?:Rs\.?|INR|₹)?\s*([\d,]+(?:\.\d{1,2})?)')
    if not raw:
        return None
    try:
        return str(Decimal(raw.replace(',', '')).quantize(Decimal('0.01')))
    except InvalidOperation:
        return None
