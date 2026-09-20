import re

from .base_parser import BasePOParser


class MyntraParser(BasePOParser):
    """
    Parser for Myntra / Myntra Jabong India Pvt Ltd Purchase Orders.

    Handles:
    - PO number
    - PO approved date
    - estimated shipment date
    - PO status
    - purchase type
    - transaction type
    - supply type
    - channel
    - bill-to / ship-to
    - buyer GSTIN
    - vendor details
    - vendor contact
    - vendor GSTIN
    - vendor emails
    - category manager
    - brand
    - Myntra SKU item table
    - total quantity
    - grand total
    """

    client_code = "myntra"
    client_name = "Myntra"

    # ---------------------------------------------------------
    # MAIN PARSER
    # ---------------------------------------------------------

    def parse(self, text, tables):
        text = text or ""

        header = self.extract_header(text)
        buyer = self.extract_buyer(text)
        vendor = self.extract_vendor(text)
        amounts = self.extract_amounts(text)

        items = self.extract_myntra_items(
            text,
            tables
        )

        return self.build_result(
            po_no=header.get("po_no"),
            po_date=header.get("po_date"),
            delivery_date=header.get(
                "delivery_date"
            ),
            expiry_date=None,

            vendor_code=None,
            vendor_name=vendor.get("name"),
            vendor_email=vendor.get("email"),
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

            site_code=None,
            site_name=None,

            basic_amount=amounts.get("basic"),
            cgst_amount=None,
            sgst_amount=None,
            igst_amount=amounts.get("igst"),
            cess_amount=None,
            tax_amount=amounts.get("tax"),
            total_amount=amounts.get("total"),

            currency="INR",

            items=items,

            extra={
                "document_type": "PURCHASE ORDER",
                "parser": "myntra",

                "po_status": header.get(
                    "po_status"
                ),

                "purchase_type": header.get(
                    "purchase_type"
                ),

                "nature_of_transaction": header.get(
                    "nature_of_transaction"
                ),

                "nature_of_supply": header.get(
                    "nature_of_supply"
                ),

                "channel": header.get(
                    "channel"
                ),

                "vendor_contact_name": vendor.get(
                    "contact_name"
                ),

                "vendor_contact_no": vendor.get(
                    "contact_no"
                ),

                "vendor_emails": vendor.get(
                    "emails"
                ),

                "category_manager": vendor.get(
                    "category_manager"
                ),

                "brand_name": vendor.get(
                    "brand_name"
                ),

                "total_quantity": amounts.get(
                    "total_quantity"
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
                r"PO\s*#\s*:\s*([A-Z0-9\-\/]+)",
                r"PO\s+NO\.?\s*:\s*([A-Z0-9\-\/]+)"
            ]
        )

        po_date = self.first_match(
            text,
            [
                r"PO\s+Approved\s+Date\s*:\s*([0-9\-\/.]+)",
                r"PO\s+Date\s*:\s*([0-9\-\/.]+)"
            ]
        )

        delivery_date = self.first_match(
            text,
            [
                r"Estimated\s+Shipment\s+Date\s*:\s*([0-9\-\/.]+)",
                r"Shipment\s+Date\s*:\s*([0-9\-\/.]+)"
            ]
        )

        po_status = self.first_match(
            text,
            [
                r"PO\s+Status\s*:\s*([^\n\r]+)"
            ]
        )

        purchase_type = self.first_match(
            text,
            [
                r"Purchase\s+Type\s*:\s*([^\n\r]+)"
            ]
        )

        nature_of_transaction = self.first_match(
            text,
            [
                r"Nature\s+of\s+Transaction\s*:\s*([^\n\r]+)"
            ]
        )

        nature_of_supply = self.first_match(
            text,
            [
                r"Nature\s+of\s+Supply\s*:\s*([^\n\r]+)"
            ]
        )

        channel = self.first_match(
            text,
            [
                r"Channel\s*:\s*([^\n\r]+)"
            ]
        )

        return {
            "po_no": po_no,
            "po_date": po_date,
            "delivery_date": delivery_date,
            "po_status": po_status,
            "purchase_type": purchase_type,
            "nature_of_transaction": (
                nature_of_transaction
            ),
            "nature_of_supply": nature_of_supply,
            "channel": channel
        }

    # ---------------------------------------------------------
    # BUYER
    # ---------------------------------------------------------

    def extract_buyer(self, text):
        buyer_name = self.first_match(
            text,
            [
                (
                    r"BILL\s+TO\s*:\s*"
                    r"(?:SHIP\s+TO\s*:)?\s*"
                    r"([^\n\r]+)"
                )
            ]
        )

        if (
            not buyer_name
            or "SHIP TO" in buyer_name.upper()
        ):
            buyer_name = "Myntra Jabong India Pvt Ltd"

        buyer_gstin = self.first_match(
            text,
            [
                (
                    r"GSTIN\s*#\s*"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        bill_to, ship_to = (
            self.extract_bill_ship_addresses(
                text
            )
        )

        return {
            "name": buyer_name,
            "gstin": buyer_gstin,
            "bill_to": bill_to,
            "ship_to": ship_to
        }

    # ---------------------------------------------------------
    # BILL TO / SHIP TO
    # ---------------------------------------------------------

    def extract_bill_ship_addresses(self, text):
        """
        Myntra prints BILL TO and SHIP TO side-by-side.

        PDF text extraction commonly converts them into sequential
        repeated address blocks.

        For the supplied format both addresses are identical.
        We identify the two Myntra address occurrences before GSTIN.
        """

        if not text:
            return None, None

        section_match = re.search(
            (
                r"BILL\s+TO\s*:.*?"
                r"(?P<section>.*?)"
                r"GSTIN\s*#"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not section_match:
            return None, None

        section = section_match.group(
            "section"
        )

        # Remove SHIP TO label if it was retained.
        section = re.sub(
            r"SHIP\s+TO\s*:",
            "",
            section,
            flags=re.IGNORECASE
        )

        lines = []

        for line in section.splitlines():
            line = self.clean(line)

            if line:
                lines.append(line)

        if not lines:
            return None, None

        buyer_indexes = []

        for index, line in enumerate(lines):
            if re.search(
                r"Myntra\s+Jabong\s+India\s+Pvt\s+Ltd",
                line,
                re.IGNORECASE
            ):
                buyer_indexes.append(index)

        if len(buyer_indexes) >= 2:
            first_index = buyer_indexes[0]
            second_index = buyer_indexes[1]

            bill_lines = lines[
                first_index:second_index
            ]

            ship_lines = lines[
                second_index:
            ]

            bill_to = "\n".join(
                bill_lines
            )

            ship_to = "\n".join(
                ship_lines
            )

            return bill_to, ship_to

        # Fallback when PDF extraction only returns one address.
        address = "\n".join(lines)

        return address, address

    # ---------------------------------------------------------
    # VENDOR
    # ---------------------------------------------------------

    def extract_vendor(self, text):
        vendor_name = self.extract_vendor_name(
            text
        )

        vendor_address = self.extract_vendor_address(
            text
        )

        contact_name = self.first_match(
            text,
            [
                r"Vendor\s+Contact\s+Name\s*:\s*([^\n\r]+)"
            ]
        )

        contact_no = self.first_match(
            text,
            [
                r"Vendor\s+Contact\s+No\s*:\s*([0-9+\-\s]+)"
            ]
        )

        if contact_no:
            contact_match = re.search(
                r"\d{10,15}",
                contact_no.replace(
                    " ",
                    ""
                )
            )

            if contact_match:
                contact_no = contact_match.group(
                    0
                )

        vendor_gstin = self.first_match(
            text,
            [
                (
                    r"Vendor\s+GSTIN\s*:\s*"
                    r"([0-9]{2}[A-Z]{5}[0-9]{4}"
                    r"[A-Z][0-9A-Z]Z[0-9A-Z])"
                )
            ]
        )

        category_manager = self.first_match(
            text,
            [
                (
                    r"Category\s+Manager\s*:\s*"
                    r"([^\n\r]+)"
                )
            ]
        )

        if category_manager:
            category_manager = re.split(
                r"Vendor\s+GSTIN\s*:",
                category_manager,
                flags=re.IGNORECASE
            )[0]

            category_manager = self.clean(
                category_manager
            )

        brand_name = self.first_match(
            text,
            [
                r"Brand\s+Name\(s\)\s*:\s*([^\n\r]+)",
                r"Brand\s+Name\s*:\s*([^\n\r]+)"
            ]
        )

        emails = self.extract_vendor_emails(
            text
        )

        primary_email = (
            emails[0]
            if emails
            else None
        )

        return {
            "name": vendor_name,
            "address": vendor_address,
            "contact_name": contact_name,
            "contact_no": contact_no,
            "gstin": vendor_gstin,
            "category_manager": category_manager,
            "brand_name": brand_name,
            "email": primary_email,
            "emails": emails
        }

    # ---------------------------------------------------------
    # VENDOR NAME
    # ---------------------------------------------------------

    def extract_vendor_name(self, text):
        if not text:
            return None

        match = re.search(
            (
                r"Vendor\s+Name\s*:\s*"
                r"(?P<name>.*?)"
                r"Vendor\s+Address\s*:"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not match:
            return None

        name = match.group(
            "name"
        )

        return self.clean(
            name
        )

    # ---------------------------------------------------------
    # VENDOR ADDRESS
    # ---------------------------------------------------------

    def extract_vendor_address(self, text):
        if not text:
            return None

        # In this Myntra layout the address itself occurs after
        # Vendor Contact Name and before Vendor Contact No.
        match = re.search(
            (
                r"Vendor\s+Contact\s+Name\s*:"
                r"\s*[^\n\r]+"
                r"\s*(?P<address>.*?)"
                r"Vendor\s+Contact\s+No\s*:"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not match:
            return None

        address = match.group(
            "address"
        )

        return self.clean_multiline(
            address
        )

    # ---------------------------------------------------------
    # VENDOR EMAILS
    # ---------------------------------------------------------

    def extract_vendor_emails(self, text):
        if not text:
            return []

        section_match = re.search(
            (
                r"Vendor\s+Email\s*:\s*"
                r"(?P<section>.*?)"
                r"SKU\s+Code"
            ),
            text,
            re.IGNORECASE | re.DOTALL
        )

        if not section_match:
            return []

        section = section_match.group(
            "section"
        )

        # Myntra PDF can split email addresses over lines:
        #
        # Manideep.
        # Yakkala@zyduswellness.com
        #
        # Join line breaks before extracting.
        normalized = re.sub(
            r"\s*\n\s*",
            "",
            section
        )

        normalized = normalized.replace(
            ",",
            " "
        )

        emails = re.findall(
            (
                r"[A-Z0-9._%+\-]+"
                r"@[A-Z0-9.\-]+"
                r"\.[A-Z]{2,}"
            ),
            normalized,
            re.IGNORECASE
        )

        result = []

        for email in emails:
            email = email.strip()

            if email and email not in result:
                result.append(email)

        return result

    # ---------------------------------------------------------
    # TOTALS
    # ---------------------------------------------------------

    def extract_amounts(self, text):
        total_quantity = self.first_match(
            text,
            [
                r"Total\s+Quantity\s*:\s*([\d,.]+)"
            ]
        )

        grand_total = self.first_match(
            text,
            [
                r"Grand\s+Total\s*:\s*([\d,.]+)"
            ]
        )

        total_quantity = self.number(
            total_quantity
        )

        grand_total = self.money(
            grand_total
        )

        # The PO gives final item totals including taxes.
        # We don't invent a PO-level basic/tax split unless
        # it is explicitly available in the document.
        return {
            "total_quantity": total_quantity,
            "basic": None,
            "igst": None,
            "tax": None,
            "total": grand_total
        }

    # ---------------------------------------------------------
    # ITEM EXTRACTION
    # ---------------------------------------------------------

    def extract_myntra_items(
        self,
        text,
        tables
    ):
        """
        Myntra's PDF text extraction is more reliable than the
        generic pdfplumber table for this format.

        Try text-based parsing first.

        Generic table extraction is only used as fallback.
        """

        items = self.extract_items_from_text(
            text
        )

        if items:
            return items

        return self.extract_items_from_tables(
            tables
        )

  
    # ---------------------------------------------------------
    # TEXT ITEM EXTRACTION
    # ---------------------------------------------------------

    def extract_items_from_text(self, text):
        """
        Extract Myntra item rows from PyMuPDF text.

        Myntra's PDF does not preserve the item as one physical row.
        One logical item is spread across multiple text lines.

        Example:

        EVNAFSER91925390 33049990
        Everyuth
        Naturals
        Hydrating &
        Exfoliating
        Walnut Apricot
        Scrub, 100g,
        Tube
        89015481436
        67
        NA
        100G
        28611896
        144
        210.00
        142.37 168.00
        18.00
        25.63 24192.00
        """

        items = []

        if not text:
            return items

        section = self.get_item_section(
            text
        )

        if not section:
            return items

        lines = []

        for line in section.splitlines():
            line = self.clean(line)

            if line:
                lines.append(line)

        if not lines:
            return items

        # Find each SKU + HSN line.
        item_starts = []

        for index, line in enumerate(lines):
            match = re.fullmatch(
                (
                    r"([A-Z0-9][A-Z0-9._\-]{5,})"
                    r"\s+"
                    r"(\d{6,8})"
                ),
                line,
                re.IGNORECASE
            )

            if not match:
                continue

            sku_code = self.clean(
                match.group(1)
            )

            hsn_code = self.clean(
                match.group(2)
            )

            item_starts.append({
                "index": index,
                "sku_code": sku_code,
                "hsn_code": hsn_code
            })

        if not item_starts:
            return items

        for item_index, item_start in enumerate(
            item_starts
        ):
            start_index = (
                item_start["index"] + 1
            )

            if item_index + 1 < len(item_starts):
                end_index = item_starts[
                    item_index + 1
                ]["index"]

            else:
                end_index = len(lines)

            block_lines = lines[
                start_index:end_index
            ]

            item = self.parse_myntra_item_lines(
                sku_code=item_start[
                    "sku_code"
                ],
                hsn_code=item_start[
                    "hsn_code"
                ],
                lines=block_lines,
                line_no=item_index + 1
            )

            if item:
                items.append(item)

        return items

    # ---------------------------------------------------------
    # GET ITEM SECTION
    # ---------------------------------------------------------

    def get_item_section(self, text):
        if not text:
            return None

        # Start from actual table heading.
        start_match = re.search(
            (
                r"SKU\s+Code\s*"
                r"\n\s*"
                r"HSN\s+Code"
            ),
            text,
            re.IGNORECASE
        )

        if not start_match:
            start_match = re.search(
                r"SKU\s+Code.*?HSN\s+Code",
                text,
                re.IGNORECASE | re.DOTALL
            )

        if not start_match:
            return None

        section = text[
            start_match.end():
        ]

        # Remove the table heading area.
        #
        # The final heading immediately before data is:
        #
        # Total
        # (plus
        # Taxes)

        taxes_match = re.search(
            (
                r"Total\s*"
                r"\n\s*"
                r"\(plus\s*"
                r"\n\s*"
                r"Taxes\)"
            ),
            section,
            re.IGNORECASE
        )

        if taxes_match:
            section = section[
                taxes_match.end():
            ]

        # Stop before totals.
        end_match = re.search(
            r"Total\s+Quantity\s*:",
            section,
            re.IGNORECASE
        )

        if end_match:
            section = section[
                :end_match.start()
            ]

        return section.strip()

    # ---------------------------------------------------------
    # PARSE ONE MYNTRA ITEM
    # ---------------------------------------------------------

    def parse_myntra_item_lines(
        self,
        sku_code,
        hsn_code,
        lines,
        line_no
    ):
        if not lines:
            return None

        lines = [
            self.clean(line)
            for line in lines
            if self.clean(line)
        ]

        if not lines:
            return None

        # -----------------------------------------------------
        # Find vendor article number.
        #
        # In supplied PO it is split:
        #
        # 89015481436
        # 67
        #
        # Combined:
        # 8901548143667
        # -----------------------------------------------------

        article_start = None
        article_parts = []

        for index, line in enumerate(lines):
            if not re.fullmatch(
                r"\d+",
                line
            ):
                continue

            # We expect a long first component.
            if len(line) < 8:
                continue

            article_start = index
            article_parts.append(line)

            next_index = index + 1

            if next_index < len(lines):
                next_line = lines[
                    next_index
                ]

                # Myntra PDF can split the article/EAN
                # number into a short continuation line.
                if (
                    re.fullmatch(
                        r"\d{1,4}",
                        next_line
                    )
                    and len(
                        line + next_line
                    ) <= 14
                ):
                    article_parts.append(
                        next_line
                    )

            break

        if article_start is None:
            return None

        vendor_article_number = "".join(
            article_parts
        )

        # Everything before article number is description.
        description_lines = lines[
            :article_start
        ]

        description = self.clean(
            " ".join(
                description_lines
            )
        )

        # Move after article number components.
        data_start = (
            article_start
            + len(article_parts)
        )

        data_lines = lines[
            data_start:
        ]

        if not data_lines:
            return None

        # -----------------------------------------------------
        # Expected supplied Myntra sequence:
        #
        # NA
        # 100G
        # 28611896
        # 144
        # 210.00
        # 142.37 168.00
        # 18.00
        # 25.63 24192.00
        #
        # Columns:
        #
        # Color
        # Size
        # Style ID
        # Qty
        # BIS Certificate Number
        # MRP
        # List Price
        # Landed Price
        # IGST Tax Percent
        # IGST Amount
        # Total (plus Taxes)
        #
        # BIS is blank in this PO.
        # -----------------------------------------------------

        color = None
        size = None
        style_id = None
        quantity = None
        bis_certificate_number = None
        mrp = None
        list_price = None
        landed_price = None
        igst_percent = None
        igst_amount = None
        total_amount = None

        if len(data_lines) >= 4:
            color = self.clean(
                data_lines[0]
            )

            size = self.clean(
                data_lines[1]
            )

            style_id = self.clean(
                data_lines[2]
            )

            quantity = self.number(
                data_lines[3]
            )

        # Remaining values after Color/Size/Style/Qty.
        price_lines = data_lines[4:]

        price_values = []

        for line in price_lines:
            matches = re.findall(
                r"-?[\d,]+(?:\.\d+)?",
                line
            )

            for match in matches:
                parsed = self.number(
                    match
                )

                if parsed is not None:
                    price_values.append(
                        parsed
                    )

        # For supplied PO:
        #
        # [
        #   210.00,
        #   142.37,
        #   168.00,
        #   18.00,
        #   25.63,
        #   24192.00
        # ]

        if len(price_values) >= 6:
            mrp = price_values[0]
            list_price = price_values[1]
            landed_price = price_values[2]
            igst_percent = price_values[3]
            igst_amount = price_values[4]
            total_amount = price_values[5]

        elif len(price_values) >= 5:
            # Conservative fallback if one price field
            # is absent in another Myntra PO.
            mrp = price_values[0]
            landed_price = price_values[1]
            igst_percent = price_values[-3]
            igst_amount = price_values[-2]
            total_amount = price_values[-1]

        # -----------------------------------------------------
        # VALIDATION
        # -----------------------------------------------------

        if not sku_code:
            return None

        if not hsn_code:
            return None

        if not description:
            return None

        # -----------------------------------------------------
        # RESULT
        # -----------------------------------------------------

        return {
            "line_no": line_no,

            "item_code": sku_code,
            "sku_code": sku_code,

            "hsn_code": hsn_code,

            "description": description,

            "vendor_item_code": (
                vendor_article_number
            ),

            "vendor_article_number": (
                vendor_article_number
            ),

            # The supplied Myntra PO does not label this
            # explicitly as EAN in the table.
            "ean": None,

            "color": color,
            "size": size,
            "style_id": style_id,

            "quantity": quantity,

            "bis_certificate_number": (
                bis_certificate_number
            ),

            "uom": None,

            "mrp": mrp,

            "list_price": list_price,

            "landed_price": landed_price,

            # Common normalized cost field.
            "unit_cost": landed_price,

            "cgst_percent": None,
            "sgst_percent": None,

            "igst_percent": (
                igst_percent
            ),

            "cess_percent": None,

            "cgst_amount": None,
            "sgst_amount": None,

            "igst_amount": (
                igst_amount
            ),

            "cess_amount": None,

            "total_amount": (
                total_amount
            ),

            "raw_row": lines
        }

    # ---------------------------------------------------------
    # NUMERIC VALUE LIST
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