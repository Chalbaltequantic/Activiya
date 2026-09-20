import os
import shutil
import tempfile
import traceback

from dotenv import load_dotenv
from fastapi import FastAPI, File, Header, HTTPException, UploadFile

from document_reader import read_document

from parsers.metro_parser import MetroParser
from parsers.myntra_parser import MyntraParser
from parsers.citymall_parser import CityMallParser
from parsers.walmart_parser import WalmartParser


# =========================================================
# ENVIRONMENT
# =========================================================

load_dotenv()


PO_API_KEY = os.getenv(
    "PO_API_KEY",
    ""
)


# =========================================================
# FASTAPI APPLICATION
# =========================================================

app = FastAPI(
    title="Activiya Smart PO Reader",
    description="Automatic Purchase Order Reader and Parser",
    version="1.0.0"
)


# =========================================================
# SUPPORTED PARSERS
# =========================================================

PARSERS = {
    "metro": MetroParser(),
    "myntra": MyntraParser(),
    "citymall": CityMallParser(),
    "walmart": WalmartParser(),
}


TEMPLATE_NAMES = {
    "metro": "Metro Cash & Carry",
    "myntra": "Myntra",
    "citymall": "CityMall",
    "walmart": "Walmart",
}


# =========================================================
# SUPPORTED FILE TYPES
# =========================================================

ALLOWED_EXTENSIONS = {
    ".pdf",
    ".jpg",
    ".jpeg",
    ".png",
    ".bmp",
    ".tif",
    ".tiff",
}


# =========================================================
# FORMAT DETECTION
# =========================================================

def detect_po_format(text):
    """
    Detect PO company/template from extracted document text.

    Detection is intentionally limited to PO formats
    that have already been validated.

    Returns:

        detected_code, scores

    Example:

        "walmart",
        {
            "metro": 0,
            "myntra": 0,
            "citymall": 0,
            "walmart": 8
        }
    """

    if not text:
        return None, {
            "metro": 0,
            "myntra": 0,
            "citymall": 0,
            "walmart": 0,
        }

    text_upper = str(text).upper()

    scores = {
        "metro": 0,
        "myntra": 0,
        "citymall": 0,
        "walmart": 0,
    }


    # -----------------------------------------------------
    # METRO
    # -----------------------------------------------------

    metro_strong = [
        "METRO CASH AND CARRY",
        "METRO CASH & CARRY",
        "METRO CASH AND CARRY INDIA",
        "METRO CASH & CARRY INDIA",
    ]

    metro_weak = [
        "METRO",
    ]

    for term in metro_strong:
        if term in text_upper:
            scores["metro"] += 5

    for term in metro_weak:
        if term in text_upper:
            scores["metro"] += 1


    # -----------------------------------------------------
    # MYNTRA
    # -----------------------------------------------------

    myntra_strong = [
        "MYNTRA JABONG INDIA PVT LTD",
        "MYNTRA JABONG INDIA",
        "MYNTRA JABONG",
        "MYNJ-",
    ]

    myntra_weak = [
        "MYNTRA",
    ]

    for term in myntra_strong:
        if term in text_upper:
            scores["myntra"] += 5

    for term in myntra_weak:
        if term in text_upper:
            scores["myntra"] += 1


    # -----------------------------------------------------
    # CITYMALL
    # -----------------------------------------------------

    citymall_strong = [
        "CMUNITY INNOVATIONS PRIVATE LIMITED",
        "CMUNITY INNOVATIONS",
        "CITYMALL.LIVE",
    ]

    citymall_weak = [
        "CITYMALL",
    ]

    for term in citymall_strong:
        if term in text_upper:
            scores["citymall"] += 5

    for term in citymall_weak:
        if term in text_upper:
            scores["citymall"] += 1


    # -----------------------------------------------------
    # WALMART
    # -----------------------------------------------------

    walmart_strong = [
        "WAL-MART INDIA PVT. LTD.",
        "WAL-MART INDIA PVT LTD",
        "WAL-MART INDIA",
        "WALMART INDIA",
        "TOTAL PO AMOUNT INCLUDING TAXES",
    ]

    walmart_weak = [
        "CASH N CARRY",
    ]

    for term in walmart_strong:
        if term in text_upper:
            scores["walmart"] += 5

    for term in walmart_weak:
        if term in text_upper:
            scores["walmart"] += 1


    # -----------------------------------------------------
    # FIND HIGHEST SCORE
    # -----------------------------------------------------

    highest_score = max(
        scores.values()
    )

    if highest_score <= 0:
        return None, scores


    winners = [
        code
        for code, score in scores.items()
        if score == highest_score
    ]


    # -----------------------------------------------------
    # DO NOT GUESS IF TWO FORMATS HAVE SAME SCORE
    # -----------------------------------------------------

    if len(winners) != 1:
        return None, scores


    detected_code = winners[0]

    return detected_code, scores


# =========================================================
# ROOT
# =========================================================

@app.get("/")
def root():
    return {
        "success": True,
        "service": "Activiya Smart PO Reader",
        "version": "1.0.0",
    }


# =========================================================
# HEALTH CHECK
# =========================================================

@app.get("/health")
def health():
    return {
        "success": True,
        "status": "healthy",
        "supported_formats": [
            "metro",
            "myntra",
            "citymall",
            "walmart",
        ],
    }


# =========================================================
# READ PURCHASE ORDER
# =========================================================

@app.post("/read-po")
async def read_po(
    file: UploadFile = File(...),
    x_api_key: str = Header(default="")
):
    """
    Upload and process one Purchase Order.

    Workflow:

        Upload
            ↓
        Save temporary file
            ↓
        document_reader.py
            ↓
        Extract text/tables
            ↓
        Detect PO company
            ↓
        Select parser
            ↓
        Normalize PO
            ↓
        Return JSON
    """

    # -----------------------------------------------------
    # API KEY CHECK
    # -----------------------------------------------------

    if PO_API_KEY:

        if x_api_key != PO_API_KEY:

            raise HTTPException(
                status_code=401,
                detail="Invalid API key"
            )


    # -----------------------------------------------------
    # FILE NAME
    # -----------------------------------------------------

    filename = file.filename or ""

    if not filename:

        raise HTTPException(
            status_code=422,
            detail="Uploaded file does not have a filename."
        )


    # -----------------------------------------------------
    # FILE EXTENSION
    # -----------------------------------------------------

    extension = os.path.splitext(
        filename
    )[1].lower()


    if extension not in ALLOWED_EXTENSIONS:

        raise HTTPException(
            status_code=422,
            detail=(
                "Unsupported file type. "
                "Allowed: PDF, JPG, JPEG, PNG, BMP, TIF, TIFF."
            )
        )


    temp_path = None


    try:

        # =================================================
        # SAVE UPLOADED FILE TEMPORARILY
        # =================================================

        with tempfile.NamedTemporaryFile(
            delete=False,
            suffix=extension
        ) as temp_file:

            shutil.copyfileobj(
                file.file,
                temp_file
            )

            temp_path = temp_file.name


        # =================================================
        # READ DOCUMENT
        # =================================================
        #
        # IMPORTANT:
        #
        # document_reader.py now has:
        #
        #     def read_document(file_path):
        #
        # Therefore ONLY ONE argument is supplied.
        #
        # Do not add filename as a second argument.
        # Do not pass an open stream.
        #
        # =================================================

        document = read_document(
            temp_path
        )


        # =================================================
        # NORMALIZE DOCUMENT READER RESPONSE
        # =================================================

        raw_text = ""

        tables = []

        pages = []


        if isinstance(
            document,
            dict
        ):

            raw_text = (
                document.get("text")
                or document.get("raw_text")
                or ""
            )

            tables = (
                document.get("tables")
                or []
            )

            pages = (
                document.get("pages")
                or []
            )


        elif isinstance(
            document,
            str
        ):

            raw_text = document


        else:

            raise RuntimeError(
                "document_reader returned unsupported response type: "
                + str(type(document))
            )


        # =================================================
        # CHECK EXTRACTED TEXT
        # =================================================

        if not str(raw_text).strip():

            return {
                "success": False,
                "status": "read_failed",
                "error_code": "read_failed",
                "message": (
                    "No readable text could be extracted "
                    "from the uploaded document."
                ),
                "filename": filename,
                "detected_template": None,
                "detected_template_name": None,
                "item_count": 0,
                "purchase_order": None,
                "raw_text": "",
            }


        # =================================================
        # AUTO-DETECT PO FORMAT
        # =================================================

        detected_code, detection_scores = detect_po_format(
            raw_text
        )


        # =================================================
        # FORMAT NOT RECOGNIZED
        # =================================================

        if not detected_code:

            return {
                "success": False,
                "status": "detection_failed",
                "error_code": "detection_failed",
                "message": "Unable to identify PO format.",
                "filename": filename,
                "detected_template": None,
                "detected_template_name": None,
                "detection_scores": detection_scores,
                "item_count": 0,
                "purchase_order": None,
                "raw_text": raw_text,
            }


        # =================================================
        # FIND PARSER
        # =================================================

        parser = PARSERS.get(
            detected_code
        )


        if parser is None:

            return {
                "success": False,
                "status": "parser_not_available",
                "error_code": "parser_not_available",
                "message": (
                    "Parser is not available for detected PO format."
                ),
                "filename": filename,
                "detected_template": detected_code,
                "detected_template_name": TEMPLATE_NAMES.get(
                    detected_code
                ),
                "detection_scores": detection_scores,
                "item_count": 0,
                "purchase_order": None,
                "raw_text": raw_text,
            }


        # =================================================
        # PARSE PURCHASE ORDER
        # =================================================
        #
        # Our PO parsers were originally designed to receive:
        #
        #     parse(text, tables)
        #
        # Some parser versions may only accept:
        #
        #     parse(text)
        #
        # We support both without changing the validated
        # parser files.
        #
        # =================================================

        try:

            purchase_order = parser.parse(
                raw_text,
                tables
            )

        except TypeError as first_error:

            try:

                purchase_order = parser.parse(
                    raw_text
                )

            except TypeError:

                raise first_error


        # =================================================
        # VALIDATE PARSER RESPONSE
        # =================================================

        if not isinstance(
            purchase_order,
            dict
        ):

            raise RuntimeError(
                "PO parser returned unsupported response type: "
                + str(type(purchase_order))
            )


        # =================================================
        # ITEMS
        # =================================================

        items = purchase_order.get(
            "items"
        )

        if not isinstance(
            items,
            list
        ):

            items = []


        # =================================================
        # SUCCESS
        # =================================================

        return {
            "success": True,
            "status": "processed",
            "filename": filename,

            "detected_template": detected_code,

            "detected_template_name": TEMPLATE_NAMES.get(
                detected_code
            ),

            "detection_scores": detection_scores,

            "item_count": len(
                items
            ),

            "purchase_order": purchase_order,

            "raw_text": raw_text,

            "document_info": {
                "page_count": len(
                    pages
                ),
                "table_count": len(
                    tables
                ),
            },
        }


    # =====================================================
    # FASTAPI ERRORS
    # =====================================================

    except HTTPException:

        raise


    # =====================================================
    # PROCESSING ERRORS
    # =====================================================

    except Exception as exc:

        print(
            "SMART PO PROCESSING ERROR"
        )

        print(
            "Filename:",
            filename
        )

        print(
            "Error:",
            str(exc)
        )

        traceback.print_exc()


        raise HTTPException(
            status_code=500,
            detail=str(exc)
        )


    # =====================================================
    # CLEANUP
    # =====================================================

    finally:

        try:

            await file.close()

        except Exception:

            pass


        if (
            temp_path
            and os.path.exists(temp_path)
        ):

            try:

                os.unlink(
                    temp_path
                )

            except Exception as cleanup_error:

                print(
                    "Temporary file cleanup error:",
                    str(cleanup_error)
                )