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
            <h1 class="m-0">Track Placement Status</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Track Placement Status</li>
				
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
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.loadsummary_auto_allocation') }}">Indent Allocation</a></li>
				  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.track.placement.status') }}">Track Placement Status</a></li>
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
								{{-- <th style="background: #fce4d6; color: #0070c0;">ZW uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Zv uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Gross<br>utilization</th>--}}
								<th style="background: #fce4d6; color: #0070c0;">Total<br>Wt</th>
								<th style="background: #fce4d6; color: #0070c0;">Total<br>Vol</th>
								<th style="background: #fce4d6; color: #0070c0;">Vendor<br>name</th>
								<th style="background: #fce4d6; color: #0070c0;">Vehicle<br>no</th>
								{{--<th style="background: #fce4d6; color: #0070c0;">Driver<br>name</th>--}}
								<th style="background: #fce4d6; color: #0070c0;">Driver<br>no.</th>
								<th style="background: #fce4d6; color: #0070c0;">Sent<br>Date</th>
								<th style="background: #fce4d6; color: #0070c0;">Last<br>Status</th>
								<th style="background: #c6e0b4; color: #0070c0;">LR No.</th>
								<th style="background: #c6e0b4; color: #0070c0;">Remarks</th>
								
								<th style="background: #c6e0b4; color: #0070c0;">Action</th>
								
													  
								</tr>
						  </thead>
						<tbody>
							@if(count($loads) > 0)
							 @foreach($loads as $row)							  
							<tr data-load-id="{{ $row->id }}" data-current="{{ $row->placement_status }}">
								<td class="sticky-col-1">{{ $row->reference_no }}</td>
								<td class="sticky-col-2">{{ $row->origin_name_code }} {{ $row->origin_name }}</td>								
								<td class="sticky-col-3">{{ $row->destination_name_code }} {{ $row->destination_city }}</td>
								<td>{{ $row->t_mode }}</td>
								<td>{{ $row->truck->description ?? 'NA' }}</td>
									{{-- <td>{{ $row->zw_util }}%</td>
								<td>{{ $row->zv_util }}%</td>
									<td>{{ $row->gross_util }}%</td>--}}
								
								<td>{{ $row->total_weight }}</td>
								<td>{{ $row->total_volume }}</td>
								<td>{{ $row->vendor_name }}</td>
								<td>{{ $row->latestSendHistory->vehicle_number }}</td>
									{{-- <td>{{ $row->latestSendHistory->driver_name }}</td>--}}
								<td>{{ $row->latestSendHistory->driver_mobile }}</td>
								<td>{{ $row->sent_at  }}</td>
								<td>
									@if($row->latestPlacement)
										<span class="badge bg-info">
											{{ ucfirst(str_replace('_',' ', $row->latestPlacement->placement_status)) }}
										</span>
									@else
										<span class="badge bg-secondary">--</span>
									@endif
								</td>
								<td>
								{{ $row->latestPlacement->lr_no }}
								</td>
								<td>
								{{ $row->latestPlacement->remarks }}
								</td>
								
								<td><a href="javascript:void(0)"
									   class="view-history text-primary ml-2">
									   View History
									</a>
								
								
								</td>
							</tr> 
							<tr class="history-row" style="display:none">
								<td colspan="14">
									<div class="history-content"></div>
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
$(document).on('click','.view-history',function () {

    let row = $(this).closest('tr');
    let historyRow = row.next('.history-row');
    let loadId = row.data('load-id');

    if (historyRow.is(':visible')) {
        historyRow.slideUp();
        return;
    }

    $.get(`/admin/placement/history/${loadId}`, function (res) {

        let html = '<ul class="list-group">';

        res.forEach(item => {
            html += `
                <li class="list-group-item">
                    <strong>${item.placement_status}</strong>
                    by ${item.admin?.name ?? 'System'}
                    <br>
                    <small>${item.created_at}</small>
                </li>
            `;
        });

        html += '</ul>';

        historyRow.find('.history-content').html(html);
        historyRow.slideDown();
    });
});
</script>
@endsection