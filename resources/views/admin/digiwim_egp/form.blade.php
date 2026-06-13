@extends('admin.admin')

@section('bodycontent')

@php
$isEdit = isset($record);
@endphp

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
                <h1 class="m-0">
                    {{ $isEdit ? 'Edit DigiWIM EGP' : 'Create DigiWIM EGP' }}
                </h1>
            </div>

            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.digiwim-egp.index') }}"
                   class="btn btn-secondary">
                    Back to List
                </a>
            </div>

        </div>

    </div>
</div>

<div class="content">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12">

<div class="card">

    <div class="card-header"
         style="background:#fce4d6;color:#0070c0;">

        <h3 class="card-title">
            {{ $isEdit ? 'Update EGP Entry' : 'Create EGP Entry' }}
        </h3>

    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())

            <div class="alert alert-danger">
                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>
            </div>

        @endif

        @if($isEdit)

            <form method="POST"
                  action="{{ route('admin.digiwim-egp.update',$record->id) }}">

                @csrf
                @method('PUT')

        @else

            <form method="POST"
                  action="{{ route('admin.digiwim-egp.store') }}">

                @csrf

        @endif


        <div class="row form-row-4">

            <div class="form-group col-md-4">
                <label>Purpose of Entry</label>
                <input type="text"
                       name="purpose_of_entry"
                       class="form-control"
                       value="{{ old('purpose_of_entry',$record->purpose_of_entry ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Outward Date</label>
                <input type="date"
                       name="outward_date"
                       class="form-control"
                       value="{{ old('outward_date',$record->outward_date ?? date('Y-m-d')) }}">
            </div>


            <div class="form-group col-md-4">
                <label>Outward Time</label>
                <input type="time"
                       name="outward_time"
                       class="form-control"
                       value="{{ old('outward_time',$record->outward_time ?? date('H:i')) }}">
            </div>


            <div class="form-group col-md-4">
                <label>Customer Name</label>
                <input type="text"
                       name="customer_name"
                       class="form-control"
                       value="{{ old('customer_name',$record->customer_name ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Customer Location</label>
                <input type="text"
                       name="customer_location"
                       class="form-control"
                       value="{{ old('customer_location',$record->customer_location ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Invoice / Challan No.</label>
                <input type="text"
                       name="invoice_challan_no"
                       class="form-control"
                       value="{{ old('invoice_challan_no',$record->invoice_challan_no ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Invoice / Challan Date</label>
                <input type="date"
                       name="invoice_challan_date"
                       class="form-control"
                       value="{{ old('invoice_challan_date',$record->invoice_challan_date ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Vendor Name</label>
                <input type="text"
                       name="vendor_name"
                       class="form-control"
                       value="{{ old('vendor_name',$record->vendor_name ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Truck No.</label>
                <input type="text"
                       name="truck_no"
                       class="form-control"
                       value="{{ old('truck_no',$record->truck_no ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>LR / CN No.</label>
                <input type="text"
                       name="lr_cn_no"
                       class="form-control"
                       value="{{ old('lr_cn_no',$record->lr_cn_no ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Driver Mobile No.</label>
                <input type="text"
                       name="driver_mobile_no"
                       class="form-control"
                       value="{{ old('driver_mobile_no',$record->driver_mobile_no ?? '') }}">
            </div>


            <div class="form-group col-md-4">
                <label>Custom</label>
                <textarea name="custom"
                          class="form-control"
                          rows="2">{{ old('custom',$record->custom ?? '') }}</textarea>
            </div>

        </div>


        <div class="form-group mt-3 text-right">

            <a href="{{ route('admin.digiwim-egp.index') }}"
               class="btn btn-secondary">
                Back
            </a>

            <button type="submit"
                    class="btn btn-success">

                {{ $isEdit ? 'Update Entry' : 'Save Entry' }}

            </button>

        </div>

        </form>

    </div>

</div>

</div>
</div>
</div>
</div>

@endsection