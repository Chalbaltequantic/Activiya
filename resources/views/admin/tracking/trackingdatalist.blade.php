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
      left: 85px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 158px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 212px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Tracking data History</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Tracking data History</li>
				
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
					<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>

						<tr>
							<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1">Indent No</th>
							<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Customer<br />PO No.</th>
							<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">Origin</th>
							<th style="background: #ddebf7; color: #0070c0;z-index:999;" class="sticky-col-4">Destination</th> 
							<th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
							<th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
							<th style="background: #fce4d6; color: #0070c0;">Type of<br> vehicle</th>
							<th style="background: #fce4d6; color: #0070c0;">LR No</th>
						  <th style="background: #fce4d6; color: #0070c0;">Cases</th>
						  <th style="background: #fce4d6; color: #0070c0;">Truck<br>No</th>
						  <th style="background: #fce4d6; color: #0070c0;">Driver<br>Mobile No</th>
						  
						<th style="background: #fce4d6; color: #0070c0;">Dispatch Date</th>
						
						<th style="background: #fce4d6; color: #0070c0;">Dispatch Time</th>
						 <th style="background: #ddebf7; color: #0070c0;">Lead Time</th> 
						
						  <th style="background: #fce4d6; color: #0070c0;">Distance<br>in km</th>
						  <th style="background: #fce4d6; color: #0070c0;">Delivery<br>Due Date</th>
						  <th style="background: #fce4d6; color: #0070c0;">Shipment Status</th>
						  <th style="background: #fce4d6; color: #0070c0;">Transit Status</th>
						  <th style="background: #fce4d6; color: #0070c0;">Distance<br>Covered (KM)</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Current<br>Location</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Distance to<br>be covered (KM)</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Tracking Links</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Reporting Date</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Reporting Time</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Release Date</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Release Time</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Detention Days </th>
						  <th style="background: #c6e0b4; color: #0070c0;">Created_date</th>
						  @if(Auth::user() && (Auth::user()->role_id == 1))
						  <th style="background: #fce4d6; color: #0070c0;">Action</th>
						@endif
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($trackingdatalist) > 0)
						  @foreach($trackingdatalist as $trackingdata)
					  
					   <tr>
						<td class="sticky-col-1">{{$trackingdata->indent_no}}</td>
						<td class="sticky-col-2">{{$trackingdata->customer_po_no}}</td>
						<td class="sticky-col-3" class="sticky-col-2 col-width">{{$trackingdata->origin}}</td>
						<td class="sticky-col-4">{{$trackingdata->destination}}</td>
						  <td>{{$trackingdata->vendor_name}}</td>
						  <td>{{$trackingdata->vendor_code}}</td>
						  <td>{{$trackingdata->vehicle_type}}</td>
						  <td>{{$trackingdata->lr_no}}</td>
						  
						  <td>{{$trackingdata->cases}}</td>
						  <td>{{$trackingdata->truck_no}}</td>
						  <td>{{$trackingdata->driver_number}}</td>
						  
						  <td>{{$trackingdata->dispatch_date}}</td>
						  <td>{{$trackingdata->dispatch_time}}</td>
						  
						  <td>{{$trackingdata->lead_time}}</td>
						  
						  <td>{{$trackingdata->distance}}</td>
						  <td>{{$trackingdata->delivery_due_date}}</td>
						  <td>{{$trackingdata->shipment_status}}</td>
						  <td>{{$trackingdata->transit_status}}</td>
						  <td>{{$trackingdata->distance_covered}}</td>
						  <td>{{$trackingdata->current_location}}</td>
						  <td>{{$trackingdata->distance_to_cover}}</td>
						  <td>{{$trackingdata->tracking_link}}</td>
						  <td>{{$trackingdata->reporting_date}}</td>
						  <td>{{$trackingdata->reporting_time}}</td>
						  <td>{{$trackingdata->release_date}}</td>
						  <td>{{$trackingdata->release_time}}</td>
						  <td>{{$trackingdata->detention_days}}</td>
						  <td>{{$trackingdata->created_at}}</td>
						  @if(Auth::user() && (Auth::user()->role_id == 1))				  
						  <td>
							
							  <a class="btn btn-info btn-sm" href="{{url('admin/trackingdata/edittrackingdata/'.$trackingdata->id)}}">
								  <i class="fas fa-pencil-alt">
								  </i>
								  Edit
							  </a>
							  <a class="btn btn-danger btn-sm" href="{{url('admin/deletetracking/'.$trackingdata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
								  <i class="fas fa-trash">
								  </i>
								  Delete
							  </a>
							
						  </td>
						  @endif
						</tr>
						  
						  @endforeach
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

