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
            <h1 class="m-0">V_Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">V_Indent</li>
				
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
                  
				  {{--  <li class="nav-item"><a class="nav-link" href="{{ route('admin.loadsummary_auto_allocation') }}">Indent Allocation</a></li>--}}
				  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.vendor.loads') }}">V_Indent</a></li>
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
								<th style="background: #c6e0b4; color: #0070c0;">Remarks</th>
								<th style="background: #c6e0b4; color: #0070c0;">Sent at</th>
								<th style="background: #c6e0b4; color: #0070c0;">Action</th>
								
													  
								</tr>
						  </thead>
						<tbody>
							@if(count($loads) > 0)
							 @foreach($loads as $row)							  
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
								<td class="fw-bold">{{ $row->sent_remarks  }}</td>
								<td class="fw-bold">{{ $row->sent_at  }}</td>
								<td>
								@if( $row->sent_status!='accepted')
									<button
										class="btn btn-success btn-sm accept-load"
										data-source_type = "{{$row->source_type}}" data-id="{{ $row->id }}" data-reference="{{$row->reference_no}}">
										Accept
									</button>
								@endif	
								@if( $row->sent_status=='accepted')
									 <button class="btn btn-warning btn-sm"
											onclick="assignLoad({{ $row->id }},'{{ $row->reference_no }}',
											'{{ $row->origin_name_code }}',
											'{{ $row->destination_name_code }}',
											'{{ $row->truck_code ?? 'NA' }}'
											, '{{$row->source_type}}')">
										Deploy
									</button>
								@endif	
									<button class="btn btn-danger btn-sm"
											onclick="rejectLoad({{ $row->id }},'{{ $row->reference_no }}',
												'{{ $row->origin_name_code }}',
												'{{ $row->destination_name_code }}',
												'{{ $row->truck_code ?? 'NA' }}', '{{$row->source_type}}'
												)">
										Deny
									</button>
								
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
function assignLoad(id, reference, origin, destination, truck, source_type)
{
    Swal.fire({
        title: 'Deploy Load',
        width: '800px',
        html: `
            <table class="table table-bordered mb-3" style="text-align:left">
                <thead>
                    <tr style="background:#fce4d6;color:#0070c0">
                        <th>Reference</th>
                        <th>Route</th>
                        <th>Truck</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>${reference}</b></td>
                        <td>${origin} → ${destination}</td>
                        <td>${truck}</td>
                    </tr>
                </tbody>
            </table>
	<table><tr><td>
            <input id="vehicle" class="swal2-input" placeholder="Vehicle Number"></td>
            <td><input id="driver" class="swal2-input" placeholder="Driver Name"></td>
            <td><input id="mobile" class="swal2-input" placeholder="Driver Mobile"></td>
		   </tr>
		   </table>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {

            const vehicle = document.getElementById('vehicle').value;
            const driver  = document.getElementById('driver').value;
            const mobile  = document.getElementById('mobile').value;

            if (!vehicle || !driver || !mobile) {
                Swal.showValidationMessage('Vehicle, Driver and Mobile are required');
                return false;
            }

            return {
                vehicle: vehicle,
                driver: driver,
                mobile: mobile
            };
        }
    }).then(result => {

        if (!result.isConfirmed) return;

        $.post("{{ route('admin.vendor.load.deploy') }}", {
            _token: "{{ csrf_token() }}",
            id: id,
            vehicle_number: result.value.vehicle,
            driver_name: result.value.driver,
            driver_mobile: result.value.mobile,
            remarks: result.value.remarks,
            source_type: source_type,
            reference_no: reference
        }, function(res){
            Swal.fire('Accepted', res.message, 'success')
                .then(() => location.reload());
        }).fail(err => {
            Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
        });
    });
}

function rejectLoad(id, reference, origin, destination, truck, source_type)
{
    Swal.fire({
        title: 'Reject Load',
        icon: 'warning',
        width: '800px',
        html: `
            <table class="table table-bordered mb-3">
                <thead>
                    <tr style="background:#fce4d6;color:#0070c0">
                        <th>Reference</th>
                        <th>Route</th>
                        <th>Truck</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>${reference}</b></td>
                        <td>${origin} → ${destination}</td>
                        <td>${truck}</td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-danger text-start">
                ⚠️ Rejecting this load may attract penalty as per contract.
            </div>

            <textarea id="remarks" class="swal2-textarea"
                placeholder="Reason for rejection (mandatory)"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirm Reject',
        preConfirm: () => {
            const remarks = document.getElementById('remarks').value;
            if (!remarks) {
                Swal.showValidationMessage('Rejection reason is required');
                return false;
            }
            return { remarks };
        }
    }).then(result => {

        if (!result.isConfirmed) return;

        $.post("{{ route('admin.vendor.load.reject') }}", {
            _token: "{{ csrf_token() }}",
            id: id,
            remarks: result.value.remarks,
            reference_no:reference,
            source_type: source_type
        }, function(res){
            Swal.fire('Rejected', res.message, 'success')
                .then(() => location.reload());
        }).fail(err => {
            Swal.fire('Error', err.responseJSON?.message || 'Something went wrong', 'error');
        });
    });
}

</script>
<script>
/*When vendor Accept the load*/
$(document).on('click', '.accept-load', function () {
    let id = $(this).data('id');
    let source_type = $(this).data('source_type');
    let reference_no = $(this).data('reference');

    if (!confirm('Do you want to accept this load?')) return;

    $.ajax({
        url: `/admin/vendor/load/${id}/accept`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
			source_type:source_type,
			reference_no:reference_no
        },
        success: function (res) {
            alert(res.message);
            location.reload(); // or update row status dynamically
        },
        error: function (xhr) {
            alert(xhr.responseJSON.message || 'Something went wrong');
        }
    });
});



</script>

@endsection