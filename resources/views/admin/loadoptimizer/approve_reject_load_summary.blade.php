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
            <h1 class="m-0">Approve Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Approve Indent</li>
				
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
                 
				<li class="nav-item"><a class="nav-link active" href="{{route('admin.loadSummaryApproval')}}">Approve Allocation</a></li>
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
								<th style="background: #fce4d6; color: #0070c0;">Status</th>
								<th style="background: #fce4d6; color: #0070c0;">Reason for<br>approval</th>
								<th style="background: #fce4d6; color: #0070c0;">Approval Date</th>
								<th style="background: #fce4d6; color: #0070c0;">Remarks</th>
								<th style="background: #fce4d6; color: #0070c0;">Action</th>
													  
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
								<td>
									@php
										if ($row->gross_util < 80) {
											$color = 'danger'; // red
										} elseif ($row->gross_util <= 90) {
											$color = 'warning'; // yellow
										} else {
											$color = 'success'; // green
										}
									@endphp
									<span class="btn btn-{{ $color }} btn-sm">&nbsp;</span>
									
								</td>
								<td>{{$row->approval_remark}}</td>
								<td>{{ \Carbon\Carbon::parse($row->approval_sent_at)->format('jS M, Y') }}</td>

								<td>
									<input type="text" class="reason" id="remark_{{$row->id}}" name="remark[{{ $row->reference_no }}]" required>
								
								</td>
								<td class="text-center">
									<button class="btn btn-success" onclick="updateStatus({{ $row->id }}, 'APPROVED', '{{ $row->source_type }}')">Approve</button>

									<button class="btn btn-danger" onclick="updateStatus({{ $row->id }}, 'REJECTED','{{ $row->source_type }}')">Reject</button>
										
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

function updateStatus(id, status, source_type) {
    let row = $('#row-' + id);
    let reason = $('#remark_' + id).val();

    $.ajax({
        url: "{{ route('admin.load.summary.ApproveReject', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            status: status,
            reason: reason,
			source_type:source_type
        },
        success: function (res) {
            alert(res.message);

            // Remove row only on success
            if (res.status === 'success') {
                row.fadeOut(300, function () {
                    $(this).remove();
					location.reload();
                });
            }
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || 'Something went wrong');
        }
    });
}

</script>

@endsection