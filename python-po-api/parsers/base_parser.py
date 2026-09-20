import re
from datetime import datetime


class BasePOParser:
    """
    Common base parser for all Purchase Order formats.

    Client-specific parsers such as MetroParser, MyntraParser,
    WalmartParser etc. inherit from this class.

    This class provides:
    - text cleanup
    - regex extraction
    - number parsing
    - percentage parsing
    - date parsing
    - GSTIN extraction
    - PAN extraction
    - email extraction
    - generic table header recognition
    - generic item extraction
    - normalized PO response structure
    """

    client_code = "generic"
    client_name = "Generic"

    # ---------------------------------------------------------
    # TEXT HELPERS
    # ---------------------------------------------------------

    def clean(self, value):
        if value is None:
            return None

        value = str(value)

        value = value.replace(
            "\r",
            " "
        )

        value = value.replace(
            "\n",
            " "
        )

        value = re.sub(
            r"\s+",
            " ",
            value
        )

        value = value.strip()

        return value or None

    def clean_multiline(self, value):
        if value is None:
            return None

        lines = []

        for line in str(value).splitlines():
            line = re.sub(
                r"\s+",
                " ",
                line
            ).strip()

            if line:
                lines.append(line)

        if not lines:
            return None

        return "\n".join(lines)

    # ---------------------------------------------------------
    # REGEX HELPERS
    # ---------------------------------------------------------

    def first_match(
        self,
        text,
        patterns,
        group=1
    ):
        if not text:
            return None

        if isinstance(
            patterns,
            str
        ):
            patterns = [patterns]

        for pattern in patterns:
            match = re.search(
                pattern,
                text,
                re.IGNORECASE | re.MULTILINE
            )

            if match:
                try:
                    return self.clean(
                        match.group(group)
                    )

                except IndexError:
                    continue

        return None

    def all_matches(
        self,
        text,
        pattern,
        group=1
    ):
        if not text:
            return []

        matches = re.finditer(
            pattern,
            text,
            re.IGNORECASE | re.MULTILINE
        )

        values = []

        for match in matches:
            try:
                value = self.clean(
                    match.group(group)
                )

                if value:
                    values.append(
                        value
                    )

            except IndexError:
                continue

        return values

    # ---------------------------------------------------------
    # NUMBER HELPERS
    # ---------------------------------------------------------

    def number(self, value):
        if value is None:
            return None

        value = self.clean(value)

        if not value:
            return None

        value = value.replace(
            ",",
            ""
        )

        value = value.replace(
            "₹",
            ""
        )

        value = re.sub(
            r"(?i)\bINR\b",
            "",
            value
        )

        match = re.search(
            r"-?\d+(?:\.\d+)?",
            value
        )

        if not match:
            return None

        try:
            return float(
                match.group(0)
            )

        except ValueError:
            return None

    def integer(self, value):
        parsed = self.number(
            value
        )

        if parsed is None:
            return None

        return int(parsed)

    def percentage(self, value):
        if value is None:
            return None

        value = str(value).replace(
            "%",
            ""
        )

        return self.number(
            value
        )

    def money(self, value):
        return self.number(
            value
        )

    # ---------------------------------------------------------
    # DATE HELPERS
    # ---------------------------------------------------------

    def date(self, value):
        if not value:
            return None

        value = self.clean(
            value
        )

        if not value:
            return None

        value = value.strip(
            " .,:;"
        )

        formats = [
            "%d.%m.%Y",
            "%d-%m-%Y",
            "%d/%m/%Y",
            "%Y-%m-%d",

            "%d.%m.%y",
            "%d-%m-%y",
            "%d/%m/%y",

            "%d-%b-%Y",
            "%d %b %Y",
            "%d/%b/%Y",

            "%d-%B-%Y",
            "%d %B %Y",

            "%d-%b-%y",
            "%d %b %y"
        ]

        for date_format in formats:
            try:
                parsed = datetime.strptime(
                    value,
                    date_format
                )

                return parsed.strftime(
                    "%Y-%m-%d"
                )

            except ValueError:
                continue

        # Try finding date inside longer text.

        date_match = re.search(
            r"\b(\d{1,2}[./-]\d{1,2}[./-]\d{2,4})\b",
            value
        )

        if date_match:
            candidate = date_match.group(
                1
            )

            for date_format in [
                "%d.%m.%Y",
                "%d-%m-%Y",
                "%d/%m/%Y",
                "%d.%m.%y",
                "%d-%m-%y",
                "%d/%m/%y"
            ]:
                try:
                    parsed = datetime.strptime(
                        candidate,
                        date_format
                    )

                    return parsed.strftime(
                        "%Y-%m-%d"
                    )

                except ValueError:
                    continue

        return None

    # ---------------------------------------------------------
    # EMAIL
    # ---------------------------------------------------------

    def extract_email(self, value):
        if not value:
            return None

        match = re.search(
            r"[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}",
            value,
            re.IGNORECASE
        )

        if not match:
            return None

        return match.group(
            0
        ).strip()

    def extract_emails(self, value):
        if not value:
            return []

        matches = re.findall(
            r"[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}",
            value,
            re.IGNORECASE
        )

        result = []

        for email in matches:
            email = email.strip()

            if (
                email
                and email not in result
            ):
                result.append(
                    email
                )

        return result

    # ---------------------------------------------------------
    # GSTIN
    # ---------------------------------------------------------

    def extract_gstin(self, value):
        if not value:
            return None

        match = re.search(
            r"\b\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]\b",
            value.upper()
        )

        if not match:
            return None

        return match.group(
            0
        )

    def extract_gstins(self, value):
        if not value:
            return []

        matches = re.findall(
            r"\b\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z]Z[0-9A-Z]\b",
            value.upper()
        )

        result = []

        for gstin in matches:
            if gstin not in result:
                result.append(
                    gstin
                )

        return result

    # ---------------------------------------------------------
    # PAN
    # ---------------------------------------------------------

    def extract_pan(self, value):
        if not value:
            return None

        match = re.search(
            r"\b[A-Z]{5}\d{4}[A-Z]\b",
            value.upper()
        )

        if not match:
            return None

        return match.group(
            0
        )

    def extract_pans(self, value):
        if not value:
            return []

        matches = re.findall(
            r"\b[A-Z]{5}\d{4}[A-Z]\b",
            value.upper()
        )

        result = []

        for pan in matches:
            if pan not in result:
                result.append(
                    pan
                )

        return result

    # ---------------------------------------------------------
    # HEADER NORMALIZATION
    # ---------------------------------------------------------

    def normalize_header(self, header):
        if not header:
            return ""

        header = str(
            header
        ).upper()

        header = header.replace(
            "\n",
            " "
        )

        header = re.sub(
            r"\s+",
            " ",
            header
        )

        header = re.sub(
            r"[^A-Z0-9%./()\- ]",
            "",
            header
        )

        return header.strip()

    # ---------------------------------------------------------
    # GENERIC COLUMN IDENTIFICATION
    # ---------------------------------------------------------

    def get_column_type(self, header):
        header = self.normalize_header(
            header
        )

        if not header:
            return None

        # Serial number

        if header in [
            "SR.NO",
            "SR NO",
            "S.NO",
            "S NO",
            "SL.NO",
            "SL NO"
        ]:
            return "line_no"

        # HSN must be checked before article/item.

        if "HSN" in header:
            return "hsn_code"

        # EAN / barcode

        if (
            "EAN" in header
            or "BARCODE" in header
            or "UPC" in header
        ):
            return "ean"

        # Vendor article

        if (
            "VENDOR ARTICLE" in header
            or "VENDOR ITEM" in header
        ):
            return "vendor_item_code"

        # Item/article/SKU

        if (
            header == "ARTICLE"
            or header == "ARTICLE NO."
            or header == "ARTICLE NO"
            or "ARTICLE ID" in header
            or "ARTICLE CODE" in header
            or "MATERIAL CODE" in header
            or "MATERIAL NO" in header
            or header == "ITEM CODE"
            or header == "SKU"
            or "SKU CODE" in header
        ):
            return "item_code"

        # Description

        if (
            "MATERIAL DESCRIPTION" in header
            or "ITEM DESCRIPTION" in header
            or "PRODUCT DESCRIPTION" in header
            or "PRODUCT NAME" in header
            or "ARTICLE NAME" in header
            or "SKU DESC" in header
            or header == "DESCRIPTION"
        ):
            return "description"

        # Delivery date

        if (
            "DELIVERY DATE" in header
            or "SHIPMENT DATE" in header
        ):
            return "delivery_date"

        # Site

        if header in [
            "SITE",
            "SITE CODE",
            "LOCATION"
        ]:
            return "site"

        # Quantity

        if (
            header == "QTY"
            or header == "QUANTITY"
            or "ORDER QTY" in header
            or "ORDERED QTY" in header
            or "PO QTY" in header
        ):
            return "quantity"

        # UOM

        if (
            header == "UOM"
            or header == "U.O.M"
            or header == "UNIT"
            or "UNIT OF MEASURE" in header
        ):
            return "uom"

        # MRP

        if "MRP" in header:
            return "mrp"

        # Cost

        if (
            "BASE COST" in header
            or "UNIT COST" in header
            or "BUYING PRICE" in header
            or "LANDED PRICE" in header
            or "UNIT PRICE" in header
            or "COST PRICE" in header
        ):
            return "unit_cost"

        # CGST %

        if "CGST" in header:
            if (
                "%" in header
                or "RATE" in header
            ):
                return "cgst_percent"

            return "cgst_amount"

        # SGST %

        if "SGST" in header:
            if (
                "%" in header
                or "RATE" in header
            ):
                return "sgst_percent"

            return "sgst_amount"

        # IGST %

        if "IGST" in header:
            if (
                "%" in header
                or "RATE" in header
            ):
                return "igst_percent"

            return "igst_amount"

        # CESS %

        if "CESS" in header:
            if (
                "%" in header
                or "RATE" in header
            ):
                return "cess_percent"

            return "cess_amount"

        # Total/base value

        if (
            "TOTAL BASE VALUE" in header
            or "TOTAL AMOUNT" in header
            or "TOTAL VALUE" in header
            or "GROSS AMOUNT" in header
            or "NET AMOUNT" in header
            or "LINE VALUE" in header
        ):
            return "total_amount"

        return None

    # ---------------------------------------------------------
    # FIND TABLE HEADER
    # ---------------------------------------------------------

    def find_header_row(self, rows):
        if not rows:
            return None

        best_index = None
        best_score = 0

        maximum_rows = min(
            len(rows),
            20
        )

        for index in range(
            maximum_rows
        ):
            row = rows[index]

            if not row:
                continue

            score = 0

            detected = set()

            for cell in row:
                column_type = self.get_column_type(
                    cell
                )

                if (
                    column_type
                    and column_type not in detected
                ):
                    detected.add(
                        column_type
                    )

                    score += 1

            if score > best_score:
                best_score = score
                best_index = index

        if best_score < 2:
            return None

        return best_index

    # ---------------------------------------------------------
    # GENERIC TABLE ITEM EXTRACTION
    # ---------------------------------------------------------

    def extract_items_from_tables(
        self,
        tables
    ):
        """
        Generic table extraction.

        This is useful for simple PO tables.

        Complex clients such as Metro should override or supplement
        this logic inside their own parser because PDF table extraction
        may merge several logical columns into one cell.
        """

        items = []

        line_number = 0

        for table in tables or []:
            rows = table.get(
                "rows",
                []
            )

            if not rows:
                continue

            header_index = self.find_header_row(
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
                field = self.get_column_type(
                    header
                )

                if (
                    field
                    and field not in mapping
                ):
                    mapping[field] = index

            if (
                "item_code" not in mapping
                and "description" not in mapping
            ):
                continue

            for row in rows[
                header_index + 1:
            ]:
                if not row:
                    continue

                row_text = " ".join(
                    str(value or "")
                    for value in row
                )

                row_text = self.clean(
                    row_text
                )

                if not row_text:
                    continue

                upper = row_text.upper()

                if self.is_summary_row(
                    upper
                ):
                    continue

                def value(field):
                    column = mapping.get(
                        field
                    )

                    if column is None:
                        return None

                    if column >= len(row):
                        return None

                    return self.clean(
                        row[column]
                    )

                item_code = value(
                    "item_code"
                )

                description = value(
                    "description"
                )

                if (
                    not item_code
                    and not description
                ):
                    continue

                line_number += 1

                parsed_line_number = self.integer(
                    value("line_no")
                )

                item = {
                    "line_no": (
                        parsed_line_number
                        if parsed_line_number is not None
                        else line_number
                    ),

                    "item_code": item_code,

                    "vendor_item_code": value(
                        "vendor_item_code"
                    ),

                    "description": description,

                    "hsn_code": value(
                        "hsn_code"
                    ),

                    "ean": value(
                        "ean"
                    ),

                    "delivery_date": self.date(
                        value(
                            "delivery_date"
                        )
                    ),

                    "site": value(
                        "site"
                    ),

                    "quantity": self.number(
                        value(
                            "quantity"
                        )
                    ),

                    "uom": value(
                        "uom"
                    ),

                    "mrp": self.money(
                        value(
                            "mrp"
                        )
                    ),

                    "unit_cost": self.money(
                        value(
                            "unit_cost"
                        )
                    ),

                    "cgst_percent": self.percentage(
                        value(
                            "cgst_percent"
                        )
                    ),

                    "sgst_percent": self.percentage(
                        value(
                            "sgst_percent"
                        )
                    ),

                    "igst_percent": self.percentage(
                        value(
                            "igst_percent"
                        )
                    ),

                    "cess_percent": self.percentage(
                        value(
                            "cess_percent"
                        )
                    ),

                    "cgst_amount": self.money(
                        value(
                            "cgst_amount"
                        )
                    ),

                    "sgst_amount": self.money(
                        value(
                            "sgst_amount"
                        )
                    ),

                    "igst_amount": self.money(
                        value(
                            "igst_amount"
                        )
                    ),

                    "cess_amount": self.money(
                        value(
                            "cess_amount"
                        )
                    ),

                    "total_amount": self.money(
                        value(
                            "total_amount"
                        )
                    ),

                    "raw_row": row
                }

                items.append(
                    item
                )

        return items

    # ---------------------------------------------------------
    # SUMMARY ROW DETECTION
    # ---------------------------------------------------------

    def is_summary_row(self, row_text):
        if not row_text:
            return True

        summary_words = [
            "GRAND TOTAL",
            "TOTAL BASIC VALUE",
            "TOTAL ORDER VALUE",
            "TOTAL CGST",
            "TOTAL SGST",
            "TOTAL IGST",
            "GRAND TOTAL OF QTY",
            "TERMS AND CONDITION",
            "TERMS & CONDITION",
            "TERMS OF PAYMENT",
            "AUTHORISED SIGNATORY",
            "AUTHORIZED SIGNATORY"
        ]

        for word in summary_words:
            if word in row_text:
                return True

        return False

    # ---------------------------------------------------------
    # AMOUNT CALCULATION
    # ---------------------------------------------------------

    def calculate_total_tax(
        self,
        cgst=None,
        sgst=None,
        igst=None,
        cess=None
    ):
        values = [
            cgst,
            sgst,
            igst,
            cess
        ]

        total = 0.0
        found = False

        for value in values:
            parsed = self.money(
                value
            )

            if parsed is not None:
                total += parsed
                found = True

        if not found:
            return None

        return round(
            total,
            2
        )

    # ---------------------------------------------------------
    # NORMALIZED RESULT
    # ---------------------------------------------------------

    def build_result(
        self,
        po_no=None,
        po_date=None,
        delivery_date=None,
        expiry_date=None,

        vendor_code=None,
        vendor_name=None,
        vendor_email=None,
        vendor_pan=None,
        vendor_gstin=None,
        vendor_address=None,

        buyer_name=None,
        buyer_contact_person=None,
        buyer_email=None,
        buyer_pan=None,
        buyer_gstin=None,
        buyer_address=None,

        bill_to=None,
        ship_to=None,

        site_code=None,
        site_name=None,

        basic_amount=None,
        cgst_amount=None,
        sgst_amount=None,
        igst_amount=None,
        cess_amount=None,
        tax_amount=None,
        total_amount=None,

        currency="INR",

        items=None,

        extra=None
    ):
        basic_amount = self.money(
            basic_amount
        )

        cgst_amount = self.money(
            cgst_amount
        )

        sgst_amount = self.money(
            sgst_amount
        )

        igst_amount = self.money(
            igst_amount
        )

        cess_amount = self.money(
            cess_amount
        )

        total_amount = self.money(
            total_amount
        )

        if tax_amount is None:
            tax_amount = self.calculate_total_tax(
                cgst=cgst_amount,
                sgst=sgst_amount,
                igst=igst_amount,
                cess=cess_amount
            )

        else:
            tax_amount = self.money(
                tax_amount
            )

        result = {
            "client_code": self.client_code,
            "client_name": self.client_name,

            "po_no": self.clean(
                po_no
            ),

            "po_date": self.date(
                po_date
            ),

            "delivery_date": self.date(
                delivery_date
            ),

            "expiry_date": self.date(
                expiry_date
            ),

            "vendor": {
                "code": self.clean(
                    vendor_code
                ),

                "name": self.clean(
                    vendor_name
                ),

                "email": self.extract_email(
                    vendor_email
                ) if vendor_email else None,

                "pan": self.extract_pan(
                    vendor_pan
                ) if vendor_pan else None,

                "gstin": self.extract_gstin(
                    vendor_gstin
                ) if vendor_gstin else None,

                "address": self.clean_multiline(
                    vendor_address
                )
            },

            "buyer": {
                "name": self.clean(
                    buyer_name
                ),

                "contact_person": self.clean(
                    buyer_contact_person
                ),

                "email": self.extract_email(
                    buyer_email
                ) if buyer_email else None,

                "pan": self.extract_pan(
                    buyer_pan
                ) if buyer_pan else None,

                "gstin": self.extract_gstin(
                    buyer_gstin
                ) if buyer_gstin else None,

                "address": self.clean_multiline(
                    buyer_address
                )
            },

            "bill_to": self.clean_multiline(
                bill_to
            ),

            "ship_to": self.clean_multiline(
                ship_to
            ),

            "site": {
                "code": self.clean(
                    site_code
                ),

                "name": self.clean(
                    site_name
                )
            },

            "amounts": {
                "basic": basic_amount,
                "cgst": cgst_amount,
                "sgst": sgst_amount,
                "igst": igst_amount,
                "cess": cess_amount,
                "tax": tax_amount,
                "total": total_amount
            },

            # Keep these top-level fields also for easier
            # Laravel/database compatibility.

            "vendor_code": self.clean(
                vendor_code
            ),

            "vendor_name": self.clean(
                vendor_name
            ),

            "vendor_email": (
                self.extract_email(
                    vendor_email
                )
                if vendor_email
                else None
            ),

            "vendor_pan": (
                self.extract_pan(
                    vendor_pan
                )
                if vendor_pan
                else None
            ),

            "vendor_gstin": (
                self.extract_gstin(
                    vendor_gstin
                )
                if vendor_gstin
                else None
            ),

            "buyer_name": self.clean(
                buyer_name
            ),

            "buyer_email": (
                self.extract_email(
                    buyer_email
                )
                if buyer_email
                else None
            ),

            "buyer_pan": (
                self.extract_pan(
                    buyer_pan
                )
                if buyer_pan
                else None
            ),

            "buyer_gstin": (
                self.extract_gstin(
                    buyer_gstin
                )
                if buyer_gstin
                else None
            ),

            "basic_amount": basic_amount,
            "cgst_amount": cgst_amount,
            "sgst_amount": sgst_amount,
            "igst_amount": igst_amount,
            "cess_amount": cess_amount,
            "tax_amount": tax_amount,
            "total_amount": total_amount,

            "currency": currency or "INR",

            "items": items or []
        }

        if extra:
            result["extra"] = extra

        return result

    # ---------------------------------------------------------
    # CLIENT PARSER
    # ---------------------------------------------------------

    def parse(
        self,
        text,
        tables
    ):
        raise NotImplementedError(
            "Client parser must implement parse()."
        )