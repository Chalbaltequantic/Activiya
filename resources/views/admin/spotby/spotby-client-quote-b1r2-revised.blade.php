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
      left: 170px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 220px; /* Adjust based on col-1 width */
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">User B1 _Round 2 (Buyer)</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">User B1 _Round 2 (Buyer) Quote</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<form id="clientOfferForm">					
						@csrf
						<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>

						<tr>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Rank1</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank3</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank4</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank5</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">R1 Vendor</th>
						<th style="background: #ddebf7; color: #0070c0;width: 40px;" class="">R2 Vendor</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">R3 Vendor</th> 						
						<th style="background: #fce4d6; color: #0070c0;">R4 Vendor</th>
						<th style="background: #fce4d6; color: #0070c0;">R5 Vendor</th>
						<th style="background: #fce4d6; color: #0070c0;">L1 Freight<br>Rate</th>
						<th style="background: #fce4d6; color: #0070c0;">Minimum<br>Transit Time</th>
						<th style="background: #fce4d6; color: #0070c0;">Target<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;"><br>Transit Time</th>
						</tr>
					  </thead>
					  <tbody>
						  @if(count($spotbylist) > 0)
						  @foreach($spotbylist as $spotbydata)
							@php
								$ranks = $spotbydata->quotes->take(5); 
							@endphp
					  
					   <tr data-spotby-id="{{ $spotbydata->id }}">
						<td class="sticky-col-1">{{$spotbydata->reference_no}}</td>
						<td class="sticky-col-2">{{$spotbydata->from}}</td>
						<td class="sticky-col-3">{{$spotbydata->to}}</td>
						<td class="sticky-col-4">{{$spotbydata->vehicle_type}}</td>
						
						@for($i=0; $i<5; $i++)
                        <td>{{ $ranks[$i]->price ?? '' }}</td>
						@endfor
						
						@for($i=0; $i<5; $i++)
							<td>{{ $ranks[$i]->vendor->vendor_name ?? '' }}</td>
						@endfor
						 					  
						<td>{{ $ranks[0]->price ?? '' }}</td>
						<td>{{ $ranks[0]->transit_time ?? '' }}</td>
						<td>
							<input type="hidden" name="spotby_id[]" value="{{ $spotbydata->id }}">
							<input type="number" step="0.01" name="client_price[]"
                               class="" placeholder="Enter Price">
						</td>
						<td>
							<input type="number" name="client_time[]"
                               placeholder="Enter Transit Time">			
						</td>
						</tr>
						  
					@endforeach
				@endif
				  
						</tbody>
					</table>
					<input type="hidden" name="round" value="1">
					<div id="responseMsg" class="mt-3"></div>
					<div class="text-right my-3">
						<button type="submit" id="saveAllVendors" class="btn btn-success">💾 Save All Quote</button>
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

						<th style="background: #fce4d6; color: #0070c0;" class="">Rank1</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank3</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank4</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Rank5</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">R1 Vendor</th>
						<th style="background: #ddebf7; color: #0070c0;width: 40px;" class="">R2 Vendor</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">R3 Vendor</th> 						
						<th style="background: #fce4d6; color: #0070c0;">R4 Vendor</th>
						<th style="background: #fce4d6; color: #0070c0;">R5 Vendor</th>
						<th style="background: #fce4d6; color: #0070c0;">L1 Freight<br>Rate</th>
						<th style="background: #fce4d6; color: #0070c0;">Minimum<br>Transit Time</th>
						<th style="background: #fce4d6; color: #0070c0;">Target<br>Freight Price</th>
						<th style="background: #fce4d6; color: #0070c0;"><br> Transit Time</th>
						</tr>
					  </thead>
					 
					  <tbody>
				  
						 
					   @if(count($historyQuotes) > 0)
						  @foreach($historyQuotes as $historyspotbydata)
							@php
								$ranks = $historyspotbydata->quotes->take(5); 
							@endphp
					  
							   <tr data-spotby-id="{{ $historyspotbydata->id }}">
								<td class="sticky-col-1" style="z-index:999;">{{$historyspotbydata->reference_no}}</td>
								<td class="sticky-col-2" style="z-index:999;">{{$historyspotbydata->from}}</td>
								<td class="sticky-col-3" style="z-index:999;">{{$historyspotbydata->to}}</td>
								<td class="sticky-col-4" style="z-index:999;">{{$historyspotbydata->vehicle_type}}</td>
								
								@for($i=0; $i<5; $i++)
								<td>{{ $ranks[$i]->price ?? '' }}</td>
								@endfor

								
								@for($i=0; $i<5; $i++)
									<td>{{ $ranks[$i]->vendor->vendor_name ?? '' }}</td>
								@endfor
													  
								<td>{{ $ranks[0]->price ?? '' }}</td>
								<td>{{ $ranks[0]->transit_time ?? '' }}</td>
								<td>
								{{$ranks[0]->client_revised_price ?? ''}}
								</td>
								<td>
								{{$ranks[0]->client_revised_transit_time ?? ''}}		
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
$('#clientOfferForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('admin.client.offers.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#responseMsg').html(
                '<div class="alert alert-success">' + response.message + '</div>'
            );
			setTimeout(function() {
				location.reload();
			}, 2000); 
        },
        error: function(xhr) {
            $('#responseMsg').html(
                '<div class="alert alert-danger">Error saving data!</div>'
            );
        }
    });
});
</script>
@endsection