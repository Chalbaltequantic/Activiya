@extends('admin.admin')
@section('bodycontent')

<style>
.form-row-4 .form-group{
    display:flex;
    align-items:center;
    margin-bottom:10px;
}
.form-row-4 label{
    width:150px;
    font-weight:600;
    margin-bottom:0;
}
.form-row-4 .form-control,
.form-row-4 textarea{
    flex:1;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create DigiWIM EGR</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.digiwim-egr.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="content">
<div class="container-fluid">
<div class="card card-primary">

<div class="card-header" style="background:#fce4d6;color:#0070c0;">
    <h3 class="card-title">Create EGR Entry</h3>
</div>

<div class="card-body">

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.digiwim-egr.store') }}">
@csrf

<div class="row form-row-4">

    <div class="form-group col-md-4">
        <label>Inward Date</label>
        <input type="date" name="inward_date" value="{{ old('inward_date') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Inward Time</label>
        <input type="time" name="inward_time" value="{{ old('inward_time') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Purpose of Entry</label>
        <input type="text" name="purpose_of_entry" value="{{ old('purpose_of_entry') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Supplier Name</label>
        <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Supplier Location</label>
        <input type="text" name="supplier_location" value="{{ old('supplier_location') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Invoice/Challan No.</label>
        <input type="text" name="invoice_challan_no" value="{{ old('invoice_challan_no') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Invoice/Challan Date</label>
        <input type="date" name="invoice_challan_date" value="{{ old('invoice_challan_date') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Vendor Name</label>
        <input type="text" name="vendor_name" value="{{ old('vendor_name') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Truck No.</label>
        <input type="text" name="truck_no" value="{{ old('truck_no') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>LR/CN No.</label>
        <input type="text" name="lr_cn_no" value="{{ old('lr_cn_no') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Driver Mobile No.</label>
        <input type="text" name="driver_mobile_no" value="{{ old('driver_mobile_no') }}" class="form-control">
    </div>

    <div class="form-group col-md-4">
        <label>Custom</label>
        <textarea name="custom" class="form-control" rows="2">{{ old('custom') }}</textarea>
    </div>

</div>

<div class="form-group mt-3 text-right">
    <button type="submit" class="btn btn-success">Save</button>
</div>

</form>

</div>
</div>
</div>
</div>

@endsection