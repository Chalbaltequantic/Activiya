"""Freight-only OCR of the printed serial on New U.P. Golden Transport Service LR.

The invoice LR is never used to choose an OCR result.  Several crops and image
preparations are evaluated because the first printed '1' touches the NO label.
"""
import re
from collections import Counter
from pathlib import Path

import pymupdf
import pytesseract
from PIL import Image, ImageOps


def extract_serial(path, page_number, page_text=''):
    result = {
        'serial_number': None, 'raw_serial_number': None,
        'source_page': page_number, 'method': 'multi_crop_serial_ocr',
        'status': 'not_read', 'ocr_variants': [], 'warnings': []
    }
    if Path(path).suffix.lower() != '.pdf':
        result['warnings'].append('Targeted serial OCR supports PDFs only')
        return result
    if not re.search(r'NEW\s+U\.?P\.?\s+GOLDEN', page_text, re.I):
        result['warnings'].append('Unknown LR layout; serial OCR not applied')
        return result
    with pymupdf.open(path) as doc:
        if not 1 <= page_number <= len(doc):
            result['warnings'].append('Invalid page number')
            return result
        page = doc[page_number - 1]
        w, h = page.rect.width, page.rect.height
        # The first digit is close to the end of SERIAL NO. Cropping at x=130
        # is unstable: Tesseract sometimes reads the first '1' as '4'.
        # These 3 slight variations retain the entire first digit in the sample.
        for x in (132, 134, 136):
            region = pymupdf.Rect(w*x/841, h*139/612, w*190/841, h*159/612)
            pix = page.get_pixmap(matrix=pymupdf.Matrix(7, 7), clip=region, alpha=False)
            image = Image.frombytes('RGB', (pix.width, pix.height), pix.samples)
            gray = ImageOps.grayscale(image)
            variants = {
                'original': image,
                'grayscale': gray,
                'threshold': gray.point(lambda v: 255 if v > 165 else 0),
            }
            for preprocessing, prepared in variants.items():
                for psm in (6, 7):
                    text = pytesseract.image_to_string(
                        prepared,
                        config=f'--psm {psm} -c tessedit_char_whitelist=0123456789'
                    ).strip()
                    digits = re.sub(r'\D', '', text)
                    result['ocr_variants'].append({
                        'crop_start': x, 'preprocessing': preprocessing,
                        'psm': psm, 'text': digits,
                    })
    valid = [v['text'] for v in result['ocr_variants'] if 4 <= len(v['text']) <= 8]
    counts = Counter(valid)
    if not counts:
        result['status'] = 'ambiguous'
        result['warnings'].append('No valid serial number recognized')
        return result
    winner, votes = counts.most_common(1)[0]
    supporting_crops = {v['crop_start'] for v in result['ocr_variants'] if v['text'] == winner}
    # Two OCR modes on the same crop are correlated, not independent proof.
    if votes < 12 or len(supporting_crops) < 2 or votes / len(result['ocr_variants']) < 0.70:
        result['status'] = 'ambiguous'
        result['warnings'].append('Serial OCR variants do not reach reliable consensus; manual review required')
        return result
    result['raw_serial_number'] = winner
    result['serial_number'] = winner.lstrip('0') or '0'
    result['status'] = 'ocr_consensus' if winner == result['serial_number'] else 'leading_zero_normalized'
    if winner != result['serial_number']:
        result['warnings'].append('Leading zeros normalized; original OCR retained')
    return result
