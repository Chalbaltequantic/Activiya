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
            <h1 class="m-0">Central – Appointment Request Board</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Central – Appointment Request Board</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Send Request to Consignee</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated Appointment Request</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						  <form id="assignForm">
							@csrf

								<table class="table table-bordered border-dark table-hover" id="table"  style="table-layout:auto; width:auto;">
								  <thead>
									<tr>
										<th style="background: #fce4d6; color: #0070c0;z-index:999; width:90px;" class="sticky-col-1">Invoice Number</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-2">Inv Doc Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">LR/CN No
										</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;"class="sticky-col-4">LR/CN Date</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:100px;" class="sticky-col-5">Consignor Name</th>

										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Location</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">No. of<br>Packages</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Truck Type</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Truck No.</th>								
										
										<th style="background: #ddebf7; color: #0070c0;" class="col-width">Expected<br />Arrival Date</th>
								
										<th style="background: #ddebf7; color: #0070c0;" class="col-width">Send Request<br/>to Consignee<br />
										<input type="checkbox" id="select_all"></th>
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
										<td class="sticky-col-2">{{$appointdata->inv_doc_date}}</td>
										<td class="sticky-col-3">{{$appointdata->lr_no}}</td>
										<td class="sticky-col-4">{{$appointdata->lr_date}}</td>
										<td class="sticky-col-5">{{$appointdata->consignor_name}}</td>
										<td class="">{{$appointdata->consignor_location}}</td>
										<td>{{$appointdata->consignee_name}}</td>
										<td>{{$appointdata->consignee_location}}</td>
										<td>{{$appointdata->vendor_name}}</td>
										<td>{{$appointdata->no_of_cases_sale}}</td>
										<td>{{ $appointdata->truck_type }}</td>
										<td>{{ $appointdata->vehicle_no }}</td>					
										<td>{{ $appointdata->arrival_date }}</td>
										<td><input type="checkbox" class="consignee_checkbox" name="consignee_ids[]" value="{{ $appointdata->id }}" {{ $appointdata->assign_to_consignee ? 'checked' : '' }}>
										</td>
											
										<input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $appointdata->id }}">
										<input type="hidden" name="data[{{ $loop->index }}][inv_number]" value="{{ $appointdata->inv_number }}">
										<input type="hidden" name="data[{{ $loop->index }}][company_code]" value="{{ $appointdata->company_code }}">
															 
									</tr>
									  
							  @endforeach
							<tr><td colspan="13"></td>
								<td> <button type="submit" class="btn btn-primary">Submit</button></td>
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
								<th style="background: #fce4d6; color: #0070c0;z-index:999; width:90px;" class="sticky-col-1">Invoice Number</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-2">Inv Doc Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">LR/CN No
								</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;"class="sticky-col-4">LR/CN Date</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:100px;" class="sticky-col-5">Consignor Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Consignor Location</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Consignee Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Consignee Location</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Vendor Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">No. of<br>Packages</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Truck Type</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Truck No.</th>								
								
								<th style="background: #ddebf7; color: #0070c0;" class="">Expected<br />Arrival Date</th>
							
								<th style="background: #ddebf7; color: #0070c0;" class="">Send Request<br/>to Consignee</th>

							</tr>
						  </thead>
						<tbody>
							
						  @if(count($updatedentries) > 0)
						  @foreach($updatedentries as $updatedappointdata)
							  
						<tr class="" data-entry-id="{{ $updatedappointdata->id }}">
								<td class="sticky-col-1">{{$updatedappointdata->inv_number}}</td>
								<td class="sticky-col-2">{{$updatedappointdata->inv_doc_date}}</td>
								<td class="sticky-col-3">{{$updatedappointdata->lr_no}}</td>
								<td class="sticky-col-4">{{$updatedappointdata->lr_date}}</td>
								<td class="sticky-col-5">{{$updatedappointdata->consignor_name}}</td>
								<td class="">{{$updatedappointdata->consignor_location}}</td>

								<td>{{$updatedappointdata->consignee_name}}</td>
								<td>{{$updatedappointdata->consignee_location}}</td>
								<td>{{$updatedappointdata->vendor_name}}</td>
								<td>{{$updatedappointdata->no_of_cases_sale}}</td>
								<td>{{$updatedappointdata->truck_type}}</td>
								<td>{{$updatedappointdata->vehicle_no}}</td>
								<td>{{$updatedappointdata->arrival_date}}</td>
								<td>
								{!! ($updatedappointdata->assigned_to_consignee==1) 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}
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
$(document).ready(function(){

    // Select/Deselect All
    $('#select_all').on('click', function(){
        $('.consignee_checkbox').prop('checked', this.checked);
    });

    $('.consignee_checkbox').on('click', function(){
        if ($('.consignee_checkbox:checked').length == $('.consignee_checkbox').length){
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });

    // Submit form via AJAX
    $('#assignForm').on('submit', function(e){
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('admin.appointments.assign.submit') }}",
            type: "POST",
            data: formData,
            success: function(response){
                if(response.status === 'success'){
                    showToast('success',response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error',response.message);
                }
            },
            error: function(xhr){
                showToast('error','An error occurred while processing the request.');
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