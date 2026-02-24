@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Rate Master Data</h1>
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
              <h3 class="card-title">Edit Rate Master Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/ratedata/updateratedata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
				  <div class="form-group col-md-6">
					<label for="consignor_name">Consignor name</label>
					<input type="text" id="consignor_name" name="consignor_name" value="{{$ratedata->consignor_name}}" class="form-control">				
					@error('title')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div><input type="hidden" id="id" name="id" value="{{$ratedata->id}}" class="form-control">
				  <div class="form-group col-md-6">
					<label for="consignor_code">Consignor code</label>
					<input type="text" id="consignor_code" name="consignor_code" value="{{$ratedata->consignor_code}}" class="form-control">
					
					@error('consignor_code')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				   <div class="form-group col-md-6">
					<label for="consignor_location">Consignor location</label>
					<input type="text" id="consignor_location" name="consignor_location" class="form-control" value="{{$ratedata->consignor_location}}">
					@error('consignor_location')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				  <div class="form-group col-md-6">
					<label for="inputDescription">S5 consignor short name & location</label>
					<input type="text" id="s5_consignor_short_name_and_location" name="s5_consignor_short_name_and_location" class="form-control tinymce_editor" value="{{$ratedata->s5_consignor_short_name_and_location}}"></textarea>
					@error('s5_consignor_short_name_and_location')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  <div class="form-group col-md-6">
					<label for="consignee_name">Consignee name</label>
					<input type="text" name="consignee_name" id="consignee_name" class="form-control" value="{{$ratedata->consignee_name}}">
					@error('consignee_name')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
					<div class="form-group col-md-6">
					<label for="consignee_code">Consignee code</label>
					<input type="text" id="consignee_code" name="consignee_code" class="form-control" value="{{$ratedata->consignee_code}}">
					  @error('consignee_code')
					  <span class="text-danger">{{$message}}</span>
					  @enderror
				  </div>
				<div class="form-group col-md-6">
					<label for="consignee_location">Consignee location</label>
					<input type="text" id="consignee_location" name="consignee_location" class="form-control" value="{{$ratedata->consignee_location}}">
					@error('consignee_location')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>  

			   <div class="form-group col-md-6">
					<label for="d5_consignor_short_name_and_location">D5 consignor short name & location</label>
					<input type="text" id="d5_consignor_short_name_and_location" name="d5_consignor_short_name_and_location" class="form-control" value="{{$ratedata->d5_consignor_short_name_and_location}}">
					@error('d5_consignor_short_name_and_location')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			   <div class="form-group col-md-6">
					<label for="mode">MOde</label>
					<input type="text" id="mode" name="mode" class="form-control" value="{{$ratedata->mode}}">
					@error('mode')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			  
			  <div class="form-group col-md-6">
					<label for="logic">Logic</label>
					<input type="text" id="logic" name="logic" class="form-control" value="{{$ratedata->logic}}">
					@error('logic')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			  
			  <div class="form-group col-md-6">
					<label for="vendor_code">Vendor code</label>
					<input type="text" id="vendor_code" name="vendor_code" class="form-control" value="{{$ratedata->vendor_code}}">
					@error('vendor_code')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			  <div class="form-group col-md-6">
					<label for="vendor_name">Vendor name</label>
					<input type="text" id="vendor_name" name="vendor_name" class="form-control" value="{{$ratedata->vendor_name}}">
					@error('vendor_name')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			  
			  <div class="form-group col-md-6">
					<label for="t_code">T code</label>
					<input type="text" id="t_code" name="t_code" class="form-control" value="{{$ratedata->t_code}}">
					@error('t_code')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
				 <div class="form-group col-md-6">
					<label for="truck_type">Truck type</label>
					<input type="text" id="truck_type" name="truck_type" class="form-control" value="{{$ratedata->truck_type}}">
					@error('truck_type')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>	
					<div class="form-group col-md-6">
						<label for="lr_cn_date">A amount</label>
						<input type="text" id="a_amount" name="a_amount" class="form-control" value="{{$ratedata->a_amount}}">
						@error('a_amount')
						<span class="text-danger">{{$message}}</span>
						@enderror
				  </div>


				 <div class="form-group col-md-6">
					<label for="validity_start">Validity start date</label>
					<input type="text" id="validity_start" name="validity_start" class="form-control" value="{{$ratedata->validity_start}}">
					@error('validity_start')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
				<div class="form-group col-md-6">
					<label for="validity_end">Validity end date</label>
					<input type="text" id="validity_end" name="validity_end" class="form-control" value="{{$ratedata->validity_end}}">
					@error('validity_end')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
			  <div class="form-group col-md-6">
					<label for="tat">TAT</label>
					<input type="text" id="tat" name="tat" class="form-control" value="{{$ratedata->tat}}">
					@error('tat')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>
			  <div class="form-group col-md-6">
					<label for="rank">Rank</label>
					<input type="text" id="rank" name="rank" class="form-control" value="{{$ratedata->rank}}">
					@error('rank')
					<span class="text-danger">{{$message}}</span>
					@enderror
			  </div>

			  <div class="form-group col-md-6">
				<label for="distance">Distance</label>
				<input type="text" id="distance" name="distance" class="form-control" value="{{$ratedata->distance}}">
				@error('distance')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			  
			  <div class="form-group col-md-6">
				<label for="custom1">Custom1</label>
				<input type="text" id="custom1" name="custom1" class="form-control" value="{{$ratedata->custom1}}">
				@error('custom1')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			  
			  <div class="form-group col-md-6">
				<label for="custom2">Custom2</label>
				<input type="text" id="custom2" name="custom2" class="form-control" value="{{$ratedata->custom2}}">
				@error('custom2')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			  <div class="form-group col-md-6">
				<label for="custom3">Custom3</label>
				<input type="text" id="custom3" name="custom3" class="form-control" value="{{$ratedata->custom3}}">
				@error('custom3')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			  <div class="form-group col-md-6">
				<label for="custom4">Custom4</label>
				<input type="text" id="custom4" name="custom4" class="form-control" value="{{$ratedata->custom4}}">
				@error('custom4')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			  <div class="form-group col-md-6">
				<label for="custom5">Custom5</label>
				<input type="text" id="custom5" name="custom5" class="form-control" value="{{$ratedata->custom5}}">
				@error('custom5')
				<span class="text-danger">{{$message}}</span>
				@enderror
			  </div>
			
			  <div class="form-group col-md-6">
				<label for="inputStatus">Status</label>
				<select id="inputStatus" name="status" class="form-control custom-select">
					<option value="1" @if($ratedata->status==1) selected @endif>Active</option>       
					<option value="0" @if($ratedata->status=0) selected @endif>Inactive</option>       
				</select>
			  </div>
			</div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Rate Data</button>
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