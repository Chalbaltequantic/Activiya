@extends('admin.admin')
@section('bodycontent')
 <style>
    .table-responsive-fixed {
      overflow-x: auto;
      position: relative;
    }

    table {
      min-width: max-content;
      font-size: 12px;
    }

    .consign-data-table th, .consign-data-table td {
      white-space: nowrap;
      vertical-align: middle;
    }

    .consign-data-table thead th {
      position: sticky;
      top: 0;
      background: #f8f9fa;
    }

    .consign-data-table .table th, .consign-data-table .table td {
      padding: 5px 10px;
    }

    /* Sticky columns */
    .sticky-col-1 {
      position: sticky;
      left: 0;
      background: #fff;
      z-index: 99;
    }

    .sticky-col-2 {
      position: sticky;
      left: 160px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
      min-width: 160px;
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
      }
    }
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Truck List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Truck master </li>
				
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
	  <div class="col-md-10">
	  <a href="{{ route('admin.truck_master.create') }}" class="btn btn-primary mb-3">Add Truck</a></div>
	  </div>
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
			
            </div>

          </div>
		  
		  
		</div>
        <!-- /.row -->
		
		
		 <div class="row">
          <div class="col-lg-12">
            <div class="card">
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
					<table class="table table-bordered border-dark table-hover">
				
					
					
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Description</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Short Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Length</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Width</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Height</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Weight capacity(KG)</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Max Volume Capacity <br>(CFT)</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Min capacity<br>(CFT)</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Utilities(%)</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">T body	</th>
				 
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Status </th>		
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created Date</th>
						 @if(Auth::user() && (Auth::user()->role_id == 1))		
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
					    @endif
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($trucks) > 0)
						  @foreach($trucks as $truck)
					  
					   <tr>
						  <td class="sticky-col-1 col-width">{{ $truck->code}}</td>
						  <td class="sticky-col-2 col-width">{{$truck->description}}</td>
						  <td>{{$truck->short_name}}</td>
						  <td>{{$truck->length}}</td>
						  <td>{{$truck->width}}</td>
						  <td>{{$truck->height}}</td>
						  <td>{{$truck->weight_capacity}}</td>
						  <td>{{$truck->max_volume_capacity}}</td>
						  <td>{{$truck->min_capacity}}</td>
						  <td>{{$truck->utilities}}</td>
						  <td>{{$truck->t_body}}</td>
						  
						  <td> @if($truck->status == 1)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif</td>
						   <td>{{$truck->created_at}}</td>
						  @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <td> 
							<a href="{{ route('admin.truck_master.edit', $truck->id) }}" class="btn btn-warning btn-sm">Edit</a>
							<form action="{{ route('admin.truck_master.destroy', $truck->id) }}" method="POST" style="display:inline;">
								@csrf
								@method('DELETE')
								<button class="btn btn-danger btn-sm" onclick="return confirm('Delete this truck?')">Delete</button>
							</form>
							</td>
						  @endif
						</tr>
						  
             	  @endforeach
				  @else
					  <tr><td colspan="13">No data found</td></tr>
				  @endif
				  
               </tbody>
          </table>
		    
        </div>
       
            </div>

           
          </div>
		  
		  
		    </div>
       
		
		</div>
		
		
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->


	 

  @endsection