@extends('admin.admin')
@section('bodycontent')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet"/>


<style>

/* ===== FORM LAYOUT FIX ===== */

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
/*
.form-row-4 textarea{
    height:38px;
    resize:none;
}*/
.select2-container--bootstrap4 .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px;
    display: flex;
    align-items: center;
}

/* Fix text vertical alignment */
.select2-container--bootstrap4 .select2-selection__rendered {
    line-height: 24px !important;
    padding-left: 0;
}

/* Perfect arrow vertical center */
.select2-container--bootstrap4 .select2-selection__arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 10px;
    height: auto !important;
    display: block !important;
}

/* Arrow icon */
.select2-container--bootstrap4 .select2-selection__arrow b {
    border-color: #555 transparent transparent transparent !important;
    border-style: solid !important;
    border-width: 5px 4px 0 4px !important;
}

</style>


<!-- Content Header -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1 class="m-0">Create LR</h1>
</div>
<div class="col-sm-6">
<ol class="breadcrumb float-sm-right">
<li class="breadcrumb-item">Home</li>
</ol>
</div>
</div>
</div>
</div>


<div class="content">
<div class="container-fluid">
<div class="row">
<div class="col-lg-12">

<div class="card">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
<strong>{{session('success')}}</strong>
<button type="button" class="close" data-dismiss="alert"></button>
</div>
@endif


<div class="card-body p-0">

<div class="card card-primary">

<div class="card-header" style="background:#fce4d6;color:#0070c0;">
<h3 class="card-title">Generate LR</h3>
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
<form method="POST" action="{{ route('admin.lr.store') }}">
@csrf

<div class="row form-row-4">

<div class="form-group col-md-4">
<label>Customer Invoice No</label>
<input type="text" name="invoice_no" value="{{ $invoiceNo }}" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Customer Invoice Date</label>
<input type="date" name="invoice_date" value="" class="form-control">
</div>
<div class="form-group col-md-4">
<label>LR No.</label>
<input type="text" name="lr_no" class="form-control" value="{{ old('lr_no') }}"  required>
</div>


<div class="form-group col-md-4">
<label>Date</label>
<input type="date" name="bill_date" class="form-control" value="{{ old('bill_date') }}" required>
</div>
<div class="form-group col-md-4">
<label>Truck Arrival Date</label>
<input type="date" name="arrival_date" value="{{ old('arrival_date') }}" class="form-control" >
</div>

<div class="form-group col-md-4">
<label>Truck Dispatch Date</label>
<input type="date" name="dispatch_date" value="{{ old('dispatch_date') }}" class="form-control" >
</div>

<div class="form-group col-md-4">
<label>Truck Type</label>
<input type="text" name="truck_type" value="{{ old('truck_type') }}" class="form-control" >
</div>

<div class="form-group col-md-4">
<label>Vehicle No</label>
<input type="text" name="vehicle_no" class="form-control" value="{{ old('vehicle_no') }}" required>
</div>

<div class="form-group col-md-4">
<label>Consignor</label>
<select name="consignor" class="form-control select2" required>
<option value="">Select</option>
@foreach($plants as $p)
<option value="{{ $p->id }}" {{old('consignor')==$p->id ?? 'selected'}}>{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
@endforeach
</select>
</div>


<div class="form-group col-md-4">
<label>Consignee</label>
<select name="consignee" class="form-control select2" required>
<option value="">Select</option>
@foreach($plants as $p)
<option value="{{ $p->id }}" {{old('consignee')==$p->id ?? 'selected'}}>{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
@endforeach
</select>
</div>


<div class="form-group col-md-4">
<label>Insurance</label>
<select name="insurance" class="form-control">
<option value="">Select</option>
<option>Yes</option>
<option>No</option>
</select>
</div>


<div class="form-group col-md-4">
<label>Indent / Ref</label>
<input type="text" name="indent_no" class="form-control">
</div>

<input type="hidden" name="fssai_no" value="{{$vendor->fssai_no}}" class="form-control">
<input type="hidden" name="gstin" value="{{$vendor->gstin_number}}" class="form-control">
<input type="hidden" name="msme" value="{{$vendor->msme_no}}" class="form-control">

<div class="form-group col-md-4">
<label>Packages</label>
<input type="number" name="packages" value="{{ old('packages') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Description</label>
<textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
</div>


<div class="form-group col-md-4">
<label>Actual Weight</label>
<input type="text" name="actual_weight" value="{{ old('actual_weight') }}" class="form-control" placeholder="Enter wt in kg">
</div>


<div class="form-group col-md-4">
<label>Charged Weight</label>
<input type="text" name="charged" value="{{ old('charged') }}" class="form-control" placeholder="Enter wt in kg">
</div>


<div class="form-group col-md-4">
<label>Invoice Value</label>
<input type="number" step="0.01" id="invoice_value" name="invoice_value" value="{{ old('invoice_value') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Surcharge</label>
<input type="number" step="0.01" id="surcharge" name="surcharge" value="{{ old('surcharge') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Hamali</label>
<input type="number" step="0.01" id="hamali" name="hamali" value="{{ old('hamali') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Risk Ch</label>
<input type="text" id="risk_charge" name="risk_charge"  value="{{ old('risk_charge') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>B Charge</label>
<input type="number" step="0.01" id="b_charge" name="b_charge" value="{{ old('b_charge') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Other Charge</label>
<input type="number" step="0.01" id="other_charge" name="other_charge" value="{{ old('other_charge') }}" class="form-control">
</div>


<div class="form-group col-md-4">
<label>Total Amount</label>
<input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" class="form-control" readonly>
</div>


<div class="form-group col-md-4" style="display:none;">
<label>Notice</label>
<textarea name="notice" class="form-control" rows="4">{{$vendor->notice}}</textarea>
</div>


<div class="form-group col-md-4" style="display:none;">
<label>Caution</label>
<textarea name="caution" class="form-control" rows="4">{{$vendor->caution}}</textarea>
</div>


</div>


<div class="form-group mt-3 text-right">
<button type="submit" class="btn btn-primary">Submit</button>
</div>

</form>

</div>
</div>
</div>
</div>
</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
$('.select2').select2({ theme:'bootstrap4' });
});
</script>


<script>

$(document).ready(function(){

function calculateTotal(){

var invoice = parseFloat($('#invoice_value').val()) || 0;
var surcharge = parseFloat($('#surcharge').val()) || 0;
var hamali = parseFloat($('#hamali').val()) || 0;
var risk = parseFloat($('#risk_charge').val()) || 0;
var bcharge = parseFloat($('#b_charge').val()) || 0;
var other = parseFloat($('#other_charge').val()) || 0;

var total = invoice + surcharge + hamali + risk + bcharge + other;

$('#total_amount').val(total.toFixed(2));

}

$('#invoice_value,#surcharge,#hamali,#risk_charge,#b_charge,#other_charge')
.on('keyup change',calculateTotal);

});

</script>

@endsection
