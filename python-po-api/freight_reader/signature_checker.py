"""Freight-only PDF signature evidence extraction. No PO/GRN changes."""
import re
from pathlib import Path
import pymupdf


def inspect_pdf_signature(path, invoice_pages=None):
    result = {
        'signature_field_present': False,
        'signature_field_names': [],
        'signer_name': None,
        'signed_at': None,
        'visible_signature_text': False,
        'pdf_signature_flags': None,
        'cryptographic_status': 'not_verified',
        'status': 'review_required',
        'warnings': [],
    }
    if Path(path).suffix.lower() != '.pdf':
        result['warnings'].append('Not a PDF; PDF signature inspection unavailable')
        return result
    with pymupdf.open(path) as doc:
        result['pdf_signature_flags'] = doc.get_sigflags()
        indices = invoice_pages if invoice_pages else list(range(1, doc.page_count + 1))
        for number in indices:
            if not isinstance(number, int) or number < 1 or number > doc.page_count:
                continue
            page = doc[number - 1]
            for widget in page.widgets() or []:
                if widget.field_type == pymupdf.PDF_WIDGET_TYPE_SIGNATURE:
                    result['signature_field_present'] = True
                    if widget.field_name not in result['signature_field_names']:
                        result['signature_field_names'].append(widget.field_name)
            # Use native text only for visible signer label; no OCR claim.
            raw = page.get_text('text')  # reading order preserves signature appearance text
            if re.search(r'digitally\s+signed', raw, re.I):
                result['visible_signature_text'] = True
            m = re.search(
                r'digitally\s+signed\s+by\s+([A-Za-z][A-Za-z .]{1,55}?)'
                r'(?=\s*Date\s*:|\s*20\d{2}[./-]\d{2}|$)',
                raw, re.I | re.M
            )
            if m:
                result['signer_name'] = ' '.join(m.group(1).split())
            # Signature appearance may have its date/time separated by line breaks.
            date = re.search(r'(20\d{2})[./-](\d{2})[./-](\d{2})\s+(\d{2}:\d{2}:\d{2})', raw)
            if date:
                result['signed_at'] = f'{date.group(1)}-{date.group(2)}-{date.group(3)}T{date.group(4)}'
    if result['signature_field_present']:
        result['warnings'].append('Signature field found, but signature contents, integrity and certificate trust have not been validated')
    else:
        result['warnings'].append('No PDF signature field found on selected invoice pages')
    return result
