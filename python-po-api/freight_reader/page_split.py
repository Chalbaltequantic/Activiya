"""Freight-only page extraction/classification. Does not modify shared document_reader."""
from pathlib import Path
import io
import re
import pymupdf
from PIL import Image
from document_reader import clean_text, ocr_pil_image


def page_texts(path):
    if Path(path).suffix.lower() != '.pdf':
        return []
    output = []
    with pymupdf.open(path) as doc:
        if doc.page_count > 40:
            raise ValueError('PDF exceeds freight page limit (40)')
        for idx, page in enumerate(doc):
            text = clean_text(page.get_text('text'))
            method = 'text'
            if len(text) < 80:
                pix = page.get_pixmap(matrix=pymupdf.Matrix(2, 2), alpha=False)
                ocr = ocr_pil_image(Image.open(io.BytesIO(pix.tobytes('png'))))
                if ocr.strip():
                    text = ocr
                method = 'ocr'
            output.append({'page': idx + 1, 'text': text, 'method': method, 'text_length': len(text)})
    return output


def classify(text):
    t = re.sub(r'\s+', ' ', text.upper())
    # Avoid classifying an email quoting an invoice as an invoice.
    if any(x in t for x in ('GMAIL', 'INBOX', 'REPLY ALL', 'SENT FROM OUTLOOK', 'SUBJECT: [EXTERNAL]')):
        return 'approval'
    invoice = sum(bool(re.search(p, t)) for p in (
        r'INVOICE\s*(?:NO\.?|NUMBER)\s*[:\-]',
        r'INVOICE\s*DATED?',
        r'AMOUNT\s+CHARGEABLE',
        r'GST\s+PAYABLE\s+UNDER',
        r'\bTAX\s+INVOICE\b',
        r'\bINVOICE\b.*\bTOTAL\b',
    ))
    lr = sum(bool(re.search(p, t)) for p in (
        r'CONSIGNMENT\s+NOTE',
        r'SERIAL\s+NO',
        r'ADDRESS\s+OF\s+DELIV',
        r'LORRY\s+RECEIPT',
        r'BOOKING\s+OFFICE',
        r'SIGNATURE\s+OF\s+CONSIGNOR',
        r'TO\s*PAY\s*/?\s*PAID',
        r'RECEIVED\s+GOODS',
        r'TERMS\s*&\s*CONDITIONS',
        r'CONSIGNOR\s*(?:S|\'S)?\s+NAME',
    ))
    if invoice >= 2 and invoice > lr:
        return 'invoice'
    if lr >= 1 and lr >= invoice:
        return 'lr'
    return 'unknown'
