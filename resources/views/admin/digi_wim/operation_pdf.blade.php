<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #d9edf7; }
    </style>
</head>
<body>

<h3>Unloading Report</h3>

<table>
    <tr>
        <th>Invoice No</th>
        <td>{{ $header->invoice_challan_no }}</td>
        <th>Invoice Date</th>
        <td>{{ $header->invoice_date }}</td>
    </tr>
    <tr>
        <th>PO No</th>
        <td>{{ $header->po_order_no }}</td>
        <th>PO Date</th>
        <td>{{ $header->po_order_date }}</td>
    </tr>
    <tr>
        <th>Supplier</th>
        <td>{{ $header->supplier_code_name }}</td>
        <th>Transporter</th>
        <td>{{ $header->transporter_name }}</td>
    </tr>
    <tr>
        <th>Truck No</th>
        <td>{{ $header->truck_number }}</td>
        <th>Truck Type</th>
        <td>{{ $header->truck_type }}</td>
    </tr>
    <tr>
        <th>LR No</th>
        <td>{{ $header->lr_no }}</td>
        <th>UOM</th>
        <td>{{ $header->uom }}</td>
    </tr>
	<tr>
		<th>Created By</th>
		<td>{{ $header->creator->name ?? '' }}</td>
		<th>&nbsp;</th>
		<td>&nbsp;</td>
	</tr>
</table>

<table>
    <thead>
        <tr>
            <th>Material Code</th>
            <th>Description</th>
            <th>Batch</th>
            <th>MFG Date</th>
            <th>Expiry Date</th>
            <th>Qty</th>
            <th>BIN No</th>
            <th>Goods Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($header->items as $item)
        <tr>
            <td>{{ $item->material_code }}</td>
            <td>{{ $item->material_description }}</td>
            <td>{{ $item->batch_no }}</td>
            <td>{{ $item->mfg_date }}</td>
            <td>{{ $item->expiry_date }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ $item->bin_no }}</td>
            <td>{{ $item->goods_status }}</td>
            <td>{{ $item->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>