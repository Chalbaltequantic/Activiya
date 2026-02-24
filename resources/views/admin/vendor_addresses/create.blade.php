@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Vendor Address for  {{ $vendor->vendor_name }}</h1>
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
				  <h3 class="card-title">Add Vendor Address</h3>
				</div>
            <div class="card-body">
			<form method="POST" action="{{ route('admin.vendor-addresses.store', $vendor->id) }}"  enctype="multipart/form-data">			
              @csrf
			  
			<div class="row">
			<div class="form-group col-md-3">
                <label for="address_line1">Address Type</label>
				<select name="address_type" id="address_type" class="form-control">
					<option value="">Select Address Type</option>
					<option value="Registered">Registered</option>
					<option value="Billing">Billing</option>
					<option value="Branch">Branch</option>
				</select>
				
				@error('address_type')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>	
			
			  <div class="form-group col-md-3">
                <label for="address_line1">Address Line 1</label>
                <input type="text" id="address_line1" name="address_line1" class="form-control" value="{{old('address_line1')}}" placeholder="Enter address line 1" autocomplete=
				"off" required>
				
				@error('address_line1')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-3">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" class="form-control"  value="{{old('address_line2')}}" placeholder="Enter address line2" autocomplete=
				"off">
              @error('address_line2')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>			
			  <div class="form-group col-md-3">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control" value="{{old('city')}}" placeholder="Enter city" required>
				@error('company_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			   <div class="form-group col-md-3">
                <label for="state">State</label>
                <input type="text" id="state" name="state" class="form-control" value="{{old('state')}}" placeholder="Enter state" required>
				@error('state')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-3">
                <label for="state_code">State Code</label>
                <input type="text" id="state_code" name="state_code" class="form-control" value="{{old('state_code')}}" placeholder="Enter state code" required>
				@error('state')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
				<input type="hidden" name="vendorid" value="{{$vendor->id}}">
			  <div class="form-group col-md-3">
                <label for="zip_code">Zip Code</label>
                <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="Enter zip code"  value="{{old('zip_code')}}" required>
				@error('zip_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
				<div class="form-group col-md-3">
					<label for="country">Country</label>
					<input type="text" id="country"  name="country" value="{{old('country')}}" class="form-control" placeholder="Enter country" required>
					@error('country')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
              <div class="form-group col-md-3">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">             
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>               
                </select>
              </div>
			  
              </div>
			  <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Add Address</button>
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