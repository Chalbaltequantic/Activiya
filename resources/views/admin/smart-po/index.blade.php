@extends('admin.admin')
@section('bodycontent')

<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}

.po-data-table table {
    min-width: 1250px;
    font-size: 12px;
}

.po-data-table th,
.po-data-table td {
    white-space: nowrap;
    vertical-align: middle;
}

.po-data-table thead th {
    position: sticky;
    top: 0;
    background: #fce4d6;
    color: #0070c0;
    z-index: 2;
}

.po-data-table .table th,
.po-data-table .table td {
    padding: 7px 10px;
}

.po-table-container {
    max-height: 520px;
    overflow-y: auto;
    border: 1px solid #ccc;
}

.po-number {
    font-weight: 600;
    color: #0070c0;
}

.po-actions {
    min-width: 190px;
}

.search-card label {
    font-size: 12px;
    margin-bottom: 4px;
}

.search-card .form-control {
    font-size: 13px;
}
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">Purchase Order Data</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        <a class="btn btn-primary"
                           href="{{ route('admin.smart-po.upload') }}">
                            Upload Purchase Order
                        </a>
                    </li>

                </ol>
            </div>

        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ session('success') }}</strong>

                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <strong>{{ session('error') }}</strong>

                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>

        <!-- Search -->
        <div class="row">
            <div class="col-lg-12">

                <div class="card card-outline card-primary search-card">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-1"></i>
                            Search Purchase Orders
                        </h3>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('admin.smart-po.index') }}"
                              method="get">

                            <div class="row">

                                <div class="form-group col-md-2">
                                    <label>PO Number</label>

                                    <input type="text"
                                           name="po_no"
                                           value="{{ request('po_no') }}"
                                           class="form-control"
                                           placeholder="PO Number">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>PO Company</label>

                                    <select name="template"
                                            class="form-control">

                                        <option value="">
                                            All Companies
                                        </option>

                                        @foreach($templates as $template)
                                            <option value="{{ $template->code }}" {{ request('template') == $template->code ? 'selected' : '' }}>
                                                {{ $template->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Vendor</label>

                                    <input type="text"
                                           name="vendor"
                                           value="{{ request('vendor') }}"
                                           class="form-control"
                                           placeholder="Vendor Name">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>PO Date From</label>

                                    <input type="date"
                                           name="date_from"
                                           value="{{ request('date_from') }}"
                                           class="form-control">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>PO Date To</label>

                                    <input type="date"
                                           name="date_to"
                                           value="{{ request('date_to') }}"
                                           class="form-control">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Status</label>

                                    <select name="status"
                                            class="form-control">

                                        <option value="">
                                            All Status
                                        </option>

                                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>
                                            Processed
                                        </option>

                                        <option value="review_required" {{ request('status') == 'review_required' ? 'selected' : '' }}>
                                            Review Required
                                        </option>

                                        <option value="detection_failed" {{ request('status') == 'detection_failed' ? 'selected' : '' }}>
                                            Format Not Recognized
                                        </option>

                                    </select>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <button type="submit"
                                            class="btn btn-primary btn-sm">

                                        <i class="fas fa-search mr-1"></i>
                                        Search

                                    </button>

                                    <a href="{{ route('admin.smart-po.index') }}"
                                       class="btn btn-default btn-sm">

                                        <i class="fas fa-redo mr-1"></i>
                                        Reset

                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

        <!-- PO List -->
        <div class="row">
            <div class="col-lg-12">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice mr-1"></i>
                            PO Data List
                        </h3>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive-fixed border rounded bg-white po-data-table po-table-container">

                            <table class="table table-bordered border-dark table-hover mb-0">

                                <thead>
                                    <tr>

                                        <th>Sl.</th>

                                        <th>
                                            PO Company
                                        </th>

                                        <th>
                                            PO Number
                                        </th>

                                        <th>
                                            PO Date
                                        </th>

                                        <th>
                                            Vendor
                                        </th>

                                        <th>
                                            Buyer
                                        </th>

                                        <th class="text-center">
                                            Items
                                        </th>

                                        <th class="text-right">
                                            Basic Amount
                                        </th>

                                        <th class="text-right">
                                            Tax
                                        </th>

                                        <th class="text-right">
                                            PO Value
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        <th>
                                            Uploaded At
                                        </th>

                                        <th style="background:#c6e0b4;color:#0070c0;">
                                            Action
                                        </th>

                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($purchaseOrders as $po)

                                        <tr>

                                            <td>
                                                {{ $purchaseOrders->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                {{ optional($po->template)->name ?? 'Unknown' }}
                                            </td>

                                            <td class="po-number">
                                                {{ $po->po_no ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $po->po_date ? $po->po_date->format('d-m-Y') : '-' }}
                                            </td>

                                            <td>
                                                {{ $po->vendor_name ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $po->buyer_name ?: '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $po->items->count() }}
                                            </td>

                                            <td class="text-right">
                                                @if($po->basic_amount !== null)
                                                    {{ number_format((float) $po->basic_amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="text-right">
                                                @if($po->tax_amount !== null)
                                                    {{ number_format((float) $po->tax_amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="text-right">
                                                @if($po->total_amount !== null)
                                                    <strong>
                                                        {{ number_format((float) $po->total_amount, 2) }}
                                                    </strong>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>

                                                @if($po->processing_status == 'processed')

                                                    <span class="badge bg-success">
                                                        Processed
                                                    </span>

                                                @elseif($po->processing_status == 'review_required')

                                                    <span class="badge bg-warning">
                                                        Review Required
                                                    </span>

                                                @else

                                                    <span class="badge bg-danger">
                                                        Not Recognized
                                                    </span>

                                                @endif

                                            </td>

                                            <td>
                                                {{ $po->created_at ? $po->created_at->format('d-m-Y H:i') : '-' }}
                                            </td>

                                            <td class="po-actions">

                                                <a class="btn btn-info btn-sm"
                                                   href="{{ route('admin.smart-po.show', $po->id) }}"
                                                   title="View PO">

                                                    <i class="fas fa-eye"></i>
                                                    View

                                                </a>

                                                <a class="btn btn-success btn-sm"
                                                   href="{{ route('admin.smart-po.excel', $po->id) }}"
                                                   title="Download Excel">

                                                    <i class="fas fa-file-excel"></i>

                                                </a>

                                                <a class="btn btn-danger btn-sm"
                                                   href="{{ route('admin.smart-po.pdf', $po->id) }}"
                                                   title="Download PDF">

                                                    <i class="fas fa-file-pdf"></i>

                                                </a>

                                                <a class="btn btn-secondary btn-sm"
                                                   href="{{ route('admin.smart-po.original', $po->id) }}"
                                                   target="_blank"
                                                   title="Original PO">

                                                    <i class="fas fa-file"></i>

                                                </a>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="13"
                                                class="text-center text-muted"
                                                style="padding:30px;">

                                                <i class="fas fa-file-invoice fa-2x mb-2"></i>

                                                <br>

                                                No purchase order data found.

                                                <br><br>

                                                <a href="{{ route('admin.smart-po.upload') }}"
                                                   class="btn btn-primary btn-sm">

                                                    Upload Purchase Order

                                                </a>

                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                    @if($purchaseOrders->hasPages())
                        <div class="card-footer">
                            {{ $purchaseOrders->links('pagination::bootstrap-4') }}
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>
</div>
<!-- /.content -->

@endsection