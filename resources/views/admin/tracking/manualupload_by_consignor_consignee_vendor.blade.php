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
            <h1 class="m-0">Tracking data Update</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Tracking data update</li>
				
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
				 <div class="card-header p-2">
					<ul class="nav nav-pills">
					  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Update Tracking</a></li>
					  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated Tracking</a></li>

					</ul>
				  </div><!-- /.card-header -->
              <div class="card-body p-0">
			  <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				  <form method="POST" action="{{ route('admin.trackingdata.updateMultipleTrackingByvenconsign') }}">
								@csrf
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
						  <thead>

							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1">Indent No</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Customer<br />PO No.</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">Origin</th>
								<th style="background: #ddebf7; color: #0070c0;z-index:999;" class="sticky-col-4">Destination</th> 
								<th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
								<th style="background: #fce4d6; color: #0070c0;">Vendor code</th>
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
							  
							  <th style="background: #c6e0b4; color: #0070c0;">Reporting<br> date</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Reporting<br> time</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Release<br> date</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Release<br>time</th>
							  
							  
							</tr>
						  </thead>
						  <tbody>
							@php
								$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
								
							@endphp
							  @php($i=1)
							  @if(count($trackingdatalist) > 0)
							  @foreach($trackingdatalist as $trackingdata)
									  
							<tr class="{{ in_array($trackingdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-entry-id="{{ $trackingdata->id }}">					  
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
							  <td>{{ $trackingdata->shipment_status }}</td>
							  <td>{{ $trackingdata->transit_status }}</td>
							  <td>{{ $trackingdata->distance_covered }}</td>
							  <td>{{ $trackingdata->current_location }}</td>
							  <td>{{ $trackingdata->distance_to_cover }}</td>
							  <td>{{ $trackingdata->tracking_link }}</td>
							  
								<td><input type="text" name="data[{{ $loop->index }}][reporting_date]" value="{{ $trackingdata->reporting_date }}"></td>

								<td><input type="text" name="data[{{ $loop->index }}][reporting_time]" value="{{ $trackingdata->reporting_time }}"></td>

								<td><input type="text" name="data[{{ $loop->index }}][release_date]" value="{{ $trackingdata->release_date }}"></td>

								<td><input type="text" name="data[{{ $loop->index }}][release_time]" value="{{ $trackingdata->release_time }}"></td>


							  <input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $trackingdata->id }}">
							  
							   <input type="hidden" name="data[{{ $loop->index }}][indent_no]" value="{{ $trackingdata->indent_no }}">
							</tr>
							  
							  @endforeach
							  @endif
					  
							</tbody>
							<tr><td colspan="20"></td>
								<td colspan="2"> <button type="submit" class="btn btn-primary">Submit</button></td>
								</tr>
						</table>
						</form>
				 </div>
				 </div>
				 
				  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						 
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
						  <thead>

							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1">Indent No</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Customer<br />PO No.</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">Origin</th>
								<th style="background: #ddebf7; color: #0070c0;z-index:999;" class="sticky-col-4">Destination</th> 
								<th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
								<th style="background: #fce4d6; color: #0070c0;">Vendor code</th>
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
							  <th style="background: #c6e0b4; color: #0070c0;">Destination<br>reporting date</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Destination<br>reporting time</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Destination<br>Release date</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Destination <br>Release time</th>
							  <th style="background: #c6e0b4; color: #0070c0;">Detention <br>Days</th>
							  
							</tr>
						  </thead>
						  <tbody>
							
							  @php($i=1)
							  @if(count($updatedtrackingdatalist) > 0)
							  @foreach($updatedtrackingdatalist as $updatedtrackingdata)
									  
							<tr>					  
							<td class="sticky-col-1">{{$updatedtrackingdata->indent_no}}</td>
							<td class="sticky-col-2">{{$updatedtrackingdata->customer_po_no}}</td>
							<td class="sticky-col-3" class="sticky-col-2 col-width">{{$updatedtrackingdata->origin}}</td>
							<td class="sticky-col-4">{{$updatedtrackingdata->destination}}</td>
							  <td>{{$updatedtrackingdata->vendor_name}}</td>
							  <td>{{$updatedtrackingdata->vendor_code}}</td>
							  <td>{{$updatedtrackingdata->vehicle_type}}</td>
							  <td>{{$updatedtrackingdata->lr_no}}</td>
							  
							  <td>{{$updatedtrackingdata->cases}}</td>
							  <td>{{$updatedtrackingdata->truck_no}}</td>
							  <td>{{$updatedtrackingdata->driver_number}}</td>
							  
							  <td>{{$updatedtrackingdata->dispatch_date}}</td>
							  <td>{{$updatedtrackingdata->dispatch_time}}</td>
							  
							  <td>{{$updatedtrackingdata->lead_time}}</td>
							  
							  <td>{{$updatedtrackingdata->distance}}</td>
							  <td>{{$updatedtrackingdata->delivery_due_date}}</td>
							  <td>{{ $updatedtrackingdata->shipment_status }}</td>
							
								<td>{{ $updatedtrackingdata->transit_status }}</td>
								<td>{{ $updatedtrackingdata->distance_covered }}</td>
								<td>{{ $updatedtrackingdata->current_location }}</td>
								<td>{{ $updatedtrackingdata->distance_to_cover }}</td>
								<td>{{ $updatedtrackingdata->tracking_link }}</td>
								<td>{{ $updatedtrackingdata->reporting_date }}</td>
								<td>{{ $updatedtrackingdata->reporting_time }}</td>
								<td>{{ $updatedtrackingdata->release_date }}</td>
								<td>{{ $updatedtrackingdata->release_time }}</td>
								<td>{{ $updatedtrackingdata->detention_days }}</td>
							 
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
		 </div>
		 </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

  @endsection

