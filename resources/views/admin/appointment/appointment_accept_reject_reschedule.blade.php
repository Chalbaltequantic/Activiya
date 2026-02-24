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
      left: 320px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    . {
      min-width: 100px;
    }

    @media (max-width: 768px) {
      . {
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
            <h1 class="m-0">Appointment Acceptance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Appointment Acceptance</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Consignee Accept / Reject / Reschedule</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated Appointment Request</a></li>

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
										<th style="background: #fce4d6; color: #0070c0;z-index:999; width:60px;" class="sticky-col-1">Invoice Number</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:80px;" class="sticky-col-2">LR/CN No
										</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">Consignor Name</th>

										<th style="background: #fce4d6; color: #0070c0;" class="">Consignor Location</th>
										
										<th style="background: #fce4d6; color: #0070c0;" class="">Vendor Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">No. of<br>Packages</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Inv. Value</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Arrival Date</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Action</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Reason for <br>Rejection / Reschedule</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Delivery <br>Require date</th>
									</tr>
								  </thead>
								  <tbody>
									@php
										$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
										
									@endphp
									  @php($i=1)
									  @if(count($entries) > 0)
									  @foreach($entries as $appointdata)
								  
									<tr class="{{ in_array($appointdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-entry-id="{{ $appointdata->id }}"  data-id="{{ $appointdata->id }}">
										<td class="sticky-col-1">{{$appointdata->inv_number}}</td>
									
										<td class="sticky-col-2">{{$appointdata->lr_no}}</td>
										
										<td class="sticky-col-3">{{$appointdata->consignor_name}}</td>
										<td class="">{{$appointdata->consignor_location}}</td>
										
										<td>{{$appointdata->vendor_name}}</td>
										<td>{{$appointdata->no_of_cases_sale}}</td>
										<td>{{ $appointdata->shipment_inv_value }}</td>				
										<td>{{ $appointdata->arrival_date }}</td>
										<td>
										  <select name="appointments[{{ $loop->index }}][status]" class="form-control status-select">
											<option value="accepted">Accept</option>
											<option value="rejected">Reject</option>
											<option value="rescheduled">Reschedule</option>
										</select>
										<input type="hidden" name="appointments[{{ $loop->index }}][appointment_id]" value="{{ $appointdata->id }}">
                   
										</td>
										<td>
										  <input type="text" name="appointments[{{ $loop->index }}][reason]" class="form-control reason-input" disabled>

										</td>
										<td>
										<input type="date" name="appointments[{{ $loop->index }}][reschedule_date]" class="form-control date-input" disabled>


										</td>
											
										<input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $appointdata->id }}">
										<input type="hidden" name="data[{{ $loop->index }}][inv_number]" value="{{ $appointdata->inv_number }}">
										<input type="hidden" name="data[{{ $loop->index }}][company_code]" value="{{ $appointdata->company_code }}">
															 
									</tr>
									  
							  @endforeach
							<tr><td colspan="8"></td>
								<td colspan="3"> <button type="submit" class="btn btn-primary">Submit</button></td>
							</tr>
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
								<th style="background: #fce4d6; color: #0070c0;z-index:999; width:60px;" class="sticky-col-1">Invoice Number</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:80px;" class="sticky-col-2">LR/CN No
										</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">Consignor Name</th>

										<th style="background: #fce4d6; color: #0070c0;" class="">Consignor Location</th>
										
										<th style="background: #fce4d6; color: #0070c0;" class="">Vendor Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">No. of<br>Packages</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Inv. Value</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Arrival Date</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Action</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Reason for <br>Rejection / Reschedule</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Delivery <br>Require date</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">OTP</th>

							</tr>
						  </thead>
						<tbody>
							
						  @if(count($updatedentries) > 0)
						  @foreach($updatedentries as $updatedappointdata)
							  
						<tr class="" data-entry-id="{{ $updatedappointdata->id }}">
								<td class="sticky-col-1">{{$updatedappointdata->inv_number}}</td>
									
										<td class="sticky-col-2">{{$updatedappointdata->lr_no}}</td>
										
										<td class="sticky-col-3">{{$updatedappointdata->consignor_name}}</td>
										<td class="">{{$updatedappointdata->consignor_location}}</td>
										
										<td>{{$updatedappointdata->vendor_name}}</td>
										<td>{{$updatedappointdata->no_of_cases_sale}}</td>
										<td>{{ $updatedappointdata->shipment_inv_value }}</td>				
										<td>{{ $updatedappointdata->arrival_date }}</td>
										<td>
										  {{ $updatedappointdata->appointment_status }}
                   
										</td>
										<td>
										  {{ $updatedappointdata->reason_not_accepting }}
										</td>
										<td>
										{{ $updatedappointdata->reschedule_date }}
										</td>
										<td>
										{{ $updatedappointdata->otp_for_driver }}
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('table');

    // Delegate paste event to table
    table.addEventListener('paste', function(e) {
        // Only handle paste when an input is focused
        if (document.activeElement.tagName !== 'INPUT') return;

        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text');

        // Split pasted data into rows and columns
        const rows = pastedData.split(/\r\n|\n|\r/).filter(row => row.length > 0);
        const startInput = document.activeElement;
        let startCell = startInput.closest('td');
        let startRow = startCell.parentElement;
        let rowIndex = Array.from(table.rows).indexOf(startRow);
        let colIndex = Array.from(startRow.cells).indexOf(startCell);

        // Loop through rows and columns and fill inputs
        rows.forEach((row, i) => {
            const cols = row.split('\t');
            const tr = table.rows[rowIndex + i];
            if (!tr) return;
            cols.forEach((col, j) => {
                const td = tr.cells[colIndex + j];
                if (!td) return;
                const input = td.querySelector('input');
                if (input) input.value = col;
            });
        });
    });
});
</script>
<script>
$(document).on('change', '.status-select', function() {
    let row = $(this).closest('tr');
    let status = $(this).val();
    let reason = row.find('.reason-input');
    let date = row.find('.date-input');

    if (status == 'rejected') {
        reason.prop('disabled', false);
        date.prop('disabled', true).val('');
    } else if (status == 'rescheduled') {
        reason.prop('disabled', false);
        date.prop('disabled', false);
    } else {
        reason.prop('disabled', true).val('');
        date.prop('disabled', true).val('');
    }


$('#appointmentsForm').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('admin.appointments.updateStatus.acceptreject') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res) {
            Swal.fire('Success', res.message, 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON.message, 'error');
        }
    });
});

	function showToast(type, message){
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 2000
        });
    }
});
</script>

@endsection