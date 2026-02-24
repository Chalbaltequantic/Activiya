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
      left: 135px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 280px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 380px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
     /* min-width: 160px;*/
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
      }
    }
	
.table-container {
    max-height: 400px;   /* Set your desired table height */
    overflow-y: auto;
    border: 1px solid #ccc;
}

#input-table {
    border-collapse: collapse;
    width: 100%;
    min-width: 1200px; /* Optional: ensures columns don't shrink too much */
}

#input-table th,
#input-table td {
    min-width: 120px;
    padding: 8px;
    border: 1px solid #ccc;
    background: #fff;
    text-align: left;
}

#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}	
	
	
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Consignee Return Duration List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Consignee Return Duration List</li>
				
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
			<div class="card-header">
                <a href="{{ route('admin.returnmanualupload') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
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
          <div class="col-lg-8">
            <div class="card">
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<table class="table table-bordered border-dark table-hover">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Return Time (Minutes)</th>
						   @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
					     @endif
						  
						</tr>
					  </thead>
					  <tbody>
				  
						 
						  @if(count($durations) > 0)
						   @foreach($durations as $index => $duration)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $duration->consignee_code }}</td>
                                <td>{{ $duration->consignee_name }}</td>
                                <td>{{ $duration->return_time_minutes }}</td>
								 @if(Auth::user() && (Auth::user()->role_id == 1))	
                                <td>
                                     <a href="{{ route('consignee-return-duration.edit', $duration->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('consignee-return-duration.destroy', $duration->id) }}" 
                                          method="POST" style="display:inline-block;"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                   
                                </td>
								@endif
                            </tr>
                        @endforeach
				  @else
					  <tr><td colspan="4">No data found</td></tr>
				  @endif
				  
               </tbody>
          </table>
				</div>
            </div>
          </div>
		 </div>
      </div><!-- /.container-fluid -->
    </div>
	</div>
    <!-- /.content -->

  @endsection

