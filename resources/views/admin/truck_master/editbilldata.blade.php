@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Bill Data</h1>
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
              <h3 class="card-title">Edit Bill Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/billdata/updatebilldata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			  <div class="form-group col-md-6">
                <label for="consignor_name">Consignor name</label>
                <input type="text" id="consignor_name" name="consignor_name" value="{{$billdata->consignor_name}}" class="form-control">				
				@error('title')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div><input type="hidden" id="id" name="id" value="{{$billdata->id}}" class="form-control">
              <div class="form-group col-md-6">
                <label for="consignor_code">Consignor code</label>
                <input type="text" id="consignor_code" name="consignor_code" value="{{$billdata->consignor_code}}" class="form-control">
				
				@error('consignor_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			   <div class="form-group col-md-6">
                <label for="consignor_location">Consignor location</label>
                <input type="text" id="consignor_location" name="consignor_location" class="form-control" value="{{$billdata->consignor_location}}">
				@error('consignor_location')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              <div class="form-group col-md-6">
                <label for="inputDescription">S5 consignor short name & location</label>
                <input type="text" id="s5_consignor_short_name_and_location" name="s5_consignor_short_name_and_location" class="form-control tinymce_editor" value="{{$billdata->s5_consignor_short_name_and_location}}"></textarea>
				@error('s5_consignor_short_name_and_location')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="consignee_name">consignee_name</label>
                <input type="text" name="consignee_name" id="consignee_name" class="form-control" value="{{$billdata->consignee_name}}">
				@error('consignee_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-6">
                <label for="consignee_code">Consignee code</label>
                <input type="text" id="consignee_code" name="consignee_code" class="form-control" value="{{$billdata->consignee_code}}">
                  @error('consignee_code')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-6">
                <label for="consignee_location">Consignee location</label>
                <input type="text" id="consignee_location" name="consignee_location" class="form-control" value="{{$billdata->consignee_location}}">
                @error('consignee_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  

		   <div class="form-group col-md-6">
                <label for="d5_consignor_short_name_and_location">D5 consignor short name & location</label>
                <input type="text" id="d5_consignor_short_name_and_location" name="d5_consignor_short_name_and_location" class="form-control" value="{{$billdata->d5_consignor_short_name_and_location}}">
                @error('d5_consignor_short_name_and_location')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="t_code">T code</label>
                <input type="text" id="t_code" name="t_code" class="form-control" value="{{$billdata->t_code}}">
                @error('t_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
			 <div class="form-group col-md-6">
                <label for="truck_type">Truck type</label>
                <input type="text" id="truck_type" name="truck_type" class="form-control" value="{{$billdata->truck_type}}">
                @error('truck_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>	


              <div class="form-group col-md-6">
                <label for="lr_no">LR No</label>
                <input type="text" id="lr_no" name="lr_no" class="form-control" value="{{$billdata->lr_no}}">
                @error('lr_no')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="lr_cn_date">LR cn date</label>
                <input type="text" id="lr_cn_date" name="lr_cn_date" class="form-control" value="{{$billdata->lr_cn_date}}">
                @error('lr_cn_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="lr_cn_date">A amount</label>
                <input type="text" id="a_amount" name="a_amount" class="form-control" value="{{$billdata->a_amount}}">
                @error('a_amount')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>

			  <div class="form-group col-md-6">
                <label for="ref2">Ref2</label>
                <input type="text" id="ref2" name="ref2" class="form-control" value="{{$billdata->ref2}}">
                @error('ref2')
                <span class="text-danger">{{$message}}</span>
                @enderror
			  </div>
			<div class="form-group col-md-6">
                <label for="ref3">Ref3</label>
                <input type="text" id="ref3" name="ref3" class="form-control" value="{{$billdata->ref3}}">
                @error('ref3')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			<div class="form-group col-md-6">
                <label for="freight_type">Freight type</label>
                <input type="text" id="freight_type" name="freight_type" class="form-control" value="{{$billdata->freight_type}}">
                @error('freight_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="ap_status">AP status</label>
                <input type="text" id="ap_status" name="ap_status" class="form-control" value="{{$billdata->ap_status}}">
                @error('ap_status')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			<div class="form-group col-md-6">
                <label for="custom">Custom</label>
                <input type="text" id="custom" name="custom" class="form-control" value="{{$billdata->custom}}">
                @error('custom')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>
			
			  <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($billdata->status==1) selected @endif>Active</option>       
                    <option value="0" @if($billdata->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Bill Data</button>
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