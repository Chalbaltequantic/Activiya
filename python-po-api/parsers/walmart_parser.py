import re

from .base_parser import BasePOParser


class WalmartParser(BasePOParser):

    client_code = "walmart"
    client_name = "Walmart"

    # ---------------------------------------------------------
    # MAIN PARSER
    # ---------------------------------------------------------

    def parse(self, text, tables):
        text = text or ""

        header = self.extract_header(text)
        vendor = self.extract_vendor(text)
        buyer = self.extract_buyer(text)
        amounts = self.extract_amounts(text)

        items = self.extract_walmart_items(
            text,
            tables
        )

        return self.build_result(
            po_no=header.get("po_no"),
            po_date=header.get("po_date"),
            delivery_date=None,
            expiry_date=header.get("cancel_date"),

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
            buyer_address=buyer.get("bill_to"),

            bill_to=buyer.get("bill_to"),
            ship_to=buyer.get("ship_to"),

            site_code=buyer.get("site_code"),
            site_name=buyer.get("site_name"),

            basic_amount=amounts.get("basic"),
            cgst_amount=amounts.get("cgst"),
            sgst_amount=amounts.get("sgst"),
            igst_amount=None,
            cess_amount=amounts.get("cess"),
            tax_amount=amounts.get("tax"),
            total_amount=amounts.get("total"),

            currency="INR",

            items=items,

            extra={
                "document_type": "PURCHASE ORDER",
                "parser": "walmart",

                "po_cancel_date": header.get(
                    "cancel_date"
                ),

                "payment_terms": header.get(
                    "payment_terms"
                ),

                "supplier_no": vendor.get(
                    "supplier_no"
                ),

                "telephone_no": vendor.get(
                    "telephone_no"
                ),

                "place_of_supply": buyer.get(
                    "place_of_supply"
                )
            }
        )

    # ---------------------------------------------------------
    # HEADER
    # ---------------------------------------------------------

    def extract_header(self, text):
        po_no = None
        po_date = None
        cancel_date = None
        payment_terms = None

        # -----------------------------------------------------
        # PRIMARY PO NUMBER
        #
        # Walmart repeats:
        #
        # : 6100086941
        # PO No
        # -----------------------------------------------------

        match = re.search(
            r":\s*(\d{8,12})\s*\nPO\s+No\b",
            text,
            re.IGNORECASE
        )

        if match:
            po_no = self.clean(
                match.group(1)
            )

        # -----------------------------------------------------
        # FALLBACK PO NUMBER
        #
        # : 6100086941
        # PURCHASE ORDER NO.
        # -----------------------------------------------------

        if not po_no:
            match = re.search(
                (
                    r":\s*(\d{8,12})\s*"
                    r"\nPURCHASE\s+ORDER\s+NO\.?"
                ),
                text,
                re.IGNORECASE
            )

            if match:
                po_no = self.clean(
                    match.group(1)
                )

        # -----------------------------------------------------
        # PO DATE
        #
        # : 25.08.2025
        # PO Order Dt
        # -----------------------------------------------------

        match = re.search(
            (
                r":\s*(\d{2}\.\d{2}\.\d{4})"
                r"\s*\nPO\s+Order\s+Dt"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            po_date = self.date(
                match.group(1)
            )

        # -----------------------------------------------------
        # FALLBACK ORDER DATE
        #
        # : 25.08.2025
        # ORDER DATE
        # -----------------------------------------------------

        if not po_date:
            match = re.search(
                (
                    r":\s*(\d{2}\.\d{2}\.\d{4})"
                    r"\s*\nORDER\s+DATE"
                ),
                text,
                re.IGNORECASE
            )

            if match:
                po_date = self.date(
                    match.group(1)
                )

        # -----------------------------------------------------
        # CANCEL DATE
        #
        # : 15.09.2025
        # PO cancel Dt
        # -----------------------------------------------------

        match = re.search(
            (
                r":\s*(\d{2}\.\d{2}\.\d{4})"
                r"\s*\nPO\s+cancel\s+Dt"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            cancel_date = self.date(
                match.group(1)
            )

        # -----------------------------------------------------
        # FALLBACK CANCEL DATE
        # -----------------------------------------------------

        if not cancel_date:
            match = re.search(
                (
                    r":\s*(\d{2}\.\d{2}\.\d{4})"
                    r"\s*\nPO\s+CANCEL\s+DATE"
                ),
                text,
                re.IGNORECASE
            )

            if match:
                cancel_date = self.date(
                    match.group(1)
                )

        # -----------------------------------------------------
        # PAYMENT TERMS
        # -----------------------------------------------------

        match = re.search(
            (
                r"PAYMENT\s+TERMS\s*"
                r"\n?\s*:\s*([^\n\r]+)"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            payment_terms = self.clean(
                match.group(1)
            )

        return {
            "po_no": po_no,
            "po_date": po_date,
            "cancel_date": cancel_date,
            "payment_terms": payment_terms
        }

    # ---------------------------------------------------------
    # VENDOR
    # ---------------------------------------------------------

    def extract_vendor(self, text):
        name = None
        address = None
        gstin = None
        supplier_no = None
        telephone_no = None

        # -----------------------------------------------------
        # TO / SHIP FROM BLOCK
        # -----------------------------------------------------

        match = re.search(
            (
                r"TO\s*/\s*SHIP\s+FROM\s*:\s*"
                r"(.*?)"
                r"GSTIN\s*:?"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if match:
            section = match.group(1)

            lines = []

            for line in section.splitlines():
                cleaned = self.clean(
                    line
                )

                if cleaned:
                    lines.append(cleaned)

            if lines:
                name = lines[0]

            if len(lines) > 1:
                address = "\n".join(
                    lines[1:]
                )

        # -----------------------------------------------------
        # VENDOR GSTIN
        #
        # Walmart extraction:
        #
        # 09AABCZ3366L1ZS
        # GSTIN :
        # -----------------------------------------------------

        match = re.search(
            (
                r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                r"\s*\nGSTIN\s*:?"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            gstin = match.group(1).upper()

        # -----------------------------------------------------
        # SUPPLIER / VENDOR NUMBER
        #
        # Reliable repeated footer:
        #
        # : 2017620901
        # Vendor No
        # -----------------------------------------------------

        match = re.search(
            (
                r":\s*(\d+)"
                r"\s*\nVendor\s+No\b"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            supplier_no = self.clean(
                match.group(1)
            )

        # -----------------------------------------------------
        # FALLBACK SUPPLIER NUMBER
        #
        # 2017620901
        # Supplier No.
        # -----------------------------------------------------

        if not supplier_no:
            match = re.search(
                (
                    r"(\d+)\s*"
                    r"\nSupplier\s+No\.?"
                ),
                text,
                re.IGNORECASE
            )

            if match:
                supplier_no = self.clean(
                    match.group(1)
                )

        # -----------------------------------------------------
        # TELEPHONE NUMBER
        #
        # Current PDF extraction:
        #
        # Supplier No.
        # 7600001654
        # Telephone No.
        #
        # Preserve this separately. Do NOT use as vendor code.
        # -----------------------------------------------------

        match = re.search(
            (
                r"Supplier\s+No\.?\s*:?\s*"
                r"\n\s*(\d+)\s*"
                r"\nTelephone\s+No\.?"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            telephone_no = self.clean(
                match.group(1)
            )

        # -----------------------------------------------------
        # VENDOR NAME FALLBACK
        #
        # ZYDUS WELLNESS PRODUCTS
        # LIMITED
        # Vendor Name :
        # -----------------------------------------------------

        if not name:
            match = re.search(
                (
                    r"([A-Z][A-Z\s&.\-]+?)"
                    r"\s*\nVendor\s+Name"
                ),
                text,
                re.IGNORECASE
            )

            if match:
                name = self.clean_multiline(
                    match.group(1)
                )

        return {
            "code": supplier_no,
            "supplier_no": supplier_no,
            "telephone_no": telephone_no,
            "name": name,
            "gstin": gstin,
            "address": address
        }

    # ---------------------------------------------------------
    # BUYER / BILL TO / SHIP TO
    # ---------------------------------------------------------

    def extract_buyer(self, text):
        bill_to = self.extract_location_block(
            text,
            "BILL TO:",
            "SHIP TO:"
        )

        ship_to = self.extract_location_block(
            text,
            "SHIP TO:",
            "Please quote Purchase order"
        )

        name = None
        gstin = None
        site_code = None
        site_name = None
        place_of_supply = None

        if bill_to:
            lines = []

            for line in bill_to.splitlines():
                cleaned = self.clean(
                    line
                )

                if cleaned:
                    lines.append(cleaned)

            if lines:
                name = lines[0]

            if len(lines) >= 2:
                site_name = lines[1]

            # Site/store code in:
            # Wal-Mart India Pvt. Ltd. (4797)

            match = re.search(
                r"\((\d+)\)",
                name or ""
            )

            if match:
                site_code = match.group(1)

            # Buyer GSTIN

            match = re.search(
                (
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                ),
                bill_to,
                re.IGNORECASE
            )

            if match:
                gstin = match.group(
                    1
                ).upper()

            # Place of Supply

            match = re.search(
                (
                    r"Place\s+of\s+Supply\s*:\s*"
                    r"([^\n\r]+)"
                ),
                bill_to,
                re.IGNORECASE
            )

            if match:
                place_of_supply = self.clean(
                    match.group(1)
                )

        return {
            "name": name,
            "gstin": gstin,
            "bill_to": bill_to,
            "ship_to": ship_to,
            "site_code": site_code,
            "site_name": site_name,
            "place_of_supply": place_of_supply
        }

    # ---------------------------------------------------------
    # LOCATION BLOCK HELPER
    # ---------------------------------------------------------

    def extract_location_block(
        self,
        text,
        start_label,
        end_label
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

        end = re.search(
            re.escape(end_label),
            section,
            re.IGNORECASE
        )

        if end:
            section = section[
                :end.start()
            ]

        section = re.sub(
            r"\nGSTIN\s+NO\s*:\s*$",
            "",
            section,
            flags=re.IGNORECASE
        )

        return self.clean_multiline(
            section
        )

    # ---------------------------------------------------------
    # PO TOTALS
    # ---------------------------------------------------------

    def extract_amounts(self, text):
        basic = None
        tax = None
        total = None

        # -----------------------------------------------------
        # TOTAL COST WITHOUT TAX
        #
        # 13743.33
        # Total cost without tax
        # -----------------------------------------------------

        match = re.search(
            (
                r"([\d,]+\.\d{2})\s*"
                r"\nTotal\s+cost\s+without\s+tax"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            basic = self.money(
                match.group(1)
            )

        # -----------------------------------------------------
        # TOTAL TAX
        #
        # 1181.22
        # Total tax amount
        # -----------------------------------------------------

        match = re.search(
            (
                r"([\d,]+\.\d{2})\s*"
                r"\nTotal\s+tax\s+amount"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            tax = self.money(
                match.group(1)
            )

        # -----------------------------------------------------
        # TOTAL PO AMOUNT
        #
        # 14,924.55
        # Total PO AMOUNT including Taxes (INR)
        # -----------------------------------------------------

        match = re.search(
            (
                r"([\d,]+\.\d{2})\s*"
                r"\nTotal\s+PO\s+AMOUNT"
            ),
            text,
            re.IGNORECASE
        )

        if match:
            total = self.money(
                match.group(1)
            )

        return {
            "basic": basic,
            "cgst": None,
            "sgst": None,
            "igst": None,
            "cess": None,
            "tax": tax,
            "total": total
        }

    # ---------------------------------------------------------
    # ITEMS
    # ---------------------------------------------------------

    def extract_walmart_items(
        self,
        text,
        tables
    ):
        items = []

        # First try pdfplumber table extraction.

        for table_info in tables or []:
            if not isinstance(
                table_info,
                dict
            ):
                continue

            rows = table_info.get(
                "rows",
                []
            )

            for row in rows or []:
                try:
                    item = self.parse_walmart_row(
                        row
                    )

                    if item:
                        items.append(item)

                except Exception:
                    continue

        items = self.remove_duplicate_items(
            items
        )

        if items:
            return items

        # Fall back to extracted text.

        return self.extract_items_from_text(
            text
        )

    # ---------------------------------------------------------
    # PDFPLUMBER TABLE ROW
    # ---------------------------------------------------------

    def parse_walmart_row(self, row):
        if not row:
            return None

        # IMPORTANT:
        # pdfplumber can return None cells.
        # Convert every cell to a safe string.

        cells = []

        for cell in row:
            if cell is None:
                cells.append("")
                continue

            cleaned = self.clean(
                str(cell)
            )

            cells.append(
                cleaned
                if cleaned is not None
                else ""
            )

        if not cells:
            return None

        joined = " | ".join(
            cells
        )

        # Must contain an article number.

        article_match = re.search(
            r"#(\d{3,})",
            joined
        )

        if not article_match:
            return None

        # First cell should normally be line number.

        if not re.fullmatch(
            r"\d+",
            cells[0] or ""
        ):
            return None

        line_no = self.integer(
            cells[0]
        )

        article_no = None
        description = None
        hsn_code = None
        ean = None

        quantity = None
        uom = None
        pack = None
        mrp = None
        cost = None
        line_cost = None
        tax_details = None
        total_amount = None

        # -----------------------------------------------------
        # STANDARD WALMART COLUMN ORDER
        # -----------------------------------------------------

        if len(cells) >= 5:
            article_no = self.remove_hash(
                cells[1]
            )

            description = self.remove_hash(
                cells[2]
            )

            hsn_code = self.remove_hash(
                cells[3]
            )

            ean = self.remove_hash(
                cells[4]
            )

        if not article_no:
            article_no = article_match.group(
                1
            )

        # Validate article.

        if not re.fullmatch(
            r"\d+",
            article_no or ""
        ):
            return None

        # -----------------------------------------------------
        # REMAINING COLUMNS
        # -----------------------------------------------------

        if len(cells) >= 6:
            quantity = self.number(
                cells[5]
            )

        if len(cells) >= 7:
            uom = cells[6] or None

        if len(cells) >= 8:
            pack = cells[7] or None

        if len(cells) >= 9:
            mrp = self.extract_money_from_text(
                cells[8]
            )

        if len(cells) >= 10:
            cost = self.money(
                cells[9]
            )

        if len(cells) >= 11:
            line_cost = self.money(
                cells[10]
            )

        if len(cells) >= 12:
            tax_details = cells[11]

        if len(cells) >= 13:
            total_amount = self.money(
                cells[12]
            )

        taxes = self.parse_tax_details(
            tax_details
        )

        return {
            "line_no": line_no,

            "item_code": article_no,
            "article_no": article_no,

            "vendor_item_code": None,

            "description": description,

            "hsn_code": hsn_code,

            "ean": ean,

            "delivery_date": None,

            "site": None,

            "quantity": quantity,

            "uom": uom,

            "pack": pack,

            "mrp": mrp,

            "unit_cost": cost,

            "cost": cost,

            "base_amount": line_cost,

            "cgst_percent": taxes.get(
                "cgst_percent"
            ),

            "sgst_percent": taxes.get(
                "sgst_percent"
            ),

            "igst_percent": taxes.get(
                "igst_percent"
            ),

            "cess_percent": taxes.get(
                "cess_percent"
            ),

            "cgst_amount": taxes.get(
                "cgst_amount"
            ),

            "sgst_amount": taxes.get(
                "sgst_amount"
            ),

            "igst_amount": taxes.get(
                "igst_amount"
            ),

            "cess_amount": taxes.get(
                "cess_amount"
            ),

            "total_amount": total_amount,

            "raw_row": cells
        }

    # ---------------------------------------------------------
    # TEXT FALLBACK
    # ---------------------------------------------------------

    def extract_items_from_text(self, text):
        items = []

        if not text:
            return items

        # Restrict to item area.

        start = re.search(
            r"Sr\.No",
            text,
            re.IGNORECASE
        )

        if not start:
            return items

        section = text[
            start.end():
        ]

        end = re.search(
            r"Total\s+cost\s+without\s+tax",
            section,
            re.IGNORECASE
        )

        if end:
            section = section[
                :end.start()
            ]

        # Walmart row starts:
        #
        # 1
        # #14374
        #
        # 2
        # #44051

        pattern = re.compile(
            (
                r"(?m)^(\d+)\s*$"
                r"\s*^#(\d+)\s*$"
            )
        )

        matches = list(
            pattern.finditer(
                section
            )
        )

        for index, match in enumerate(matches):
            block_start = match.end()

            if index + 1 < len(matches):
                block_end = matches[
                    index + 1
                ].start()
            else:
                block_end = len(section)

            block = section[
                block_start:block_end
            ]

            item = self.parse_walmart_text_block(
                line_no=self.integer(
                    match.group(1)
                ),
                article_no=match.group(2),
                block=block
            )

            if item:
                items.append(item)

        return self.remove_duplicate_items(
            items
        )

    # ---------------------------------------------------------
    # TEXT ITEM BLOCK
    # ---------------------------------------------------------

    def parse_walmart_text_block(
        self,
        line_no,
        article_no,
        block
    ):
        lines = []

        for line in block.splitlines():
            cleaned = self.clean(
                line
            )

            if cleaned:
                lines.append(cleaned)

        if not lines:
            return None

        # -----------------------------------------------------
        # DESCRIPTION UNTIL HSN
        # -----------------------------------------------------

        description_lines = []
        hsn_code = None
        hsn_index = None

        for index, line in enumerate(lines):
            match = re.fullmatch(
                r"#(\d{6,8})",
                line
            )

            if match:
                hsn_code = match.group(1)
                hsn_index = index
                break

            description_lines.append(
                self.remove_hash(
                    line
                )
            )

        if hsn_index is None:
            return None

        description = self.clean(
            " ".join(
                description_lines
            )
        )

        cursor = hsn_index + 1

        # -----------------------------------------------------
        # EAN
        # -----------------------------------------------------

        ean = None

        if cursor < len(lines):
            match = re.fullmatch(
                r"#(\d{12,14})",
                lines[cursor]
            )

            if match:
                ean = match.group(1)
                cursor += 1

        # -----------------------------------------------------
        # QUANTITY
        # -----------------------------------------------------

        quantity = None

        if cursor < len(lines):
            quantity = self.number(
                lines[cursor]
            )

            cursor += 1

        # -----------------------------------------------------
        # UOM
        # -----------------------------------------------------

        uom = None

        if cursor < len(lines):
            uom = lines[cursor]
            cursor += 1

        # -----------------------------------------------------
        # PACK
        # -----------------------------------------------------

        pack = None

        if cursor < len(lines):
            pack = lines[cursor]
            cursor += 1

        # -----------------------------------------------------
        # MRP
        #
        # Example:
        # 56.00/EA
        # -----------------------------------------------------

        mrp = None

        if cursor < len(lines):
            mrp = self.extract_money_from_text(
                lines[cursor]
            )

            cursor += 1

        # -----------------------------------------------------
        # COST
        # -----------------------------------------------------

        cost = None

        if cursor < len(lines):
            cost = self.money(
                lines[cursor]
            )

            cursor += 1

        # -----------------------------------------------------
        # REMAINING TAX/TOTAL BLOCK
        # -----------------------------------------------------

        remaining_lines = lines[
            cursor:
        ]

        remaining = "\n".join(
            remaining_lines
        )

        # First number in remaining block is Line Cost Excl Tax.

        line_cost = None

        match = re.match(
            r"\s*([\d,]+\.\d{2})",
            remaining
        )

        if match:
            line_cost = self.money(
                match.group(1)
            )

        taxes = self.parse_tax_details(
            remaining
        )

        # -----------------------------------------------------
        # TOTAL AMOUNT
        #
        # Find last standalone monetary amount before
        # "Price change".
        # -----------------------------------------------------

        total_amount = None

        before_price_change = re.split(
            r"Price\s+change",
            remaining,
            maxsplit=1,
            flags=re.IGNORECASE
        )[0]

        standalone_amounts = re.findall(
            r"(?m)^\s*([\d,]+\.\d{2})\s*$",
            before_price_change
        )

        if standalone_amounts:
            total_amount = self.money(
                standalone_amounts[-1]
            )

        return {
            "line_no": line_no,

            "item_code": article_no,
            "article_no": article_no,

            "vendor_item_code": None,

            "description": description,

            "hsn_code": hsn_code,

            "ean": ean,

            "delivery_date": None,

            "site": None,

            "quantity": quantity,

            "uom": uom,

            "pack": pack,

            "mrp": mrp,

            "unit_cost": cost,

            "cost": cost,

            "base_amount": line_cost,

            "cgst_percent": taxes.get(
                "cgst_percent"
            ),

            "sgst_percent": taxes.get(
                "sgst_percent"
            ),

            "igst_percent": taxes.get(
                "igst_percent"
            ),

            "cess_percent": taxes.get(
                "cess_percent"
            ),

            "cgst_amount": taxes.get(
                "cgst_amount"
            ),

            "sgst_amount": taxes.get(
                "sgst_amount"
            ),

            "igst_amount": taxes.get(
                "igst_amount"
            ),

            "cess_amount": taxes.get(
                "cess_amount"
            ),

            "total_amount": total_amount,

            "raw_row": lines
        }

    # ---------------------------------------------------------
    # TAX DETAILS
    # ---------------------------------------------------------

    def parse_tax_details(self, value):
        value = str(
            value or ""
        )

        result = {
            "cgst_percent": None,
            "cgst_amount": None,

            "sgst_percent": None,
            "sgst_amount": None,

            "igst_percent": None,
            "igst_amount": None,

            "cess_percent": None,
            "cess_amount": None
        }

        # -----------------------------------------------------
        # CGST
        # -----------------------------------------------------

        match = re.search(
            (
                r"CGST\s*\(\s*"
                r"([\d.]+)\s*%\s*\)"
                r"\s*-\s*([\d,.]+)"
            ),
            value,
            re.IGNORECASE
        )

        if match:
            result["cgst_percent"] = self.number(
                match.group(1)
            )

            result["cgst_amount"] = self.money(
                match.group(2)
            )

        # -----------------------------------------------------
        # SGST
        # -----------------------------------------------------

        match = re.search(
            (
                r"SGST\s*\(\s*"
                r"([\d.]+)\s*%\s*\)"
                r"\s*-\s*([\d,.]+)"
            ),
            value,
            re.IGNORECASE
        )

        if match:
            result["sgst_percent"] = self.number(
                match.group(1)
            )

            result["sgst_amount"] = self.money(
                match.group(2)
            )

        # -----------------------------------------------------
        # IGST
        # -----------------------------------------------------

        match = re.search(
            (
                r"IGST\s*\(\s*"
                r"([\d.]+)\s*%\s*\)"
                r"\s*-\s*([\d,.]+)"
            ),
            value,
            re.IGNORECASE
        )

        if match:
            result["igst_percent"] = self.number(
                match.group(1)
            )

            result["igst_amount"] = self.money(
                match.group(2)
            )

        # -----------------------------------------------------
        # GST COMP CESS
        #
        # Example:
        # GST Comp. CESS(0.00%) - 0.00
        # -----------------------------------------------------

        match = re.search(
            (
                r"(?:GST\s+Comp\.\s*)?"
                r"CESS\s*\(\s*"
                r"([\d.]+)\s*%\s*\)"
                r"\s*-\s*([\d,.]+)"
            ),
            value,
            re.IGNORECASE
        )

        if match:
            result["cess_percent"] = self.number(
                match.group(1)
            )

            result["cess_amount"] = self.money(
                match.group(2)
            )

        return result

    # ---------------------------------------------------------
    # REMOVE HASH
    # ---------------------------------------------------------

    def remove_hash(self, value):
        if value is None:
            return None

        value = str(value).strip()

        value = re.sub(
            r"^#+",
            "",
            value
        )

        value = self.clean(
            value
        )

        return value

    # ---------------------------------------------------------
    # EXTRACT MONEY FROM TEXT
    # ---------------------------------------------------------

    def extract_money_from_text(
        self,
        value
    ):
        if value is None:
            return None

        match = re.search(
            r"-?[\d,]+(?:\.\d+)?",
            str(value)
        )

        if not match:
            return None

        return self.money(
            match.group(0)
        )

    # ---------------------------------------------------------
    # REMOVE DUPLICATE ITEMS
    # ---------------------------------------------------------

    def remove_duplicate_items(
        self,
        items
    ):
        result = []
        seen = set()

        for item in items or []:
            if not item:
                continue

            line_no = item.get(
                "line_no"
            )

            item_code = item.get(
                "item_code"
            )

            if not item_code:
                continue

            key = (
                line_no,
                str(item_code)
            )

            if key in seen:
                continue

            seen.add(key)

            result.append(
                item
            )

        return result