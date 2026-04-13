@extends('admin.admin')
@section('bodycontent')

<div class="content-header">
    <div class="container-fluid">
        <h1>Edit LR</h1>
    </div>
</div>

<div class="content">
<div class="container-fluid">
<div class="card">
<div class="card-body">

@if(session('success'))
<div class="alert alert-success">{{session('success')}}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.lr.update',$lr->id) }}">
@csrf


@php
    $isFinal = $lr->status === 'final';
@endphp

<div class="row form-row-4">
				
		<div class="form-group col-md-4">
		<label>Indent / Ref</label>
		<input type="text" name="indent_no" value="{{ $lr->lr_no }}" class="form-control">
		</div>	
			
		<div class="form-group col-md-4">
		<label>Consignor</label>
		<select name="consignor" class="form-control select2" required>
		<option value="">Select</option>
		@foreach($plants as $p)
		<option value="{{ $p->id }}" {{ $lr->consignor_id == $p->id ? 'selected' : '' }}>{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
		@endforeach
		</select>
		</div>


		<div class="form-group col-md-4">
		<label>Consignee</label>
		<select name="consignee" class="form-control select2" required>
		<option value="">Select</option>
		@foreach($plants as $p)
		<option value="{{ $p->id }}" {{ $lr->consignee_id == $p->id ? 'selected' :'' }}>{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
		@endforeach
		</select>
		</div>	

		<div class="col-md-4 mb-3">
		<label>Customer Invoice No</label>
		<input type="text" name="invoice_no" class="form-control"
		value="{{ old('invoice_no',$lr->invoice_no) }}" readonly>
		</div>

		<div class="form-group col-md-4">
		<label>Customer Invoice Date</label>
		<input type="date" name="invoice_date" value="{{ old('invoice_date',$lr->invoice_date) }}" class="form-control">
		</div>
			
		<div class="form-group col-md-4 mb-3">
		<label>LR No</label>
		<input type="text" class="form-control"
		value="{{ $lr->lr_no }}"
		readonly>
		</div>
		<div class="form-group col-md-4">
		<label>LR Date</label>
		<input type="date" name="bill_date" class="form-control" value="{{ old('bill_date',$lr->bill_date) }}" required>
		</div>

		<div class="form-group col-md-4">
		<label>Truck Arrival Date</label>
		<input type="date" name="arrival_date" value="{{ old('arrival_date',$lr->arrival_date) }}" class="form-control" >
		</div>

		<div class="form-group col-md-4">
		<label>Truck Dispatch Date</label>
		<input type="date" name="dispatch_date" value="{{ old('dispatch_date',$lr->dispatch_date) }}" class="form-control" >
		</div>
		
		<div class="form-group col-md-4">
		<label>Truck Type</label>
		<input type="text" name="truck_type" value="{{ old('truck_type', $lr->truck_type) }}" class="form-control" >
		</div>
		
		<div class="col-md-4 mb-3">
		<label>Truck No</label>
		<input type="text" name="vehicle_no" class="form-control"
		value="{{ old('vehicle_no',$lr->vehicle_no) }}"
		{{ $isFinal ? 'readonly' : '' }}>
		</div>
		
		<div class="form-group col-md-4">
		<label>Eway Bill No.</label>
		<input type="text" name="eway_bill_no" class="form-control" value="{{ old('eway_bill_no', $lr->eway_bill_no) }}" required>
		</div>
		
		<div class="form-group col-md-4">
		<label>Insurance</label>
		<select name="insurance" class="form-control">
		<option value="">Select</option>
		<option value="At Owner Risk" {{$lr->insurance =='At Owner Risk' ? 'selected' : '' }}>At Owner Risk</option>
		<option value="At Career Risk" {{$lr->insurance =='At Career Risk' ? 'selected' : ''}}>At Career Risk</option>
		</select>
		</div>


	<div class="form-group col-md-4">
	<label>Packages</label>
	<input type="text" name="packages" class="form-control"
	value="{{ old('packages',$lr->packages) }}"
	{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="form-group col-md-4">
	<label>Description</label>
	<textarea name="description" class="form-control"
	{{ $isFinal ? 'readonly' : '' }}>{{ old('description',$lr->description) }}</textarea>
	</div>
	<div class="form-group col-md-4">
	<label>Actual Weight</label>
	<input type="text" name="actual_weight" value="{{ old('actual_weight', $lr->actual_weight) }}" class="form-control" placeholder="Enter wt in kg">
	</div>


	<div class="form-group col-md-4">
	<label>Charged Weight</label>
	<input type="text" name="charged" value="{{ old('charged', $lr->charged) }}" class="form-control" placeholder="Enter wt in kg">
	</div>
	
	<div class="form-group col-md-4">
		<label>Invoice Value</label>
		<input type="number" name="invoice_value" id="invoice_value"
		class="form-control"
		value="{{ old('invoice_value',$lr->invoice_value) }}"
		{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="form-group col-md-4">
		<label>Surcharge</label>
		<input type="number" name="surcharge" id="surcharge"
		class="form-control"
		value="{{ old('surcharge',$lr->surcharge) }}"
		{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="form-group col-md-4">
		<label>Hamali</label>
		<input type="number" name="hamali" id="hamali"
		class="form-control"
		value="{{ old('hamali',$lr->hamali) }}"
		{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="form-group col-md-4">
		<label>Risk Charge</label>
		<input type="number" name="risk_charge" id="risk_charge"
		class="form-control"
		value="{{ old('risk_charge',$lr->risk_charge) }}"
		{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="form-group col-md-4">
		<label>B Charge</label>
		<input type="number" name="b_charge" id="b_charge"
		class="form-control" step="0.01"
		value="{{ old('b_charge',$lr->b_charge) }}"
		{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="col-md-4 mb-3">
	<label>Other Charge</label>
	<input type="number" name="other_charge" id="other_charge"
	class="form-control" step="0.01"
	value="{{ old('other_charge',$lr->other_charge) }}"
	{{ $isFinal ? 'readonly' : '' }}>
	</div>

	<div class="col-md-4 mb-3">
	<label>Total Amount</label>
	<input type="number" name="total_amount" id="total_amount"
	class="form-control"
	value="{{ $lr->total_amount }}"
	readonly>
	</div>
	
</div>

@if(!$isFinal)

<div class="text-right mt-3">

<button type="submit" name="action" value="draft" class="btn btn-warning">
Update Draft
</button>

<button type="submit" name="action" value="final" class="btn btn-success">
Save Final
</button>

</div>

@else

<div class="alert alert-info mt-3">
This LR is Final. Editing is disabled.
</div>

@endif

</form>

</div>
</div>
</div>
</div>

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