@extends('admin.admin')
@section('bodycontent')


<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">



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
      left: 150px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 230px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">User B1 _Round 1 (Buyer)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">User B1 _Round 1 (Buyer)</li>
				
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
          <div class="col-lg-12">
            <div class="card">
			<div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">User B1 _Round 1 (Buyer)</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">User B1 _Round 1 (Buyer) History</a></li>

                </ul>
              </div><!-- /.card-header -->
			
              <div class="card-body p-0">
			  <div class="tab-content">
                  <div class="active tab-pane" id="activity">			  
			  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>

						<tr>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-1;">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">No of<br> vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">Goods<br> qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Loading <br>charges</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Unloading<br> charges</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="">RFQ start date time</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">RFQ end date time</th>						
						  <th style="background: #fce4d6; color: #0070c0;">Created date</th>
						  
						  <th style="background: #c6e0b4; color: #0070c0;">Action</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($spotbylist) > 0)
						  @foreach($spotbylist as $spotbydata)
					  
					   <tr>
						<td class="sticky-col-1" style="">{{$spotbydata->reference_no}}</td>
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
						  <td>{{$spotbydata->rfq_start_date_time}}</td>					  
						  <td>{{$spotbydata->rfq_end_date_time}}</td>					  
						  <td>{{$spotbydata->created_at}}</td>
						  
						  
						  
						 <td>
							<select class="form-control selectpicker vendor-select"
                                  data-spotby-id="{{ $spotbydata->id }}"
                                  multiple
                                  data-live-search="true"
                                  data-actions-box="false"
                                  data-max-options="7" data-width="150px" data-selected-text-format="count > 2">
                              @foreach($vendors as $vendor)
                                  <option value="{{ $vendor->id }}"
                                    {{ in_array($vendor->id, $spotbydata->vendors->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $vendor->vendor_name }}
                                  </option>
                              @endforeach
                          </select>
						</td>
						</tr>
						  
						  @endforeach
						  @endif
				  
						</tbody>
					</table>
					<div class="text-right my-3">
						<button id="saveAllVendors" class="btn btn-success">💾 Save All Vendors</button>
					</div>
				</div>
				
				</div>
				 <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
					<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>

						<tr>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-1;">Reference No</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-2">From</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-3">To</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-4">Vehicle type</th>				

						<th style="background: #fce4d6; color: #0070c0;" class="">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">No of<br> vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">Goods<br> qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Loading <br>charges</th>
						<th style="background: #ddebf7; color: #0070c0;" class="">Unloading<br> charges</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="">RFQ start date time</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">RFQ end date time</th>						
						  <th style="background: #c6e0b4; color: #0070c0;">Created date</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;">Vendors</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($historyspotbylist) > 0)
						  @foreach($historyspotbylist as $historyspotbydata)
					  
					   <tr>
						<td class="sticky-col-1" style="">{{$historyspotbydata->reference_no}}</td>
						<td class="sticky-col-2">{{$historyspotbydata->from}}</td>
						<td class="sticky-col-3">{{$historyspotbydata->to}}</td>
						<td class="sticky-col-4">{{$historyspotbydata->vehicle_type}}</td>
						<td>{{$historyspotbydata->valid_from}}</td>
						  <td>{{$historyspotbydata->valid_upto}}</td>
						  <td>{{$historyspotbydata->no_of_vehicles}}</td>
						  <td>{{$historyspotbydata->goods_qty}}</td>
						  
						  <td>{{$historyspotbydata->uom}}</td>
						  <td>{{$historyspotbydata->loading_charges}}</td>
						  <td>{{$historyspotbydata->unloading_charges}}</td>
						  
						  <td>{{$historyspotbydata->special_instruction}}</td>
						  <td>{{$historyspotbydata->rfq_start_date_time}}</td>
						  <td>{{$historyspotbydata->rfq_end_date_time}}</td>
						  <td>{{$historyspotbydata->created_at}}</td>					  
						 <td>
							{{ $vendors->filter(function($vendor) use ($historyspotbydata) {
								return in_array($vendor->id, $historyspotbydata->vendors->pluck('id')->toArray());
							})->pluck('vendor_name')->implode(', ') ?? 'NA' }}
                          </select>
						</td>
						</tr>
						  
						  @endforeach
						  @endif
				  
						</tbody>
					</table>
					
				</div>
				
				  </div>	
				 <!-- /The timeline -->
				 </div> <!-- Tab pane -->
			</div>
			
			
			
			
          </div>
		 </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<script>
$(document).ready(function() {
    
    $('#saveAllVendors').click(function() {
        let data = {};

		$('.vendor-select').each(function() {
			let spotbyId = $(this).data('spotby-id');
			let vendorIds = $(this).val() || []; // ensure array
			if (spotbyId !== undefined && spotbyId !== "") {
				data[spotbyId] = vendorIds;
			}
		});
		//console.log(data);
        $.ajax({
            url: "{{ route('admin.spotby.vendors.bulkStore') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                vendors: data
            },
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

