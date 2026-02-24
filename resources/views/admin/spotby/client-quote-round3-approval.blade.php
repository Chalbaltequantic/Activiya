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
      left: 90px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 140px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 170px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
     /* min-width: 160px;*/
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
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
    min-width: 120px;
    padding: 8px;
    border: 1px solid #ccc;
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
            <h1 class="m-0">User B1 _Round 2 (Buyer)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">User B1 _Round 2 (Buyer)</li>
				
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
			<div class="alert alert-success alert-dismissible fade show ">
					<strong>{{session('success')}}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
					</div>
					@endif

					@if(session('error'))
						<div class="alert alert-warning alert-dismissible fade show ">
					<strong>{{session('error')}}</strong>
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">User B1 _Round 3 (Approval)</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">User B1 _Round 3 (Buyer) Approval History</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                     <div id="formMessage" style="margin-top:10px;"></div>

					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<form id="approvalForm">					
						@csrf
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>

						<tr>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">No of<br> vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">Goods<br> qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Loading <br>charges</th>
						<th style="background: #ddebf7; color: #0070c0;width: 40px" class="">Unloading<br> charges</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;">Market<br>Average Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Target<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Transit Time<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Final<br>Vendor Name</th>
						
						<th style="background: #fce4d6; color: #0070c0;"><br>Final Rate</th>
						<th style="background: #fce4d6; color: #0070c0;">
						Approve<br>
						  <input type="checkbox" id="selectAllApprove">
					</th>
					<th style="background: #fce4d6; color: #0070c0;">
						Reject<br>
						<input type="checkbox" id="selectAllReject">
					</th>
						
						</tr>
					  </thead>
					  <tbody>
						  @if(count($spotbylist) > 0)
						  @foreach($spotbylist as $spotbydata)
							@php
								$ranks = $spotbydata->quotes->take(5); 
							@endphp
					  
					   <tr data-spotby-id="{{ $spotbydata->id }}">
						<td class="sticky-col-1" style="z-index:999;">{{$spotbydata->reference_no}}</td>
						<td class="sticky-col-2" style="z-index:999;">{{$spotbydata->from}}</td>
						<td class="sticky-col-3" style="z-index:999;">{{$spotbydata->to}}</td>
						<td class="sticky-col-4" style="z-index:999;">{{$spotbydata->vehicle_type}}</td>
					
						<td>{{$spotbydata->valid_from}}</td>
						  <td>{{$spotbydata->valid_upto}}</td>
						  <td>{{$spotbydata->no_of_vehicles}}</td>
						  <td>{{$spotbydata->goods_qty}}</td>
						  
						  <td>{{$spotbydata->uom}}</td>
						  <td>{{$spotbydata->loading_charges}}</td>
						  <td>{{$spotbydata->unloading_charges}}</td>
						  
						  <td>{{$spotbydata->special_instruction}}</td>
						  
						  <td>{{ number_format($spotbydata->market_avg_price, 2) }}</td>
							<td>{{ $spotbydata->target_freight_rate }}</td>
							<td>{{ $spotbydata->target_transit_time}}</td>
							<td>{{ $spotbydata->freeze_vendor_name }}</td>
							<td>{{ $spotbydata->final_rate }}</td>
							
							<td>
							 <input type="checkbox" class="approve-checkbox" name="approvals[{{ $spotbydata->id }}]" value="Approved">
							</td>
							<td>
							<input type="checkbox" class="reject-checkbox" name="approvals[{{ $spotbydata->id }}]" value="Rejected">
							</td>
						</tr>
						  
					@endforeach
				@endif
				  
						</tbody>
					</table>
					

					<div class="text-right my-3">
					    <button type="button" id="submitApproval" class="btn btn-success">💾Submit</button>

					</div>
				
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
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">No of<br> vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">Goods<br> qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Loading <br>charges</th>
						<th style="background: #ddebf7; color: #0070c0;width: 40px" class="">Unloading<br> charges</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;">Market<br>Average Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Target<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Transit Time<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;">Final<br>Vendor Name</th>
						
						<th style="background: #fce4d6; color: #0070c0;"><br>Final Rate</th>
						<th style="background: #fce4d6; color: #0070c0;"><br>Freeze By</th>

						<th style="background: #fce4d6; color: #0070c0;">Approve Status</th>
						<th style="background: #fce4d6; color: #0070c0;">Approved By</th>
						
						</tr>
					  </thead>
					 
					  <tbody>
				  
						 
					   @if(count($historyQuotes) > 0)
						  @foreach($historyQuotes as $historyspotbydata)
						
					  
							   <tr data-spotby-id="{{ $historyspotbydata->id }}">
									<td class="sticky-col-1" style="z-index:999;">{{$historyspotbydata->reference_no}}</td>
									<td class="sticky-col-2" style="z-index:999;">{{$historyspotbydata->from}}</td>
									<td class="sticky-col-3" style="z-index:999;">{{$historyspotbydata->to}}</td>
									<td class="sticky-col-4" style="z-index:999;">{{$historyspotbydata->vehicle_type}}</td>

									<td>{{$historyspotbydata->valid_from}}</td>
									<td>{{$historyspotbydata->valid_upto}}</td>
									<td>{{$historyspotbydata->no_of_vehicles}}</td>
									<td>{{$historyspotbydata->goods_qty}}</td>

									<td>{{$historyspotbydata->uom}}</td>
									<td>{{$historyspotbydata->loading_charges}}</td>
									<td>{{$historyspotbydata->unloading_charges}}</td>

									<td>{{$historyspotbydata->special_instruction}}</td>

									<td>{{ number_format($historyspotbydata->market_avg_price, 2) }}</td>
									<td>{{ $historyspotbydata->target_freight_rate }}</td>
									<td>{{ $historyspotbydata->target_transit_time}}</td>
									<td>{{ $historyspotbydata->freeze_vendor_name }}</td>
									<td>{{ $historyspotbydata->final_rate }}</td>
									<td>{{ $historyspotbydata->freeze_by_name  }}</td>
									<td>
									@if($historyspotbydata->approve_status=='Approved')
									<span class="badge bg-success" style="font-size: 100%;">{{ $historyspotbydata->approve_status }}</span>
									@else
									<span class="badge bg-danger"  style="font-size: 100%;">{{ $historyspotbydata->approve_status }}</span>
									@endif
									</td>
									<td>{{ $historyspotbydata->approved_by_name  }}</td>
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
    //  Select All Approve
    // Select all Approve
    $('#selectAllApprove').on('change', function() {
        $('.approve-checkbox').prop('checked', this.checked).trigger('change');
    });

    // Select all Reject
    $('#selectAllReject').on('change', function() {
        $('.reject-checkbox').prop('checked', this.checked).trigger('change');
    });

    // Prevent approving & rejecting same vendor
    $(document).on('change', '.approve-checkbox', function() {
        if ($(this).is(':checked')) {
            $(this).closest('tr').find('.reject-checkbox').prop('checked', false);
        }
    });

    $(document).on('change', '.reject-checkbox', function() {
        if ($(this).is(':checked')) {
            $(this).closest('tr').find('.approve-checkbox').prop('checked', false);
        }
    });

    //  Submit Form via Ajax
    $('#submitApproval').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.spotby.bulkApproval') }}",
            type: "POST",
            data: $('#approvalForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $('#submitApproval').prop('disabled', true).text('Submitting...');
            },
            success: function (res) {
                if (res.success) {
                    alert(res.message);
                    setTimeout(() => location.reload(), 2000); // reload after 2s
                } else {
                    alert("Something went wrong!");
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            },
            complete: function () {
                $('#submitApproval').prop('disabled', false).text('Submit');
            }
        });
    });
});
 </script>
@endsection