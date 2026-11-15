# Activiya Freight Reader – correction for combined invoice/LR PDF

This package replaces ONLY the new `freight_reader/` folder. It does not contain or modify main.py, document_reader.py, grn/, or parsers/ (PO).

## Install on Windows

1. Back up your current `freight_reader` folder.
2. Copy the `freight_reader` folder from this ZIP into `D:\xampp\htdocs\Activiya\python-po-api\`, replacing only the files inside that folder.
3. Keep existing main.py registration: `from freight_reader.api import router as freight_router` and `app.include_router(freight_router)`.
4. Keep your existing .env and FREIGHT_API_KEY unchanged.
5. Restart using ` .\venv\Scripts\python.exe -m uvicorn main:app --host 127.0.0.1 --port 5002 --reload `.
6. Test POST /api/freight/extract with the same sample PDF. Test PO and GRN with known-working samples.

## Sample test observations

- Page 1: freight invoice; expected invoice Z/25-26/0434, date 2026-02-05, PO 5000062544, LR 17655 (invoice cross-reference), LR date 2026-01-29 (invoice cross-reference), amount 18000.00, GSTINs 09AAIFN3136A1ZM (vendor) and 09AABCZ3366L1ZS (customer).
- Page 2: scanned consignment note/LR. Page 3: scanned reverse side and delivery acknowledgement. Page 4: email approval screenshot; not an invoice.
- LR number/date handwritten OCR may remain null; DO NOT claim LR fields independently matched based only on the invoice cross-reference.
- Signatures and stamps are NOT authenticated. Status remains review_required. A visible 'Digitally signed' label is not proof of a valid cryptographic PDF signature; a scanned stamp-like image is not proof of authenticity.
- GSTIN role extraction is template-based; verify against vendor/customer master records in Laravel.

## Safety and scope

This is a template-specific improvement, not a universal invoice/LR recognition model. OCR errors, handwriting and alternative invoice formats require testing. No automatic approval until all mandatory checks are verified. Laravel integration is separate.
