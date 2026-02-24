@extends('admin.admin')
@section('bodycontent')
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Spot By Upload </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Spot By </li>
				
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
			@if(session('error'))
			<div class="alert alert-warning alert-dismissible fade show ">
			<strong>{{session('error')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif	
			@if(session('errorRows'))
				<div class="alert alert-warning">
					<strong>Rows with errors:</strong>
					<ul>
						@foreach(session('errorRows') as $error)
							<li>Row {{ $error['row'] }}: {{ $error['reason'] }}</li>
						@endforeach
					</ul>
				</div>
			@endif			
			
              <div class="card-body">
				<div class="row">
					<div class="form-group col-md-6">
						 <form action="{{ route('admin.spotbyimport') }}" method="POST" enctype="multipart/form-data">
							@csrf	
							
							<label for="excel_file">Import Spot By Data</label>
							<input type="file" name="excel_file" id="excel_file" required>
								<button type="submit" class="btn btn-primary">Import</button>
							
						</form>
					</div>
					<div class="form-group col-md-4 border-left text-right">
							<label for="excel_file"> &nbsp; </label>
							<a href="{{route('admin.spotbymanualupload')}}" class="btn btn-warning">Manual Upload Spot By Data</a>
					</div>
				</div>
				
				
				<div class="row">
				<div class="form-group col-md-6"><a href="{{ URL::asset('consignmentapp_SampleDATA/spotby.xlsx') }}">Download Sample Data</a></div>
				</div>
			  </div>
            </div>
          </div>
		</div>
        <!-- /.row -->
		</div>
	</div>
    <!-- /.content -->

@endsection