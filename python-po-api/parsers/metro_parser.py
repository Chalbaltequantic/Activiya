import re

from .base_parser import BasePOParser


class MetroParser(BasePOParser):
    """
    Parser for Metro Cash & Carry Purchase Orders.

    Metro PDFs contain:
    - PO header information
    - seller/vendor information
    - delivery/site information
    - buyer information
    - tax/amount summary
    - item table

    pdfplumber may combine multiple logical Metro columns into
    individual cells. Therefore Metro item extraction is handled
    separately instead of relying only on the generic base parser.
    """

    client_code = "metro"
    client_name = "Metro Cash & Carry"

    # ---------------------------------------------------------
    # MAIN PARSER
    # ---------------------------------------------------------

    def parse(self, text, tables):
        text = text or ""

        header = self.extract_header(
            text
        )

        vendor = self.extract_vendor(
            text
        )

        buyer = self.extract_buyer(
            text
        )

        site = self.extract_site(
            text
        )

        amounts = self.extract_amounts(
            text
        )

        items = self.extract_metro_items(
            tables
        )

        # If pdfplumber did not produce usable item rows,
        # try extracting Metro item information from raw text.
        if not items:
            items = self.extract_items_from_text(
                text
            )

        return self.build_result(
            po_no=header.get("po_no"),
            po_date=header.get("po_date"),
            delivery_date=header.get(
                "delivery_date"
            ),
            expiry_date=None,

            vendor_code=vendor.get(
                "code"
            ),
            vendor_name=vendor.get(
                "name"
            ),
            vendor_email=vendor.get(
                "email"
            ),
            vendor_pan=vendor.get(
                "pan"
            ),
            vendor_gstin=vendor.get(
                "gstin"
            ),
            vendor_address=vendor.get(
                "address"
            ),

            buyer_name=buyer.get(
                "name"
            ),
            buyer_contact_person=buyer.get(
                "contact_person"
            ),
            buyer_email=buyer.get(
                "email"
            ),
            buyer_pan=buyer.get(
                "pan"
            ),
            buyer_gstin=buyer.get(
                "gstin"
            ),
            buyer_address=buyer.get(
                "address"
            ),

            bill_to=buyer.get(
                "address"
            ),
            ship_to=site.get(
                "address"
            ),

            site_code=site.get(
                "code"
            ),
            site_name=site.get(
                "name"
            ),

            basic_amount=amounts.get(
                "basic"
            ),
            cgst_amount=amounts.get(
                "cgst"
            ),
            sgst_amount=amounts.get(
                "sgst"
            ),
            igst_amount=amounts.get(
                "igst"
            ),
            cess_amount=amounts.get(
                "cess"
            ),
            total_amount=amounts.get(
                "total"
            ),

            currency="INR",

            items=items,

            extra={
                "document_type": "PURCHASE ORDER",
                "parser": "metro",
                "site_code": site.get(
                    "code"
                ),
                "site_name": site.get(
                    "name"
                )
            }
        )

    # ---------------------------------------------------------
    # PO HEADER
    # ---------------------------------------------------------

    def extract_header(self, text):
        po_no = self.first_match(
            text,
            [
                r"PO\s*NO\.?\s*:\s*([A-Z0-9\/\-]+)",
                r"PURCHASE\s+ORDER\s+Number\s*:\s*([A-Z0-9\/\-]+)",
                r"Number\s*:\s*([A-Z0-9\/\-]+)"
            ]
        )

        po_date = self.first_match(
            text,
            [
                r"PO\s*Date\s*:\s*([0-9.\-/]+)",
                r"Po\s*Date\s*:\s*([0-9.\-/]+)"
            ]
        )

        delivery_date = self.first_match(
            text,
            [
                r"DELIVERY\s+DATE\s*:\s*([0-9.\-/]+)",
                r"Delivery\s+Date\s*:\s*([0-9.\-/]+)"
            ]
        )

        return {
            "po_no": po_no,
            "po_date": po_date,
            "delivery_date": delivery_date
        }

    # ---------------------------------------------------------
    # VENDOR / SELLER
    # ---------------------------------------------------------

    def extract_vendor(self, text):
        vendor_code = self.first_match(
            text,
            [
                r"Vendor\s+Code\s*:\s*([A-Z0-9\/\-]+)",
                r"Vendor\s+Code\s+([A-Z0-9\/\-]+)"
            ]
        )

        vendor_name = self.first_match(
            text,
            [
                (
                    r"Vendor\s+Code\s*:\s*[A-Z0-9\/\-]+"
                    r"\s*\n\s*([^\n]+)"
                )
            ]
        )

        # Prevent accidental capture of labels.
        if vendor_name:
            upper_name = vendor_name.upper()

            invalid_names = [
                "PURCHASE ORDER",
                "SELLER",
                "VENDOR CODE",
                "PO NO"
            ]

            if any(
                invalid in upper_name
                for invalid in invalid_names
            ):
                vendor_name = None

        vendor_email = self.first_match(
            text,
            [
                (
                    r"E-?Mail\s*:\s*"
                    r"([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})"
                )
            ]
        )

        vendor_pan = self.first_match(
            text,
            [
                r"Pan\s+No\.?\s*:\s*([A-Z]{5}[0-9]{4}[A-Z])",
                r"PAN\s+No\.?\s*:\s*([A-Z]{5}[0-9]{4}[A-Z])"
            ]
        )

        vendor_gstin = self.first_match(
            text,
            [
                (
                    r"GSTN\s+No\s*:\s*"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                ),
                (
                    r"GSTIN\s+No\s*:\s*"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        vendor_address = self.extract_vendor_address(
            text
        )

        return {
            "code": vendor_code,
            "name": vendor_name,
            "email": vendor_email,
            "pan": vendor_pan,
            "gstin": vendor_gstin,
            "address": vendor_address
        }

    # ---------------------------------------------------------
    # VENDOR ADDRESS
    # ---------------------------------------------------------

    def extract_vendor_address(self, text):
        if not text:
            return None

        match = re.search(
            (
                r"Vendor\s+Code\s*:\s*[A-Z0-9\/\-]+"
                r"\s*\n"
                r"(?P<section>.*?)"
                r"(?:"
                r"E-?Mail\s*:|"
                r"Pan\s+No\.?\s*:|"
                r"Vendor\s+Status\s*:|"
                r"GSTN\s+No\s*:"
                r")"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not match:
            return None

        section = match.group(
            "section"
        )

        lines = []

        for line in section.splitlines():
            line = self.clean(
                line
            )

            if not line:
                continue

            # First line is normally vendor name.
            lines.append(
                line
            )

        if not lines:
            return None

        # Remove vendor name from address.
        if len(lines) > 1:
            lines = lines[1:]

        if not lines:
            return None

        return "\n".join(
            lines
        )

    # ---------------------------------------------------------
    # BUYER
    # ---------------------------------------------------------

    def extract_buyer(self, text):
        buyer_name = self.first_match(
            text,
            [
                (
                    r"For\s+[^\n]+\s*\n\s*"
                    r"(Metro\s+Cash\s+And\s+Carry\s+India\s+Limited)"
                ),
                (
                    r"\b(Metro\s+Cash\s+And\s+Carry\s+India\s+Limited)\b"
                )
            ]
        )

        if not buyer_name:
            buyer_name = "Metro Cash And Carry India Limited"

        buyer_contact_person = self.first_match(
            text,
            [
                r"Buyer\s*:\s*([^\n\r]+)"
            ]
        )

        # Buyer email is specifically the email following Buyer.
        buyer_email = self.first_match(
            text,
            [
                (
                    r"Buyer\s*:\s*[^\n\r]+"
                    r"\s*\n\s*Email\s*:\s*"
                    r"([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})"
                )
            ]
        )

        buyer_gstin = self.extract_buyer_gstin(
            text
        )

        buyer_pan = self.extract_buyer_pan(
            text
        )

        buyer_address = self.extract_delivery_address(
            text
        )

        return {
            "name": buyer_name,
            "contact_person": buyer_contact_person,
            "email": buyer_email,
            "pan": buyer_pan,
            "gstin": buyer_gstin,
            "address": buyer_address
        }

    # ---------------------------------------------------------
    # BUYER GSTIN
    # ---------------------------------------------------------

    def extract_buyer_gstin(self, text):
        # First preference:
        # GSTIN appearing inside Delivery Address section.

        delivery_section = self.get_delivery_section(
            text
        )

        if delivery_section:
            gstin = self.extract_gstin(
                delivery_section
            )

            if gstin:
                return gstin

        # Second preference:
        # GSTIN Number Details section.

        gstin = self.first_match(
            text,
            [
                (
                    r"GSTIN\s+Number\s+Details\s*:"
                    r".*?"
                    r"GSTIN\s+No\s*:\s*"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        return gstin

    # ---------------------------------------------------------
    # BUYER PAN
    # ---------------------------------------------------------

    def extract_buyer_pan(self, text):
        if not text:
            return None

        section_match = re.search(
            r"GSTIN\s+Number\s+Details\s*:?(.*?)(?:PURCHASE\s+ORDER|$)",
            text,
            re.IGNORECASE | re.DOTALL
        )

        if section_match:
            section = section_match.group(1)

            pan = self.first_match(
                section,
                [
                    r"Pan\s+No\s*:?\s*([A-Z]{5}[0-9]{4}[A-Z])",
                    r"PAN\s*:?\s*([A-Z]{5}[0-9]{4}[A-Z])"
                ]
            )

            if pan:
                return pan

        return None

    # ---------------------------------------------------------
    # DELIVERY ADDRESS
    # ---------------------------------------------------------

    def get_delivery_section(self, text):
        if not text:
            return None

        match = re.search(
            (
                r"Delivery\s+Address\s*:\s*"
                r"(?P<section>.*?)"
                r"(?:"
                r"TOTAL\s+BASIC\s+VALUE|"
                r"DELIVERY\s+DATE\s*:|"
                r"Payment\s+Terms\s*:|"
                r"Buyer\s*:"
                r")"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not match:
            return None

        return match.group(
            "section"
        )

    def extract_delivery_address(self, text):
        section = self.get_delivery_section(
            text
        )

        if not section:
            return None

        lines = []

        for line in section.splitlines():
            line = self.clean(
                line
            )

            if not line:
                continue

            upper = line.upper()

            # Do not put communication/tax fields
            # into the physical address.
            if upper.startswith("TEL"):
                continue

            if upper.startswith("GSTN"):
                continue

            if upper.startswith("GSTIN"):
                continue

            if upper.startswith("EMAIL"):
                continue

            lines.append(
                line
            )

        if not lines:
            return None

        return "\n".join(
            lines
        )

    # ---------------------------------------------------------
    # SITE
    # ---------------------------------------------------------

    def extract_site(self, text):
        site_code = self.first_match(
            text,
            [
                (
                    r"PO\s*NO\.?\s*:\s*[A-Z0-9\/\-]+"
                    r"\s+Site\s*:\s*([A-Z0-9\/\-]+)"
                ),
                r"\bSite\s*:\s*([A-Z0-9\/\-]+)"
            ]
        )

        site_name = None

        # Metro places site name immediately after PO Date
        # in the tested document.
        po_date_match = re.search(
            (
                r"PO\s+Date\s*:\s*[0-9.\-/]+"
                r"\s*\n+\s*([^\n]+)"
            ),
            text,
            re.IGNORECASE
        )

        if po_date_match:
            candidate = self.clean(
                po_date_match.group(1)
            )

            if candidate:
                upper = candidate.upper()

                if (
                    "PURCHASE ORDER" not in upper
                    and "DELIVERY ADDRESS" not in upper
                ):
                    site_name = candidate

        # Fallback to Annexure For Site Details.
        if not site_name and site_code:
            pattern = (
                r"Annexure\s+For\s+Site\s+Details"
                r".*?"
                + re.escape(site_code)
                + r"\s*\n\s*([^\n]+)"
            )

            match = re.search(
                pattern,
                text,
                re.IGNORECASE | re.DOTALL
            )

            if match:
                site_name = self.clean(
                    match.group(1)
                )

        site_address = self.extract_delivery_address(
            text
        )

        return {
            "code": site_code,
            "name": site_name,
            "address": site_address
        }

    # ---------------------------------------------------------
    # AMOUNTS
    # ---------------------------------------------------------

    def extract_amounts(self, text):
        basic = self.extract_amount_after_label(
            text,
            "TOTAL BASIC VALUE"
        )

        cgst = self.extract_amount_after_label(
            text,
            "TOTAL CGST"
        )

        sgst = self.extract_amount_after_label(
            text,
            "TOTAL SGST"
        )

        igst = self.extract_amount_after_label(
            text,
            "TOTAL IGST"
        )

        cess = self.extract_amount_after_label(
            text,
            "TOTAL CESS"
        )

        total = self.extract_amount_after_label(
            text,
            "Total Order Value"
        )

        return {
            "basic": basic,
            "cgst": cgst,
            "sgst": sgst,
            "igst": igst,
            "cess": cess,
            "total": total
        }

    def extract_amount_after_label(
        self,
        text,
        label
    ):
        pattern = (
            re.escape(label)
            + r"\s*:?"
            + r"\s*(?:INR)?"
            + r"\s*([\d,]+(?:\.\d+)?)"
        )

        match = re.search(
            pattern,
            text,
            re.IGNORECASE
        )

        if match:
            return self.money(
                match.group(1)
            )

        # Metro PDF often places:
        #
        # TOTAL BASIC VALUE
        # INR
        # 865,920.00

        pattern = (
            re.escape(label)
            + r"\s*:?"
            + r"\s*(?:\r?\n|\s)*"
            + r"(?:INR)?"
            + r"\s*(?:\r?\n|\s)*"
            + r"([\d,]+(?:\.\d+)?)"
        )

        match = re.search(
            pattern,
            text,
            re.IGNORECASE
        )

        if match:
            return self.money(
                match.group(1)
            )

        return None

    # ---------------------------------------------------------
    # METRO TABLE EXTRACTION
    # ---------------------------------------------------------

    def extract_metro_items(self, tables):
        items = []

        line_counter = 0

        for table in tables or []:
            rows = table.get(
                "rows",
                []
            )

            if not rows:
                continue

            for row in rows:
                if not row:
                    continue

                cleaned = [
                    self.clean(cell) or ""
                    for cell in row
                ]

                row_text = " ".join(
                    cleaned
                )

                if not row_text:
                    continue

                upper = row_text.upper()

                if self.is_summary_row(
                    upper
                ):
                    continue

                item = self.parse_metro_row(
                    cleaned
                )

                if not item:
                    continue

                line_counter += 1

                if not item.get(
                    "line_no"
                ):
                    item["line_no"] = line_counter

                items.append(
                    item
                )

        return items

    # ---------------------------------------------------------
    # PARSE ONE METRO ROW
    # ---------------------------------------------------------

    def parse_metro_row(self, row):
        """
        Handles the Metro table structure seen in the supplied PO.

        Example pdfplumber row:

        [
            "1",
            "491538329 33049990",
            "8901548143629",
            "EVERYUTH ... 10.09.2025 T2FM",
            "55.000 21,120.000",
            "CAR EA",
            "22,656.00 59.00",
            "15,744.00",
            "9.00 9.00 0.00 0.00",
            "77,932.80 77,932.80 0.00 0.00",
            "865,920.00"
        ]
        """

        if len(row) < 8:
            return None

        # First cell should be serial number.
        line_no = self.integer(
            row[0]
        )

        if line_no is None:
            return None

        # -----------------------------------------------------
        # ARTICLE + HSN
        # -----------------------------------------------------

        article_no = None
        hsn_code = None

        article_hsn_numbers = re.findall(
            r"\d+",
            row[1] if len(row) > 1 else ""
        )

        if len(article_hsn_numbers) >= 1:
            article_no = article_hsn_numbers[0]

        if len(article_hsn_numbers) >= 2:
            hsn_code = article_hsn_numbers[1]

        # -----------------------------------------------------
        # EAN
        # -----------------------------------------------------

        ean = None

        if len(row) > 2:
            ean_match = re.search(
                r"\b\d{8,14}\b",
                row[2]
            )

            if ean_match:
                ean = ean_match.group(
                    0
                )

        # -----------------------------------------------------
        # DESCRIPTION + DELIVERY DATE + SITE
        # -----------------------------------------------------

        description = None
        delivery_date = None
        site = None

        if len(row) > 3:
            description_cell = row[3]

            date_match = re.search(
                r"\b\d{1,2}[./-]\d{1,2}[./-]\d{2,4}\b",
                description_cell
            )

            if date_match:
                delivery_date = self.date(
                    date_match.group(0)
                )

                description_part = (
                    description_cell[
                        :date_match.start()
                    ]
                )

                description = self.clean(
                    description_part
                )

                after_date = (
                    description_cell[
                        date_match.end():
                    ]
                )

                after_date = self.clean(
                    after_date
                )

                if after_date:
                    site_match = re.search(
                        r"\b[A-Z0-9]{2,10}\b",
                        after_date,
                        re.IGNORECASE
                    )

                    if site_match:
                        site = site_match.group(
                            0
                        )

            else:
                description = self.clean(
                    description_cell
                )

        # -----------------------------------------------------
        # QUANTITY + SECOND QUANTITY/PACK VALUE
        # -----------------------------------------------------

        quantity = None
        secondary_quantity = None

        if len(row) > 4:
            quantity_numbers = self.extract_numbers(
                row[4]
            )

            if len(quantity_numbers) >= 1:
                quantity = quantity_numbers[0]

            if len(quantity_numbers) >= 2:
                secondary_quantity = (
                    quantity_numbers[1]
                )

        # -----------------------------------------------------
        # UOM
        # -----------------------------------------------------

        uom = None
        secondary_uom = None

        if len(row) > 5:
            uoms = re.findall(
                r"\b[A-Z]{1,10}\b",
                row[5].upper()
            )

            if len(uoms) >= 1:
                uom = uoms[0]

            if len(uoms) >= 2:
                secondary_uom = uoms[1]

        # -----------------------------------------------------
        # MRP + SECOND VALUE
        # -----------------------------------------------------

        mrp = None
        secondary_price = None

        if len(row) > 6:
            price_numbers = self.extract_numbers(
                row[6]
            )

            if len(price_numbers) >= 1:
                mrp = price_numbers[0]

            if len(price_numbers) >= 2:
                secondary_price = (
                    price_numbers[1]
                )

        # -----------------------------------------------------
        # BASE COST
        # -----------------------------------------------------

        base_cost = None

        if len(row) > 7:
            base_cost = self.money(
                row[7]
            )

        # -----------------------------------------------------
        # TAX %
        # -----------------------------------------------------

        cgst_percent = None
        sgst_percent = None
        cess_percent = None
        cess_fixed_rate = None

        if len(row) > 8:
            tax_rates = self.extract_numbers(
                row[8]
            )

            if len(tax_rates) >= 1:
                cgst_percent = tax_rates[0]

            if len(tax_rates) >= 2:
                sgst_percent = tax_rates[1]

            if len(tax_rates) >= 3:
                cess_percent = tax_rates[2]

            if len(tax_rates) >= 4:
                cess_fixed_rate = tax_rates[3]

        # -----------------------------------------------------
        # TAX AMOUNTS
        # -----------------------------------------------------

        cgst_amount = None
        sgst_amount = None
        cess_amount = None
        cess_fixed_value = None

        if len(row) > 9:
            tax_amounts = self.extract_numbers(
                row[9]
            )

            if len(tax_amounts) >= 1:
                cgst_amount = tax_amounts[0]

            if len(tax_amounts) >= 2:
                sgst_amount = tax_amounts[1]

            if len(tax_amounts) >= 3:
                cess_amount = tax_amounts[2]

            if len(tax_amounts) >= 4:
                cess_fixed_value = tax_amounts[3]

        # -----------------------------------------------------
        # TOTAL BASE VALUE
        # -----------------------------------------------------

        total_base_value = None

        if len(row) > 10:
            total_base_value = self.money(
                row[10]
            )

        # Reject rows that do not resemble a real Metro item.
        if (
            not article_no
            and not description
            and not ean
        ):
            return None

        return {
            "line_no": line_no,

            "item_code": article_no,
            "article_no": article_no,

            "vendor_item_code": None,

            "description": description,

            "hsn_code": hsn_code,

            "ean": ean,

            "delivery_date": delivery_date,

            "site": site,

            "quantity": quantity,

            "secondary_quantity": (
                secondary_quantity
            ),

            "uom": uom,

            "secondary_uom": (
                secondary_uom
            ),

            "mrp": mrp,

            "secondary_price": (
                secondary_price
            ),

            "unit_cost": base_cost,
            "base_cost": base_cost,

            "cgst_percent": cgst_percent,
            "sgst_percent": sgst_percent,

            "igst_percent": None,

            "cess_percent": cess_percent,

            "cess_fixed_rate": (
                cess_fixed_rate
            ),

            "cgst_amount": cgst_amount,
            "sgst_amount": sgst_amount,

            "igst_amount": None,

            "cess_amount": cess_amount,

            "cess_fixed_value": (
                cess_fixed_value
            ),

            "total_amount": (
                total_base_value
            ),

            "total_base_value": (
                total_base_value
            ),

            "raw_row": row
        }

    # ---------------------------------------------------------
    # NUMBER LIST
    # ---------------------------------------------------------

    def extract_numbers(self, value):
        if not value:
            return []

        matches = re.findall(
            r"-?[\d,]+(?:\.\d+)?",
            str(value)
        )

        result = []

        for match in matches:
            parsed = self.number(
                match
            )

            if parsed is not None:
                result.append(
                    parsed
                )

        return result

    # ---------------------------------------------------------
    # TEXT FALLBACK ITEM EXTRACTION
    # ---------------------------------------------------------

    def extract_items_from_text(self, text):
        """
        Fallback for Metro PDFs where pdfplumber cannot extract
        a usable table.

        This is intentionally conservative. It does not invent
        missing values. Such records can later be marked for review.
        """

        items = []

        if not text:
            return items

        table_start = re.search(
            r"Sr\.?\s*No\.?\s+Article\s+No\.?",
            text,
            re.IGNORECASE
        )

        if not table_start:
            return items

        table_text = text[
            table_start.end():
        ]

        end_match = re.search(
            r"Grand\s+Total\s+of\s+Qty",
            table_text,
            re.IGNORECASE
        )

        if end_match:
            table_text = table_text[
                :end_match.start()
            ]

        lines = [
            self.clean(line)
            for line in table_text.splitlines()
            if self.clean(line)
        ]

        # This fallback only attempts to find obvious item starts.
        # We preserve uncertainty instead of incorrectly assigning
        # every following numeric value to a field.

        for index, line in enumerate(lines):
            if not re.fullmatch(
                r"\d+",
                line
            ):
                continue

            line_no = self.integer(
                line
            )

            if (
                line_no is None
                or line_no <= 0
                or line_no > 9999
            ):
                continue

            if index + 1 >= len(lines):
                continue

            article_candidate = lines[
                index + 1
            ]

            if not re.fullmatch(
                r"\d{5,15}",
                article_candidate
            ):
                continue

            item = {
                "line_no": line_no,
                "item_code": article_candidate,
                "article_no": article_candidate,
                "vendor_item_code": None,
                "description": None,
                "hsn_code": None,
                "ean": None,
                "delivery_date": None,
                "site": None,
                "quantity": None,
                "secondary_quantity": None,
                "uom": None,
                "secondary_uom": None,
                "mrp": None,
                "secondary_price": None,
                "unit_cost": None,
                "base_cost": None,
                "cgst_percent": None,
                "sgst_percent": None,
                "igst_percent": None,
                "cess_percent": None,
                "cess_fixed_rate": None,
                "cgst_amount": None,
                "sgst_amount": None,
                "igst_amount": None,
                "cess_amount": None,
                "cess_fixed_value": None,
                "total_amount": None,
                "total_base_value": None,
                "raw_row": None
            }

            items.append(
                item
            )

        return items