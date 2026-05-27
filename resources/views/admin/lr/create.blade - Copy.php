@extends('admin.admin')
@section('bodycontent')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> 
<style>
.select2-selection__arrow {
   display: block !important;
}
.select2-container--bootstrap4 .select2-selection--single {
    height: 38px;
    padding: 6px 12px;
}

.select2-container--bootstrap4 .select2-selection__arrow {
    height: 36px;
    right: 6px;
}
</style>     
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create LR</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show ">
			<strong>{{session('success')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif	
              <div class="card-body p-0">
			
               <div class="card card-primary">
            <div class="card-header" style="background: #fce4d6; color: #0070c0;">
              <h3 class="card-title">Generate LR</h3>

            </div>
            <div class="card-body">
			<form method="POST" action="{{ route('admin.lr.store') }}">
              @csrf
			<div class="row">	
			 <div class="form-group col-md-3">
                <label for="invoice_no">Invoice No</label>
                <input type="text" name="invoice_no" value="{{ $invoiceNo }}" class="form-control">
				
				@error('invoice_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			   <div class="form-group col-md-3">
                <label for="lr_no">LR No.</label>
                <input type="text" id="lr_no" name="lr_no" class="form-control"  value="" placeholder="Enter LR No" autocomplete="off" required>				
				@error('lr_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-3">
                <label for="bill_date">Date</label>
                <input type="date" id="bill_date" name="bill_date" class="form-control"  value="" placeholder="Enter Bill Date" autocomplete=
				"off" required>
				
				@error('bill_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-3">
                <label for="vehicle_no">Vehicle No.</label>
                <input type="text" id="vehicle_no" name="vehicle_no" class="form-control"  value="" placeholder="Enter Vehicle No" autocomplete="off" required>
				
				@error('vehicle_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>	  
			  <div class="form-group col-md-3">
                <label for="co">Consignor</label>
                <select id="consignor" name="consignor" class="form-control select2" required>
				<option value="">Select Consignor</option>
				@foreach($plants as $p)
				<option value="{{ $p->id }}">{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
				@endforeach
				</select>
              @error('consignor')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
			  
			  <div class="form-group col-md-3">
                <label for="consignee">Consignee</label>
                <select id="consignee" name="consignee" class="form-control select2" required>
				<option value="">Select Consignee</option>
				@foreach($plants as $p)
				<option value="{{ $p->id }}">{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
				@endforeach
				</select>
              @error('consignee')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
			  
        
			  
			 
			  
			  <div class="form-group col-md-3">
                <label for="vehicle_no">Insurance</label>
                <select id="insurance" name="insurance" class="form-control" required>
					<option value="">Select </option>
					<option value="Yes">Yes</option>
					<option value="No">No</option>
				</select>
				
				@error('insurance')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			   <div class="form-group col-md-3">
                <label for="indent_no">Indent / Reference No.</label>
                <input type="text" id="indent_no" name="indent_no" class="form-control"  value="" placeholder="Enter Indent / Reference No" autocomplete="off">
				
				@error('fssai_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			   <div class="form-group col-md-3">
                <label for="fssai_no">FSSAI No.</label>
                <input type="text" id="fssai_no" name="fssai_no" class="form-control"  value="{{$vendor->fssai_no}}" placeholder="Enter FSSAI No" autocomplete="off">
				
				@error('fssai_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			   <div class="form-group col-md-3">
                <label for="gstin">GSTIN</label>
                <input type="text" id="gstin" name="gstin" class="form-control"  value="{{$vendor->gstin_number}}" placeholder="Enter GSTIN No" autocomplete="off">
				
				@error('gstin')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-3">
                <label for="msme">MSME/UDYAM </label>
                <input type="text" id="msme" name="msme" class="form-control"  value="{{$vendor->msme_no}}" placeholder="Enter msme No" autocomplete="off">
				
				@error('gstin')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-3">
				<label for="packages">Packages</label>
				<input type="text" id="packages" name="packages" class="form-control"  value="" placeholder="Enter Package No" autocomplete="off">
				
				@error('packages')
				<span class="text-danger">{{$message}}</span>
				@enderror
			 </div>

			<div class="form-group col-md-3">
				<label for="description">Description</label>
				<textarea id="description" name="description" class="form-control"  value="" placeholder="Enter Description"></textarea>
				
				@error('description')
				<span class="text-danger">{{$message}}</span>
				@enderror
			 </div>
			
					
					<div class="form-group col-md-3">
						<label for="actual_weight">Actual Weight</label>
						<input type="text" id="actual_weight" name="actual_weight" class="form-control"  value="" placeholder="Enter Actual Weight">
						@error('actual_weight')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 <div class="form-group col-md-3">
						<label for="charged">Charged Weight</label>
						<input type="text" id="charged" name="charged" class="form-control"  value="" placeholder="Enter charged">
						@error('charged')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					

					<div class="form-group col-md-3">
						<label for="invoice_value">Invoice Value</label>
						<input type="number" step="0.01" id="invoice_value" name="invoice_value" class="form-control"  value="" placeholder="Enter Invoice Value" autocomplete="off" required>
						
						@error('invoice_value')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>

					<div class="form-group col-md-3">
						<label for="surcharge">Surcharge</label>
						<input type="number" step="0.01" id="surcharge" name="surcharge" class="form-control"  value="" placeholder="Enter Surcharge" rows="4"></textarea>
						
						@error('surcharge')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					<div class="form-group col-md-3">
						<label for="hamali">Hamali</label>
						<input type="number" step="0.01" id="hamali" name="hamali" class="form-control"  value="" placeholder="Enter Hamali">
						@error('hamali')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 <div class="form-group col-md-3">
						<label for="risk_charge">Risk Ch.</label>
						<input type="text" id="risk_charge" name="risk_charge" class="form-control"  value="" placeholder="Enter Risk Charge">
						@error('risk_charge')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 
					  <div class="form-group col-md-3">
						<label for="b_charge">B. Charge</label>
						<input type="number" step="0.01" id="b_charge" name="b_charge" class="form-control"  value="" placeholder="Enter B Charge">
						@error('b_charge')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 
					 <div class="form-group col-md-3">
						<label for="other_charge">Other Charge</label>
						<input type="number" step="0.01" id="other_charge" name="other_charge" class="form-control"  value="" placeholder="Enter Other Charge">
						@error('other_charge')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 
					 <div class="form-group col-md-3">
						<label for="total_amount">Total Amount(rs)</label>
						<input type="number" id="total_amount" name="total_amount" class="form-control"  value="" readonly>
						@error('total_amount')
						<span class="text-danger">{{$message}}</span>
						@enderror
					 </div>
					 
					 <div class="form-group col-md-3">
				<label for="notice">Notice</label>
				<textarea id="notice" name="notice" class="form-control" placeholder="Enter Notice">{{$vendor->notice}}</textarea>
				
				@error('notice')
				<span class="text-danger">{{$message}}</span>
				@enderror
			 </div>
			 
			 <div class="form-group col-md-3">
				<label for="Caution">Caution</label>
				<textarea id="caution" name="caution" class="form-control" placeholder="Enter caution">{{$vendor->caution}}</textarea>
				
				@error('caution')
				<span class="text-danger">{{$message}}</span>
				@enderror
			 </div>

				</div>
			
				
				<div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Submit</button>
				</div>
            </div>
			</form>
            <!-- /.card-body -->
          </div>
        
		</div>       
		</div>           
	  </div>
	 </div>
	<!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
  $('.select2').select2({
    theme: 'bootstrap4'
  });
});
</script>
<script>
$(document).ready(function(){

    function calculateTotal() {

        var invoice_value = parseFloat($('#invoice_value').val()) || 0;
        var surcharge = parseFloat($('#surcharge').val()) || 0;
        var hamali = parseFloat($('#hamali').val()) || 0;
        var risk = parseFloat($('#risk_charge').val()) || 0;
        var bcharge = parseFloat($('#b_charge').val()) || 0;
        var other = parseFloat($('#other_charge').val()) || 0;

        var total = invoice_value + surcharge + hamali + risk + bcharge + other;

        $('#total_amount').val(total.toFixed(2));
    }

    $('#invoice_value, #surcharge, #hamali, #risk_charge, #b_charge, #other_charge').on('keyup change', function(){
        calculateTotal();
    });

});
</script>
@endsection