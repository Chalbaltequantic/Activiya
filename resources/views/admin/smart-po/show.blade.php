@extends('admin.admin')
@section('bodycontent')

<style>
.po-label {
    color: #6c757d;
    font-size: 12px;
    margin-bottom: 3px;
}

.po-value {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
}

.po-item-table {
    font-size: 12px;
    min-width: 1300px;
}

.po-item-table th,
.po-item-table td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 6px 8px;
}

.po-item-table thead th {
    background: #fce4d6;
    color: #0070c0;
}

.po-items-container {
    overflow-x: auto;
}
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="m-0">
                    Purchase Order Detail
                </h1>

            </div>

            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.smart-po.index') }}">
                            PO Data List
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{ $purchaseOrder->po_no ?: 'PO Detail' }}
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

        @if($purchaseOrder->processing_status != 'processed')

            <div class="alert alert-warning">

                <strong>
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Review Required
                </strong>

                <br>

                {{ $purchaseOrder->processing_remark ?: 'Please review the extracted PO data.' }}

            </div>

        @endif

        <!-- PO Summary -->
        <div class="row">

            <div class="col-lg-12">

                <div class="card card-primary">

                    <div class="card-header">

                        <h3 class="card-title">
                            PO Summary
                        </h3>

                        <div class="card-tools">

                            <a href="{{ route('admin.smart-po.excel', $purchaseOrder->id) }}"
                               class="btn btn-success btn-sm">

                                <i class="fas fa-file-excel"></i>
                                Excel

                            </a>

                            <a href="{{ route('admin.smart-po.pdf', $purchaseOrder->id) }}"
                               class="btn btn-danger btn-sm">

                                <i class="fas fa-file-pdf"></i>
                                PDF

                            </a>

                            <a href="{{ route('admin.smart-po.original', $purchaseOrder->id) }}"
                               target="_blank"
                               class="btn btn-light btn-sm">

                                <i class="fas fa-file"></i>
                                Original PO

                            </a>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">
                                <div class="po-label">
                                    PO Company
                                </div>

                                <div class="po-value">
                                    {{ optional($purchaseOrder->template)->name ?? 'Unknown' }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="po-label">
                                    PO Number
                                </div>

                                <div class="po-value">
                                    {{ $purchaseOrder->po_no ?: '-' }}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="po-label">
                                    PO Date
                                </div>

                                <div class="po-value">
                                    {{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d-m-Y') : '-' }}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="po-label">
                                    Delivery Date
                                </div>

                                <div class="po-value">
                                    {{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d-m-Y') : '-' }}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="po-label">
                                    Expiry Date
                                </div>

                                <div class="po-value">
                                    {{ $purchaseOrder->expiry_date ? $purchaseOrder->expiry_date->format('d-m-Y') : '-' }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Vendor / Buyer -->
        <div class="row">

            <div class="col-md-6">

                <div class="card card-outline card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            Vendor Details
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-12">
                                <div class="po-label">Vendor Name</div>
                                <div class="po-value">
                                    {{ $purchaseOrder->vendor_name ?: '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="po-label">Vendor Code</div>
                                <div class="po-value">
                                    {{ $purchaseOrder->vendor_code ?: '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="po-label">Vendor GSTIN</div>
                                <div class="po-value">
                                    {{ $purchaseOrder->vendor_gstin ?: '-' }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card card-outline card-info">

                    <div class="card-header">
                        <h3 class="card-title">
                            Buyer Details
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="po-label">
                            Buyer Name
                        </div>

                        <div class="po-value">
                            {{ $purchaseOrder->buyer_name ?: '-' }}
                        </div>

                        <div class="po-label">
                            Buyer GSTIN
                        </div>

                        <div class="po-value">
                            {{ $purchaseOrder->buyer_gstin ?: '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Billing / Shipping -->
        <div class="row">

            <div class="col-md-6">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            Bill To
                        </h3>
                    </div>

                    <div class="card-body">
                        {!! nl2br(e($purchaseOrder->bill_to ?: '-')) !!}
                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            Ship To
                        </h3>
                    </div>

                    <div class="card-body">
                        {!! nl2br(e($purchaseOrder->ship_to ?: '-')) !!}
                    </div>

                </div>

            </div>

        </div>

        <!-- PO Items -->
        <div class="row">

            <div class="col-lg-12">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            PO Item Details
                        </h3>
                    </div>

                    <div class="card-body p-0">

                        <div class="po-items-container">

                            <table class="table table-bordered table-hover po-item-table mb-0">

                                <thead>
                                    <tr>
                                        <th>Sl.</th>
                                        <th>Item Code</th>
                                        <th>Description</th>
                                        <th>HSN</th>
                                        <th>EAN</th>
                                        <th>Qty</th>
                                        <th>UOM</th>
                                        <th>MRP</th>
                                        <th>Unit Cost</th>
                                        <th>CGST %</th>
                                        <th>SGST %</th>
                                        <th>IGST %</th>
                                        <th>Cess %</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($purchaseOrder->items as $item)

                                        <tr>

                                            <td>
                                                {{ $item->line_no ?: $loop->iteration }}
                                            </td>

                                            <td>
                                                {{ $item->item_code ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $item->description ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $item->hsn_code ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $item->ean ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $item->quantity ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->uom ?: '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->mrp !== null ? number_format((float) $item->mrp, 2) : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->unit_cost !== null ? number_format((float) $item->unit_cost, 2) : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->cgst_percent !== null ? $item->cgst_percent : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->sgst_percent !== null ? $item->sgst_percent : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->igst_percent !== null ? $item->igst_percent : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->cess_percent !== null ? $item->cess_percent : '-' }}
                                            </td>

                                            <td class="text-right">
                                                {{ $item->total_amount !== null ? number_format((float) $item->total_amount, 2) : '-' }}
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="14"
                                                class="text-center text-muted"
                                                style="padding:25px;">

                                                No PO items were extracted.

                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Totals -->
        <div class="row">

            <div class="col-md-5 ml-auto">

                <div class="card card-outline card-success">

                    <div class="card-header">
                        <h3 class="card-title">
                            PO Amount Summary
                        </h3>
                    </div>

                    <div class="card-body">

                        <table class="table table-sm">

                            <tr>
                                <td>
                                    Basic Amount
                                </td>

                                <td class="text-right">
                                    {{ number_format((float) ($purchaseOrder->basic_amount ?? 0), 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Tax Amount
                                </td>

                                <td class="text-right">
                                    {{ number_format((float) ($purchaseOrder->tax_amount ?? 0), 2) }}
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    Total PO Value
                                </th>

                                <th class="text-right">
                                    {{ number_format((float) ($purchaseOrder->total_amount ?? 0), 2) }}
                                </th>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mb-3">

            <div class="col-md-12">

                <a href="{{ route('admin.smart-po.index') }}"
                   class="btn btn-default">

                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to PO Data List

                </a>

            </div>

        </div>

    </div>
</div>
<!-- /.content -->

@endsection