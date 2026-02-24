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
            <h1 class="m-0">Create Invoice</h1>
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
              <h3 class="card-title">Generate Invoice</h3>

            </div>
            <div class="card-body">
			<form method="POST" action="{{ route('admin.invoice.store') }}">
              @csrf
			<div class="row">	
			 <div class="form-group col-md-4">
                <label for="invoice_no">Invoice No</label>
                <input type="text" name="invoice_no" value="{{ $invoiceNo }}" class="form-control">
				
				@error('invoice_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			
			  <div class="form-group col-md-4">
                <label for="bill_date">Bill Date</label>
                <input type="date" id="bill_date" name="bill_date" class="form-control"  value="" placeholder="Enter Bill Date" autocomplete=
				"off" required>
				
				@error('bill_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-4">
                <label for="site_plant_id">Client</label>
                <select name="site_plant_id" name="site_plant_id" class="form-control select2" required>
				<option value="">Select Client</option>
				@foreach($plants as $p)
				<option value="{{ $p->id }}">{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
				@endforeach
				</select>
              @error('site_plant_id')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
			
			  
            </div>
			  
			  <div class="row">

					<div class="col-md-6">
						<div class="form-group">
							<label>Billing Address</label>
							<select name="billing_address_id" class="form-control" required>
								@foreach($vendor->addresses->where('address_type','Billing') as $b)
								<option value="{{ $b->id }}">{{ $b->address_line1 }}, {{ $b->city }} {{ $b->state }}, {{ $b->zip_code }} </option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label>Branch Address</label>
							<select name="branch_address_id" class="form-control" required>
								@foreach($vendor->addresses->where('address_type','Branch') as $b)
								<option value="{{ $b->id }}">{{ $b->address_line1 }}, {{ $b->city }} {{ $b->state }}, {{ $b->zip_code }} </option>
								@endforeach
							</select>
						</div>
					</div>

				</div>
			
				
				<div class="row">
				{{--<div id="items">--}}
						<div class="form-group col-md-4">
							<label for="Description">LR NO.</label>
							<input type="text" name="items[0][lr_no]" class="form-control">
						</div>
						  <div class="form-group col-md-4">
							<label for="Description">LR Date</label>
						   <input type="date" name="items[0][lr_date]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="taxable_0">Vehicle Dispatch Date</label>
						   <input type="date" name="items[0][vehicle_dispatch_date]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">From</label>
						   <input type="text" name="items[0][from]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">To</label>
						   <input type="text" name="items[0][to]" class="form-control">				
						  </div>
						 
						   <div class="form-group col-md-4">
							<label for="gst_0">PO No.</label>
						   <input type="text" name="items[0][po_no]" class="form-control">			
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Taxable</label>
						   <input type="number" step="0.01" name="items[0][taxable]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">GST</label>
						   <input type="number" step="0.01" name="items[0][gst]" class="form-control">
						  </div>
						  {{--</div>
					<div class="col-md-12">
						<button type="button" class="btn btn-success btn-sm"  onclick="addItem()">+</button>
					</div> --}}
				</div>
				
				{{-- ================= ANNEXURE ================= --}}
			<div class="card card-warning">
				<div class="card-header">
					<h3 class="card-title">Annexure Details</h3>
				</div>
				<div class="row">
				{{--<div id="itemsannexure">--}}
									
						<div class="form-group col-md-4">
							<label for="Description">Customer Reference No.</label>
							<input type="text" name="annexures[0][customer_ref_no]" class="form-control">
						</div>
						  <div class="form-group col-md-4">
							<label for="Description">OBD No</label>
						   <input type="text" name="annexures[0][obd_no]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="taxable_0">Arrival Date</label>
						   <input type="date" name="annexures[0][arrival_date]" class="form-control">
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Delivery Date</label>
						   <input type="date" name="annexures[0][delivery_date]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Transit Days</label>
						   <input type="number" name="annexures[0][transit_days]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Vehicle No</label>
						   <input type="text" name="annexures[0][vehicle_no]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Actual Weight</label>
						   <input type="text" name="annexures[0][actual_weight]" class="form-control">		
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Freight</label>
						   <input type="text" name="annexures[0][freight]" class="form-control">				
						  </div>
						   <div class="form-group col-md-4">
							<label for="gst_0">Charge Weight</label>
						   <input type="text" type="text" name="annexures[0][charge_weight]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="gst_0">Loading Charge</label>
						   <input type="text" name="annexures[0][loading_charge]" class="form-control">
						  </div>
						  
						  <div class="form-group col-md-4">
							<label for="gst_0">Unloading Charge</label>
						   <input type="text" name="annexures[0][unloading_charge]" class="form-control">
						  </div>
						  
						  <div class="form-group col-md-4">
							<label for="gst_0">Loading Pl Det. Charge</label>
						   <input type="text" name="annexures[0][loading_pl_det_charge]" class="form-control">
						  </div>
						  
						  <div class="form-group col-md-4">
							<label for="gst_0">UnLoading Pl Det. Charge</label>
						   <input type="text" name="annexures[0][unloading_pl_det_charge]" class="form-control">
						  </div>
						  
						  <div class="form-group col-md-4">
							<label for="gst_0">Incentive Charge</label>
						   <input type="text" name="annexures[0][incentive_charge]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="gst_0">Other Charge</label>
						   <input type="text" name="annexures[0][other_charge]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="gst_0">Two Point Delivery</label>
						   <input type="text" name="annexures[0][two_point_delivery]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="gst_0">Toll Tax</label>
						   <input type="text" name="annexures[0][toll_tax]" class="form-control">
						  </div>
						  <div class="form-group col-md-4">
							<label for="gst_0">Green Tax</label>
						   <input type="text" name="annexures[0][green_tax]" class="form-control">
						  </div>
					{{--</div> 
					<div class="col-md-12">
						<button type="button" class="btn btn-success btn-sm"  onclick="addAnnexure()">+</button>
                   
					</div>--}}
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
@endsection