@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Employee Mapping Data</h1>
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
              <h3 class="card-title">Edit Eployee Mapping Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/employeemapping/update') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			 <input type="hidden" id="id" name="id" value="{{$mappingdata->id}}">
              
			   <div class="form-group col-md-6">
                <label for="company_code">Company code</label>
                <input type="text" id="company_code" name="company_code" value="{{$mappingdata->company_code}}" class="form-control">
				
				@error('company_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-6">
                <label for="consignor_code">Consignor code</label>
                <input type="text" id="consignor_code" name="consignor_code" value="{{$mappingdata->consignor_code}}" class="form-control">
				
				@error('consignor_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			   <div class="form-group col-md-6">
                <label for="consignee_code">Consigee Code</label>
                <input type="text" id="consignee_code" name="consignee_code" class="form-control" value="{{$mappingdata->consignee_code}}">
				@error('consignee_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              
			    <div class="form-group col-md-6">
                <label for="vendor_code">Vendor code</label>
                <input type="text" id="vendor_code" name="vendor_code" class="form-control" value="{{$mappingdata->vendor_code}}">
                  @error('vendor_code')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-6">
                <label for="subvendor_code">Subvendor code</label>
                <input type="text" id="subvendor_code" name="subvendor_code" class="form-control" value="{{$mappingdata->subvendor_code}}">
                @error('subvendor_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div> 
			<div class="form-group col-md-6">
                <label for="employee_code">Employee code</label>
                <input type="text" id="employee_code" name="employee_code" class="form-control" value="{{$mappingdata->employee_code}}">
                @error('employee_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  	

			  <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($mappingdata->status==1) selected @endif>Active</option>       
                    <option value="0" @if($mappingdata->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Employee Mapping Data</button>
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