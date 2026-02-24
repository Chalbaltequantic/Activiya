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
      left: 100px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
.sticky-col-3 {
      position: sticky;
      left: 180px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 240px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-5 {
      position: sticky;
      left: 315px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
      min-width: 100px;
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
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
    min-width: 50px;
    padding: 2px;
    border: 0.5px solid #ccc;
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
            <h1 class="m-0">Appointment Request Board</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Appointment Request Board </li>
				
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
					
				@if(session('mismatches') || session('fileErrors') || session('saveErrors'))
					<div class="alert alert-warning alert-dismissible fade show " style="display: none; max-height: 300px; overflow-y: auto;">
						<strong>Errors:</strong>
							<ul>
								
								@foreach((array) session('fileErrors') as $fileError)
									<li>{{ $fileError }}</li>
								@endforeach
								@foreach((array) session('saveErrors') as $saveError)
									<li>{{ $saveError }}</li>
								@endforeach
							</ul>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif			       
            </div>
          </div>
		</div>
        <!-- /.row -->
		 <div class="row">          	  
			<div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Assgn Appointment Date & Time</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Reschedule Appointment Date & Time</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<form id="appointmentsForm">
							@csrf

								<table class="table table-bordered border-dark table-hover" id="table"  style="table-layout:auto; width:auto;">
								  <thead>
									<tr>
										<th style="background: #fce4d6; color: #0070c0;z-index:999; width:90px;" class="sticky-col-1">Invoice Number</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-2">Inv Doc Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">PO No
										</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;"class="sticky-col-4">PO Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:100px;" class="sticky-col-5">Consignor Name</th>

										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Location</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">No. of<br>Packages</th>
										
										<th style="background: #ddebf7; color: #0070c0;" class="col-width">Appointment<br/>Date & Time</th>
										
										<th style="background: #ddebf7; color: #0070c0;" class="col-width">Action</th>
									</tr>
								  </thead>
								  <tbody>
									@php
										$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
										
									@endphp
									  @php($i=1)
									  @if(count($entries) > 0)
									  @foreach($entries as $appointdata)
								  
									<tr class="{{ in_array($appointdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-id="{{ $appointdata->id }}">
										<td class="sticky-col-1">{{$appointdata->inv_number}}</td>
										<td class="sticky-col-2">{{$appointdata->inv_doc_date}}</td>
										<td class="sticky-col-3">{{$appointdata->po_no}}</td>
										<td class="sticky-col-4">{{$appointdata->po_date}}</td>
										<td class="sticky-col-5">{{$appointdata->consignor_name}}</td>
										<td class="">{{$appointdata->consignor_location}}</td>
										
										<td>{{$appointdata->consignee_name}}</td>
										<td>{{$appointdata->consignee_location}}</td>							  
										<td>{{$appointdata->vendor_name}}</td>							  
										<td>{{$appointdata->no_of_cases_sale}}</td>
										
										<td><input type="text" id="appointment_date_{{$appointdata->id}}" name="appointment_date_{{$appointdata->id}}" class="quick-datetime" /></td>
										
										<td><button type="submit" class="btn btn-primary btn-submit">Submit</button></td>	
																								 
										</tr>
									  
							  @endforeach
							 
							  @endif
							  
						   </tbody>
						    
						   
					  </table>
					  
					</form>
					 
					</div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						 
						<table id="appointdataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999; width:90px;" class="sticky-col-1">Invoice Number</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-2">Inv Doc Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">PO No
								</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;"class="sticky-col-4">PO Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:100px;" class="sticky-col-5">Consignor Name</th>

								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Location</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">No. of<br>Packages</th>
								
								<th style="background: #ddebf7; color: #0070c0;" class="col-width">Appointment<br/>Date & Time</th>
								
								<th style="background: #ddebf7; color: #0070c0;" class="col-width">Action</th>

							</tr>
						  </thead>
						<tbody>
							
						  @if(count($updatedentries) > 0)
						  @foreach($updatedentries as $updatedappointdata)
							  
						<tr class="" data-entry-id="{{ $updatedappointdata->id }}">
								<td class="sticky-col-1">{{$updatedappointdata->inv_number}}</td>
								<td class="sticky-col-2">{{$updatedappointdata->inv_doc_date}}</td>
								<td class="sticky-col-3">{{$updatedappointdata->po_no}}</td>
								<td class="sticky-col-4">{{$updatedappointdata->po_date}}</td>
								<td class="sticky-col-5">{{$updatedappointdata->consignor_name}}</td>
								<td class="">{{$updatedappointdata->consignor_location}}</td>

								<td>{{$updatedappointdata->consignee_name}}</td>
								<td>{{$updatedappointdata->consignee_location}}</td>							  
								<td>{{$updatedappointdata->vendor_name}}</td>							  
								<td>{{$updatedappointdata->no_of_cases_sale}}</td>							  
								<td>{{ \Carbon\Carbon::parse($updatedappointdata->appointment_date)->format('M j, Y, g A') }}
								
								</td>
								
								<td>
									@if($updatedappointdata->appointment_status=='Reschedule')
									<button type="submit" class="btn btn-warning btn-reschedule">Reschedule</button>
									else
									{{$updatedappointdata->appointment_status}}
									@endif
								</td>

							</tr>			  
						  @endforeach						 
						  @endif						  
					   </tbody>
					</table>
					</div>

				  </div>
                  <!-- /.tab-pane -->
					</div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
      
  </div><!-- /.container-fluid -->
</div>
</div>
<!-- /.content -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.21/build/jquery.datetimepicker.min.css">

<script src="https://cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.21/build/jquery.datetimepicker.full.min.js"></script>
<script>
$(document).ready(function(){

 $('.quick-datetime').datetimepicker({
        format:'Y-m-d H:i',    // 24-hour format
        step: 1,               // minutes step
        inline:false,
        scrollMonth:false,
        scrollInput:false,
        defaultTime:'09:00',   // optional default
        defaultDate: new Date(),
        onSelectDate: function(ct,$i){
            // When a date is clicked, it automatically sets the current time
            // you can customize this if needed
        },
        onShow:function(ct,$i){
            // Prevent extra popups
            $i.val('');
        }
    });

});
</script>
<script>
$(document).ready(function() {

    $('.btn-submit').on('click', function(e) {
        e.preventDefault();

        let $btn = $(this);
        let $row = $btn.closest('tr');
        let id = $row.data('id');
        let appointmentDate = $row.find('.quick-datetime').val();

        if(!appointmentDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please enter appointment date!'
            });
            return;
        }

        $btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: '{{ route("admin.preappointment.updateDateTime") }}',
            method: 'POST',
            data: {
                id: id,
                appointment_date: appointmentDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
							icon: 'success',
							title: 'Success',
							text: 'Appointment date updated successfully!'
						}).then((result) => {
							if (result.isConfirmed) {
								location.reload(); // refresh page
							}
						});
                    $btn.prop('disabled', true).text('Submitted');
                    $row.find('.quick-datetime').prop('disabled', true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Update failed!'
                    });
                    $btn.prop('disabled', false).text('Submit');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: 'Please try again later.'
                });
                $btn.prop('disabled', false).text('Submit');
            }
        });
    });

});
</script>

@endsection