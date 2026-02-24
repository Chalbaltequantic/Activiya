@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Site Plant</h1>
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
              <h3 class="card-title">Edit Site Plant Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/siteplant/updatesiteplantdata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			  <div class="form-group col-md-6">
                <label for="plant_site_code">PLANT Site Code</label>
                <input type="text" id="plant_site_code" name="plant_site_code" value="{{$siteplantdata->plant_site_code}}" class="form-control">				
				@error('plant_site_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div><input type="hidden" id="id" name="id" value="{{$siteplantdata->id}}" class="form-control">
              <div class="form-group col-md-6">
                <label for="plant_site_location_name">Plant/Site Location Name</label>
                <input type="text" id="plant_site_location_name" name="plant_site_location_name" value="{{$siteplantdata->plant_site_location_name}}" class="form-control">
				
				@error('plant_site_location_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			   <div class="form-group col-md-6">
                <label for="site_code">Site Code</label>
                <input type="text" id="site_code" name="site_code" class="form-control" value="{{$siteplantdata->site_code}}">
				@error('site_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              <div class="form-group col-md-6">
                <label for="plant_site_name">Plant/Site Name</label>
                <input type="text" id="plant_site_name" name="plant_site_name" class="form-control tinymce_editor" value="{{$siteplantdata->plant_site_name}}"></textarea>
				@error('plant_site_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="street_house_number">Street/House number</label>
                <input type="text" name="street_house_number" id="street_house_number" class="form-control" value="{{$siteplantdata->street_house_number}}">
				@error('street_house_number')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-6">
                <label for="street1">STREET1</label>
                <input type="text" id="street1" name="street1" class="form-control" value="{{$siteplantdata->street1}}">
                  @error('street1')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-6">
                <label for="street2">STREET2</label>
                <input type="text" id="street2" name="street2" class="form-control" value="{{$siteplantdata->street2}}">
                @error('street2')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  

		   <div class="form-group col-md-6">
                <label for="city">CITY</label>
                <input type="text" id="city" name="city" class="form-control" value="{{$siteplantdata->city}}">
                @error('city')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="post_code">Post code</label>
                <input type="text" id="post_code" name="post_code" class="form-control" value="{{$siteplantdata->post_code}}">
                @error('post_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
			 <div class="form-group col-md-6">
                <label for="state_code">State code</label>
                <input type="text" id="state_code" name="state_code" class="form-control" value="{{$siteplantdata->state_code}}">
                @error('state_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>	


              <div class="form-group col-md-6">
                <label for="state_name">State Name</label>
                <input type="text" id="state_name" name="state_name" class="form-control" value="{{$siteplantdata->state_name}}">
                @error('state_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="pan_no">PAN number</label>
                <input type="text" id="pan_no" name="pan_no" class="form-control" value="{{$siteplantdata->pan_no}}">
                @error('pan_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="food_license_no">Food License No.</label>
                <input type="text" id="food_license_no" name="food_license_no" class="form-control" value="{{$siteplantdata->food_license_no}}">
                @error('food_license_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>

			  <div class="form-group col-md-6">
                <label for="food_license_expiry">Food License Expiry</label>
                <input type="text" id="food_license_expiry" name="food_license_expiry" class="form-control" value="{{$siteplantdata->food_license_expiry}}">
                @error('food_license_expiry')
                <span class="text-danger">{{$message}}</span>
                @enderror
			  </div>
			<div class="form-group col-md-6">
                <label for="gstin_number">GSTIN Number</label>
                <input type="text" id="gstin_number" name="gstin_number" class="form-control" value="{{$siteplantdata->gstin_number}}">
                @error('gstin_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="site_executive_name">Site Executive Name</label>
                <input type="text" id="site_executive_name" name="site_executive_name" class="form-control" value="{{$siteplantdata->site_executive_name}}">
                @error('site_executive_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_executive_contact_no">Site Executive Contact No</label>
                <input type="text" id="site_executive_contact_no" name="site_executive_contact_no" class="form-control" value="{{$siteplantdata->site_executive_contact_no}}">
                @error('site_executive_contact_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_executive_mail_id">Site Executive Mail ID</label>
                <input type="text" id="site_executive_mail_id" name="site_executive_mail_id" class="form-control" value="{{$siteplantdata->site_executive_mail_id}}">
                @error('site_executive_mail_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_incharge_name">Site Incharge Name</label>
                <input type="text" id="site_incharge_name" name="site_incharge_name" class="form-control" value="{{$siteplantdata->site_incharge_name}}">
                @error('site_incharge_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_incharge_contact_no">Site Incharge Contact No.</label>
                <input type="text" id="site_incharge_contact_no" name="site_incharge_contact_no" class="form-control" value="{{$siteplantdata->site_incharge_contact_no}}">
                @error('site_incharge_contact_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_incharge_mail_id">Site Incharge Mail ID</label>
                <input type="text" id="site_incharge_mail_id" name="site_incharge_mail_id" class="form-control" value="{{$siteplantdata->site_incharge_mail_id}}">
                @error('site_incharge_mail_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_manager_name">Site Manager Name</label>
                <input type="text" id="site_manager_name" name="site_manager_name" class="form-control" value="{{$siteplantdata->site_manager_name}}">
                @error('site_manager_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="site_manager_contact_no">Site Manager Contact No.</label>
                <input type="text" id="site_manager_contact_no" name="site_manager_contact_no" class="form-control" value="{{$siteplantdata->site_manager_contact_no}}">
                @error('site_manager_contact_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="site_manager_mail_id">Site Manager Mail ID</label>
                <input type="text" id="site_manager_mail_id" name="site_manager_mail_id" class="form-control" value="{{$siteplantdata->site_manager_mail_id}}">
                @error('site_manager_mail_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="company_code">Company Code</label>
                <input type="text" id="company_code" name="company_code" class="form-control" value="{{$siteplantdata->company_code}}">
                @error('company_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="company_type">Company Type</label>
                <input type="text" id="company_type" name="company_type" class="form-control" value="{{$siteplantdata->company_type}}">
                @error('company_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
						
			  <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($siteplantdata->status==1) selected @endif>Active</option>       
                    <option value="0" @if($siteplantdata->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Site Plant Data</button>
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