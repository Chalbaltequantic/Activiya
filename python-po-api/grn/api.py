"""Standalone GRN OCR service; independent of the existing PO API."""

import os
import tempfile
from pathlib import Path

from dotenv import load_dotenv
from fastapi import APIRouter, File, Header, HTTPException, UploadFile

from document_reader import read_document
from .parsers import reliance


load_dotenv()

router = APIRouter(tags=["GRN OCR"])

API_KEY = os.getenv("GRN_API_KEY", "")
MAX_BYTES = int(os.getenv("GRN_MAX_UPLOAD_BYTES", str(20 * 1024 * 1024)))
ALLOWED_EXTENSIONS = {".pdf", ".jpg", ".jpeg", ".png", ".bmp", ".tif", ".tiff"}


@router.get("/grn/health")
def health():
    """Return the service status and currently supported client formats."""
    return {
        "success": True,
        "status": "healthy",
        "supported_formats": ["reliance"],
    }


@router.post("/read-grn")
async def read_grn(
    file: UploadFile = File(...),
    x_api_key: str = Header(default=""),
):
    """Read an uploaded GRN and return normalized header and line items."""
    if not API_KEY or x_api_key != API_KEY:
        raise HTTPException(status_code=401, detail="Invalid or missing API key")

    filename = Path(file.filename or "").name
    extension = Path(filename).suffix.lower()

    if extension not in ALLOWED_EXTENSIONS:
        raise HTTPException(status_code=422, detail="Unsupported file extension")

    temp_path = None

    try:
        # Save in chunks to prevent an unlimited upload from consuming memory.
        with tempfile.NamedTemporaryFile(delete=False, suffix=extension) as temporary:
            temp_path = temporary.name
            total_bytes = 0

            while True:
                chunk = await file.read(1024 * 1024)

                if not chunk:
                    break

                total_bytes += len(chunk)

                if total_bytes > MAX_BYTES:
                    raise HTTPException(status_code=413, detail="File too large")

                temporary.write(chunk)

        document = read_document(temp_path)
        text = document.get("text", "")

        if not text.strip():
            return {
                "success": False,
                "status": "read_failed",
                "message": "No readable text found in the document.",
                "goods_receipt_note": None,
            }

        upper_text = text.upper()
        is_reliance = (
            "RELIANCE RETAIL LIMITED" in upper_text
            and "GOODS RECEIPT NOTE" in upper_text
        )

        if not is_reliance:
            return {
                "success": False,
                "status": "unsupported_format",
                "message": "No supported GRN template matched; manual review needed.",
                "goods_receipt_note": None,
            }

        parsed = reliance.parse(text, document.get("tables", []))

        return {
            "success": True,
            "status": "review_required" if parsed["review_required"] else "parsed",
            "detected_template": parsed["client_code"],
            "item_count": len(parsed["items"]),
            "goods_receipt_note": parsed,
            "pages": document.get("pages", []),
        }

    except HTTPException:
        raise
    except Exception as exception:
        # Do not expose sensitive OCR text or local filesystem paths.
        raise HTTPException(
            status_code=422,
            detail=f"GRN extraction failed: {type(exception).__name__}",
        ) from exception
    finally:
        await file.close()

        if temp_path and os.path.exists(temp_path):
            os.remove(temp_path)
