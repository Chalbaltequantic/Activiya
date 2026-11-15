"""LR parser: scanned handwritten values are candidates, not verified values."""
import re
from .common import extract_gstins, normalize_date


def parse(text):
    def first(patterns):
        for pattern in patterns:
            m = re.search(pattern, text, re.I | re.M)
            if m:
                return m.group(1).strip()
        return None

    number = first([
        r'\b(?:SERIAL\s*NO\.?|CONSIGNMENT\s*NOTE\s*(?:NO\.?|#)|L\.?R\.?\s*(?:NO\.?|#))\s*[:#-]?\s*(\d{3,8})',
    ])
    date = normalize_date(first([
        r'\b(?:DATE|DATED|BOOKING\s*DATE)\s*[:\-]?\s*(\d{1,2}[./-]\d{1,2}[./-]\d{2,4})',
    ]))
    po = first([r'\bP\.?O\.?\s*(?:NO\.?|NUMBER)?\s*[:#-]\s*(\d{6,15})'])
    return {'lr_number': number, 'lr_date': date, 'po_number': po,
            'gstins_found': extract_gstins(text)}
