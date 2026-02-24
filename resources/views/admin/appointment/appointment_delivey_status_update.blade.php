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
      left: 80px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
.sticky-col-3 {
      position: sticky;
      left: 149px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 200px; /* Adjust based on col-1 width */
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
					
					<div class="alert alert-warning alert-dismissible fade show " style="display: none;">
						
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
					</div>
					<div class="alert alert-success alert-dismissible fade show " style="display: none;">
						
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
					</div>
							       
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
				  <li class="nav-item mobile-hide"><a class="nav-link" href="#timeline" data-toggle="tab">Delivered Status</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
								
								<table class="table table-bordered border-dark table-hover" id="billDataTable1"  style="table-layout:auto; width:auto;">
								  <thead>
									<tr>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1">Invoice<br>Number
										</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">LR/CN No
										</th>
										
										<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">Consignor<br>Name
										</th>
										<th style="background: #fce4d6; color: #0070c0;"  class="mobile-hide">Consignor<br>Location
										</th>
										
										<th style="background: #fce4d6; color: #0070c0;" class="">Consignee<br>Name
										</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Consignee<br>Phone
										</th>
											
										<th style="background: #fce4d6; color: #0070c0;" class="">Vendor Name
										</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">No. of<br>Packages
										</th>
										<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Inv<br>Value
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Arrival Date
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Delivery Remarks
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Action
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Reason for <br>Rejection / Reschedule
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Delivery <br>Require date
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Update Delivery <br>Status
										</th>
										<th style="background: #ddebf7; color: #0070c0;display:none;" class="otpth">OTP
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Last <br>Status
										</th>
										<th style="background: #ddebf7; color: #0070c0;" class="">Remarks
										</th>
										
									</tr>
								  </thead>
								  <tbody>
									@php
										$mismatchedIds = collect(session('mismatches'))->pluck('id')->toArray();
										
									@endphp
									
									  @if(count($entries) > 0)
									  @foreach($entries as $appointdata)
											@php
												// Get all status for this appointment
												$history = $appointdata->histories->pluck('delivery_status')->toArray();

												$isReported = in_array('Reported', $history);
												$isReturned = in_array('Returned', $history);
												$isDelivered = in_array('Delivered', $history);
											@endphp
								  
									<tr class="{{ in_array($appointdata->id, $mismatchedIds) ? 'table-danger mismatch-row' : '' }}" data-entry-id="{{ $appointdata->id }}"  data-id="{{ $appointdata->id }}">
										<td class="sticky-col-1">{{$appointdata->inv_number}}</td>
									
										<td class="sticky-col-2">{{$appointdata->lr_no}}</td>
										
										<td class="sticky-col-3">{{$appointdata->consignor_name}}</td>
										<td class="mobile-hide">{{$appointdata->consignor_location}}</td>
										<td class="">{{$appointdata->consignee_name}}</td>
										<td class="">{{$appointdata->site_incharge_contact_no}}</td>

										<td>{{$appointdata->vendor_name}}</td>
										<td>{{$appointdata->no_of_cases_sale}}</td>
										<td class="mobile-hide">{{ $appointdata->shipment_inv_value }}</td>				
										<td class="mobile-hide">{{ $appointdata->arrival_date }}</td>
										<td class="mobile-hide">{{ $appointdata->delivery_remarks }}</td>
										<td class="mobile-hide">
										  {{ $appointdata->appointment_status }}
                   
										</td>
										<td class="mobile-hide">
										  {{ $appointdata->reason_not_accepting }}
										</td>
										<td class="mobile-hide">
										{{ $appointdata->reschedule_date }}
										</td>
										<td>
										@if(auth()->user()->role->name == 'Driver')
											     {{-- STATUS BUTTONS --}}
											<button class="btn btn-sm btn-primary update-status" data-id="{{ $appointdata->id }}"data-status="Reported" @if($isReported) disabled style="background:#6c757d;border-color:#6c757d;" 
											@endif>Reported</button>											
											<button class="btn btn-sm btn-danger update-status" data-id="{{ $appointdata->id }}"data-status="Returned" @if($isReturned) disabled style="background:#6c757d;border-color:#6c757d;" @endif>Returned</button>
											<button class="btn btn-sm btn-success delivered-btn update-status" data-id="{{ $appointdata->id }}" data-status="Delivered" @if($isDelivered) disabled style="background:#6c757d;border-color:#6c757d;" 
											@endif>Delivered</button>							
											
											@else
											{{-- in case of other role except driver --}}
											<button type="button" class="btn btn-primary btn-sm updateStatusBtn" data-id="{{ $appointdata->id }}" data-invoice="{{ $appointdata->inv_number }}"data-consignee="{{ $appointdata->consignee_name }}">Update Status</button>
										 @endif
										<input type="hidden" name="appointments[{{ $loop->index }}][appointment_id]" value="{{ $appointdata->id }}">
                   
										</td>
										<td class="otp-box" id="otp-box-{{ $appointdata->id }}" style="display:none;">
											<div class="mt-2">
											<input type="text" class="otp-input" placeholder="Enter OTP" id="otp-{{ $appointdata->id }}" style="width:60px;" data-id="{{ $appointdata->id }}">
											
												<button class="btn btn-sm btn-dark confirm-delivery submit-otp" data-id="{{ $appointdata->id }}">Submit</button>
											</div></td>
										<td class="latest-status">{{$appointdata->latestDeliveryStatus->delivery_status ?? 'Pending' }}</td>
										
																				
										<td class="latest-status-rep">{{ $appointdata->latestDeliveryStatus->remarks  ?? 'NA'}}</td>
											
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
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color:#0070c0;z-index:999;" class="sticky-col-1">Invoice<br>Number</th>
								
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">LR/CN No
								</th>
								
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">Consignor<br>Name</th>
								<th style="background: #fce4d6; color: #0070c0;"  class="mobile-hide">Consignor<br>Location</th>
								
								<th style="background: #fce4d6; color: #0070c0;" class="">Consignee<br>Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Consignee<br>Phone</th>
								
								<th style="background: #fce4d6; color: #0070c0;" class="">Vendor Name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">No. of<br>Packages</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Inv<br>Value</th>
								<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Arrival Date</th>
								<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Delivery Remarks</th>
								<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Action</th>
								<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Reason for <br>Rejection / Reschedule</th>
								
								
								<th style="background: #ddebf7; color: #0070c0;" class="mobile-hide">Delivery <br>Require date</th>
								
								<th style="background: #ddebf7; color: #0070c0;" class="">Last <br>Status</th>
								<th style="background: #ddebf7; color: #0070c0;" class="">Remarks</th>
								<th style="background: #ddebf7; color: #0070c0;" class="">POD<br>Front</th>
								<th style="background: #ddebf7; color: #0070c0;" class="">POD<br>Back</th>
								
							</tr>
						  </thead>
						  <tbody>
							 @if(count($updatedentries) > 0)
							  @foreach($updatedentries as $delivered_appointdata)
						  
							<tr class="">
								<td class="sticky-col-1">{{$delivered_appointdata->inv_number}}</td>
								<td class="sticky-col-2">{{$delivered_appointdata->lr_no}}</td>								
								<td class="sticky-col-3">{{$delivered_appointdata->consignor_name}}</td>
								<td class="mobile-hide">{{$delivered_appointdata->consignor_location}}</td>
								<td class="">{{$delivered_appointdata->consignee_name}}</td>
								<td class="">{{$delivered_appointdata->site_incharge_contact_no}}</td>
								
								<td>{{$delivered_appointdata->vendor_name}}</td>
								<td>{{$delivered_appointdata->no_of_cases_sale}}</td>
								<td class="mobile-hide">{{ $delivered_appointdata->shipment_inv_value }}</td>		
								<td  class="mobile-hide">{{ $delivered_appointdata->arrival_date }}</td>
								
								<td  class="mobile-hide">{{ $delivered_appointdata->delivery_remarks }}</td>
								<td class="mobile-hide">
								  {{ $delivered_appointdata->appointment_status }}
		   
								</td>
								<td class="mobile-hide">
								  {{ $delivered_appointdata->reason_not_accepting }}
								</td>
								<td class="mobile-hide">
								{{ $delivered_appointdata->reschedule_date }}
								</td>								
								<td>{{$delivered_appointdata->latestDeliveryStatus->delivery_status ?? 'Pending' }}</td>
								<td>{{ $delivered_appointdata->latestDeliveryStatus->remarks  ?? '' }}</td>
								
								<td> @if(optional($delivered_appointdata->podFront)->file_path)
									<a href="{{ asset($delivered_appointdata->podFront->file_path) }}" target="_blank">View</a>
								@else
									<span class="text-danger">No File</span>
								@endif</td>
								<td>@if(optional($delivered_appointdata->podBack)->file_path)
									<a href="{{ asset($delivered_appointdata->podBack->file_path) }}" target="_blank">View</a>
								@else
									<span class="text-danger">No File</span>
								@endif</td>								
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
                            <option value="Unloading Start" class="mobile-hide">Unloading Start</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Detained" class="mobile-hide">Detained</option>
                            <option value="Return by Driver" class="mobile-hide">Return by Driver</option>
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
					//console.log('tr[data-id="'+id+'"]');
                    $('tr[data-id="'+id+'"]').find('.latest-status').text(res.status);
                    $('tr[data-id="'+id+'"]').find('.latest-status-rep').text(res.remarks);
                    modal.modal('hide');
					$(".alert-success").html(res.message);
					$(".alert-success").show();
					$(".alert-warning").html('');
					$(".alert-warning").hide();
					
                } else {
                    //alert(res.message);
					$(".alert-warning").html(res.message);
					$(".alert-warning").show();
					$(".alert-success").html('');
					$(".alert-success").hide();
                }
            },
            error: function(xhr){
                alert("Something went wrong!");
            }
        });
    });
});	
	
// driver delivery status Update

$('.update-status').click(function () {
    let id = $(this).data('id');
    let status = $(this).data('status');
	
	let otp = "";

    if (status === "Delivered") {
		$('.otpth').show();
        $('#otp-box-' + id).show();
    }
	
	let url = "/admin/appointments/"+id+"/update-delivery-status";
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id,
            status: status,
			otp: otp,
            _token: '{{ csrf_token() }}'
        },
        success: function(res){
                if(res.success){
                    // update table without reload
				$('button[data-id="' + id + '"][data-status="' + status + '"]')
                .prop("disabled", true)
                .css({"background":"#6c757d", "border-color":"#6c757d"});
					
                    $('tr[data-id="'+id+'"]').find('.latest-status').text(res.status);
					 $('tr[data-id="'+id+'"]').find('.latest-status-rep').text(res.remarks);
					
					$(".alert-success").html(res.message);
					$(".alert-success").show();
					$(".alert-warning").html('');
					$(".alert-warning").hide();
                } else {
					$(".alert-success").html('');
					$(".alert-success").hide();
					$(".alert-warning").html(res.message);
					$(".alert-warning").show();
                  
                }
            },
            error: function(xhr){
                alert("Something went wrong!");
            }
    });
});

$(document).on("click", ".submit-otp", function () {

    var id = $(this).data("id");
    var otp = $("#otp-" + id).val();

    $.ajax({
        url: "{{ route('admin.update.delivery.otp') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            otp: otp
        },
        success: function (res) {
            if (!res.success) {
				$(".alert-warning").html("Invalid OTP. Please try again.");
				$(".alert-warning").show();
                return;
            }
			
			alert("OTP verified & saved");
			$(".alert-success").html('OTP verified & saved');
			$(".alert-success").show();
			
			// Hide OTP box after success
			$('.otpth').hide();
			$('#otp-box-' + id).hide();
           
        }
    });
});
</script>

@endsection