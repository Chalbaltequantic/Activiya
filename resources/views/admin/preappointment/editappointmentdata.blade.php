@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Appointment Data</h1>
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
              <h3 class="card-title">Edit Appointment Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/preappointment/updateappointmentdata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			   <div class="form-group col-md-4">
                <label for="company_code">Company Code</label>
                <input type="text" id="company_code" name="company_code" value="{{$appointmentdata->company_code}}" class="form-control">				
				@error('company_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-4">
                <label for="inv_number">Inv Number</label>
                <input type="text" id="inv_number" name="inv_number" value="{{$appointmentdata->inv_number}}" class="form-control">				
				@error('inv_number')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div><input type="hidden" id="id" name="id" value="{{$appointmentdata->id}}" class="form-control">
              <div class="form-group col-md-4">
                <label for="inv_doc_date">Inv Doc Date</label>
                <input type="text" id="inv_doc_date" name="inv_doc_date" value="{{$appointmentdata->inv_doc_date}}" class="form-control">
				
				@error('inv_doc_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-4">
                <label for="po_no">PO Number</label>
                <input type="text" id="po_no" name="po_no" value="{{$appointmentdata->po_no}}" class="form-control">				
				@error('po_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              <div class="form-group col-md-4">
                <label for="po_date">PO Date</label>
                <input type="text" id="po_date" name="po_date" value="{{$appointmentdata->po_date}}" class="form-control">
				
				@error('po_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			  
              
			   <div class="form-group col-md-">
                <label for="po_no">PO No</label>
                <input type="text" id="po_no" name="po_no" class="form-control" value="{{$appointmentdata->po_no}}">
				@error('po_no')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              <div class="form-group col-md-4">
                <label for="po_date">PO Date</label>
                <input type="text" id="po_date" name="po_date" class="form-control" value="{{$appointmentdata->po_date}}">
				@error('po_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-4">
                <label for="consignor_code">Consignor Code</label>
                <input type="text" name="consignor_code" id="consignor_code" class="form-control" value="{{$appointmentdata->consignor_code}}">
				@error('consignor_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-4">
                <label for="consignor_name">Consignor Name</label>
                <input type="text" id="consignor_name" name="consignor_name" class="form-control" value="{{$appointmentdata->consignor_name}}">
                  @error('consignor_name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			  <div class="form-group col-md-4">
                <label for="consignor_location">Consignor Location</label>
                <input type="text" id="consignor_location" name="consignor_location" class="form-control" value="{{$appointmentdata->consignor_location}}">
                  @error('consignor_location')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-4">
                <label for="consignor_short_location">Consignor Short <br />name & Location</label>
                <input type="text" id="consignor_short_location" name="consignor_short_location" class="form-control" value="{{$appointmentdata->consignor_short_location}}">
                @error('consignor_short_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  

		   <div class="form-group col-md-4">
                <label for="consignee_code">Consignee Code</label>
                <input type="text" id="consignee_code" name="consignee_code" class="form-control" value="{{$appointmentdata->consignee_code}}">
                @error('consignee_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="consignee_name">Consignee Name</label>
                <input type="text" id="consignee_name" name="consignee_name" class="form-control" value="{{$appointmentdata->consignee_name}}">
                @error('consignee_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="consignee_location">Consignee Location</label>
                <input type="text" id="consignee_location" name="consignee_location" class="form-control" value="{{$appointmentdata->consignee_location}}">
                @error('consignee_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="consignee_short_location">Consignee short<br> name & location</label>
                <input type="text" id="consignee_short_location" name="consignee_short_location" class="form-control" value="{{$appointmentdata->consignee_short_location}}">
                @error('consignee_short_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  
		  

		<div class="form-group col-md-4">
                <label for="v_code">V Code</label>
                <input type="text" id="v_code" name="v_code" class="form-control" value="{{$appointmentdata->v_code}}">
                @error('v_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="vendor_name">Vendor Name</label>
                <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="{{$appointmentdata->vendor_name}}">
                @error('vendor_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="cases_sale">No of <br>Cases sale</label>
                <input type="text" id="cases_sale" name="cases_sale" class="form-control" value="{{$appointmentdata->cases_sale}}">
                @error('cases_sale')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>	

		  
		  <div class="form-group col-md-4">
                <label for="shipment_inv_value">Shipment <br />Inv Value</label>
                <input type="text" id="shipment_inv_value" name="shipment_inv_value" class="form-control" value="{{$appointmentdata->shipment_inv_value}}">
                @error('shipment_inv_value')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="delivery_gross_weight">Delivery<br />Gross weight</label>
                <input type="text" id="delivery_gross_weight" name="delivery_gross_weight" class="form-control" value="{{$appointmentdata->delivery_gross_weight}}">
                @error('delivery_gross_weight')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-4">
                <label for="company_code">Company<br />Code</label>
                <input type="text" id="company_code" name="company_code" class="form-control" value="{{$appointmentdata->company_code}}">
                @error('company_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  
		  <div class="form-group col-md-4">
                <label for="remarks">Remarks</label>
                <input type="text" id="remarks" name="remarks" class="form-control" value="{{$appointmentdata->remarks}}">
                @error('remarks')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>

			
			  <div class="form-group col-md-4">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($appointmentdata->status==1) selected @endif>Active</option>       
                    <option value="0" @if($appointmentdata->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Appointment Data</button>
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