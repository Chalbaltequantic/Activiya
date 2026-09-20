import os
import io

import cv2
import numpy as np
import pymupdf
import pdfplumber
import pytesseract

from PIL import Image
from dotenv import load_dotenv


load_dotenv()


# ---------------------------------------------------------
# TESSERACT CONFIGURATION
# ---------------------------------------------------------

TESSERACT_CMD = os.getenv(
    "TESSERACT_CMD",
    r"C:\Program Files\Tesseract-OCR\tesseract.exe"
)

if TESSERACT_CMD:
    pytesseract.pytesseract.tesseract_cmd = TESSERACT_CMD


# ---------------------------------------------------------
# SETTINGS
# ---------------------------------------------------------

MIN_TEXT_LENGTH = 40

SUPPORTED_IMAGE_EXTENSIONS = {
    ".jpg",
    ".jpeg",
    ".png",
    ".bmp",
    ".tif",
    ".tiff",
}

SUPPORTED_EXTENSIONS = {
    ".pdf",
    *SUPPORTED_IMAGE_EXTENSIONS,
}


# ---------------------------------------------------------
# TEXT CLEANING
# ---------------------------------------------------------

def clean_text(text):
    """
    Clean extracted text while preserving line structure.
    """

    if text is None:
        return ""

    text = str(text)

    text = text.replace("\x00", "")
    text = text.replace("\r\n", "\n")
    text = text.replace("\r", "\n")

    lines = []

    for line in text.split("\n"):

        line = line.strip()

        if line:
            lines.append(line)

    return "\n".join(lines)


# ---------------------------------------------------------
# IMAGE PREPROCESSING
# ---------------------------------------------------------

def preprocess_image(image):
    """
    Prepare an OpenCV image for OCR.
    """

    if image is None:
        return None

    if len(image.shape) == 3:

        gray = cv2.cvtColor(
            image,
            cv2.COLOR_BGR2GRAY
        )

    else:

        gray = image

    # Enlarge image for better OCR
    gray = cv2.resize(
        gray,
        None,
        fx=2,
        fy=2,
        interpolation=cv2.INTER_CUBIC
    )

    # Light blur removes scanning noise
    gray = cv2.GaussianBlur(
        gray,
        (3, 3),
        0
    )

    # Automatic threshold
    threshold = cv2.threshold(
        gray,
        0,
        255,
        cv2.THRESH_BINARY + cv2.THRESH_OTSU
    )[1]

    return threshold


# ---------------------------------------------------------
# PIL -> OPENCV
# ---------------------------------------------------------

def pil_to_cv2(pil_image):
    """
    Convert PIL image into OpenCV BGR image.
    """

    rgb_image = pil_image.convert("RGB")

    numpy_image = np.array(
        rgb_image
    )

    cv_image = cv2.cvtColor(
        numpy_image,
        cv2.COLOR_RGB2BGR
    )

    return cv_image


# ---------------------------------------------------------
# OCR
# ---------------------------------------------------------

def ocr_pil_image(pil_image):
    """
    Perform OCR on PIL image.
    """

    try:

        cv_image = pil_to_cv2(
            pil_image
        )

        processed_image = preprocess_image(
            cv_image
        )

        if processed_image is None:
            return ""

        text = pytesseract.image_to_string(
            processed_image,
            config="--oem 3 --psm 6"
        )

        return clean_text(
            text
        )

    except Exception as exc:

        print(
            "OCR error:",
            str(exc)
        )

        return ""


# ---------------------------------------------------------
# PDF TEXT EXTRACTION
# ---------------------------------------------------------

def extract_pdf_text(file_path):
    """
    Extract text from PDF using PyMuPDF.

    If a page contains very little embedded text,
    OCR is automatically used for that page.
    """

    all_text = []

    page_details = []

    document = pymupdf.open(
        file_path
    )

    try:

        for page_number in range(
            document.page_count
        ):

            page = document.load_page(
                page_number
            )

            # First try native PDF text
            text = page.get_text(
                "text"
            )

            text = clean_text(
                text
            )

            extraction_method = "text"

            # -------------------------------------------------
            # OCR FALLBACK
            # -------------------------------------------------

            if len(text.strip()) < MIN_TEXT_LENGTH:

                extraction_method = "ocr"

                matrix = pymupdf.Matrix(
                    2.0,
                    2.0
                )

                pixmap = page.get_pixmap(
                    matrix=matrix,
                    alpha=False
                )

                image_bytes = pixmap.tobytes(
                    "png"
                )

                pil_image = Image.open(
                    io.BytesIO(
                        image_bytes
                    )
                )

                ocr_text = ocr_pil_image(
                    pil_image
                )

                if ocr_text.strip():

                    text = ocr_text

            if text.strip():

                all_text.append(
                    text
                )

            page_details.append({
                "page": page_number + 1,
                "method": extraction_method,
                "text_length": len(text),
            })

    finally:

        document.close()

    return (
        "\n\n".join(all_text),
        page_details
    )


# ---------------------------------------------------------
# PDF TABLE EXTRACTION
# ---------------------------------------------------------

def extract_pdf_tables(file_path):
    """
    Extract tables using pdfplumber.

    Tables are preserved as rows and columns so
    individual PO parsers can interpret them.
    """

    tables = []

    try:

        with pdfplumber.open(
            file_path
        ) as pdf:

            for page_number, page in enumerate(
                pdf.pages,
                start=1
            ):

                page_tables = page.extract_tables()

                if not page_tables:
                    continue

                for table_index, table in enumerate(
                    page_tables,
                    start=1
                ):

                    if not table:
                        continue

                    cleaned_rows = []

                    for row in table:

                        if row is None:
                            continue

                        cleaned_row = []

                        for cell in row:

                            if cell is None:

                                cleaned_row.append("")

                            else:

                                cleaned_row.append(
                                    clean_text(cell)
                                )

                        if any(
                            value.strip()
                            for value in cleaned_row
                        ):

                            cleaned_rows.append(
                                cleaned_row
                            )

                    if cleaned_rows:

                        tables.append({
                            "page": page_number,
                            "table_index": table_index,
                            "rows": cleaned_rows,
                        })

    except Exception as exc:

        # Table extraction failure should not stop
        # the entire PO processing.
        print(
            "PDF table extraction error:",
            str(exc)
        )

    return tables


# ---------------------------------------------------------
# IMAGE OCR
# ---------------------------------------------------------

def extract_image_text(file_path):
    """
    Extract text from image documents.

    Supports:
    JPG
    JPEG
    PNG
    BMP
    TIF
    TIFF

    Multi-page TIFF files are also supported.
    """

    try:

        pil_image = Image.open(
            file_path
        )

        text_parts = []

        frame_number = 0

        while True:

            try:

                pil_image.seek(
                    frame_number
                )

            except EOFError:

                break

            frame = pil_image.copy()

            text = ocr_pil_image(
                frame
            )

            if text.strip():

                text_parts.append(
                    text
                )

            frame_number += 1

        return "\n\n".join(
            text_parts
        )

    except Exception as exc:

        raise RuntimeError(
            "Unable to read image document: "
            + str(exc)
        )


# ---------------------------------------------------------
# MAIN DOCUMENT READER
# ---------------------------------------------------------

def read_document(file_path):
    """
    Read a PO document.

    IMPORTANT:

    This function accepts ONE argument:

        read_document(file_path)

    Example:

        document = read_document(
            r"C:\\temp\\walmart-po.pdf"
        )

    Returns:

        {
            "text": "...",
            "tables": [...],
            "pages": [...],
            "file_type": "pdf",
            "filename": "..."
        }
    """

    if not file_path:

        raise ValueError(
            "Document file path is required."
        )

    if not isinstance(
        file_path,
        (str, os.PathLike)
    ):

        raise TypeError(
            "read_document() expects a filesystem path."
        )

    file_path = os.fspath(
        file_path
    )

    if not os.path.isfile(
        file_path
    ):

        raise FileNotFoundError(
            "Document not found: "
            + file_path
        )

    filename = os.path.basename(
        file_path
    )

    extension = os.path.splitext(
        filename
    )[1].lower()

    if extension not in SUPPORTED_EXTENSIONS:

        raise ValueError(
            "Unsupported document type: "
            + extension
        )

    # -----------------------------------------------------
    # PDF DOCUMENT
    # -----------------------------------------------------

    if extension == ".pdf":

        text, page_details = extract_pdf_text(
            file_path
        )

        tables = extract_pdf_tables(
            file_path
        )

        return {
            "text": clean_text(text),
            "tables": tables,
            "pages": page_details,
            "file_type": "pdf",
            "filename": filename,
        }

    # -----------------------------------------------------
    # IMAGE DOCUMENT
    # -----------------------------------------------------

    if extension in SUPPORTED_IMAGE_EXTENSIONS:

        text = extract_image_text(
            file_path
        )

        return {
            "text": clean_text(text),
            "tables": [],
            "pages": [],
            "file_type": "image",
            "filename": filename,
        }

    raise ValueError(
        "Unable to process document."
    )