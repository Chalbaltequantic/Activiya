@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Spotby Data</h1>
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
              <h3 class="card-title">Edit Spotby Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/spotby/updatespotby') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			  <div class="form-group col-md-6">
                <label for="from">From</label>
                <input type="text" id="from" name="from" value="{{$spotby->from}}" class="form-control">				
				@error('from')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div><input type="hidden" id="id" name="id" value="{{$spotby->id}}" class="form-control">
              <div class="form-group col-md-6">
                <label for="to">To</label>
                <input type="text" id="to" name="to" value="{{$spotby->to}}" class="form-control">
				
				@error('to')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
              
			   <div class="form-group col-md-6">
                <label for="vehicle_type">Vehicle type</label>
                <input type="text" id="vehicle_type" name="vehicle_type" class="form-control" value="{{$spotby->vehicle_type}}">
				@error('vehicle_type')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
              <div class="form-group col-md-6">
                <label for="valid_from">Valid from</label>
                <input type="text" id="valid_from" name="valid_from" class="form-control tinymce_editor" value="{{$spotby->valid_from}}"></textarea>
				@error('valid_from')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="valid_upto">Valid upto</label>
                <input type="text" name="valid_upto" id="valid_upto" class="form-control" value="{{$spotby->valid_upto}}">
				@error('valid_upto')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			    <div class="form-group col-md-6">
                <label for="no_of_vehicles">No of vehicles</label>
                <input type="text" id="no_of_vehicles" name="no_of_vehicles" class="form-control" value="{{$spotby->no_of_vehicles}}">
                  @error('no_of_vehicles')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
			<div class="form-group col-md-6">
                <label for="goods_qty">Goods qty</label>
                <input type="text" id="goods_qty" name="goods_qty" class="form-control" value="{{$spotby->goods_qty}}">
                @error('goods_qty')
                <span class="text-danger">{{$message}}</span>
                @enderror
			</div>  

		   <div class="form-group col-md-6">
                <label for="uom">UOM</label>
                <input type="text" id="uom" name="uom" class="form-control" value="{{$spotby->uom}}">
                @error('uom')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="loading_charges">Loading charges</label>
                <input type="text" id="loading_charges" name="loading_charges" class="form-control" value="{{$spotby->loading_charges}}">
                @error('loading_charges')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
			 <div class="form-group col-md-6">
                <label for="unloading_charges">Unloading charges</label>
                <input type="text" id="unloading_charges" name="unloading_charges" class="form-control" value="{{$spotby->unloading_charges}}">
                @error('unloading_charges')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>	


              <div class="form-group col-md-6">
                <label for="special_instruction">Special instruction</label>
                <input type="text" id="special_instruction" name="special_instruction" class="form-control" value="{{$spotby->special_instruction}}">
                @error('special_instruction')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="rfq_start_date_time">RFQ start date & time</label>
                <input type="text" id="rfq_start_date_time" name="rfq_start_date_time" class="form-control" value="{{$spotby->rfq_start_date_time}}">
                @error('rfq_start_date_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>
		  <div class="form-group col-md-6">
                <label for="rfq_end_date_time">RFQ end date time</label>
                <input type="text" id="rfq_end_date_time" name="rfq_end_date_time" class="form-control" value="{{$spotby->rfq_end_date_time}}">
                @error('rfq_end_date_time')
                <span class="text-danger">{{$message}}</span>
                @enderror
          </div>

			  <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($spotby->status==1) selected @endif>Active</option>       
                    <option value="0" @if($spotby->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update spotby data</button>
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