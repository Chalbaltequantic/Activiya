@extends('admin.admin')
@section('bodycontent')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet"/>

<style>
.form-row-4 .form-group{
    display:flex;
    align-items:center;
    margin-bottom:10px;
}
.form-row-4 label{
    width:140px;
    font-weight:600;
    margin-bottom:0;
}
.form-row-4 .form-control,
.form-row-4 textarea,
.form-row-4 .select2-container{
    flex:1;
}
.select2-container--bootstrap4 .select2-selection--single{
    height:38px;
    padding:6px 10px;
}
.readonly-bg{
    background:#e9ecef !important;
    pointer-events:none;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.invoice.list') }}">Invoice List</a></li>
                    <li class="breadcrumb-item active">Edit Invoice</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
<div class="container-fluid">
<div class="card">
<div class="card-body">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $item = $invoice->items->first();
    $isFinal = ($invoice->status ?? 'draft') === 'final';
@endphp

<form method="POST" action="{{ route('admin.invoice.update', $invoice->id) }}">
    @csrf

    <div class="row form-row-4">

        <div class="form-group col-md-4">
            <label>Client</label>
            <select name="site_plant_id" class="form-control select2 {{ $isFinal ? 'readonly-bg' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                <option value="">Select Client</option>
                @foreach($plants as $p)
                    <option value="{{ $p->id }}" {{ old('site_plant_id', $invoice->site_plant_id) == $p->id ? 'selected' : '' }}>
                        {{ $p->plant_site_name }} ({{ $p->plant_site_code }})
                    </option>
                @endforeach
            </select>
            @if($isFinal)
                <input type="hidden" name="site_plant_id" value="{{ old('site_plant_id', $invoice->site_plant_id) }}">
            @endif
        </div>

        <div class="form-group col-md-4">
            <label>Indent Id</label>
            <input type="text" name="indent_id" class="form-control"
                   value="{{ old('indent_id', $invoice->indent_id) }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Invoice No</label>
            <input type="text" class="form-control readonly-bg"
                   value="{{ $invoice->invoice_no }}" readonly>
        </div>

        <div class="form-group col-md-4">
            <label>Invoice Date</label>
            <input type="date" name="bill_date" class="form-control"
                   value="{{ old('bill_date', !empty($invoice->bill_date) ? date('Y-m-d', strtotime($invoice->bill_date)) : '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Billing Address</label>
            <select name="billing_address_id" class="form-control {{ $isFinal ? 'readonly-bg' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                @foreach($billing as $b)
                    <option value="{{ $b->id }}" {{ old('billing_address_id', $invoice->billing_address_id) == $b->id ? 'selected' : '' }}>
                        {{ $b->address_line1 }}, {{ $b->city }}
                    </option>
                @endforeach
            </select>
            @if($isFinal)
                <input type="hidden" name="billing_address_id" value="{{ old('billing_address_id', $invoice->billing_address_id) }}">
            @endif
        </div>

        <div class="form-group col-md-4">
            <label>Branch Address</label>
            <select name="branch_address_id" class="form-control {{ $isFinal ? 'readonly-bg' : '' }}" {{ $isFinal ? 'disabled' : '' }}>
                @foreach($branch as $b)
                    <option value="{{ $b->id }}" {{ old('branch_address_id', $invoice->branch_address_id) == $b->id ? 'selected' : '' }}>
                        {{ $b->address_line1 }}, {{ $b->city }}
                    </option>
                @endforeach
            </select>
            @if($isFinal)
                <input type="hidden" name="branch_address_id" value="{{ old('branch_address_id', $invoice->branch_address_id) }}">
            @endif
        </div>

        <input type="hidden" name="items[0][id]" value="{{ $item->id ?? '' }}">

        <div class="form-group col-md-4">
            <label>PO No.</label>
            <input type="text" name="items[0][po_no]" class="form-control"
                   value="{{ old('items.0.po_no', $item->po_no ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>From</label>
            <input type="text" name="items[0][from]" class="form-control"
                   value="{{ old('items.0.from', $item->from_location ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>To</label>
            <input type="text" name="items[0][to]" class="form-control"
                   value="{{ old('items.0.to', $item->to_location ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>LR No</label>
            <input type="text" class="form-control readonly-bg"
                   value="{{ old('items.0.lr_no', $item->lr_no ?? '') }}"
                   readonly>
            <input type="hidden" name="items[0][lr_no]" value="{{ old('items.0.lr_no', $item->lr_no ?? '') }}">
        </div>

        <div class="form-group col-md-4">
            <label>LR Date</label>
            <input type="date" class="form-control readonly-bg"
                   value="{{ old('items.0.lr_date', !empty($item->lr_date) ? date('Y-m-d', strtotime($item->lr_date)) : '') }}"
                   readonly>
            <input type="hidden" name="items[0][lr_date]" value="{{ old('items.0.lr_date', !empty($item->lr_date) ? date('Y-m-d', strtotime($item->lr_date)) : '') }}">
        </div>

        <div class="form-group col-md-4">
            <label>Dispatch Date</label>
            <input type="date" name="items[0][vehicle_dispatch_date]" class="form-control"
                   value="{{ old('items.0.vehicle_dispatch_date', !empty($item->vehicle_dispatch_date) ? date('Y-m-d', strtotime($item->vehicle_dispatch_date)) : '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Truck Type</label>
            <input type="text" name="items[0][truck_type]" class="form-control"
                   value="{{ old('items.0.truck_type', $item->truck_type ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Truck No.</label>
            <input type="text" name="items[0][vehicle_no]" class="form-control"
                   value="{{ old('items.0.vehicle_no', $item->vehicle_no ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Actual Weight</label>
            <input type="text" name="items[0][actual_weight]" class="form-control"
                   value="{{ old('items.0.actual_weight', $item->actual_weight ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Charge Weight</label>
            <input type="text" name="items[0][charge_weight]" class="form-control"
                   value="{{ old('items.0.charge_weight', $item->charged_weight ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        <div class="form-group col-md-4">
            <label>Description</label>
            <input type="text" name="items[0][description]" class="form-control"
                   value="{{ old('items.0.description', $item->description ?? '') }}"
                   {{ $isFinal ? 'readonly' : '' }}>
        </div>

        {{-- Amount section - non editable --}}
        <div class="form-group col-md-4">
            <label>Base Freight</label>
            <input type="number" step="0.01" class="form-control readonly-bg"
                   value="{{ old('items.0.base_freight', $item->base_freight ?? 0) }}"
                   readonly>
            <input type="hidden" name="items[0][base_freight]" value="{{ old('items.0.base_freight', $item->base_freight ?? 0) }}">
        </div>

        <div class="form-group col-md-4">
            <label>Taxable</label>
            <input type="number" step="0.01" class="form-control readonly-bg"
                   value="{{ old('items.0.taxable', $item->taxable ?? 0) }}"
                   readonly>
            <input type="hidden" name="items[0][taxable]" value="{{ old('items.0.taxable', $item->taxable ?? 0) }}">
        </div>

        <div class="form-group col-md-4">
            <label>GST %</label>
            <input type="number" step="0.01" class="form-control readonly-bg"
                   value="{{ old('items.0.gst', $item->gst_percent ?? 0) }}"
                   readonly>
            <input type="hidden" name="items[0][gst]" value="{{ old('items.0.gst', $item->gst_percent ?? 0) }}">
        </div>

       

        <div class="form-group col-md-4">
            <label>Status</label>
            <input type="text" class="form-control readonly-bg"
                   value="{{ ucfirst($invoice->status ?? 'draft') }}"
                   readonly>
        </div>

    </div>

    <div class="mt-3">
        @if(!$isFinal)
            <button type="submit" name="action" value="draft" class="btn btn-warning">
                Update Draft
            </button>

            <button type="submit" name="action" value="final" class="btn btn-success">
                Save Final
            </button>
        @else
            <div class="alert alert-info mb-0">
                This invoice is Final. Editing is disabled.
            </div>
        @endif

        <a href="{{ route('admin.invoice.list') }}" class="btn btn-secondary">Back</a>
    </div>

</form>

</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('.select2').select2({ theme:'bootstrap4' });
});
</script>

@endsection