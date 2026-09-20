from .base_parser import BasePOParser


class DealShareParser(BasePOParser):
    client_code = "dealshare"
    client_name = "DealShare"

    def parse(self, text, tables):
        po_no = self.first_match(
            text,
            [
                r"PO Number\s*[\r\n\s:]*([A-Z0-9\/\-]+)",
                r"PO No\.?\s*[\r\n\s:]*([A-Z0-9\/\-]+)"
            ]
        )

        po_date = self.first_match(
            text,
            [
                r"PO Created Date\s*[\r\n\s:]*([0-9.\-/]+)",
                r"PO Date\s*[\r\n\s:]*([0-9.\-/]+)"
            ]
        )

        delivery_date = self.first_match(
            text,
            [
                r"PO Delivery Date\s*[\r\n\s:]*([0-9.\-/]+)",
                r"Delivery Date\s*[\r\n\s:]*([0-9.\-/]+)"
            ]
        )

        expiry_date = self.first_match(
            text,
            [
                r"PO Expiry Date\s*[\r\n\s:]*([0-9.\-/]+)",
                r"Expiry Date\s*[\r\n\s:]*([0-9.\-/]+)"
            ]
        )

        vendor_code = self.first_match(
            text,
            [
                r"Vendor Code\s*:\s*([A-Z0-9\/\-]+)"
            ]
        )

        total_amount = self.first_match(
            text,
            [
                r"Total SKU\s*:\s*\d+\s+\d+\s+([\d,]+\.\d{2})",
                r"Grand Total\s*:\s*(?:INR)?\s*([\d,]+\.\d{2})",
                r"Total Amount\s*:\s*(?:INR)?\s*([\d,]+\.\d{2})"
            ]
        )

        items = self.extract_items_from_tables(
            tables
        )

        return self.build_result(
            po_no=po_no,
            po_date=po_date,
            delivery_date=delivery_date,
            expiry_date=expiry_date,
            vendor_code=vendor_code,
            total_amount=total_amount,
            items=items
        )