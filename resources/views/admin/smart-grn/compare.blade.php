@extends('admin.admin')

@section('bodycontent')
<div class="content-header"><div class="container-fluid"><h1>PO vs GRN Comparison</h1></div></div>
<div class="content"><div class="container-fluid">
    <div class="card"><div class="card-body">
        <p><strong>GRN:</strong> {{ $data['grn']->grn_number ?: '-' }} |
           <strong>Referenced PO:</strong> {{ $data['grn']->po_number ?: '-' }} |
           <strong>Matched PO:</strong> {{ $data['po']->po_no ?? 'Not found' }}</p>
        @if($data['message']) <div class="alert alert-info">{{ $data['message'] }}</div> @endif
        <a href="{{ route('admin.smart-grn.compare.excel', $data['grn']) }}" class="btn btn-success btn-sm">Download CSV (Excel)</a>
        <a href="{{ route('admin.smart-grn.compare.pdf', $data['grn']) }}" class="btn btn-danger btn-sm">Download PDF</a>
        <a href="{{ route('admin.smart-grn.show', $data['grn']) }}" class="btn btn-secondary btn-sm">Back to GRN</a>
    </div></div>
    <div class="card"><div class="card-body table-responsive">
        <table class="table table-bordered table-sm">
            <thead><tr><th>Item Code</th><th>EAN</th><th>Description</th><th>PO Qty</th><th>GRN Received</th><th>GRN Accepted</th><th>Difference</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($data['rows'] as $row)
                    <tr>
                        <td>{{ $row['item_code'] ?: '-' }}</td><td>{{ $row['ean'] ?: '-' }}</td>
                        <td>{{ $row['description'] ?: '-' }}</td>
                        <td>{{ $row['po_qty'] ?? '-' }}</td><td>{{ $row['received_qty'] ?? '-' }}</td>
                        <td>{{ $row['accepted_qty'] ?? '-' }}</td><td>{{ $row['difference'] ?? '-' }}</td>
                        <td>{{ $row['status'] }}</td>
                    </tr>
                @empty <tr><td colspan="8" class="text-center">No comparison rows available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
</div></div>
@endsection
