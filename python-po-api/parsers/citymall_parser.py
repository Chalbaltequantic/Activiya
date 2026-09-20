import re

from .base_parser import BasePOParser


class CityMallParser(BasePOParser):

    client_code = "citymall"
    client_name = "CityMall"

    # ---------------------------------------------------------
    # MAIN
    # ---------------------------------------------------------

    def parse(self, text, tables):
        text = text or ""

        header = self.extract_header(text)
        buyer = self.extract_buyer(text)
        vendor = self.extract_vendor(text)
        amounts = self.extract_amounts(text)

        items = self.extract_citymall_items(
            text,
            tables
        )

        return self.build_result(
            po_no=header.get("po_no"),
            po_date=header.get("po_date"),
            delivery_date=None,
            expiry_date=header.get("expiry_date"),

            vendor_code=vendor.get("code"),
            vendor_name=vendor.get("name"),
            vendor_email=None,
            vendor_pan=None,
            vendor_gstin=vendor.get("gstin"),
            vendor_address=vendor.get("address"),

            buyer_name=buyer.get("name"),
            buyer_contact_person=None,
            buyer_email=None,
            buyer_pan=None,
            buyer_gstin=buyer.get("gstin"),
            buyer_address=buyer.get("address"),

            bill_to=buyer.get("billing_address"),
            ship_to=buyer.get("delivery_address"),

            site_code=None,
            site_name=None,

            basic_amount=amounts.get("basic"),
            cgst_amount=None,
            sgst_amount=None,
            igst_amount=amounts.get("igst"),
            cess_amount=amounts.get("cess"),
            tax_amount=amounts.get("tax"),
            total_amount=amounts.get("total"),

            currency="INR",

            items=items,

            extra={
                "document_type": "PURCHASE ORDER",
                "parser": "citymall",

                "vendor_contact_name": vendor.get(
                    "contact_name"
                ),

                "vendor_contact_no": vendor.get(
                    "contact_no"
                ),

                "state_code": buyer.get(
                    "state_code"
                ),

                "tcs_amount": amounts.get(
                    "tcs"
                ),

                "requested_by": header.get(
                    "requested_by"
                ),

                "requested_by_email": header.get(
                    "requested_by_email"
                ),

                "requested_by_phone": header.get(
                    "requested_by_phone"
                ),

                "approved_by": header.get(
                    "approved_by"
                ),

                "approved_by_phone": header.get(
                    "approved_by_phone"
                )
            }
        )

    # ---------------------------------------------------------
    # HEADER
    # ---------------------------------------------------------

    def extract_header(self, text):
        po_no = self.first_match(
            text,
            [
                r"Purchase\s+Order\s+(PO-\d+)"
            ]
        )

        po_date = self.first_match(
            text,
            [
                (
                    r"Purchase\s+Order\s+Date\s*"
                    r"([0-9]{2}-[0-9]{2}-[0-9]{4})"
                )
            ]
        )

        expiry_date = self.first_match(
            text,
            [
                (
                    r"Purchase\s+Order\s+Expiry\s+Date\s*"
                    r"([0-9]{2}-[0-9]{2}-[0-9]{4})"
                )
            ]
        )

        requested_by = None
        requested_by_email = None
        requested_by_phone = None
        approved_by = None
        approved_by_phone = None

        requested_match = re.search(
            (
                r"Requested\s+By\s+"
                r"(.+?)\s+"
                r"([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})"
                r"\s+(\d{10,15})"
            ),
            text,
            re.IGNORECASE
        )

        if requested_match:
            requested_by = self.clean(
                requested_match.group(1)
            )

            requested_by_email = self.clean(
                requested_match.group(2)
            )

            requested_by_phone = self.clean(
                requested_match.group(3)
            )

        approved_match = re.search(
            (
                r"Approved\s+By\s+"
                r"(.+?)\s+--\s+"
                r"(\d{10,15})"
            ),
            text,
            re.IGNORECASE
        )

        if approved_match:
            approved_by = self.clean(
                approved_match.group(1)
            )

            approved_by_phone = self.clean(
                approved_match.group(2)
            )

        return {
            "po_no": po_no,
            "po_date": po_date,
            "expiry_date": expiry_date,
            "requested_by": requested_by,
            "requested_by_email": requested_by_email,
            "requested_by_phone": requested_by_phone,
            "approved_by": approved_by,
            "approved_by_phone": approved_by_phone
        }

    # ---------------------------------------------------------
    # BUYER / COMPANY
    # ---------------------------------------------------------

    def extract_buyer(self, text):
        section = self.extract_section(
            text,
            "Company Details",
            "Vendor Details"
        )

        if not section:
            return {
                "name": None,
                "gstin": None,
                "state_code": None,
                "billing_address": None,
                "delivery_address": None,
                "address": None
            }

        name = self.first_match(
            section,
            [
                r"Name\s+([^\n\r]+)"
            ]
        )

        gstin = self.first_match(
            section,
            [
                (
                    r"GST\s+"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        state_code = self.first_match(
            section,
            [
                r"State\s+Code\s*-\s*(\d{2})"
            ]
        )

        billing_address = self.extract_address_between(
            section,
            r"Billing\s+Address",
            r"Delivery\s+Address"
        )

        delivery_address = self.extract_address_between(
            section,
            r"Delivery\s+Address",
            None
        )

        return {
            "name": name,
            "gstin": gstin,
            "state_code": state_code,
            "billing_address": billing_address,
            "delivery_address": delivery_address,
            "address": billing_address
        }

    # ---------------------------------------------------------
    # VENDOR
    # ---------------------------------------------------------

    def extract_vendor(self, text):
        section = self.extract_section(
            text,
            "Vendor Details",
            "Ordered Items"
        )

        if not section:
            return {
                "code": None,
                "name": None,
                "gstin": None,
                "contact_name": None,
                "contact_no": None,
                "address": None
            }

        name = self.first_match(
            section,
            [
                r"Issued\s+To\s+([^\n\r]+)"
            ]
        )

        code = self.first_match(
            section,
            [
                r"Vendor\s+Code\s+([^\n\r]+)"
            ]
        )

        gstin = self.first_match(
            section,
            [
                (
                    r"GST\s+"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        contact_name = self.first_match(
            section,
            [
                (
                    r"Contact\s+Person\s+Name\s+"
                    r"([^\n\r]+)"
                )
            ]
        )

        address = self.extract_address_between(
            section,
            r"Address",
            r"Vendor\s+Contact"
        )

        contact_no = self.first_match(
            section,
            [
                (
                    r"Vendor\s+Contact\s*"
                    r"(?:Number)?\s*"
                    r"(\d{10,15})"
                )
            ]
        )

        return {
            "code": code,
            "name": name,
            "gstin": gstin,
            "contact_name": contact_name,
            "contact_no": contact_no,
            "address": address
        }

    # ---------------------------------------------------------
    # AMOUNTS
    # ---------------------------------------------------------

    def extract_amounts(self, text):
        """
        Use CityMall's explicit Total Amount summary instead of
        deriving totals from item rows.
        """

        summary_match = re.search(
            r"Total\s+Amount\s+(.*?)$",
            text,
            re.IGNORECASE | re.DOTALL
        )

        summary = (
            summary_match.group(1)
            if summary_match
            else text
        )

        basic = self.first_match(
            summary,
            [
                (
                    r"Base\s+Amount\s*"
                    r"([\d,]+(?:\.\d+)?)"
                )
            ]
        )

        igst = self.first_match(
            summary,
            [
                (
                    r"IGST\s+"
                    r"([\d,]+(?:\.\d+)?)"
                )
            ]
        )

        tcs = self.first_match(
            summary,
            [
                (
                    r"TCS\s+Amount\s*"
                    r"([\d,]+(?:\.\d+)?)"
                )
            ]
        )

        cess = self.first_match(
            summary,
            [
                (
                    r"Cess\s+"
                    r"([\d,]+(?:\.\d+)?)"
                )
            ]
        )

        total = self.first_match(
            summary,
            [
                (
                    r"Net\s+Amount\s+"
                    r"([\d,]+(?:\.\d+)?)"
                )
            ]
        )

        basic = self.money(basic)
        igst = self.money(igst)
        tcs = self.money(tcs)
        cess = self.money(cess)
        total = self.money(total)

        tax = None

        if igst is not None:
            tax = igst

            if cess is not None:
                tax += cess

        return {
            "basic": basic,
            "igst": igst,
            "tcs": tcs,
            "cess": cess,
            "tax": tax,
            "total": total
        }

    # ---------------------------------------------------------
    # ITEMS
    # ---------------------------------------------------------

    def extract_citymall_items(
        self,
        text,
        tables
    ):
        """
        CityMall PDF preserves its item table well through
        pdfplumber, so use tables first.
        """

        items = []

        for table_info in tables or []:
            rows = table_info.get(
                "rows",
                []
            )

            for row in rows:
                item = self.parse_citymall_row(
                    row
                )

                if item:
                    items.append(item)

        if items:
            return self.remove_duplicate_items(
                items
            )

        return self.extract_items_from_text(
            text
        )

    # ---------------------------------------------------------
    # TABLE ROW
    # ---------------------------------------------------------

    def parse_citymall_row(self, row):
        if not row:
            return None

        cells = [
            self.clean(cell)
            for cell in row
        ]

        if len(cells) < 8:
            return None

        # Actual CityMall table row:
        #
        # 1
        # CM0030729
        # Sugarfree...
        # 30045035
        # 320.00
        # 239.99
        # 48
        # 11519.52
        # 12.00 0.00
        # 1382.34 0.00
        # 12901.86

        if not re.fullmatch(
            r"\d+",
            cells[0] or ""
        ):
            return None

        if len(cells) < 11:
            return None

        item_code = cells[1]

        if not re.fullmatch(
            r"CM\d+",
            item_code or "",
            re.IGNORECASE
        ):
            return None

        description = cells[2]
        hsn_code = cells[3]

        mrp = self.money(
            cells[4]
        )

        unit_cost = self.money(
            cells[5]
        )

        quantity = self.number(
            cells[6]
        )

        base_amount = self.money(
            cells[7]
        )

        tax_rates = self.extract_numeric_values(
            cells[8]
        )

        tax_amounts = self.extract_numeric_values(
            cells[9]
        )

        igst_percent = (
            tax_rates[0]
            if len(tax_rates) >= 1
            else None
        )

        cess_percent = (
            tax_rates[1]
            if len(tax_rates) >= 2
            else None
        )

        igst_amount = (
            tax_amounts[0]
            if len(tax_amounts) >= 1
            else None
        )

        cess_amount = (
            tax_amounts[1]
            if len(tax_amounts) >= 2
            else None
        )

        total_amount = self.money(
            cells[10]
        )

        return {
            "line_no": self.integer(
                cells[0]
            ),

            "item_code": item_code,

            "vendor_item_code": None,

            "description": description,

            "hsn_code": hsn_code,

            "ean": None,

            "delivery_date": None,

            "site": None,

            "quantity": quantity,

            "uom": None,

            "mrp": mrp,

            "unit_cost": unit_cost,

            "base_cost": unit_cost,

            "base_amount": base_amount,

            "cgst_percent": None,
            "sgst_percent": None,

            "igst_percent": igst_percent,

            "cess_percent": cess_percent,

            "cgst_amount": None,
            "sgst_amount": None,

            "igst_amount": igst_amount,

            "cess_amount": cess_amount,

            "total_amount": total_amount,

            "raw_row": row
        }

    # ---------------------------------------------------------
    # TEXT FALLBACK
    # ---------------------------------------------------------

    def extract_items_from_text(self, text):
        """
        Conservative fallback when pdfplumber cannot extract
        the CityMall table.

        The current CityMall template is expected to use table
        extraction in normal PDFs.
        """

        items = []

        if not text:
            return items

        section = self.extract_section(
            text,
            "Ordered Items",
            "Approval Details"
        )

        if not section:
            return items

        pattern = re.compile(
            (
                r"(?m)^(\d+)\s*$"
                r"\s*^(CM\d+)\s*$"
            ),
            re.IGNORECASE
        )

        matches = list(
            pattern.finditer(section)
        )

        for index, match in enumerate(matches):
            start = match.end()

            if index + 1 < len(matches):
                end = matches[
                    index + 1
                ].start()
            else:
                end = len(section)

            block = section[start:end]

            item = {
                "line_no": self.integer(
                    match.group(1)
                ),
                "item_code": self.clean(
                    match.group(2)
                ),
                "description": None,
                "hsn_code": None,
                "quantity": None,
                "uom": None,
                "mrp": None,
                "unit_cost": None,
                "igst_percent": None,
                "cess_percent": None,
                "igst_amount": None,
                "cess_amount": None,
                "total_amount": None,
                "raw_row": block.splitlines()
            }

            items.append(item)

        return items

    # ---------------------------------------------------------
    # SECTION HELPER
    # ---------------------------------------------------------

    def extract_section(
        self,
        text,
        start_label,
        end_label=None
    ):
        if not text:
            return None

        start = re.search(
            re.escape(start_label),
            text,
            re.IGNORECASE
        )

        if not start:
            return None

        section = text[
            start.end():
        ]

        if end_label:
            end = re.search(
                re.escape(end_label),
                section,
                re.IGNORECASE
            )

            if end:
                section = section[
                    :end.start()
                ]

        return section.strip()

    # ---------------------------------------------------------
    # ADDRESS HELPER
    # ---------------------------------------------------------

    def extract_address_between(
        self,
        text,
        start_pattern,
        end_pattern=None
    ):
        if not text:
            return None

        if end_pattern:
            pattern = (
                start_pattern
                + r"\s*(.*?)\s*"
                + end_pattern
            )
        else:
            pattern = (
                start_pattern
                + r"\s*(.*)$"
            )

        match = re.search(
            pattern,
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not match:
            return None

        return self.clean_multiline(
            match.group(1)
        )

    # ---------------------------------------------------------
    # NUMERIC HELPER
    # ---------------------------------------------------------

    def extract_numeric_values(
        self,
        value
    ):
        if not value:
            return []

        matches = re.findall(
            r"-?[\d,]+(?:\.\d+)?",
            str(value)
        )

        values = []

        for match in matches:
            number = self.number(
                match
            )

            if number is not None:
                values.append(number)

        return values

    # ---------------------------------------------------------
    # REMOVE DUPLICATES
    # ---------------------------------------------------------

    def remove_duplicate_items(
        self,
        items
    ):
        result = []
        seen = set()

        for item in items:
            key = (
                item.get("line_no"),
                item.get("item_code")
            )

            if key in seen:
                continue

            seen.add(key)
            result.append(item)

        return result