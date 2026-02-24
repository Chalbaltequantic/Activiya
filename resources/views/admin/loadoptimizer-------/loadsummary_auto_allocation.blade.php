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
            <h1 class="m-0">Load Optimizer</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Load Optimiser</li>
				
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
					<div class="alert alert-success alert-dismissible fade show">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.lopmanualupload')}}">Create</a></li>
                   <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummary')}}">Indent</a></li>
				   <li class="nav-item"><a class="nav-link" href="#">Qualified Indent</a></li>
				  <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummaryApproval')}}">Approve / Reject Summary</a></li>
				  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.loadsummary_auto_allocation') }}">Vendor Allocation</a></li>
				   <li class="nav-item"><a class="nav-link" href="{{ route('admin.vendor.loads') }}">Approve/Reject Vendor Allocation</a></li>
				  
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                 
                  <!-- /.tab-pane -->
                  <div class="tab-pane active" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<table id="appointdataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;" class="">Reference<br>no</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Origin<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Destination<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;">Mode</th>
								<th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
								<th style="background: #fce4d6; color: #0070c0;">ZW uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Zv uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Gross<br>utilization</th>
								<th style="background: #c6e0b4; color: #0070c0;">Vendor<br>name</th>
								<th style="background: #c6e0b4; color: #0070c0;">Vendor<br>rank</th>
								<th style="background: #c6e0b4; color: #0070c0;">Source</th>
								<th style="background: #c6e0b4; color: #0070c0;">Edit</th>
								<th style="background: #c6e0b4; color: #0070c0;">Send</th>
													  
								</tr>
						  </thead>
						<tbody>
							@if(count($qualifiedloads) > 0)
							 @foreach($qualifiedloads as $row)
							  
							<tr>
								<td class="sticky-col-1">{{ $row->reference_no }}</td>
								<td class="sticky-col-2">{{ $row->origin_name_code }} {{ $row->origin_name }}</td>
								
								<td class="sticky-col-3">{{ $row->destination_name_code }} {{ $row->destination_city }}</td>
								<td>{{ $row->t_mode }}</td>
									<td>{{ $row->truck->description ?? 'NA' }}</td>
									<td>{{ $row->zw_util }}%</td>
									<td>{{ $row->zv_util }}%</td>
									<td class="fw-bold">{{ $row->gross_util }}%</td>
									
									<td class="fw-bold">{{ $row->vendor_name }}</td>
									<td class="fw-bold">{{ $row->vendor_rank }}</td>
									<td class="fw-bold">
									@if(!empty($row->vendor_code_source))
										@if( $row->vendor_code_source=='Manual Edit')
											<span class="badge badge-info">Change</span>
										@else
											<span class="badge badge-success">Auto</span>
										@endif
									@endif
										
									</td>
									
									<td>
									
										<button
											class="btn btn-sm btn-warning editVendorBtn"
											data-id="{{ $row->id }}">
											Edit
										</button>
									
								
									</td>

									<td>
										@if($row->sent_status=='sent_to_vendor')
											<span class="btn btn-secondary">Sent to Vendor</span>
										@elseif($row->sent_status=='approval_required' )
											<span class="btn btn-secondary">Sent for Approval</span>
										@else
											@if(!empty($row->vendor_code))
											<button class="btn btn-primary btn-sm" onclick="sendToVendor({{ $row->id }}, {{ $row->vendor_code }}, {{ $row->vendor_rank }}, '{{ $row->vendor_code_source }}', '{{ $row->reference_no }}', '{{ $row->origin_name_code }}', '{{ $row->destination_name_code }}')">Send</button>
											@endif
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

<div class="modal fade" id="editVendorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit Vendor Allocation</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="load_summary_id">

                <table class="table table-bordered">
                    <tr style="background: #fce4d6; color: #0070c0;"><th style="background: #fce4d6; color: #0070c0;">Reference</th><th>Route</th><th>Truck</th></tr>
                    <tr><td id="ref_no"></td><td id="route"></td><td id="truck"></tr>
                </table>

                <div class="form-group">
                    <label>Select Vendor</label>
                    <select id="vendor_select" class="form-control"></select>
                </div>

                <div class="form-group">
                    <label>Remarks *</label>
                    <textarea id="remarks" class="form-control"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="saveVendor">
                    Update Vendor
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function runAutoAllocation() {
    $.post("{{ route('admin.loadsummary.auto.process') }}", {
        _token: '{{ csrf_token() }}'
    })
    .done(function (res) {

        if (res.completed === true) {

            if (res.failed > 0) {

                let rows = res.errors.map(e => `
                    <tr>
                        <td>${e.reference_no}</td>
                        <td>${e.origin} → ${e.destination}</td>
                        <td>${e.truck}</td>
                        <td style="color:red">${e.reason}</td>
                    </tr>
                `).join('');

                Swal.fire({
                    icon: 'warning',
                    title: 'Allocation Completed with Errors',
                    width: '70%',
                    html: `
                        <p><b>Processed:</b> ${res.processed}</p>
                        <p><b>Failed:</b> ${res.failed}</p>
                        <table border="1" width="100%" cellpadding="6">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Route</th>
                                    <th>Truck</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    `
                });

            } else {

                Swal.fire({
                    icon: 'success',
                    title: 'Completed',
                    text: 'All loads allocated successfully.'
                }).then(() => location.reload());
            }
        }
    })
    .fail(() => {
        Swal.fire('Error', 'Unexpected server error', 'error');
    });
}

$(document).ready(function () {
    Swal.fire({
        title: 'Auto Vendor Allocation',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            runAutoAllocation();
        }
    });
});
</script>

<script>
$('.editVendorBtn').click(function () {

    let id = $(this).data('id');

    $.get("{{ url('/admin/loadsummary') }}/" + id + "/edit-vendor", function (res) {

        $('#load_summary_id').val(res.load.id);
        $('#ref_no').text(res.load.reference_no);
        $('#route').text(res.load.origin_name_code + ' → ' + res.load.destination_name_code);
        $('#truck').text(res.load.truck_code);

        let options = '';
        res.vendors.forEach(v => {
            options += `<option value="${v.vendor_code}" data-name="${v.vendor_name}">
                Rank ${v.rank} - ${v.vendor_name}
            </option>`;
        });

        $('#vendor_select').html(options);
        $('#editVendorModal').modal('show');
    });
});

$('#saveVendor').click(function () {

    let vendor = $('#vendor_select option:selected');

    $.post("{{ route('admin.loadsummary.update.vendor') }}", {
        _token: '{{ csrf_token() }}',
        load_summary_id: $('#load_summary_id').val(),
        vendor_code: vendor.val(),
        vendor_name: vendor.data('name'),
        remarks: $('#remarks').val()
    })
    .done(() => {
        Swal.fire('Updated', 'Vendor updated successfully', 'success')
            .then(() => location.reload());
    })
    .fail(res => {
        Swal.fire('Error', res.responseJSON.message, 'error');
    });
});
</script>
<script>

function sendToVendor(loadId, vendor_code,vendor_rank,vendor_code_source, refno, origin, destination) 
{

        Swal.fire({
        title: 'Send Load',
        width: '650px',
        html: `
            <table class="table table-bordered mb-3">
                <thead>
                    <tr style="background:#fce4d6;color:#0070c0">
                        <th>Reference</th>
                        <th>Route</th>
                        <th>Vendor </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>${refno}</b></td>
                        <td>${origin}-${destination}</td>
                        <td>${vendor_code}</td>
                    </tr>
                </tbody>
            </table>

            <textarea id="sendRemarks"
                class="swal2-textarea"
                placeholder="Enter remarks (required)"
                maxlength="500"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        focusConfirm: false,
        preConfirm: () => {
            const remarks = document.getElementById('sendRemarks').value;
            if (!remarks) {
                Swal.showValidationMessage('Remarks are required');
            }
            return remarks;
        }
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post("{{ route('admin.vendor.send') }}", {
            _token: "{{ csrf_token() }}",
            load_id: loadId,
            vendor_code: vendor_code,
            vendor_rank: vendor_rank,
            allocation_source: vendor_code_source,
            remarks: result.value
        }, function (res) {

            if (res.status === 'sent_to_vendor') {
                Swal.fire('Sent to Vendor', res.message, 'success');
            } else if (res.status === 'sent_to_approver') {
                Swal.fire('Approval Required', res.message, 'info');
            } else {
                Swal.fire('Info', res.message, 'warning');
            }

            // Optional
            // setTimeout(() => location.reload(), 800);
        }).fail(xhr => {
            Swal.fire(
                'Error',
                xhr.responseJSON?.message || 'Something went wrong',
                'error'
            );
        });
    });
}


$('.sendBtn').click(function () {
    $.post("{{ route('admin.vendor.send') }}", {
        _token: '{{ csrf_token() }}',
        load_id: $(this).data('load'),
        vendor_code: $(this).data('vendor'),
        vendor_rank: $(this).data('rank'),
        allocation_source: $(this).data('source')
    }, function (res) {

        if (res.status === 'sent_to_vendor') {
            Swal.fire('Sent','Load sent to vendor','success');
        } else {
            Swal.fire('Approval Required','Sent to approver','info');
        }

       // location.reload();
    });
});

</script>


@endsection