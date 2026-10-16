"""Reliance Retail Goods Receipt Note parser.

Rules below are based on the supplied Reliance sample document.
Other clients must have their own GRN-specific parsers.
"""

import re
from datetime import datetime


def find_first(text, pattern):
    """Return the first captured regex group, or None."""
    match = re.search(pattern, text, flags=re.IGNORECASE | re.MULTILINE)
    return match.group(1).strip() if match else None


def parse_date(value):
    """Convert Indian GRN dates to YYYY-MM-DD for Laravel."""
    if not value:
        return None

    for date_format in ("%d.%m.%Y", "%d/%m/%Y", "%d-%m-%Y"):
        try:
            return datetime.strptime(value, date_format).strftime("%Y-%m-%d")
        except ValueError:
            continue

    return None


def quantity(value):
    """Read quantities such as 1,215 or 1215."""
    return float(value.replace(",", ""))


def parse(text, tables=None):
    """Extract the GRN header and items from Reliance's known layout."""
    upper_text = text.upper()

    if "RELIANCE RETAIL LIMITED" not in upper_text:
        raise ValueError("Not a Reliance Retail document")

    if "GOODS RECEIPT NOTE" not in upper_text:
        raise ValueError("Document is not a Goods Receipt Note")

    grn_number = find_first(
        text,
        r"GOODS\s+RECEIPT\s+NOTE\s+No\.?\s*:\s*(\d+)",
    )
    po_number = find_first(text, r"PO\s+Number\s*:\s*([A-Z0-9/-]+)")
    date_text = find_first(
        text,
        r"GOODS\s+RECEIPT\s+NOTE\s+No\.?\s*:\s*\d+\s+Date\s*:\s*(\d{2}\.\d{2}\.\d{4})",
    )

    items = []

    # Layout 1: OCR yields one complete product row on each line.
    row_pattern = re.compile(
        r"^\s*(\d+)\s+(\d{7,12})\s+(.+?)\s+"
        r"(EA|PCS|KG|BOX)\s+([\d,]+)\s+([\d,]+)\s+([\d,]+)\s*$",
        re.IGNORECASE | re.MULTILINE,
    )
    matches = list(row_pattern.finditer(text))

    for index, match in enumerate(matches):
        next_start = matches[index + 1].start() if index + 1 < len(matches) else len(text)
        following_text = text[match.end():next_start]

        ean = find_first(following_text, r"\b(\d{13})\b")
        mrp_match = re.search(
            r"\b\d{13}\b\s*\n\s*([\d,]+\.\d{2})",
            following_text,
        )

        items.append({
            "line_no": int(match.group(1)),
            "item_code": match.group(2),
            "description": match.group(3).strip(),
            "uom": match.group(4).upper(),
            "challan_qty": quantity(match.group(5)),
            "received_qty": quantity(match.group(6)),
            "accepted_qty": quantity(match.group(7)),
            "ean": ean,
            "mrp": quantity(mrp_match.group(1)) if mrp_match else None,
        })

    if not items:
        # Layout 2: PDF extraction yields one cell per line.
        column_pattern = re.compile(
            r"(?m)^\s*(\d{1,3})\s*\n\s*(\d{7,12})\s*\n"
            r"\s*([^\n]+?)\s*\n\s*(\d{13})\s*\n"
            r"\s*(EA|PCS|KG|BOX)\s*\n\s*([\d,]+)\s*\n"
            r"\s*([\d,]+)\s*\n\s*([\d,]+)\s*\n"
            r"\s*([\d,]+\.\d{2})",
            re.IGNORECASE,
        )

        for match in column_pattern.finditer(text):
            items.append({
                "line_no": int(match.group(1)),
                "item_code": match.group(2),
                "description": match.group(3).strip(),
                "ean": match.group(4),
                "uom": match.group(5).upper(),
                "challan_qty": quantity(match.group(6)),
                "received_qty": quantity(match.group(7)),
                "accepted_qty": quantity(match.group(8)),
                "mrp": quantity(match.group(9)),
            })

    return {
        "document_type": "GRN",
        "client_code": "reliance",
        "grn_number": grn_number,
        "grn_date": parse_date(date_text),
        "po_number": po_number,
        "supplier_name": find_first(
            text, r"Supplier\s*:\s*([^\n]+?)\s{2,}Code\s*:"
        ) or find_first(text, r"Supplier\s*:\s*([^\n]+)"),
        "supplier_code": find_first(text, r"Code\s*:\s*(\d+)"),
        "supplier_gstin": find_first(
            text, r"GSTIN\s+NO\s*:\s*(\d{2}[A-Z0-9]{13})"
        ),
        "buyer_name": "Reliance Retail Limited",
        "invoice_number": find_first(
            text, r"Vendor\s+invoice\s+no\s*:\s*([A-Z0-9/-]+)"
        ),
        "delivery_number": find_first(
            text, r"Delivery\s+No\s*:\s*([A-Z0-9/-]+)"
        ),
        "items": items,
        "item_count": len(items),
        "review_required": not bool(grn_number and po_number and items),
    }
