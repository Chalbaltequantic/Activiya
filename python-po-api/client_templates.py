CLIENT_TEMPLATES = {

    "metro": {
        "client_name": "Metro Cash And Carry India Limited",

        "detect": [
            "METRO CASH AND CARRY",
            "SELLER PURCHASE ORDER"
        ],

        "po_patterns": [
            r"PO\s*NO\.?\s*:\s*([A-Z0-9\-\/]+)",
            r"PURCHASE ORDER Number\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"PO\s*Date\s*:\s*([0-9.\-/]+)"
        ],

        "delivery_date_patterns": [
            r"DELIVERY DATE\s*:\s*([0-9.\-/]+)"
        ],

        "vendor_code_patterns": [
            r"Vendor Code\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "total_patterns": [
            r"Total Order Value\s*:\s*(?:INR)?\s*([\d,]+\.\d{2})"
        ]
    },

    "myntra": {
        "client_name": "Myntra Jabong India Pvt Ltd",

        "detect": [
            "MYNTRA JABONG INDIA",
            "MJIPL"
        ],

        "po_patterns": [
            r"PO\s*#\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"PO Approved Date\s*:\s*([0-9.\-/]+)"
        ],

        "delivery_date_patterns": [
            r"Estimated Shipment Date\s*:\s*([0-9.\-/]+)"
        ],

        "vendor_code_patterns": [],

        "total_patterns": [
            r"Grand Total\s*:\s*([\d,]+\.\d{2})"
        ]
    },

    "citymall": {
        "client_name": "CMUNITY INNOVATIONS PRIVATE LIMITED",

        "detect": [
            "CMUNITY INNOVATIONS PRIVATE LIMITED",
            "CITYMALL"
        ],

        "po_patterns": [
            r"Purchase Order\s+(PO-[A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"Purchase Order Date\s*([0-9.\-/]+)"
        ],

        "expiry_patterns": [
            r"Purchase Order Expiry Date\s*([0-9.\-/]+)"
        ],

        "vendor_code_patterns": [
            r"Vendor Code\s*([A-Z0-9\-\/]+)"
        ],

        "total_patterns": [
            r"Net Amount\s*([\d,]+\.\d{2})"
        ]
    },

    "walmart": {
        "client_name": "Wal-Mart India Pvt. Ltd.",

        "detect": [
            "WAL-MART INDIA",
            "WALMART INDIA",
            "AMENDED PURCHASE ORDER"
        ],

        "po_patterns": [
            r"PURCHASE ORDER NO\.?\s*:\s*([A-Z0-9\-\/]+)",
            r"PO No\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"ORDER DATE\s*:\s*([0-9.\-/]+)",
            r"PO Order Dt\s*:\s*([0-9.\-/]+)"
        ],

        "expiry_patterns": [
            r"PO CANCEL DATE\s*:\s*([0-9.\-/]+)"
        ],

        "vendor_code_patterns": [
            r"Supplier No\.?\s*:\s*([A-Z0-9\-\/]+)",
            r"Vendor No\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "total_patterns": [
            r"Total PO AMOUNT including Taxes\s*\(INR\)\s*([\d,]+\.\d{2})"
        ]
    },

    "apollo": {
        "client_name": "APOLLO HEALTHCO LIMITED",

        "detect": [
            "APOLLO HEALTHCO LIMITED"
        ],

        "po_patterns": [
            r"PO No\.?\s*:\s*(PO-[A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"PO Date\s*:\s*([0-9A-Za-z.\-/]+)"
        ],

        "expiry_patterns": [
            r"PO Exp Date\s*:\s*([0-9A-Za-z.\-/]+)"
        ],

        "vendor_code_patterns": [
            r"VEND-([A-Z0-9\-]+)"
        ],

        "total_patterns": [
            r"Total\s*:\s*\d+\s*([\d,]+\.\d{2})"
        ]
    },

    "dealshare": {
        "client_name": "MERABO LABS PRIVATE LIMITED",

        "detect": [
            "MERABO LABS PRIVATE LIMITED",
            "DASN A(HUB)",
            "DASNA(HUB)"
        ],

        "po_patterns": [
            r"PO Number\s*[\r\n\s]*([A-Z0-9\-\/]+)"
        ],

        "date_patterns": [
            r"PO Created Date\s*[\r\n\s]*([0-9.\-/]+)"
        ],

        "delivery_date_patterns": [
            r"PO Delivery Date\s*[\r\n\s]*([0-9.\-/]+)"
        ],

        "expiry_patterns": [
            r"PO Expiry Date\s*[\r\n\s]*([0-9.\-/]+)"
        ],

        "vendor_code_patterns": [
            r"Vendor Code\s*:\s*([A-Z0-9\-\/]+)"
        ],

        "total_patterns": [
            r"Total SKU\s*:\s*\d+\s+\d+\s+([\d,]+\.\d{2})"
        ]
    }
}