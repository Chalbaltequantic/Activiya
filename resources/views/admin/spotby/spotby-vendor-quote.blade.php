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
      left: 210px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 300px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
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

/* Prevent table cells from expanding unnecessarily */
#billDataTable th, #billDataTable td {
    padding: 4px 8px !important;
    white-space: nowrap;
}

/* Compact size for form inputs in table */
#billDataTable input {
    width: 100px !important;
    max-width: 100%;
    padding: 2px 5px;
    font-size: 13px;
}
	
	
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">User S1 _Round 1 (Supplier)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">User S1 _Round 1 (Supplier)</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">User S1 _Round 1 (Supplier) </a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">User S1 _Round 1 (Supplier) Quote</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<table id="billDataTable" style="width:100%" class="table table-bordered border-dark table-hover enable-responsive nowrap">
					  <thead>

						<tr>
						<th class="dtr-control"></th>
						<th class="all" style="background: #fce4d6; color: #0070c0;">Reference No</th>
						<th class="all" style="background: #fce4d6; color: #0070c0;">From</th>
						<th class="all" style="background: #fce4d6; color: #0070c0;">To</th>
						<th class="all" style="background: #fce4d6; color: #0070c0;">Vehicle type</th>				
			

						<th class="desktop" style="background: #fce4d6; color: #0070c0;">Valid from</th>
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">Valid upto</th>
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">No of<br> vehicles</th>
						
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">Goods<br>qty</th>
						
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">UOM</th>
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">Loading <br>charges</th>
						<th data-priority="16" style="background: #fce4d6; color: #0070c0;">Unloading<br> charges</th> 
						<th class="desktop" style="background: #fce4d6; color: #0070c0;">Special<br>instruction</th> 
						
						  <th class="all" style="background: #c6e0b4; color: #0070c0;">Freight Rate</th>
						   <th class="all" style="background: #c6e0b4; color: #0070c0;">TAT</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($spotbylist) > 0)
						  @foreach($spotbylist as $spotbydata)
					  
					   <tr data-spotby-id="{{ $spotbydata->id }}">
					   <td></td>
						<td>{{$spotbydata->reference_no}}</td>
						<td>{{$spotbydata->from}} - {{$spotbydata->source_city}}</td>
						<td>{{$spotbydata->to}} - {{$spotbydata->destination_city}}</td>
						<td>{{$spotbydata->vehicle_type}}</td>
						<td>{{$spotbydata->valid_from}}</td>
						  <td>{{$spotbydata->valid_upto}}</td>
						  <td>{{$spotbydata->no_of_vehicles}}</td>
						  <td>{{$spotbydata->goods_qty}}</td>
						  
						  <td>{{$spotbydata->uom}}</td>
						  <td>{{$spotbydata->loading_charges}}</td>
						  <td>{{$spotbydata->unloading_charges}}</td>
						  
						  <td>{{$spotbydata->special_instruction}}</td>
						 					  
						 <td>
						   <input type="number" class="price" step="0.01" placeholder="Enter Price">
						</td>
						 <td>
						  <input type="text" class="transit_time char-3" placeholder="Transit Time">
						  </td>
						</tr>
						  
						  @endforeach
						  @endif
				  
						</tbody>
					</table>
					<div class="text-right my-3">
						<button id="saveAllVendors" class="btn btn-success">💾 Save All Quote</button>
					</div>
					 
					</div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						 
						<table id="appointdataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<thead>

						<tr>
						<th data-priority="1" style="background: #fce4d6; color: #0070c0;z-index:999;" class="{{ (count($historyQuotes) > 0) ? 'sticky-col-1':'' }}">Reference No</th>
						<th data-priority="2" style="background: #fce4d6; color: #0070c0;z-index:999;" class="{{ (count($historyQuotes) > 0) ? 'sticky-col-2':'' }}">From</th>
						<th data-priority="3" style="background: #fce4d6; color: #0070c0;z-index:999;" class="{{ (count($historyQuotes) > 0) ? 'sticky-col-3':'' }}">To</th>
						<th data-priority="4" style="background: #fce4d6; color: #0070c0;z-index:999;" class="{{ (count($historyQuotes) > 0) ? 'sticky-col-4':'' }}">Vehicle type</th>				

						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">Valid from</th>
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">Valid upto</th>
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">No of<br> vehicles</th>
						
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">Goods<br> qty</th>
						
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">UOM</th>
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;">Loading <br>charges</th>
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;width: 40px;">Unloading<br> charges</th> 
						<th data-priority="1000" style="background: #fce4d6; color: #0070c0;z-index:999;">Special instruction</th> 
						
						  <th data-priority="5" style="background: #fce4d6; color: #0070c0;z-index:999;">Freight Rate</th>
						   <th data-priority="6" style="background: #fce4d6; color: #0070c0;z-index:999;">Transit Time</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($historyQuotes) > 0)
						  @foreach($historyQuotes as $historyspotbydata)
					  
					   <tr data-spotby-id="{{ $historyspotbydata->id }}">
						<td class="sticky-col-1" style="z-index:999;">{{$historyspotbydata->reference_no}}</td>
						<td class="sticky-col-2" style="z-index:999;">{{$historyspotbydata->from}} - {{$historyspotbydata->source_city}}</td>
						<td class="sticky-col-3" style="z-index:999;">{{$historyspotbydata->to}} - {{$historyspotbydata->destination_city}}</td>
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
    $('#saveAllVendors').click(function() {
        let quotes = [];

        $('#billDataTable tbody tr').each(function() {
            let spotbyId = $(this).data('spotby-id');
            let price = $(this).find('.price').val();
            let transitTime = $(this).find('.transit_time').val();

            if (price && transitTime) {
                quotes.push({
                    spotby_id: spotbyId,
                    price: price,
                    transit_time: transitTime
                });
            }
        });

        if (quotes.length === 0) {
            alert('Please enter at least one quote before saving.');
            return;
        }

        $.ajax({
            url: "{{ route('admin.vendor.quotes.saveAll') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                quotes: quotes
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Something went wrong while saving quotes.');
            }
        });
    });
});
</script>
@endsection