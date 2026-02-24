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
            <h1 class="m-0">Vendor mapping</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Vendor mapping</li>
				
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
            </div>
          </div>
		</div>
        <!-- /.row -->
		 <div class="row">
          <div class="col-lg-12">
            <div class="card">
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<table class="table table-bordered border-dark table-hover">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Operation Type</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Company code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Code</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">SubVendor code</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created date</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">status </th>
						   @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
						   @endif	
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($mappingdatalist) > 0)
						  @foreach($mappingdatalist as $mappingdata)
					  
					   <tr>
						  <td class="sticky-col-1 col-width">{{$mappingdata->operation_type}}</td>
						  <td class="sticky-col-2 col-width">{{$mappingdata->company_code}}</td>
						  <td>{{$mappingdata->consignor_code}}</td>
						  <td>{{$mappingdata->consignee_code}}</td>
						  <td>{{$mappingdata->vendor_code}}</td>
						  <td>{{$mappingdata->subvendor_code}}</td>
						 
						  <td>{{$mappingdata->created_at}}</td>
						    <td>{!! ($mappingdata->status == 1)?"<span class='badge bg-success'>Active</span>":"<span class='badge bg-warning'>Inactive</span>" !!}</td>
						  @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <td><a class="btn btn-info btn-sm" href="{{url('admin/mapping/editmappingdata/'.$mappingdata->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="{{url('admin/deletemapping/'.$mappingdata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a></td>
						  @endif
						  
						</tr>
						  
             	  @endforeach
				  @else
					  <tr><td colspan="7">No data found</td></tr>
				  @endif
				  
               </tbody>
          </table>
				</div>
            </div>
          </div>
		 </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

  @endsection

