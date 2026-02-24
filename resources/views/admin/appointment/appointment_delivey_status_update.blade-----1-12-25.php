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
            <h1 class="m-0">Delivery Stage/Status</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Delivery Stage/Status</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Delivery Stage/Status</a></li>
				  {{-- <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Updated Appointment Delivery Status</a></li>--}}

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						 
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
										<th style="background: #ddebf7; color: #0070c0;" class="">Delivery Remarks</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Action</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Reason for <br>Rejection / Reschedule</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Delivery <br>Require date</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Update Delivery <br>Status</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Last <br>Status</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Remarks</th>
										
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
										<td>{{ $appointdata->delivery_remarks }}</td>
										<td>
										  {{ $appointdata->appointment_status }}
                   
										</td>
										<td>
										  {{ $appointdata->reason_not_accepting }}
										</td>
										<td>
										{{ $appointdata->reschedule_date }}
										</td>
										<td>
										 <button type="button" class="btn btn-primary btn-sm updateStatusBtn" data-id="{{ $appointdata->id }}" data-invoice="{{ $appointdata->inv_number }}"data-consignee="{{ $appointdata->consignee_name }}">Update Status</button>
										<input type="hidden" name="appointments[{{ $loop->index }}][appointment_id]" value="{{ $appointdata->id }}">
                   
										</td>
										<td>{{$appointdata->latestDeliveryStatus->delivery_status ?? 'Pending' }}</td>
										
										{{--<td class="latest-status text-primary"
											style="cursor:pointer;"
											data-id="{{ $appointdata->id }}"
											data-toggle="popover"
											data-trigger="click"
											data-placement="left"
											title="Status History">
											{{ $appointdata->latestDeliveryStatus->delivery_status ?? 'Pending' }}
										</td>--}}
										
										
										
										<td>{{ $appointdata->latestDeliveryStatus->remarks  ?? '';}}</td>
											
										<input type="hidden" name="data[{{ $loop->index }}][id]" value="{{ $appointdata->id }}">
										<input type="hidden" name="data[{{ $loop->index }}][inv_number]" value="{{ $appointdata->inv_number }}">
										<input type="hidden" name="data[{{ $loop->index }}][company_code]" value="{{ $appointdata->company_code }}">
															 
									</tr>
									  
							  @endforeach
							
							  @endif
						   </tbody>
					  </table>
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
										<th style="background: #ddebf7; color: #0070c0;" class="">Last Status</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Remarks</th>

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
							<td>{{ $updatedappointdata->appointment_status }}</td>
							<td>{{ $updatedappointdata->reason_not_accepting }}</td>
							<td>{{ $updatedappointdata->reschedule_date }}</td>
							<td>{{ $updatedappointdata->delivery_status }}</td>
							<td>{{ $updatedappointdata->reschedule_remarks }}</td>
										
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
<!-- Modal -->
<div class="modal fade" id="statusModal">
    <div class="modal-dialog">
        <form id="statusForm">
            @csrf
            <input type="hidden" name="appointment_id" id="appointmentId">
            <input type="hidden" name="inv_no" id="invno">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Invoice:</strong> <span id="modalInvoice"></span></p>
                    <p><strong>Consignee:</strong> <span id="modalConsignee"></span></p>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Reported">Reported</option>
                            <option value="Unloading Start">Unloading Start</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Detained">Detained</option>
                            <option value="Return by Driver">Return by Driver</option>
                            <option value="Return by Buyer">Return by Buyer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" name="remarks" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
    let modal = $('#statusModal');
    let form  = $('#statusForm');

    // open modal
    $(document).on('click', '.updateStatusBtn', function(){
        let id = $(this).data('id');
        let invoice = $(this).data('invoice');
        let consignee = $(this).data('consignee');

        $('#appointmentId').val(id);
        $('#invno').val(invoice);
        $('#modalInvoice').text(invoice);
        $('#modalConsignee').text(consignee);

        modal.modal('show');
    });

    // submit via ajax
    form.on('submit', function(e){
        e.preventDefault();
        let id = $('#appointmentId').val();
        let url = "/admin/appointments/"+id+"/update-delivery-status";

        $.ajax({
            url: url,
            type: "POST",
            data: form.serialize(),
            success: function(res){
                if(res.success){
                    // update table without reload
                    $('tr[data-id="'+id+'"]').find('.latest-status').text(res.status);
                    modal.modal('hide');
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr){
                alert("Something went wrong!");
            }
        });
    });
});

    // init popover for history on click
    $(document).on('click', '.latest-status', function(){
        let cell = $(this);

        // close other popovers first
        $('[data-toggle="popover"]').not(cell).popover('hide');

        // if already has popover visible, let Bootstrap toggle it
        if(cell.data('bs.popover')) {
            cell.popover('toggle');
        } else {
            cell.popover({
                html: true,
                content: 'Loading...'
            }).popover('show');

            let id = cell.data('id');
            $.get('/admin/appointments/'+id+'/history-ajax', function(res){ alert(res.html)
                cell.data('bs.popover').setContent({ '.popover-body': res.html });
            });
        }
    });

    // close popovers when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.latest-status, .popover').length) {
            $('[data-toggle="popover"]').popover('hide');
        }
    });


</script>

@endsection