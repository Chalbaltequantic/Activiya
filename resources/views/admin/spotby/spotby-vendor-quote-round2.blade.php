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
      left: 130px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">User S1 _Round 2 (Supplier)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">User S1 _Round 2 (Supplier)</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">User S1 _Round 2 (Supplier) </a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">User S1 _Round 2 (Supplier) Quote</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                   <form id="vendor-round2-form">
				   @csrf
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
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
						<th style="background: #fce4d6; color: #0070c0;width: 40px" class="">Unloading<br> charges</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;z-index:999;">Target<br>Freight Rate</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;">Transit Time</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;">Target<br>Revised <br>Freight Rate</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;">Revised<br>Transit Time</th>   
						   
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($spotbylist) > 0)
						  @foreach($spotbylist as $spotbydata)
					  
					   <tr data-spotby-id="{{ $spotbydata->id }}">
						<td class="sticky-col-1">{{$spotbydata->reference_no}}</td>
						<td class="sticky-col-2">{{$spotbydata->from}}</td>
						<td class="sticky-col-3">{{$spotbydata->to}}</td>
						<td class="sticky-col-4">{{$spotbydata->vehicle_type}}</td>
						<td>{{$spotbydata->valid_from}}</td>
						  <td>{{$spotbydata->valid_upto}}</td>
						  <td>{{$spotbydata->no_of_vehicles}}</td>
						  <td>{{$spotbydata->goods_qty}}</td>
						  
						  <td>{{$spotbydata->uom}}</td>
						  <td>{{$spotbydata->loading_charges}}</td>
						  <td>{{$spotbydata->unloading_charges}}</td>
						  
						  <td>{{$spotbydata->special_instruction}}</td>
						    @foreach($spotbydata->quotes as $quote)
						  <td>{{ $quote->client_revised_price ?? '—' }}</td>
						  <td>{{ $quote->client_revised_transit_time ?? '—' }}</td>
						  @endforeach
						 <td>
                        <input type="number" class="revised-price"
                               name="quotes[{{ $loop->index }}][price]"
                               value="">
                        <input type="hidden" name="quotes[{{ $loop->index }}][spotby_id]" value="{{ $spotbydata->id }}">
                    </td>
                    <td>
                        <input type="number" class="revised-time"
                               name="quotes[{{ $loop->index }}][transit_time]"
                               value="">
                    </td>
						</tr>
						  
						  @endforeach
						  @endif
				  
						</tbody>
					</table>
					<div class="text-right my-3">
					       
						<button type="button" id="saveAllBtn" class="btn btn-success">💾 Save All Quote</button>
					</div>
					 
					</div>
					</form>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						 
						<table id="appointdataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<thead>

						<tr>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1;">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">No of<br> vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">Goods<br> qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Loading <br>charges</th>
						<th style="background: #ddebf7; color: #0070c0;width: 40px;" class="">Unloading<br> charges</th> 
						<th style="background: #ddebf7; color: #0070c0;z-index:999;" class="">Special instruction</th> 
						
						  <th style="background: #fce4d6; color: #0070c0;z-index:999;">Freight Rate</th>
						   <th style="background: #fce4d6; color: #0070c0;z-index:999;">Transit Time</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
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
						 					  
						  <td>{{ $historyspotbydata->quotes->first()->price ?? '-' }}</td>
                            <td>{{ $historyspotbydata->quotes->first()->transit_time ?? '-' }}</td>
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
$(document).ready(function() {
    $('#saveAllBtn').on('click', function() {
        let formData = $('#vendor-round2-form').serialize();

        $.ajax({
            url: "{{ route('admin.vendor.quotes.saveAllRound2') }}",
            type: "POST",
            data: formData + "&_token={{ csrf_token() }}",
            success: function(response) {
                alert(response.message);
				location.reload();
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection