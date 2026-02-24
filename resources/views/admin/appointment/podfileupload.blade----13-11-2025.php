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
            <h1 class="m-0">Upload POD</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Upload POD</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Shipment POD File Upload</a></li>
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
										<th style="background: #fce4d6; color: #0070c0;z-index:999; width:60px;" class="col-width">Invoice Number</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:80px;" class="col-width">LR/CN No
										</th>
										<th style="background: #ddebf7; color: #0070c0;"  class="col-width">Attach <br>POD(front)</th>
										<th style="background: #ddebf7; color: #0070c0;"  class="col-width">Attach <br>POD(back)</th>
										
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
										<td>{{$appointdata->inv_number}}</td>				
										<td>{{$appointdata->lr_no}}</td>
										
										<td> @if($appointdata->podFront)
											<a href="{{ asset($appointdata->podFront->file_path) }}" target="_blank">View POD Front</a>
											@else
											<input type="file" class="pod-upload" data-type="podfront" data-id="{{ $appointdata->id }}" data-appointment-id="{{ $appointdata->id }}" data-file-type="podfront" >
										
										<button type="button" id="cameraFrontBtn" class="btn btn-sm btn-primary mt-2" onclick="capturePhoto('pod_front')">
											📷 Camera
										</button>										
											@endif</td>
											
										<td>@if($appointdata->podBack)
												<a href="{{ asset($appointdata->podBack->file_path) }}" target="_blank">View POD Back</a>
											@else
												<input type="file" class="pod-upload" data-type="podback" data-id="{{ $appointdata->id }}" data-appointment-id="{{ $appointdata->id }}" data-file-type="podfront">
											
												<button type="button" id="cameraBackBtn" class="btn btn-sm btn-primary mt-2" onclick="capturePhoto('pod_back')">
													📷 Camera
												</button>
											@endif
										</td>
									<input type="hidden" id="appointmentId" value="{{ $appointdata->id }}">
		
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

<script>
$(document).ready(function () {
    $('.pod-upload').on('change', function () {
        let fileInput = $(this);
        let file = fileInput[0].files[0];

        if (!file) return;

        let appointmentId = fileInput.data('appointment-id');
        let fileType = fileInput.data('file-type');

        let formData = new FormData();
        formData.append('file', file);
        formData.append('file_type', fileType);
        formData.append('appointment_id', appointmentId);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("admin.pod.upload") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                fileInput.after('<em class="uploading">Uploading...</em>');
            },
            success: function (res) {
                if (res.success) {
                    // Remove file input and uploading message
                    fileInput.next('.uploading').remove();
                    fileInput.replaceWith(
                        `<a href="${res.file_url}" target="_blank">View ${res.file_type.toUpperCase()}</a>`
                    );
                } else {
                    alert('Upload failed.');
                    fileInput.next('.uploading').remove();
                }
            },
            error: function () {
                alert('Upload error.');
                fileInput.next('.uploading').remove();
            }
        });
    });
});
</script>

<!-- Camera Capture Script Integration -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

    // Hide camera buttons if not mobile
    if (!isMobile) {
        const frontBtn = document.getElementById('cameraFrontBtn');
        const backBtn = document.getElementById('cameraBackBtn');
        if (frontBtn) frontBtn.style.display = 'none';
        if (backBtn) backBtn.style.display = 'none';
    }

    // Function to trigger camera and upload automatically
    window.capturePhoto = function (inputId) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.capture = 'environment'; // opens rear camera
        input.onchange = function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const originalInput = document.getElementById(inputId);
            if (originalInput) {
                // Assign the new file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                originalInput.files = dataTransfer.files;

                // 🔹 Trigger jQuery’s upload handler manually
                if (window.jQuery) {
                    $(originalInput).trigger('change');
                } else {
                    originalInput.dispatchEvent(new Event('change'));
                }
            }
        };
        input.click();
    };
});
</script>

@endsection