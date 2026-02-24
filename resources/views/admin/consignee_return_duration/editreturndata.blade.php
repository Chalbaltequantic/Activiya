@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Return Duration Data</h1>
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
              <h3 class="card-title">Edit Return Duration Data</h3>

            </div>
            <div class="card-body">
			<form action="{{ url('/admin/consignee-return-duration/updatedata') }}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="row">
			  <div class="form-group">
					<label for="consignee_id">Consignee</label>
					<select name="consignee_id" id="consignee_id" class="form-control" required>
						<option value="">-- Select Consignee --</option>
						@foreach($consignees as $consignee)
							<option value="{{ $consignee->consignee_code }}" 
								{{ $duration->consignee_code == $consignee->consignee_code ? 'selected' : '' }}>
								{{ $consignee->consignee_code }} - {{ $consignee->consignee_name }}
							</option>
						@endforeach
					</select>
				</div><input type="hidden" id="id" name="id" value="{{$duration->id}}" class="form-control">
				
				<div class="form-group">
					<label for="consignee_name">Consignee name</label>
					<input type="text" name="consignee_name" id="consignee_name" 
						   value="{{ old('consignee_name', $duration->consignee_name) }}" 
						   class="form-control" required>
				</div>
				
				<div class="form-group">
					<label for="return_time_minutes">Return Time (in Minutes)</label>
					<input type="number" name="return_time_minutes" id="return_time_minutes" 
					value="{{ old('return_time_minutes', $duration->return_time_minutes) }}" 
					class="form-control" required>
				</div>
              
			   <div class="form-group col-md-6">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{$duration->end_date}}" class="form-control">
				
				@error('end_date')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($duration->status==1) selected @endif>Active</option>       
                    <option value="0" @if($duration->status=0) selected @endif>Inactive</option>       
                </select>
              </div>
			  </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update Return Duration Data</button>
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