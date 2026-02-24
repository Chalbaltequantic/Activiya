@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Truck</h1>
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
              <h3 class="card-title">Add Truck</h3>

            </div>
            <div class="card-body">
			@if(session('error'))
				<div class="alert alert-warning alert-dismissible fade show ">
			<strong>{{session('error')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif
			<form action="{{route('admin.truck_master.store')}}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			
			<div class="row">	
			  <div class="form-group col-md-6">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" class="form-control"  value="{{old('code')}}" placeholder="Enter code" autocomplete=
				"off" required>
				
				@error('code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-6">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" class="form-control"  value="{{old('description')}}" placeholder="Enter description" autocomplete=
				"off" required>
              @error('description')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
			
			  <div class="form-group col-md-6">
                <label for="short_name">Short Name</label>
                <input type="text" id="short_name" name="short_name" class="form-control" value="{{old('short_name')}}" placeholder="Enter short name" required>
				@error('short_name ')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			   <div class="form-group col-md-6">
                <label for="length">Length</label>
                <input type="text" id="length" name="length" class="form-control" value="{{old('length')}}" placeholder="Enterlength" required>
				@error('length')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>

			  <div class="form-group col-md-6">
                <label for="width">Width</label>
                <input type="text" name="width" id="width" class="form-control" placeholder="Enter width" value="{{old('width')}}"  required>
				@error('width')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-6">
					<label for="height">Height</label>
					<input type="text" id="height"  name="height" value="{{old('height')}}" class="form-control" placeholder="Enter height">
					@error('height')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
				<div class="form-group  col-md-6">
					<label for="weight_capacity">Weight capacity(KG)</label>
					<input type="text" id="weight_capacity" name="weight_capacity" class="form-control" value="{{old('weight_capacity')}}" placeholder="Enter weight capacity">					
					@error('weight_capacity')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				 <div class="form-group col-md-6">
					<label for="max_volume_capacity">Max volume capacity(CFT)</label>
					<input  type="text" id="max_volume_capacity" name="max_volume_capacity" value="{{old('max_volume_capacity')}}" autocomplete="off" class="form-control" placeholder="" readonly>
					
					@error('max_volume_capacity')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div> 
				  
				  <div class="form-group col-md-6">
					<label for="utilities">Utilities(%)</label>
					<input  type="number" id="utilities" name="utilities" value="{{old('utilities')}}" autocomplete="off" class="form-control" placeholder="Enter utilities">
					
					@error('utilities')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				  <div class="form-group col-md-6">
					<label for="min_capacity">Min capacity</label>
					<input  type="text" id="min_capacity" name="min_capacity" value="{{old('min_capacity')}}" autocomplete="off" class="form-control" placeholder="Enter min capacity" readonly>
					@error('min_capacity')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				  <div class="form-group col-md-6">
					<label for="t_body">T body</label>
					<input  type="text" id="t_body" name="t_body" value="{{old('t_body')}}" autocomplete="off" class="form-control" placeholder="Enter T body">
					@error('t_body')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				
								  
              <div class="form-group col-md-6">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">             
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>               
                </select>
              </div>
              </div>
			  <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Add Truck</button>
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
<script type="text/javascript">

function calculateVolumes() {
    let length = parseFloat(document.getElementById('length').value) || 0;
    let width = parseFloat(document.getElementById('width').value) || 0;
    let height = parseFloat(document.getElementById('height').value) || 0;
    let utilities = parseFloat(document.getElementById('utilities').value) || 0;

    let maxVolume = length * width * height;
    document.getElementById('max_volume_capacity').value = maxVolume.toFixed(2);

    let minCapacity = maxVolume * (utilities / 100);
    document.getElementById('min_capacity').value = minCapacity.toFixed(2);
}

document.getElementById('length').addEventListener('input', calculateVolumes);
document.getElementById('width').addEventListener('input', calculateVolumes);
document.getElementById('height').addEventListener('input', calculateVolumes);
document.getElementById('utilities').addEventListener('input', calculateVolumes);

// Trigger calculation on page load (for edit form)
window.addEventListener('DOMContentLoaded', calculateVolumes);

</script>
@endsection