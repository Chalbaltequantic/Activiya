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
            <h1 class="m-0">Update Truck & Driver Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Update Truck & Driver Details</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Update Truck & Driver Details </a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated Truck & Driver Details</a></li>

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
										<th style="background: #fce4d6; color: #0070c0;">Truck No.</th>								
										<th style="background: #fce4d6; color: #0070c0;">Driver Name</th>						
										<th style="background: #ddebf7; color: #0070c0;">Driver Number</th>
										<th style="background: #ddebf7; color: #0070c0;">Expected<br />Arrival Date</th>
										<th style="background: #ddebf7; color: #0070c0;">Delivery<br/>Remarks</th>
									</tr>
								  </thead>
								  <tbody>
									@php
										$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
										
									@endphp
									  @php($i=1)
									  @if(count($entries) > 0)
									  @foreach($entries as $appointdata)
								  
									<tr class="{{ in_array($appointdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-entry-id="{{ $appointdata->id }}">
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
										<td>
											<input type="text" name="data[{{ $loop->index }}][truck_type]" value="{{ $appointdata->truck_type }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][vehicle_no]" value="{{ $appointdata->vehicle_no }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][driver_name]" value="{{ $appointdata->driver_name }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][driver_no]" value="{{ $appointdata->driver_no }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][arrival_date]" value="{{ $appointdata->arrival_date }}">
										</td>
										
										<td>
											<input type="text" name="data[{{ $loop->index }}][delivery_remarks]" value="{{ $appointdata->arrival_date }}">
										</td>
										
											<input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $appointdata->id }}">
											<input type="hidden" name="data[{{ $loop->index }}][inv_number]" value="{{ $appointdata->inv_number }}">
											<input type="hidden" name="data[{{ $loop->index }}][company_code]" value="{{ $appointdata->company_code }}">
															 
										</tr>
									  
							  @endforeach
							  @endif
							  
						   </tbody>
						    <tr><td colspan="8"></td>
							<td colspan="2"> <button type="submit" class="btn btn-primary">Submit</button></td>
							<td colspan="5"></td>
							</tr>
						   
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

									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Location</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">No. of<br>Packages</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Truck Type</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Truck No.</th>								
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Driver Name</th>						
									<th style="background: #ddebf7; color: #0070c0;" class="col-width">Driver Number</th>
									<th style="background: #ddebf7; color: #0070c0;" class="col-width">Expected<br />Arrival Date</th>

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
								<td>
								{{$updatedappointdata->truck_type}}
								</td>
								<td>
								{{$updatedappointdata->vehicle_no}}
								</td>
								<td>
								{{$updatedappointdata->driver_name}}
								</td>
								<td>
								{{$updatedappointdata->driver_no}}
								</td>
								<td>
								{{$updatedappointdata->arrival_date}}
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
$(document).ready(function () {
    $(document).on('change', '.upload-input', function () {

        const fileInput = this;
        const file = fileInput.files[0];
        const lr_no = $(this).data('lr');
        const type = $(this).data('type');
        const id = $(this).data('id');

        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('lr_no', lr_no);
        formData.append('type', type);
        formData.append('id', id);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('admin.file.upload') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                if (response.status === 'success') {
                    const fileId = `${type}-${id}`;
                    const fileBlock = `<a href="/uploads/${type}/${response.filename}" target="_blank" class="btn btn-sm btn-primary">View</a><button class="btn btn-sm btn-danger ml-2 delete-btn" data-filename="uploads/${type}/${response.filename}" data-id="${id}" data-type="${type}" data-target="${fileId}">Delete</button>`;
                    $(`#${fileId}`).html(fileBlock);
                }
            }
        });				
    });

});


</script>
<script>
$(document).ready(function () {

$(document).on('click', '.delete-btn', function () {
    const filename = $(this).data('filename');
    const type = $(this).data('type');
    const target = $(this).data('target');
    const id = $(this).data('id');
    const lr_no = target.split('-')[1]; // Assumes id format like 'invoice-123'

    if (!confirm("Are you sure you want to delete this file?")) {
        return;
    }

    $.ajax({
        url: '{{ route("admin.file.delete") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            filename: filename,
            type: type,
            id: id
        },
        success: function (response) {
	            if (response.status === 'deleted') {
                const newInput = `
                    <input type="file" class="upload-input" data-lr="${lr_no}" data-type="${type}" data-id="${id}">
                `;
                $('#' + target).html(newInput);
            } else {
                alert('File not found.');
            }
        },
        error: function () {
            alert('Error while deleting file.');
        }
    });
});
});
</script>
@endsection