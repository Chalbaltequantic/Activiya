@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Tracking Data</h1>
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
            <div class="card-header">
              <h3 class="card-title">Edit Tracking Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/tracking/updatetrackingdata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			  <div class="form-group col-md-6">
                <label for="consignor_name">Indent No</label>
                <input type="text" id="indent_no" name="indent_no" value="{{$trackingdata->indent_no}}" class="form-control">				
				@error('title')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div><input type="hidden" id="id" name="id" value="{{$trackingdata->id}}" class="form-control">
              <div class="form-group col-md-6">
                <label for="customer_po_no">customer po no </label>
                <input type="text" id="customer_po_no" name="customer_po_no" value="{{$trackingdata->customer_po_no}}" class="form-control">
				
				@error('customer_po_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			   <div class="form-group col-md-6">
                <label for="origin">Origin</label>
                <input type="text" id="origin" name="origin" class="form-control" value="{{$trackingdata->origin}}">
				@error('origin')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              <div class="form-group col-md-6">
                <label for="destination">Destination</label>
                <input type="text" id="destination" name="destination" class="form-control tinymce_editor" value="{{$trackingdata->destination}}"></textarea>
				@error('destination')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="consignee_name">Vendor name</label>
                <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{$trackingdata->vendor_name}}">
				@error('vendor_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-6">
                <label for="vehicle_type">Vehicle type</label>
                <input type="text" id="vehicle_type" name="vehicle_type" class="form-control" value="{{$trackingdata->vehicle_type}}">
                  @error('vehicle_type')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-6">
                <label for="lr_no">LR no</label>
                <input type="text" id="lr_no" name="lr_no" class="form-control" value="{{$trackingdata->lr_no}}">
                @error('lr_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  

		   <div class="form-group col-md-6">
                <label for="cases">Cases</label>
                <input type="number" id="cases" name="cases" class="form-control" value="{{$trackingdata->cases}}">
                @error('cases')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="truck_no">Truck no</label>
                <input type="text" id="truck_no" name="truck_no" class="form-control" value="{{$trackingdata->truck_no}}">
                @error('truck_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
			 <div class="form-group col-md-6">
                <label for="driver_number">Driver number</label>
                <input type="text" id="driver_number" name="driver_number" class="form-control" value="{{$trackingdata->driver_number}}">
                @error('driver_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>	


              <div class="form-group col-md-6">
                <label for="dispatch_date">Dispatch date</label>
                <input type="text" id="dispatch_date" name="dispatch_date" class="form-control" value="{{$trackingdata->dispatch_date}}">
                @error('dispatch_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="dispatch_time">Dispatch time</label>
                <input type="text" id="dispatch_time" name="dispatch_time" class="form-control" value="{{$trackingdata->dispatch_time}}">
                @error('dispatch_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="a_amount">Lead time</label>
                <input type="number" id="lead_time" name="lead_time" class="form-control" value="{{$trackingdata->lead_time}}">
                @error('lead_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>

			  <div class="form-group col-md-6">
                <label for="distance">Dstance</label>
                <input type="number" id="distance" name="distance" class="form-control" value="{{$trackingdata->distance}}">
                @error('distance')
                <span class="text-danger">{{$message}}</span>
                @enderror
			  </div>
			<div class="form-group col-md-6">
                <label for="ref3">Delivery due date</label>
                <input type="date" id="delivery_due_date" name="delivery_due_date" class="form-control" value="{{$trackingdata->delivery_due_date}}">
                @error('delivery_due_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="shipment_status">Shipment status</label>
                <input type="text" id="shipment_status" name="shipment_status" class="form-control" value="{{$trackingdata->shipment_status}}">
                @error('shipment_status')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="transit_status">Transit status</label>
                <input type="text" id="transit_status" name="transit_status" class="form-control" value="{{$trackingdata->transit_status}}">
                @error('transit_status')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="distance_covered">Distance covered</label>
                <input type="text" id="distance_covered" name="distance_covered" class="form-control" value="{{$trackingdata->distance_covered}}">
                @error('distance_covered')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="current_location">Current location</label>
                <input type="text" id="current_location" name="current_location" class="form-control" value="{{$trackingdata->current_location}}">
                @error('current_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="distance_to_cover">Distance to cover</label>
                <input type="text" id="distance_to_cover" name="distance_to_cover" class="form-control" value="{{$trackingdata->distance_to_cover}}">
                @error('distance_to_cover')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="tracking_link">Tracking link</label>
                <input type="text" id="tracking_link" name="tracking_link" class="form-control" value="{{$trackingdata->tracking_link}}">
                @error('tracking_link')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="reporting_date">Reporting date</label>
                <input type="date" id="reporting_date" name="reporting_date" class="form-control" value="{{$trackingdata->reporting_date}}">
                @error('reporting_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			  <div class="form-group col-md-6">
                <label for="reporting_time">Reporting time</label>
                <input type="text" id="reporting_time" name="reporting_time" class="form-control" value="{{$trackingdata->reporting_time}}">
                @error('reporting_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="release_date">Release date</label>
                <input type="date" id="release_date" name="release_date" class="form-control" value="{{$trackingdata->release_date}}">
                @error('release_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
			  
			  <div class="form-group col-md-6">
                <label for="release_time">Release time</label>
                <input type="text" id="release_time" name="release_time" class="form-control" value="{{$trackingdata->release_time}}">
                @error('release_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
			  
			   <div class="form-group col-md-6">
                <label for="detention_days">Detention days</label>
                <input type="number" id="detention_days" name="detention_days" class="form-control" value="{{$trackingdata->detention_days}}">
                @error('detention_days')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
			  
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Tracking Data</button>
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
@endsection