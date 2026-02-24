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
      left: 70px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
.sticky-col-3 {
      position: sticky;
      left: 170px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Manual Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Manual Indent</li>
				
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
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.manualloadsummary')}}">Create</a></li>
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.manualloadSummarydatalist')}}">Indent</a></li>
				
				
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                 
                  <!-- /.tab-pane -->
                  <div class="tab-pane active" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1">Reference<br>no</th>
								<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2">Origin<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">Destination<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;">Mode</th>
								<th style="background: #fce4d6; color: #0070c0;">Truck Code</th>
								<th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
								<th style="background: #fce4d6; color: #0070c0;">Qty</th>
								<th style="background: #fce4d6; color: #0070c0;">Weight(Kg)</th>
								<th style="background: #fce4d6; color: #0070c0;">CFT</th>
								<th style="background: #fce4d6; color: #0070c0;">No. of<br>Cases</th>
								<th style="background: #fce4d6; color: #0070c0;">Required<br>Pickup Date</th>
								<th style="background: #fce4d6; color: #0070c0;">Indent Instruction</th>
								<th style="background: #fce4d6; color: #0070c0;">Remarks</th>
								{{--<th style="background: #c6e0b4; color: #0070c0;">Edit</th>
								<th style="background: #c6e0b4; color: #0070c0;">Send</th>--}}
											  
								</tr>
						  </thead>
						<tbody>
							@if(count($loads) > 0)
							 @foreach($loads as $row)
							  
							<tr>
								<td class="sticky-col-1">{{ $row->reference_no }}</td>
								<td class="sticky-col-2">{{ $row->origin_name_code }} - {{ $row->origin_name }}</td>
								
								<td class="sticky-col-3">{{ $row->destination_name_code }} - {{ $row->destination_name }}</td>
								<td>{{ $row->t_mode }}</td>
								<td>{{ $row->truck_code ?? 'NA' }}</td>
								<td>{{ $row->truck_name ?? 'NA' }}</td>
								<td>{{ $row->qty ?? 'NA' }}</td>
								<td>{{ $row->total_weight ?? 'NA'}}</td>
								<td>{{ $row->total_volume ?? 'NA'}}</td>
								<td>{{ $row->no_of_cases ?? 'NA'}}</td>
								<td>{{ $row->required_pickup_date ?? 'NA'}}</td>
								<td>{{ $row->indent_instructions ?? 'NA'}}</td>
								<td>{{ $row->remarks ?? 'NA'}}</td>
									{{--<td><button	class="btn btn-sm btn-warning editVendorBtn"
											data-id="{{ $row->id }}">Edit</button>
								
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
									</td>--}}

									
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
function runAutoAllocation() {
    $.post("{{ route('admin.manualloadsummary.vendor.allocation.process') }}", {
        _token: '{{ csrf_token() }}'
    })
    .done(function (res) {

        // CASE 1: Nothing new close loader
        if (res.completed === false) {
            Swal.close();
            return;
        }

        // CASE 2: Completed with errors
        if (res.completed === true && res.failed > 0) {

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
                title: 'Indent Completed with Errors',
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

            return;
        }

        // CASE 3: Completed successfully
        if (res.completed === true) {
            Swal.fire({
                icon: 'success',
                title: 'Completed',
                text: 'All Indent allocated successfully.'
            }).then(() => location.reload());
        }

    })
    .fail(() => {
        Swal.close();
        Swal.fire('Error', 'Unexpected server error', 'error');
    });
}

$(document).ready(function () {

    Swal.fire({
        title: 'Manual Indent Loading',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            runAutoAllocation();
        }
    });

});
</script>

@endsection