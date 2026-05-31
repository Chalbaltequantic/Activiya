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
      left: 71px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 133px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 180px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Digi Wim Data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Digi Wim</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.digiWim')}}" data-toggle="tab">Create</a></li>
                 
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
					
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							

							<div class="excel-wrapper">
								<table class="table table-bordered excel-table" id="billDataTable">

									<thead>
										<tr>

											<th  class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Indent ID</th>

											<th  class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">Supplier <br>Code</th>

											<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Supplier <br>Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Supplier <br>Location</th>

											<th style="background: #fce4d6; color: #0070c0;">PO No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Invoice/Challan<br>No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Inv/Challan<br>Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee <br>Code</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee <br>Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee<br> Location</th>

											<th style="background: #fce4d6; color: #0070c0;">M.Code*</th>

											<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">Material<br>Descriptions</th>

											<th style="background: #fce4d6; color: #0070c0;">Batch No.</th>

											<th style="background: #fce4d6; color: #0070c0;">MFG Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Expiry Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Qty<br> (Units)</th>

											<th style="background: #fce4d6; color: #0070c0;">Total Cs</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter<br>Code</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter<br>Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Truck No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Ewaybill <br>No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Truck <br>Code</th>

											<th style="background: #fce4d6; color: #0070c0;"> Vehicle  Type</th>

											<th style="background: #fce4d6; color: #0070c0;">Custom</th>

											<th style="background: #fce4d6; color: #0070c0;">Custom 1</th>

											<th style="background: #fce4d6; color: #0070c0;">Custom 2</th>

											<th style="background: #fce4d6; color: #0070c0;">Custom 3</th>

											<th style="background: #fce4d6; color: #0070c0;">Custom 4</th>

										</tr>
									</thead>

									<tbody>

										
										
										  @php($i=1)
										  @if(count($datalist) > 0)
										  @foreach($datalist as $digiwimdata)
									  
									   <tr>
										<td class="sticky-col-1">{{$digiwimdata->indent_id}}</td>
										<td class="sticky-col-2">{{$digiwimdata->supplier_code}}</td>
										<td class="sticky-col-3">{{$digiwimdata->supplier_name}}</td>
										<td>{{$digiwimdata->supplier_location }}</td>

										<td>{{$digiwimdata->po_no}}</td>

										<td>  {{$digiwimdata->invoice_challan_no}}</td>

										<td>
										{{ !empty($digiwimdata->invoice_challan_date) ? \Carbon\Carbon::parse($digiwimdata->invoice_challan_date)->format('Y-m-d') : '' }}
										</td>

										<td>{{$digiwimdata->consignee_code}}</td>

										<td>{{$digiwimdata->consignee_name}}
										</td>

										<td>{{$digiwimdata->consignee_location}}
										</td>

										<td>{{$digiwimdata->m_code}}
										</td>

										<td>{{$digiwimdata->material_descriptions}}
										</td>

										<td>{{$digiwimdata->batch_no}}
										</td>

										<td>{{$digiwimdata->mfg_date}}
										</td>

										<td>{{$digiwimdata->expiry_date}}
										</td>

											<td>
												{{$digiwimdata->qty_units}}
											</td>

											<td>
												{{$digiwimdata->total_cs}}
											</td>

											<td>
												{{$digiwimdata->transporter_code}}
											</td>

											<td>
												{{$digiwimdata->transporter_name}}
											</td>

											<td>
												{{$digiwimdata->truck_no}}
											</td>

											<td>
												{{$digiwimdata->lr_no}}
											</td>

											<td>
											{{$digiwimdata->lr_date}}
											</td>

											<td>
											{{$digiwimdata->ewaybill_no}}
											</td>

											<td>
												{{$digiwimdata->truck_code}}
											</td>

											<td>
												{{$digiwimdata->vehicle_type}}
											</td>

											<td>
												{{$digiwimdata->custom}}
											</td>

											<td>
											{{$digiwimdata->custom_1}}
											</td>

											<td>
												{{$digiwimdata->custom_2}}
											</td>

											<td>
												{{$digiwimdata->custom_3}}
											</td>

											<td>
												{{$digiwimdata->custom_4}}
											</td>

										</tr>

										@endforeach
									@endif
									</tbody>

								</table>
							</div>
							
						</div>

						

					
                  </div>
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
@endsection