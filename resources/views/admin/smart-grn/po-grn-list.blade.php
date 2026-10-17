@extends('admin.admin')

@section('bodycontent')
<div class="content-header px-0 pt-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="m-0">PO vs GRN Reconciliation</h1>
        <div class="mb-2">
            <a href="{{ route('admin.smart-po.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-file-invoice"></i> Purchase Orders
            </a>
            <a href="{{ route('admin.smart-grn.upload') }}" class="btn btn-primary">
                <i class="fas fa-cloud-upload-alt"></i> Upload GRN
            </a>
        </div>
    </div>
</div>

<div class="card card-primary">
    <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> Purchase Orders and Matching GRNs</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.smart-grn.reconciliation.index') }}" class="form-row mb-3">
            <div class="col-md-5 mb-2">
                <input type="text" name="search" class="form-control" value="{{ $search }}"
                       placeholder="Search PO number, GRN number or supplier">
            </div>
            <div class="col-md-3 mb-2">
                <select name="status" class="form-control">
                    <option value="">All purchase orders</option>
                    <option value="with_grn" {{ $status === 'with_grn' ? 'selected' : '' }}>With GRN</option>
                    <option value="without_grn" {{ $status === 'without_grn' ? 'selected' : '' }}>Without GRN</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="per_page" class="form-control">
                    @foreach ([10, 20, 50, 100] as $count)
                        <option value="{{ $count }}" {{ $perPage === $count ? 'selected' : '' }}>{{ $count }} / page</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                <a href="{{ route('admin.smart-grn.reconciliation.index') }}" class="btn btn-default">Reset</a>
            </div>
        </form>

        <p class="text-muted small">Summary only: PO/GRN dates, suppliers, item counts, matched and variance counts. GRN item counts and reconciliation use the latest parsed GRN; the GRNs column shows the number of linked parsed GRNs.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>PO Number</th>
                        <th>PO Date</th>
                        <th>PO Supplier</th>
                        <th>PO Items</th>
                        <th>GRN Number</th>
                        <th>GRN Date</th>
                        <th>GRN Supplier</th>
                        <th>GRN Items</th>
                        <th>Matched</th>
                        <th>Variance</th>
                        <th>GRNs</th>
                        <th>Status</th>
                        <th style="min-width:170px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td><strong>{{ $row->po_no ?: '--' }}</strong></td>
                            <td>{{ $row->po_date ? \Illuminate\Support\Carbon::parse($row->po_date)->format('d-m-Y') : '--' }}</td>
                            <td>{{ $row->vendor_name ?: '--' }}</td>
                            <td>{{ $row->items_count }}</td>
                            <td>{{ $row->grn_number ?: '--' }}</td>
                            <td>{{ $row->grn_date ? \Illuminate\Support\Carbon::parse($row->grn_date)->format('d-m-Y') : '--' }}</td>
                            <td>{{ $row->grn_supplier ?: '--' }}</td>
                            <td>{{ $row->grn_items_count ?? '--' }}</td>
                            <td>{{ $row->grn_id ? $row->matched_count : '--' }}</td>
                            <td>{{ $row->variance_count ?? '--' }}</td>
                            <td>{{ $row->grn_count ?: '--' }}</td>
                            <td>
                                @if (! $row->grn_id)
                                    <span class="badge badge-warning">GRN Pending</span>
                                @elseif ($row->reconciliation_status === 'Matched')
                                    <span class="badge badge-success">Matched</span>
                                @else
                                    <span class="badge badge-danger">Variance</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.smart-po.show', ['id' => $row->id]) }}"
                                   class="btn btn-sm btn-outline-primary mb-1">View PO</a>
                                @if ($row->grn_id)
                                    <a href="{{ route('admin.smart-grn.compare', ['grn' => $row->grn_id]) }}"
                                       class="btn btn-sm btn-info mb-1">Compare</a>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="14" class="text-center text-muted py-4">No purchase orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $rows->links() }}</div>
    </div>
</div>

<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">GRNs Without a Matching Purchase Order</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">These GRNs have no matching PO in the current admin account. PO details are shown as --.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-light">
                    <tr><th>PO Number</th><th>PO Date</th><th>PO Items</th><th>GRN Number</th><th>GRN Date</th><th>GRN Supplier</th><th>GRN Items</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($unmatchedGrns as $grn)
                        <tr>
                            <td>{{ $grn->po_number ?: '--' }}</td>
                            <td>--</td>
                            <td>--</td>
                            <td>{{ $grn->grn_number ?: '--' }}</td>
                            <td>{{ $grn->grn_date ? $grn->grn_date->format('d-m-Y') : '--' }}</td>
                            <td>{{ $grn->supplier_name ?: '--' }}</td>
                            <td>{{ $grn->line_count }}</td>
                            <td><span class="badge badge-warning">PO Missing</span></td>
                            <td><a class="btn btn-sm btn-primary" href="{{ route('admin.smart-grn.show', ['grn' => $grn->id]) }}">View GRN</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-3">No unmatched GRNs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $unmatchedGrns->links() }}</div>
    </div>
</div>
@endsection
