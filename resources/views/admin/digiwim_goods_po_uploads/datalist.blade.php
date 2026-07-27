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
            <h1 class="m-0">Digi Wim M Goods PO uploads Data</h1>
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.digiwim-goods-po.index')}}" data-toggle="tab">Create</a></li>
                 
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
					
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							

							<div class="excel-wrapper">
								<table class="table table-bordered excel-table" id="billDataTable">
									<thead>

										<tr>

										<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Buyer Code</th>
										<th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">Buyer Name</th>
										<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Buyer Location</th>

										<th style="background: #fce4d6; color: #0070c0;">Bill To Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Bill To Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Bill To Location</th>

										<th style="background: #fce4d6; color: #0070c0;">Ship To Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Ship To Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Ship To Location</th>

										<th style="background: #fce4d6; color: #0070c0;">Supplier Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Supplier Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Supplier Location</th>

										<th style="background: #fce4d6; color: #0070c0;">P.O.No.</th>
										<th style="background: #fce4d6; color: #0070c0;">Date</th>

										<th style="background: #fce4d6; color: #0070c0;">Material Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Material Description</th>

										<th style="background: #fce4d6; color: #0070c0;">Qty.(Units)</th>
										<th style="background: #fce4d6; color: #0070c0;">Total Cs</th>
										<th style="background: #fce4d6; color: #0070c0;">Rate Per Unit</th>
										<th style="background: #fce4d6; color: #0070c0;">Tax</th>
										<th style="background: #fce4d6; color: #0070c0;">Conversion</th>
										<th style="background: #fce4d6; color: #0070c0;">Discount</th>
										<th style="background: #fce4d6; color: #0070c0;">Inco Terms</th>
										<th style="background: #fce4d6; color: #0070c0;">Freight</th>

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
									  @foreach($datalist as $digiwimmuploaddata)
									  
									   <tr>
										<td class="sticky-col-1">{{$digiwimmuploaddata->buyer_code }}</td>
										<td class="sticky-col-2">{{$digiwimmuploaddata->buyer_name}}</td>
										<td class="sticky-col-3">{{$digiwimmuploaddata->buyer_location}}</td>
										<td>{{$digiwimmuploaddata->bill_to_code}}</td>

										<td>{{$digiwimmuploaddata->bill_to_name}}</td>

										<td>  {{$digiwimmuploaddata->bill_to_location}}</td>

										<td>
										{{ $digiwimmuploaddata->ship_to_code }}
										</td>

										<td>{{$digiwimmuploaddata->ship_to_name}}</td>

										<td>{{$digiwimmuploaddata->ship_to_location}}
										</td>

										<td>{{$digiwimmuploaddata->consignee_location}}
										</td>

										<td>{{$digiwimmuploaddata->supplier_code}}
										</td>

										<td>{{$digiwimmuploaddata->supplier_name}}
										</td>

										<td>{{$digiwimmuploaddata->supplier_location}}
										</td>

										<td>{{$digiwimmuploaddata->po_no }}
										</td>

										<td>{{$digiwimmuploaddata->po_date}}
										</td>

											<td>
												{{$digiwimmuploaddata->material_code}}
											</td>

											<td>
												{{$digiwimmuploaddata->material_description}}
											</td>

											<td>
												{{$digiwimmuploaddata->qty_units}}
											</td>

											<td>
												{{$digiwimmuploaddata->total_cs}}
											</td>

											<td>
												{{$digiwimmuploaddata->rate_per_unit}}
											</td>

											<td>
												{{$digiwimmuploaddata->tax}}
											</td>

											<td>
											{{$digiwimmuploaddata->conversion}}
											</td>

											<td>
											{{$digiwimmuploaddata->discount}}
											</td>

											<td>
												{{$digiwimmuploaddata->inco_terms}}
											</td>

											<td>
												{{$digiwimmuploaddata->freight}}
											</td>

											<td>
												{{$digiwimmuploaddata->custom}}
											</td>

											<td>
											{{$digiwimmuploaddata->custom_1}}
											</td>

											<td>
												{{$digiwimmuploaddata->custom_2}}
											</td>

											<td>
												{{$digiwimmuploaddata->custom_3}}
											</td>
											<td>
												{{$digiwimmuploaddata->custom_4}}
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