@extends('admin.admin')
@section('bodycontent')
<style>
.freight-table-wrap{width:100%;max-height:570px;overflow:auto;border:1px solid #dee2e6}
.freight-table{width:max-content;min-width:100%;font-size:12px;margin-bottom:0}
.freight-table th,.freight-table td{padding:8px 10px;white-space:nowrap;vertical-align:middle}
.freight-table thead th{position:sticky;top:0;z-index:5;background:#fce4d6;color:#0070c0}
.freight-table .remark-col{white-space:normal;overflow-wrap:anywhere}
.freight-toolbar{padding:15px;background:#f8f9fa;border:1px solid #dee2e6}
.freight-footer{padding:15px;border:1px solid #dee2e6;border-top:0}
.freight-footer .pagination{margin-bottom:0}
.freight-tabs .nav-link{margin-right:5px}
.freight-toolbar {
    padding: 8px 12px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    margin-bottom: 10px !important;
}
.freight-toolbar .row {
    margin-left: -4px;
    margin-right: -4px;
    align-items: end;
}
.freight-toolbar .row > div {
    padding-left: 4px;
    padding-right: 4px;
    margin-bottom: 0 !important;
}
.freight-toolbar label {
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 2px;
}
.freight-toolbar .form-control {
    height: 30px;
    font-size: 12px;
    padding: 3px 6px;
}
.freight-toolbar .btn {
    height: 30px;
    font-size: 12px;
    padding: 4px 9px;
}
.freight-table th.sortable a {
    color: #0070c0;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.freight-table th.sortable a:hover {
    color: #004080;
}
.freight-table th.sortable .sort-icon {
    font-size: 10px;
    color: #6c757d;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Approved Freight Bills</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Approved Freight Bills</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills freight-tabs">
                    <li class="nav-item">
                        <a href="{{ route('admin.freight.approve.reject') }}" class="nav-link">
                            Pending Approval
                            <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.freight.approved') }}" class="nav-link active">
                            Approved Bills
                            <span class="badge badge-success ml-1">{{ $approvedCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ url()->current() }}" class="freight-toolbar">
					<div class="row">
						<div class="col-xl-2 col-md-4">
							<label>Search</label>
							<input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search all">
						</div>
						<div class="col-xl-2 col-md-4">
							<label>Vendor</label>
							<input type="text" name="vendor_name" class="form-control form-control-sm" value="{{ request('vendor_name') }}" placeholder="Vendor name">
						</div>
						<div class="col-xl-2 col-md-4">
							<label>LR/CN No.</label>
							<input type="text" name="lr_no" class="form-control form-control-sm" value="{{ request('lr_no') }}" placeholder="LR/CN">
						</div>
						<div class="col-xl-2 col-md-4">
							<label>Invoice No.</label>
							<input type="text" name="invoice_no" class="form-control form-control-sm" value="{{ request('invoice_no') }}" placeholder="Invoice">
						</div>
						<div class="col-xl-1 col-md-3">
							<label>From</label>
							<input type="date" name="date_from" class="form-control form-control-sm" min="2026-10-05" value="{{ request('date_from') }}">
						</div>
						<div class="col-xl-1 col-md-3">
							<label>To</label>
							<input type="date" name="date_to" class="form-control form-control-sm" min="2026-10-05" value="{{ request('date_to') }}">
						</div>
						<div class="col-xl-1 col-md-3">
							<label>Rows</label>
							<select name="per_page" class="form-control form-control-sm">
								@foreach([10,25,50,100,200] as $size)
									<option value="{{ $size }}" {{ (int) request('per_page',25) === $size ? 'selected' : '' }}>{{ $size }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xl-1 col-md-3 d-flex align-items-end">
							<button type="submit" class="btn btn-primary btn-sm mr-1" title="Apply Filters">
								<i class="fas fa-search"></i>
							</button>
							<a href="{{ url()->current() }}" class="btn btn-secondary btn-sm" title="Reset Filters">
								<i class="fas fa-sync-alt"></i>
							</a>
						</div>
					</div>
					<input type="hidden" name="sort_by" value="{{ request('sort_by','created_at') }}">
					<input type="hidden" name="sort_order" value="{{ request('sort_order','desc') }}">
				</form>

                <div class="freight-table-wrap">
				@php
					$currentSort = request('sort_by', 'created_at');
					$currentOrder = request('sort_order', 'desc');

					$sortUrl = function ($column) use ($currentSort, $currentOrder) {
						$nextOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';

						return url()->current().'?'.http_build_query(array_merge(
							request()->except(['page', 'sort_by', 'sort_order']),
							['sort_by' => $column, 'sort_order' => $nextOrder]
						));
					};
				@endphp
                    <table class="table table-bordered table-hover freight-table">
                        <thead>
							<tr>
								<th class="sortable"><a href="{{ $sortUrl('consignor') }}">S5 Consignor <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('destination') }}">D5 Consignor <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('ref1') }}">Order Ref No. <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('vendor_name') }}">Vendor Name <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('lr_no') }}">LR/CN No. <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('lr_date') }}">LR/CN Date <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('truck_type') }}">Truck Type <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('ref2') }}">Freight PO <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('invoice_no') }}">Invoice No. <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('invoice_date') }}">Invoice Date <i class="fas fa-sort sort-icon"></i></a></th>
								<th class="sortable"><a href="{{ $sortUrl('amount') }}">Amount <i class="fas fa-sort sort-icon"></i></a></th>
								<th>Custom 5</th>
								<th>Freight Invoice</th>
								<th>POD</th>
								<th>Approvals</th>
								<th>Status</th>
								<th class="remark-col">Approval Remark</th>
								<th>Approved At</th>
							</tr>
						</thead>
                        <tbody>
                            @forelse($approvedentries as $billdata)
                                <tr>
                                    <td>{{ $billdata->s5_consignor_short_name_and_location }}</td>
                                    <td>{{ $billdata->d5_consignor_short_name_and_location }}</td>
                                    <td>{{ $billdata->ref1 }}</td>
                                    <td>{{ $billdata->vendor_name }}</td>
                                    <td>{{ $billdata->lr_no }}</td>
                                    <td>{{ $billdata->lr_cn_date }}</td>
                                    <td>{{ $billdata->truck_type }}</td>
                                    <td>{{ $billdata->ref2 }}</td>
                                    <td>{{ $billdata->freight_invoice_no }}</td>
                                    <td>{{ $billdata->freight_invoice_date }}</td>
                                    <td>{{ is_numeric($billdata->freight_amount) ? number_format($billdata->freight_amount,2) : $billdata->freight_amount }}</td>
                                    <td>{{ $billdata->rate_custom5 ?? 'NA' }}</td>
                                    <td>
                                        @if($billdata->freight_invoice_file)
                                            <a href="{{ asset($billdata->freight_invoice_file) }}" target="_blank">View Invoice</a>
                                        @else
                                            NA
                                        @endif
                                    </td>
                                    <td>
                                        @if($billdata->pod_file)
                                            <a href="{{ asset($billdata->pod_file) }}" target="_blank">View POD</a>
                                        @else
                                            NA
                                        @endif
                                    </td>
                                    <td>
                                        @if($billdata->approval_file)
                                            <a href="{{ asset($billdata->approval_file) }}" target="_blank">View Approval</a>
                                        @else
                                            NA
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Approved</span>
                                    </td>
                                    <td class="remark-col">{{ $billdata->approval_remark ?? '--' }}</td>
                                    <td>{{ $billdata->approved_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="18" class="text-center">No approved freight bills found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="freight-footer">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-2">
                            Showing {{ $approvedentries->firstItem() ?? 0 }} to {{ $approvedentries->lastItem() ?? 0 }} of {{ $approvedentries->total() }} approved records
                        </div>
                        <div class="col-md-7 d-flex justify-content-md-end">
                            {{ $approvedentries->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection