@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Vendor Accounts for  {{ $vendor->vendor_name }}</h1>
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
               <div class="card">
				<div class="card-header" style="background: #fce4d6; color: #0070c0;">
				  <h3 class="card-title">Add Vendor Accounts</h3>
				</div>
            <div class="card-body">
			<form method="POST" action="{{ route('admin.vendor-bank-accounts.store', $vendor->id) }}"  enctype="multipart/form-data">			
              @csrf
			  
			<div class="row">	
			  <div class="form-group col-md-3">
                <label for="bank_name">Bank Name</label>
                <input type="text" id="bank_name" name="bank_name" class="form-control" value="{{old('bank_name')}}" placeholder="Enter bank name" autocomplete=
				"off" required>
				
				@error('bank_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-3">
                <label for="branch_name">Branch name</label>
                <input type="text" id="branch_name" name="branch_name" class="form-control"  value="{{old('branch_name')}}" placeholder="Enter Branch Name" autocomplete=
				"off">
              @error('branch_name')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>			
			  <div class="form-group col-md-3">
                <label for="account_holder_name">Account Holder Name</label>
                <input type="text" id="account_holder_name" name="account_holder_name" class="form-control" value="{{old('account_holder_name')}}" placeholder="Enter Account Holder Name" required>
				@error('account_holder_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			   <div class="form-group col-md-3">
                <label for="account_number">Account Number</label>
                <input type="text" id="account_number" name="account_number" class="form-control" value="{{old('account_number')}}" placeholder="Enter Account number" required>
				@error('account_number')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
				<input type="hidden" name="vendorid" value="{{$vendor->id}}">
			  <div class="form-group col-md-3">
                <label for="ifsc_code">IFSC Code</label>
                <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" placeholder="Enter ifsc code"  value="{{old('ifsc_code')}}" required>
				@error('ifsc_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
				<div class="form-group col-md-3">
					<label for="account_type">Account type</label>
					<input type="text" id="account_type"  name="account_type" value="{{old('account_type')}}" class="form-control" placeholder="Enter account type">
					@error('account_type')
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
               <button type="submit" name="submit" class="btm btn-primary">Add Account</button>
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