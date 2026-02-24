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

/*Specific Widths based on typical data lengths */
input[name*="sku"], input[name*="code"] {
    width: 15ch; /* Fits approx 15 characters */
}

input[name*="qty"], input[name*="priority"] {
    width: 6ch;  /* Fits approx 6 characters */
    text-align: center;
}
input[name*="qty"], input[name*="priority"] {
    width: 6ch;  /* Fits approx 6 characters */
    text-align: center;
}
input[name*="z_weight"], input[name*="z_volume"], input[name*="totalwt"], input[name*="totalvol"], input[name*="totalzwutil"],input[name*="totalzvutil"], input[name*="totalgross"] {
    width: 9ch;  /* Fits approx 6 characters */
    text-align: center;
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
    min-width: 20px;
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
            <h1 class="m-0">Load Optimizer</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Load Optimiser</li>
				
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
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.lopmanualupload')}}" data-toggle="tab">Create</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummary')}}">Indent</a></li>
				 <li class="nav-item"><a class="nav-link active" href="{{route('admin.qualifiedloadsummary')}}">Qualified Indent</a></li>
				<li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummaryApproval')}}">Approve / Reject Summary</a></li>				 
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  <div id="ajaxSuccess"
					 class="alert alert-success d-none">
				</div>
				
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						
						  <table class="table-bordered border-dark table-hover" id="skuTable" width="100%">
							  <thead>
								<tr>
									<th style="background: #fce4d6; color: #0070c0;" class="">Reference No.</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin <br>Name & Code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination <br>Name & code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">SKU code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">SKU description</th>
									<th style="background: #fce4d6; color: #0070c0;">Priority</th>
									<th style="background: #fce4d6; color: #0070c0;">SKU class</th>
									<th style="background: #fce4d6; color: #0070c0;">T mode</th>
									<th style="background: #fce4d6; color: #0070c0;">QTY</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total <br>weight</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total <br>Volume</th>
											  
								</tr>
							  </thead>
							  <tbody>
								@if(count($items) > 0)
								@foreach($items as $i => $row)
								<tr class="sku-row">

									<td>{{ $referenceNo }}</td>

									<td>{{ $row->origin_name_code }} - {{ $row->origin_name }}</td>

									<td>{{ $row->destination_name_code }} - {{ $row->destination_city }}</td>

									<td>
										{{ $row->sku_code }}</td>

									<td>
										{{ $row->sku_description }}
									</td>

									<td>{{ $row->priority }}</td>

									<td>{{ $row->sku_class }}</td>

									<td>{{ $row->t_mode }}</td>

									<td>{{ $row->qty }}</td>

									<td>{{ $row->z_total_weight }}</td>

									<td>{{ $row->z_total_volume }}</td>
									
								</tr>
								@endforeach
								@endif
								</tbody>
								
							</table>
						
							
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