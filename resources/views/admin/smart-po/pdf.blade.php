<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            margin: 0 0 15px 0;
            font-size: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .summary td {
            padding: 5px;
            vertical-align: top;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items th,
        .items td {
            border: 1px solid #bbb;
            padding: 5px;
        }

        .items th {
            background: #eee;
        }

        .right {
            text-align: right;
        }

        .totals {
            width: 40%;
            margin-left: auto;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>

<h1>
    Purchase Order {{ $purchaseOrder->po_no ?: '#' . $purchaseOrder->id }}
</h1>

<table class="summary">
    <tr>
        <td>
            <strong>PO Company:</strong><br>
            {{ optional($purchaseOrder->template)->name ?? 'Unknown' }}
        </td>

        <td>
            <strong>PO Date:</strong><br>
            {{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d-m-Y') : '-' }}
        </td>

        <td>
            <strong>Delivery Date:</strong><br>
            {{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d-m-Y') : '-' }}
        </td>

        <td>
            <strong>Expiry Date:</strong><br>
            {{ $purchaseOrder->expiry_date ? $purchaseOrder->expiry_date->format('d-m-Y') : '-' }}
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <strong>Vendor:</strong><br>
            {{ $purchaseOrder->vendor_name ?: '-' }}<br>
            {{ $purchaseOrder->vendor_gstin ?: '' }}
        </td>

        <td colspan="2">
            <strong>Buyer:</strong><br>
            {{ $purchaseOrder->buyer_name ?: '-' }}<br>
            {{ $purchaseOrder->buyer_gstin ?: '' }}
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th>#</th>
            <th>Item Code</th>
            <th>Description</th>
            <th>HSN</th>
            <th>EAN</th>
            <th>Qty</th>
            <th>UOM</th>
            <th>MRP</th>
            <th>Unit Cost</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>IGST</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($purchaseOrder->items as $item)
            <tr>
                <td>{{ $item->line_no ?: $loop->iteration }}</td>
                <td>{{ $item->item_code ?: '-' }}</td>
                <td>{{ $item->description ?: '-' }}</td>
                <td>{{ $item->hsn_code ?: '-' }}</td>
                <td>{{ $item->ean ?: '-' }}</td>
                <td class="right">{{ $item->quantity ?? '-' }}</td>
                <td>{{ $item->uom ?: '-' }}</td>
                <td class="right">{{ $item->mrp !== null ? number_format((float) $item->mrp, 2) : '-' }}</td>
                <td class="right">{{ $item->unit_cost !== null ? number_format((float) $item->unit_cost, 2) : '-' }}</td>
                <td class="right">{{ $item->cgst_percent !== null ? $item->cgst_percent . '%' : '-' }}</td>
                <td class="right">{{ $item->sgst_percent !== null ? $item->sgst_percent . '%' : '-' }}</td>
                <td class="right">{{ $item->igst_percent !== null ? $item->igst_percent . '%' : '-' }}</td>
                <td class="right">{{ $item->total_amount !== null ? number_format((float) $item->total_amount, 2) : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Basic Amount</td>
        <td class="right">
            {{ number_format((float) ($purchaseOrder->basic_amount ?? 0), 2) }}
        </td>
    </tr>

    <tr>
        <td>Tax Amount</td>
        <td class="right">
            {{ number_format((float) ($purchaseOrder->tax_amount ?? 0), 2) }}
        </td>
    </tr>

    <tr>
        <td><strong>Total Amount</strong></td>
        <td class="right">
            <strong>
                {{ number_format((float) ($purchaseOrder->total_amount ?? 0), 2) }}
            </strong>
        </td>
    </tr>
</table>

</body>
</html>