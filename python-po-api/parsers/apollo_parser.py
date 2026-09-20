from .base_parser import BasePOParser


class ApolloParser(BasePOParser):
    client_code = "apollo"
    client_name = "Apollo"

    def parse(self, text, tables):
        po_no = self.first_match(
            text,
            [
                r"PO No\.?\s*:\s*(PO-[A-Z0-9\/\-]+)",
                r"PO Number\s*:\s*(PO-[A-Z0-9\/\-]+)"
            ]
        )

        po_date = self.first_match(
            text,
            [
                r"PO Date\s*:\s*([0-9A-Za-z.\-/ ]+)"
            ]
        )

        expiry_date = self.first_match(
            text,
            [
                r"PO Exp Date\s*:\s*([0-9A-Za-z.\-/ ]+)",
                r"PO Expiry Date\s*:\s*([0-9A-Za-z.\-/ ]+)"
            ]
        )

        vendor_code = self.first_match(
            text,
            [
                r"VEND-([A-Z0-9\-]+)",
                r"Vendor Code\s*:\s*([A-Z0-9\/\-]+)"
            ]
        )

        total_amount = self.first_match(
            text,
            [
                r"Total\s*:\s*\d+\s*([\d,]+\.\d{2})",
                r"Grand Total\s*:\s*(?:INR)?\s*([\d,]+\.\d{2})"
            ]
        )

        items = self.extract_items_from_tables(
            tables
        )

        return self.build_result(
            po_no=po_no,
            po_date=po_date,
            expiry_date=expiry_date,
            vendor_code=vendor_code,
            total_amount=total_amount,
            items=items
        )