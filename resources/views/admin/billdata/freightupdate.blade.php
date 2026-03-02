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
      left: 250px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 340px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-5 {
      position: sticky;
      left: 420px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">V_Freight Bills Update</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">V_Freight Bills Update </li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Update Freight Invoice </a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Attach Freight Documents</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<form method="POST" action="{{ route('admin.freightdata.updateMultiple') }}">
							@csrf
								<table class="table table-bordered border-dark table-hover" id="table"  style="table-layout:auto; width:auto;">
								  <thead>
									<tr>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:125px;" class="sticky-col-1">S5 consignor short<br> name & location</th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:125px;" class="sticky-col-2">D5 consignor short<br> name & location</th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:90px;" class="sticky-col-3">Order Ref No.<br />( <small>Indent ID</small>)
										</th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:80px;"class="sticky-col-4">Charged<br /><small>Truck Type</small></th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-5">LR/CN No.</th>
									  <th style="background: #fce4d6; color: #0070c0;" class="">LR/CN Date</th>
									  <th style="background: #fce4d6; color: #0070c0;" class="">Ref 1<br />FPO NO.</th>
									  <th style="background: #fce4d6; color: #0070c0;" class="">Ref 2 <br />FGRN</th>
								 	<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Freight <br>Invoice No.</th>
									<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Invoice Dt.</th>
									<th style="background: #c6e0b4; color: #0070c0;">Amount</th>
									{{--<th style="background: #fce4d6; color: #0070c0;" class="col-width">Freight Invoice</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">POD</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-width">Approvals</th>						
									 <th style="background: #ddebf7; color: #0070c0;" class="col-width">Validate</th> 
									 <th style="background: #ddebf7; color: #0070c0;" class="col-width">Submit</th> 
									  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Return</th>
									  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Remark</th>
									   <th style="background: #fce4d6; color: #0070c0;" class="col-width">Custom </th>
									  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Custom 1</th>
									  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Custom 2</th>
									  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 3</th>--}}
									  
									</tr>
								  </thead>
								  <tbody>
									@php
										$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
										
									@endphp
									  @php($i=1)
									  @if(count($entries) > 0)
									  @foreach($entries as $billdata)
								  
									<tr class="{{ in_array($billdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-entry-id="{{ $billdata->id }}">
										<td class="sticky-col-1">{{$billdata->s5_consignor_short_name_and_location}}</td>
										<td class="sticky-col-2">{{$billdata->d5_consignor_short_name_and_location}}</td>
										<td class="sticky-col-3">{{$billdata->ref1}}</td>
										<td class="sticky-col-4">{{$billdata->truck_type}}</td>
										<td class="sticky-col-5">{{$billdata->lr_no}}</td>
										<td>{{$billdata->lr_cn_date}}</td>
										<td>{{$billdata->ref2}}</td>
										<td>{{$billdata->ref3}}</td>
									  
										<td>
											<input type="text" name="data[{{ $loop->index }}][freight_invoice_no]" value="{{ $billdata->freight_invoice_no }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][freight_invoice_date]" value="{{ $billdata->freight_invoice_date }}">
										</td>
										<td>
											<input type="text" name="data[{{ $loop->index }}][freight_amount]" value="{{ number_format($billdata->freight_amount) }}">
										</td>
											<input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $billdata->id }}">
											<input type="hidden" name="data[{{ $loop->index }}][lr_no]" value="{{ $billdata->lr_no }}">
											<input type="hidden" name="data[{{ $loop->index }}][freight_type]" value="{{ $billdata->freight_type }}">
									 
									 {{--  <td>{{$billdata->custom}}</td>
										  <td>{{$billdata->custom1}}</td>
										  <td>{{$billdata->custom2}}</td>
											<td>{{$billdata->custom3}}</td>
									 --}}
									 
										</tr>
									  
							  @endforeach
							  @else
								  <tr><td colspan="15">No data found</td></tr>
							  @endif
							  
						   </tbody>
						   
						   
					  </table>
					  
					
					 
					</div>
					<div class="row text-right">
						<div class="col-md-10">
						<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>
					</form>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
						 
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:120px;" class="sticky-col-1">S5 consignor short<br> name & location</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:120px" class="sticky-col-2">D5 consignor short<br> name & location</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:80px;" class="sticky-col-3">Order Ref No.<br />( <small>Indent ID</small>)
								</th>
														  
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:30px;" class="sticky-col-4">LR/CN No.</th>								
								
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:40px;" class="sticky-col-5">Freight <br>Invoice No.</th>
								<th style="background: #fce4d6; color: #0070c0;">Invoice Dt.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Amount</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Freight Invoice</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">POD</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Approvals</th>						
								<th style="background: #ddebf7; color: #0070c0;" class="">Validate</th> 
								<th style="background: #ddebf7; color: #0070c0;" class="">Submit</th> 
								<th style="background: #fce4d6; color: #0070c0;" class="">Return</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Remark</th>
								

							</tr>
						  </thead>
						<tbody>
							
						  @if(count($updatedentries) > 0)
						  @foreach($updatedentries as $updatedbilldata)
							  
						<tr>
							<td class="sticky-col-1">{{$updatedbilldata->s5_consignor_short_name_and_location}}</td>
							<td class="sticky-col-2">{{$updatedbilldata->d5_consignor_short_name_and_location}}</td>
							<td class="sticky-col-3">{{$updatedbilldata->ref1}}</td>
							<td class="sticky-col-4">{{$updatedbilldata->lr_no}}</td>			
							<td class="sticky-col-5">{{ $updatedbilldata->freight_invoice_no }}</td>
							<td>{{ $updatedbilldata->freight_invoice_date }}</td>
							<td>{{ number_format($updatedbilldata->freight_amount) }}</td>
							<td>
								<span class="uploaded-file" id="invoice-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->freight_invoice_file)
										<a href="{{ asset( $updatedbilldata->freight_invoice_file) }}" target="_blank"  class="btn btn-sm btn-primary">
											View
										</a>
										@if($updatedbilldata->submit!=1)
										<button class="btn btn-sm btn-danger delete-btn" data-id="{{ $updatedbilldata->id }}" data-filename="{{ $updatedbilldata->freight_invoice_file }}" data-type="invoice" data-target="invoice-{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}">Delete</button>
										@endif
									@else
										<input type="file" class="upload-input" data-id="{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}" data-type="invoice">
									@endif
								</span>
							</td>
							<td>								
								<span class="uploaded-file" id="pod-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->pod_file)
										<a href="{{ asset( $updatedbilldata->pod_file) }}" target="_blank" class="btn btn-sm btn-primary">
											View
										</a>
										@if($updatedbilldata->submit!=1)
										<button class="btn btn-sm btn-danger delete-btn" data-filename="{{ $updatedbilldata->pod_file }}" data-type="pod" data-target="pod-{{ $updatedbilldata->id }}" data-id="{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}">Delete</button>
										@endif
									@else
										<input type="file" class="upload-input" data-id="{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}" data-type="pod">
									@endif
								</span>
							</td>
							<td>
								@if ($updatedbilldata->freight_type=='ADHOC')
									<span class="uploaded-file" id="approval-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->approval_file)
										<a href="{{ asset($updatedbilldata->approval_file) }}" target="_blank">View</a>
										@if($updatedbilldata->submit!=1)
										<button class="btn btn-sm btn-danger delete-btn" data-filename="{{ $updatedbilldata->approval_file }}" data-id="{{ $updatedbilldata->id }}" data-type="approval" data-target="approval-{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}">Delete</button>
										@endif
									@else
										<input type="file" class="upload-input" data-id="{{ $updatedbilldata->id }}" data-lr="{{ $updatedbilldata->lr_no }}" data-type="approval">
									@endif
									</span>
									@else NA
								@endif
							</td>
							<td>{!! ($updatedbilldata->validated_status=='submitted') 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td>
							<td>{!! ($updatedbilldata->submit==1) 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td>
							<td>{!! ($updatedbilldata->f_return==1) 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td>
							<td>{{$updatedbilldata->validation_remark}}</td>
													 
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