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
      left: 230px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-5 {
      position: sticky;
      left: 290px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Update LR/NO & Appointment Status</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Update LR/NO & Appointment Status</li>
				
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
								@foreach((array) session('mismatches') as $item)
									<li>Amount mismatch for Ref No: {{ $item['order_ref_no'] }}</li>
								@endforeach
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Update LR/NO & Appointment Status</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated LR/NO & Appointment Status</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<form method="POST" action="{{ route('admin.appointmentdata.updateMultipleAppointment') }}">
							@csrf

								<table class="table table-bordered border-dark table-hover" id="table"  style="table-layout:auto; width:auto;">
								  <thead>
									<tr>
										<th style="background: #fce4d6; color: #0070c0;z-index:999; width:90px;" class="sticky-col-1">Invoice Number</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Inv Doc Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">PO No
										</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;"class="sticky-col-4">PO Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-5">Consignor<br>Name</th>

										<th style="background: #fce4d6; color: #0070c0;">Consignor<br>Location</th>
										<th style="background: #fce4d6; color: #0070c0;">Consignee<br>Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Consignee<br>Location</th>
										<th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
										<th style="background: #fce4d6; color: #0070c0;">No. of<br>Packages</th>
										<th style="background: #fce4d6; color: #0070c0;">Appointment<br>Date</th>
										
										<th style="background: #ddebf7; color: #0070c0;">LR NO</th>
										<th style="background: #ddebf7; color: #0070c0;">LR Date</th>								
										<th style="background: #ddebf7; color: #0070c0;">T Code</th>								
										<th style="background: #ddebf7; color: #0070c0;">Truck Type</th>								
										<th style="background: #ddebf7; color: #0070c0;">Vehicle No.</th>
										<th style="background: #ddebf7; color: #0070c0;">Driver Name</th>
										<th style="background: #ddebf7; color: #0070c0;">Driver No.</th>										
										<th style="background: #ddebf7; color: #0070c0;">Remarks</th>										
										<th style="background: #ddebf7; color: #0070c0;">Action</th>
										
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
										<td>{{$appointdata->appointment_date}}</td>
										<td><input class="lr-input lr_no d-none"></td>
										<td><input type="date" class="lr-input lr_date d-none"></td>
										<td><input class="lr-input t_code d-none"></td>
										<td><input class="lr-input truck_type d-none"></td>
										<td><input class="lr-input vehicle_no d-none"></td>
										<td><input class="lr-input driver_name d-none"></td>
										<td><input class="lr-input driver_no d-none"></td>
										<td><input type="text" class="mt-1 remark-input d-none" placeholder="Enter Remark"></td>
										
										<td class="action-cell">
										<button type="button" class="btn btn-sm btn-success btn-supply">Supply</button>
										<button type="button" class="btn btn-sm btn-warning btn-reschedule">Reschedule</button>
										<button type="button" class="btn btn-sm btn-success btn-close">Close</button>

										<button type="button" class="btn btn-sm btn-primary btn-submit d-none">Submit</button>
										<button type="button" class="btn btn-sm btn-secondary btn-cancel d-none">Cancel</button>
									</td>
									
										<input type="hidden" class="row-action" value="">	 
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
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Inv Doc Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">PO No
								</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;"class="sticky-col-4">PO Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-5">Consignor<br>Name</th>

								<th style="background: #fce4d6; color: #0070c0;">Consignor<br>Location</th>
								<th style="background: #fce4d6; color: #0070c0;">Consignee<br>Name</th>
								<th style="background: #fce4d6; color: #0070c0;">Consignee<br>Location</th>
								<th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
								<th style="background: #fce4d6; color: #0070c0;">No. of<br>Packages</th>
								<th style="background: #fce4d6; color: #0070c0;">Appointment<br>Date</th>
								
								<th style="background: #ddebf7; color: #0070c0;">LR NO</th>
								<th style="background: #ddebf7; color: #0070c0;">LR Date</th>								
								<th style="background: #ddebf7; color: #0070c0;">T Code</th>								
								<th style="background: #ddebf7; color: #0070c0;">Truck Type</th>								
								<th style="background: #ddebf7; color: #0070c0;">Vehicle No.</th>
								<th style="background: #ddebf7; color: #0070c0;">Driver Name</th>
								<th style="background: #ddebf7; color: #0070c0;">Driver No.</th>										
								<th style="background: #ddebf7; color: #0070c0;">Remarks</th>
								<th style="background: #ddebf7; color: #0070c0;">Appointment<br>Status</th>
									
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
							<td>{{$updatedappointdata->appointment_date}}</td>
							<td>{{$updatedappointdata->lr_no}}</td>
							<td>{{$updatedappointdata->lr_date}}</td>
							<td>{{$updatedappointdata->t_code}}</td>
							<td>{{$updatedappointdata->truck_type}}</td>
							<td>{{$updatedappointdata->vehicle_no}}</td>
							<td>{{$updatedappointdata->driver_name}}</td>
							<td>{{$updatedappointdata->driver_no}}</td>
							<td>{{$updatedappointdata->remarks}}</td>
							<td>{{$updatedappointdata->appointment_status}}</td>

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
function resetRow(row) {
    row.find('.lr-input').addClass('d-none');
    row.find('.remark-input').addClass('d-none');

    row.find('.btn-submit, .btn-cancel').addClass('d-none');
    row.find('.btn-supply, .btn-reschedule, .btn-close').removeClass('d-none');
}

function disableOtherRows(currentRow) {
    $('tbody tr').not(currentRow).find('button').prop('disabled', true);
}

function enableAllRows() {
    $('tbody tr').find('button').prop('disabled', false);
}

$(document).on('click', '.btn-cancel', function () {
    let row = $(this).closest('tr');

    resetRow(row);
    enableAllRows();
});

$(document).on('click', '.btn-supply', function () {
    let row = $(this).closest('tr');

    resetRow(row);
    disableOtherRows(row);
	row.find('.row-action').val('supply');
    row.find('.lr-input, .remark-input').removeClass('d-none');
    row.find('.btn-supply, .btn-reschedule, .btn-close').addClass('d-none');
    row.find('.btn-submit, .btn-cancel').removeClass('d-none');
});

$(document).on('click', '.btn-reschedule', function () {
    let row = $(this).closest('tr');

    resetRow(row);
    disableOtherRows(row);
	row.find('.row-action').val('reschedule');
    row.find('.remark-input').removeClass('d-none').focus();
    row.find('.btn-supply, .btn-reschedule, .btn-close').addClass('d-none');
    row.find('.btn-submit, .btn-cancel').removeClass('d-none');
});

$(document).on('click', '.btn-close', function () {
    let row = $(this).closest('tr');

    resetRow(row);
    disableOtherRows(row);
	row.find('.row-action').val('close');
    row.find('.remark-input').removeClass('d-none').focus();
    row.find('.btn-supply, .btn-reschedule, .btn-close').addClass('d-none');
    row.find('.btn-submit, .btn-cancel').removeClass('d-none');
});


$(document).on('click', '.btn-submit', function () {
    let row = $(this).closest('tr');
    let action = row.find('.row-action').val();
    let id = row.data('id');

    let errors = [];

    // Collect values
    let data = {
        _token: "{{ csrf_token() }}",
        id: id,
        action: action,
        lr_no: row.find('.lr_no').val(),
        lr_date: row.find('.lr_date').val(),
        t_code: row.find('.t_code').val(),
        truck_type: row.find('.truck_type').val(),
        vehicle_no: row.find('.vehicle_no').val(),
        driver_name: row.find('.driver_name').val(),
        driver_no: row.find('.driver_no').val(),
        remark: row.find('.remark-input').val()
    };

    // VALIDATION
    if (action === 'supply') {
        if (!data.lr_no) errors.push('LR No is required');
        if (!data.lr_date) errors.push('LR Date is required');
        if (!data.vehicle_no) errors.push('Vehicle No is required');
        if (!data.driver_name) errors.push('Driver Name is required');
        if (!data.driver_no) errors.push('Driver No is required');
        if (!data.remark) errors.push('Remark is required');
    }

    if (action === 'reschedule' || action === 'close') {
        if (!data.remark) errors.push('Remark is required');
    }

    if (errors.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            html: errors.join('<br>')
        });
        return;
    }

    // CONFIRMATION
    Swal.fire({
        title: 'Confirm Submission',
        text: 'Are you sure you want to submit?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Submit'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.preappointment.update.lr') }}", 
            type: "POST",
            data: data,
            beforeSend: function () {
                Swal.showLoading();
            },
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message || 'Updated successfully'
                }).then((result) => {
						if (result.isConfirmed) {
							location.reload(); // refresh page
						}
					});

                resetRow(row);
                enableAllRows();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Something went wrong'
                });
            }
        });

    });
});
</script>
@endsection