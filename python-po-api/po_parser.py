import re

from datetime import datetime

from client_templates import CLIENT_TEMPLATES


def clean(value):

    if value is None:
        return None

    value = re.sub(
        r"\s+",
        " ",
        str(value)
    ).strip()

    return value or None


def parse_number(value):

    if value is None:
        return None

    value = re.sub(
        r"[^0-9.\-]",
        "",
        str(value)
    )

    try:
        return float(value)
    except Exception:
        return None


def normalize_date(value):

    if not value:
        return None

    value = clean(value)

    formats = [
        "%d.%m.%Y",
        "%d-%m-%Y",
        "%d/%m/%Y",
        "%Y-%m-%d",
        "%d-%b-%Y",
        "%d %b %Y"
    ]

    for fmt in formats:

        try:

            return datetime.strptime(
                value,
                fmt
            ).strftime("%Y-%m-%d")

        except ValueError:
            pass

    return None


def first_match(
    text,
    patterns
):

    for pattern in patterns:

        match = re.search(
            pattern,
            text,
            re.IGNORECASE
        )

        if match:
            return clean(
                match.group(1)
            )

    return None


def detect_client(text):

    upper = text.upper()

    best_key = "generic"
    best_score = 0

    for key, template in CLIENT_TEMPLATES.items():

        score = 0

        for keyword in template.get(
            "detect",
            []
        ):

            if keyword.upper() in upper:
                score += 1

        if score > best_score:

            best_score = score
            best_key = key

    return best_key


def generic_po_number(text):

    patterns = [
        r"PO\s*(?:NO|NUMBER|#)\.?\s*[:\-]?\s*([A-Z0-9\-\/]+)",
        r"PURCHASE ORDER\s*(?:NO|NUMBER)?\.?\s*[:\-]?\s*([A-Z0-9\-\/]+)"
    ]

    return first_match(
        text,
        patterns
    )


def generic_po_date(text):

    patterns = [
        r"PO\s*(?:DATE|CREATED DATE|APPROVED DATE)\s*[:\-]?\s*([0-9A-Za-z.\-/]+)",
        r"ORDER DATE\s*[:\-]?\s*([0-9A-Za-z.\-/]+)",
        r"Purchase Order Date\s*[:\-]?\s*([0-9A-Za-z.\-/]+)"
    ]

    return normalize_date(
        first_match(
            text,
            patterns
        )
    )


def extract_company_data(
    text,
    client_key
):

    template = CLIENT_TEMPLATES.get(
        client_key,
        {}
    )

    po_no = first_match(
        text,
        template.get(
            "po_patterns",
            []
        )
    )

    if not po_no:
        po_no = generic_po_number(
            text
        )

    po_date = first_match(
        text,
        template.get(
            "date_patterns",
            []
        )
    )

    delivery_date = first_match(
        text,
        template.get(
            "delivery_date_patterns",
            []
        )
    )

    expiry_date = first_match(
        text,
        template.get(
            "expiry_patterns",
            []
        )
    )

    vendor_code = first_match(
        text,
        template.get(
            "vendor_code_patterns",
            []
        )
    )

    total = first_match(
        text,
        template.get(
            "total_patterns",
            []
        )
    )

    return {
        "client_code": client_key,

        "client_name": template.get(
            "client_name",
            client_key.upper()
        ),

        "po_no": po_no,

        "po_date": normalize_date(
            po_date
        ) if po_date else generic_po_date(text),

        "delivery_date": normalize_date(
            delivery_date
        ),

        "expiry_date": normalize_date(
            expiry_date
        ),

        "vendor_code": vendor_code,

        "total_amount": parse_number(
            total
        )
    }


def looks_like_item_table(rows):

    if not rows:
        return False

    first_rows = rows[:3]

    text = " ".join(
        " ".join(row)
        for row in first_rows
    ).upper()

    keywords = [
        "SKU",
        "ARTICLE",
        "MATERIAL",
        "HSN",
        "QUANTITY",
        "QTY",
        "MRP",
        "COST"
    ]

    matches = sum(
        1
        for keyword in keywords
        if keyword in text
    )

    return matches >= 3


def find_header_row(rows):

    best_index = None
    best_score = 0

    keywords = [
        "SKU",
        "ARTICLE",
        "MATERIAL",
        "DESCRIPTION",
        "HSN",
        "EAN",
        "QUANTITY",
        "QTY",
        "UOM",
        "MRP",
        "COST",
        "PRICE",
        "TOTAL"
    ]

    for index, row in enumerate(
        rows[:6]
    ):

        text = " ".join(
            row
        ).upper()

        score = sum(
            1
            for keyword in keywords
            if keyword in text
        )

        if score > best_score:

            best_score = score
            best_index = index

    return best_index


def normalize_header(value):

    value = clean(
        value
    ) or ""

    return value.upper()


def column_type(header):

    header = normalize_header(
        header
    )

    if (
        "SKU" in header
        or "ARTICLE ID" in header
        or "ARTICLE NO" in header
        or "MATERIAL CODE" in header
    ):
        return "item_code"

    if (
        "DESCRIPTION" in header
        or "ARTICLE NAME" in header
        or "PRODUCT NAME" in header
        or "SKU DESC" in header
    ):
        return "description"

    if "HSN" in header:
        return "hsn_code"

    if "EAN" in header:
        return "ean"

    if (
        "QUANTITY" in header
        or header == "QTY"
        or "QTY." in header
    ):
        return "quantity"

    if "UOM" in header:
        return "uom"

    if "MRP" in header:
        return "mrp"

    if (
        "UNIT COST" in header
        or "BASE COST" in header
        or "BUYING PRICE" in header
        or "LANDED PRICE" in header
    ):
        return "unit_cost"

    if (
        "IGST%" in header
        or "IGST (%)" in header
    ):
        return "igst_percent"

    if (
        "CGST%" in header
        or "CGST (%)" in header
    ):
        return "cgst_percent"

    if (
        "SGST%" in header
        or "SGST (%)" in header
    ):
        return "sgst_percent"

    if "CESS%" in header:
        return "cess_percent"

    if (
        "TOTAL AMOUNT" in header
        or "TOTAL VALUE" in header
        or "GROSS AMOUNT" in header
    ):
        return "total_amount"

    return None


def extract_items(tables):

    items = []

    for table in tables:

        rows = table.get(
            "rows",
            []
        )

        if not looks_like_item_table(
            rows
        ):
            continue

        header_index = find_header_row(
            rows
        )

        if header_index is None:
            continue

        headers = rows[
            header_index
        ]

        mapping = {}

        for index, header in enumerate(
            headers
        ):

            field = column_type(
                header
            )

            if field and field not in mapping:
                mapping[field] = index

        if not (
            "item_code" in mapping
            or "description" in mapping
        ):
            continue

        for row in rows[
            header_index + 1:
        ]:

            if not row:
                continue

            row_text = " ".join(
                row
            ).strip()

            if not row_text:
                continue

            upper = row_text.upper()

            if (
                upper.startswith("TOTAL")
                or "GRAND TOTAL" in upper
                or "TERMS AND CONDITION" in upper
            ):
                continue

            def value(field):

                index = mapping.get(
                    field
                )

                if index is None:
                    return None

                if index >= len(row):
                    return None

                return clean(
                    row[index]
                )

            item_code = value(
                "item_code"
            )

            description = value(
                "description"
            )

            if not item_code and not description:
                continue

            items.append({
                "item_code": item_code,
                "description": description,
                "hsn_code": value("hsn_code"),
                "ean": value("ean"),
                "quantity": parse_number(value("quantity")),
                "uom": value("uom"),
                "mrp": parse_number(value("mrp")),
                "unit_cost": parse_number(value("unit_cost")),
                "cgst_percent": parse_number(value("cgst_percent")),
                "sgst_percent": parse_number(value("sgst_percent")),
                "igst_percent": parse_number(value("igst_percent")),
                "cess_percent": parse_number(value("cess_percent")),
                "total_amount": parse_number(value("total_amount")),
                "raw_row": row
            })

    return items


def parse_purchase_order(
    text,
    tables
):

    client_key = detect_client(
        text
    )

    header = extract_company_data(
        text,
        client_key
    )

    items = extract_items(
        tables
    )

    header["items"] = items

    header["item_count"] = len(
        items
    )

    return header