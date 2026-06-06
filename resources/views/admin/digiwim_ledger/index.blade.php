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
            <h1 class="m-0">Digi Wim Ledger</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Digi Wim Ledger</li>
				
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
      
        <!-- /.row -->
		 <div class="row">          	  
			<div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
               <form method="GET" action="{{ route('admin.digiwim.ledger') }}" class="mb-3">
					<div class="row">

						<div class="col-md-4">
							<label>Search Based on Location</label>
							<input type="text"
								   name="location"
								   class="form-control"
								   value="{{ request('location') }}"
								   placeholder="Plant code / plant name / city">
						</div>

						<div class="col-md-3">
							<label>Date</label>
							<input type="date"
								   name="date"
								   class="form-control"
								   value="{{ request('date') }}">
						</div>

						<div class="col-md-3 mt-4">
							<button type="submit" class="btn btn-primary">
								Search
							</button>

							<a href="{{ route('admin.digiwim.ledger') }}" class="btn btn-secondary">
								Reset
							</a>
						</div>

					</div>
				</form>
                 
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
					
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							

							<div class="excel-wrapper">
								<table class="table table-bordered excel-table" id="billDataTable">

									<thead>
											<tr>

												<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Date</th>

												<th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">Material Code</th>

												<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Description</th>

												<th style="background: #fce4d6; color: #0070c0;">Division</th>

												<th style="background: #fce4d6; color: #0070c0;">Brand</th>

												<th style="background: #fce4d6; color: #0070c0;">Sub Brand</th>

												<th style="background: #fce4d6; color: #0070c0;">UOM</th>

												<th style="background: #fce4d6; color: #0070c0;">Piece/Box</th>

												<th style="background: #fce4d6; color: #0070c0;">MRP</th>

												<th style="background: #fce4d6; color: #0070c0;">Weight</th>

												<th style="background: #fce4d6; color: #0070c0;">Volume</th>

												<th style="background: #fce4d6; color: #0070c0;">Plant Code</th>

												<th style="background: #fce4d6; color: #0070c0;">Plant Name</th>

												<th style="background: #fce4d6; color: #0070c0;">Plant Location</th>

												<th style="background: #fce4d6; color: #0070c0;">Batch</th>

												<th style="background: #fce4d6; color: #0070c0;">Outward Qty</th>

												<th style="background: #fce4d6; color: #0070c0;">Outward Case</th>

												<th style="background: #fce4d6; color: #0070c0;">Outward BIN</th>

												<th style="background: #fce4d6; color: #0070c0;">Inward Qty</th>

												<th style="background: #fce4d6; color: #0070c0;">Inward Case</th>

												<th style="background: #fce4d6; color: #0070c0;">Inward BIN</th>

												<th style="background: #fce4d6; color: #0070c0;">Custom</th>

												<th style="background: #fce4d6; color: #0070c0;">Custom1</th>

												<th style="background: #fce4d6; color: #0070c0;">Custom2</th>

												<th style="background: #fce4d6; color: #0070c0;">Custom3</th>

												<th style="background: #fce4d6; color: #0070c0;">Custom4</th>

												</tr>
										</thead>
									<tbody>										
										  @php($i=1)
										  @if(count($records) > 0)
										  @foreach($records as $row)
											<tr>
												<td class="sticky-col-1">{{ $row->created_at }}</td>

												<td class="sticky-col-2">{{ $row->material_code }}</td>

												<td class="sticky-col-3">{{ $row->material_description }}</td>

												<td>{{ $row->division }}</td>

												<td>{{ $row->brand }}</td>

												<td>{{ $row->sub_brand }}</td>

												<td>{{ $row->uom }}</td>

												<td>{{ $row->piece_per_box }}</td>

												<td>{{ $row->mrp }}</td>

												<td>{{ $row->weight }}</td>

												<td>{{ $row->volume }}</td>

												<td>{{ $row->plant_site_code }}</td>

												<td>{{ $row->plant_name }}</td>

												<td>{{ $row->city }}</td>

												<td>{{ $row->batch_no }}</td>

												@if($row->movement=='OUTWARD')

												<td>{{ $row->qty }}</td>
												<td>{{ $row->qty_case }}</td>
												<td>{{ $row->bin_no }}</td>

												<td></td>
												<td></td>
												<td></td>

												@else

												<td></td>
												<td></td>
												<td></td>

												<td>{{ $row->qty }}</td>
												<td>{{ $row->qty_case }}</td>
												<td>{{ $row->bin_no }}</td>

												@endif

												<td>{{ $row->custom }}</td>
												<td>{{ $row->custom1 }}</td>
												<td>{{ $row->custom2 }}</td>
												<td>{{ $row->custom3 }}</td>
												<td>{{ $row->custom4 }}</td>

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