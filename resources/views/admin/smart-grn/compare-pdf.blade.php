<!doctype html>
<html><head><meta charset="utf-8"><style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #aaa; padding: 5px; }
th { background: #eee; }
</style></head><body>
<h2>PO vs GRN Comparison</h2>
<p>GRN: {{ $data['grn']->grn_number ?: '-' }} | PO: {{ $data['grn']->po_number ?: '-' }}</p>
<p>{{ $data['message'] }}</p>
<table><thead><tr><th>Code</th><th>EAN</th><th>Description</th><th>PO Qty</th><th>Received</th><th>Accepted</th><th>Difference</th><th>Status</th></tr></thead><tbody>
@foreach($data['rows'] as $row)
<tr><td>{{ $row['item_code'] }}</td><td>{{ $row['ean'] }}</td><td>{{ $row['description'] }}</td><td>{{ $row['po_qty'] }}</td><td>{{ $row['received_qty'] }}</td><td>{{ $row['accepted_qty'] }}</td><td>{{ $row['difference'] }}</td><td>{{ $row['status'] }}</td></tr>
@endforeach
</tbody></table></body></html>
