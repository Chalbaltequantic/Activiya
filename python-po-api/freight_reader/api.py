"""Independent FastAPI freight router; no PO/GRN code is modified."""
import os
import tempfile
import re

from pathlib import Path
from fastapi import APIRouter, File, Header, HTTPException, UploadFile
from dotenv import load_dotenv
from document_reader import read_document
from .page_split import page_texts, classify
from .signature_checker import inspect_pdf_signature
from .lr_serial_ocr import extract_serial
from .parsers import invoice as invoice_parser, lr as lr_parser

load_dotenv(Path(__file__).resolve().parent.parent / '.env')
router = APIRouter(prefix='/api/freight', tags=['Freight Invoice / LR OCR'])
API_KEY = os.getenv('FREIGHT_API_KEY', '')
MAX_BYTES = int(os.getenv('FREIGHT_MAX_UPLOAD_BYTES', str(20 * 1024 * 1024)))
ALLOWED = {'.pdf', '.jpg', '.jpeg', '.png', '.bmp', '.tif', '.tiff'}


async def save_upload(upload):
    filename = Path(upload.filename or '').name
    extension = Path(filename).suffix.lower()
    if extension not in ALLOWED:
        raise HTTPException(status_code=422, detail='Unsupported file extension')
    path = None
    try:
        with tempfile.NamedTemporaryFile(delete=False, suffix=extension) as tmp:
            path = tmp.name
            count = 0
            while True:
                chunk = await upload.read(1024 * 1024)
                if not chunk:
                    break
                count += len(chunk)
                if count > MAX_BYTES:
                    raise HTTPException(status_code=413, detail='File too large')
                tmp.write(chunk)
        return path
    except Exception:
        if path and os.path.exists(path):
            os.unlink(path)
        raise


def analyze_file(path, separate_lr=False):
    """Extract and classify each page, retaining uncertain LR pages for review."""
    document = read_document(path)  # existing shared OCR; never modify it
    pages = page_texts(path)
    if not pages:
        pages = [{'page': 1, 'text': document.get('text', ''), 'method': 'shared_reader'}]
    details = []
    previous_kind = None
    for p in pages:
        detected = classify(p['text'])
        kind = detected
        # The back of a consignment note may be textless or badly scanned.
        if detected == 'unknown' and previous_kind == 'lr':
            kind = 'lr_supporting'
        if separate_lr and detected == 'unknown':
            kind = 'lr_unclassified'
        details.append({
            'page': p['page'], 'type': kind, 'classification': detected,
            'ocr_method': p.get('method'), 'text_length': len(p['text']),
            'extracted_text': p['text'][:10000],
        })
        if detected != 'unknown':
            previous_kind = detected
    return pages, details


def section(pages, details, accepted):
    chosen = [p for p, d in zip(pages, details) if d['type'] in accepted]
    return '\n'.join(p['text'] for p in chosen), [p['page'] for p in chosen]


def normalize_lr_number(value):
    """Normalize numeric LR numbers by removing leading zeros."""
    if value is None:
        return None

    value = str(value).strip().upper()

    if not value:
        return None

    if value.isdigit():
        return value.lstrip("0") or "0"

    return value


@router.get('/health')
def health():
    return {'success': True, 'status': 'healthy', 'module': 'freight', 'po_grn_unchanged': True}


@router.post('/extract')
async def extract_freight(
    invoice: UploadFile = File(...),
    lr: UploadFile | None = File(default=None),
    x_api_key: str = Header(default=''),
):
    if not API_KEY or x_api_key != API_KEY:
        raise HTTPException(status_code=401, detail='Invalid or missing API key')
    invoice_path = lr_path = None
    try:
        invoice_path = await save_upload(invoice)
        invoice_raw_pages, invoice_details = analyze_file(invoice_path)
        invoice_text, invoice_pages = section(invoice_raw_pages, invoice_details, {'invoice'})
        invoice_readable = bool(invoice_text.strip())
        invoice_ambiguous = not bool(invoice_pages)
        if lr is not None:
            lr_path = await save_upload(lr)
            lr_raw_pages, lr_details = analyze_file(lr_path, separate_lr=True)
            lr_text, lr_pages = section(lr_raw_pages, lr_details, {'lr', 'lr_supporting', 'lr_unclassified'})
            all_page_details = {'invoice_file': invoice_details, 'lr_file': lr_details}
        else:
            lr_text, lr_pages = section(invoice_raw_pages, invoice_details, {'lr', 'lr_supporting'})
            all_page_details = {'combined_file': invoice_details}
        lr_ambiguous = not bool(lr_pages)
        # Pages 1-3 only for combined files; page 4 approval email is excluded.
        if lr is None:
            all_page_details['combined_file'] = [d for d in invoice_details if d['page'] <= 3]
        invoice_data = invoice_parser.parse(invoice_text) if invoice_readable else {}
        lr_data = lr_parser.parse(lr_text) if lr_text.strip() else {}
        # Read LR serial independently from the scanned pink LR page.
        # Never copy invoice values into LR verification fields.
        serial_evidence = None
        lr_source_path = lr_path if lr_path else invoice_path
        lr_source_details = lr_details if lr_path else invoice_details
        for detail in lr_source_details:
            if detail['page'] in lr_pages and detail['type'] == 'lr':
                serial_evidence = extract_serial(lr_source_path, detail['page'], detail['extracted_text'])
                if serial_evidence['serial_number']:
                    break
        if serial_evidence is not None:
            lr_data['serial_number_evidence'] = serial_evidence
            if serial_evidence['serial_number']:
                lr_data['lr_number'] = serial_evidence['serial_number']
        invoice_lr = str(invoice_data.get('lr_number') or '').strip() or None
        scanned_lr = str(lr_data.get('lr_number') or '').strip() or None
        scanned_lr_raw = (serial_evidence or {}).get('raw_serial_number') or scanned_lr
        invoice_normalized = normalize_lr_number(invoice_lr)
        scanned_normalized = normalize_lr_number(scanned_lr)
        exact_match = bool(invoice_lr and scanned_lr_raw and invoice_lr == scanned_lr_raw)
        normalized_match = bool(invoice_normalized and scanned_normalized
                                and invoice_normalized == scanned_normalized)
        lr_match = {
            'invoice_lr_number': invoice_lr,
            'page_2_serial_number': scanned_lr,
            'page_2_raw_ocr_serial_number': scanned_lr_raw,
            'invoice_normalized': invoice_normalized,
            'lr_normalized': scanned_normalized,
            'exact_match': exact_match,
            'normalized_match': normalized_match,
            # Do not mark an OCR-normalized difference as an exact match.
            'match': normalized_match,
            'comparison_match': normalized_match,
            'requires_manual_review': not normalized_match,
            'status': ('matched' if normalized_match
                       else 'mismatch_review_required' if invoice_lr and scanned_lr
                       else 'not_verified'),
            'source': 'independent scanned LR OCR',
        }
        # Cross-reference values from invoice only when LR OCR is inconclusive.
        # These are NOT represented as values independently verified on the LR.
        if lr_data:
            lr_data['invoice_lr_number_reference'] = invoice_data.get('lr_number')
            lr_data['invoice_lr_date_reference'] = invoice_data.get('lr_date')
        # Inspect only invoice pages; this reports PDF signature evidence,
        # never treats a signature field as cryptographic verification.
        signature_data = inspect_pdf_signature(invoice_path, invoice_pages)
        warnings = []
        if not invoice_readable:
            warnings.append('No readable invoice text')
        if invoice_ambiguous:
            warnings.append('Invoice page classification uncertain; verify extracted fields')
        if not lr_text.strip():
            warnings.append('LR not found or unreadable; inspect page_details for OCR/classification results')
        elif lr_ambiguous:
            warnings.append('LR page classification uncertain; verify extracted fields')
        if any(d['type'] in ('unknown', 'lr_unclassified') for details in all_page_details.values() for d in details):
            warnings.append('Unclassified pages present: check page_details; do not assume missing LR')
        if not lr_match['match']:
            warnings.append('LR serial number is not independently matched; manual review required')
        warnings.append('Invoice signature and stamp require manual verification')
        warnings.append('LR signature and stamp require manual verification')
        warnings.append('GSTIN roles require vendor/customer master mapping')
        return {
            'success': invoice_readable,
            'status': 'review_required' if invoice_readable else 'read_failed',
            'invoice': invoice_data,
            'invoice_digital_signature': signature_data,
            'lr': lr_data,
            'lr_number_match': lr_match,
            'invoice_pages': invoice_pages,
            'lr_pages': lr_pages,
            'page_details': all_page_details,
            'checks': {
                'invoice_signature': 'review_required',
                'invoice_stamp': 'review_required',
                'lr_signature': 'review_required',
                'lr_stamp': 'review_required',
            },
            'warnings': warnings,
        }
    except HTTPException:
        raise
    except Exception as exc:
        raise HTTPException(status_code=422, detail=f'Freight extraction failed: {type(exc).__name__}') from exc
    finally:
        await invoice.close()
        if lr is not None:
            await lr.close()
        for path in (invoice_path, lr_path):
            if path and os.path.exists(path):
                os.unlink(path)
