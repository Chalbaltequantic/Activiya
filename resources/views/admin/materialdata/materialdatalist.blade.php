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
      left: 185px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 260px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Material Master Data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active"><a class="btn btn-primary"href="{{ route('admin.material') }}">Upload Material Master Data</a></li>
				
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
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table id="billDataTable" class="table table-bordered border-dark table-hover">
					  <thead>
						<tr>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-1">Material code</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-2">Material description</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-3">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;">Division</th>
						<th style="background: #fce4d6; color: #0070c0;">Piece<br> per box</th>
						<th style="background: #fce4d6; color: #0070c0;">Length(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Width(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Height(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Net weight(kg)</th>
						<th style="background: #fce4d6; color: #0070c0;">Gross(kg)</th>
						<th style="background: #fce4d6; color: #0070c0;">Volume(cft)</th>
						<th style="background: #fce4d6; color: #0070c0;">Category</th>
						<th style="background: #fce4d6; color: #0070c0;">Pallets</th>
						<th style="background: #fce4d6; color: #0070c0;">Brand</th>
						<th style="background: #fce4d6; color: #0070c0;">Sub brand</th>
						<th style="background: #fce4d6; color: #0070c0;">Thickness</th>
						<th style="background: #fce4d6; color: #0070c0;">Load<br/>sequence</th>
						<th style="background: #fce4d6; color: #0070c0;">Associated</th>
						<th style="background: #fce4d6; color: #0070c0;">Parent</th>
						<th style="background: #fce4d6; color: #0070c0;">Child</th>
						<th style="background: #fce4d6; color: #0070c0;">Created at</th>
						<th style="background: #fce4d6; color: #0070c0;">Status</th>
						<th style="background: #fce4d6; color: #0070c0;">Action</th>
						
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($materialdatalist) > 0)
						  @foreach($materialdatalist as $materialdata)
					  
					   <tr>
						  <td class="sticky-col-1" style="width: 60px;">{{$materialdata->material_code}}</td>
						  <td class="sticky-col-2" style="width: 95px;">{{$materialdata->material_description}}</td>
						  <td class="sticky-col-3">{{$materialdata->uom}}</td>
						  <td>{{$materialdata->division}}</td>
						  <td>{{$materialdata->piece_per_box}}</td>
						  <td>{{$materialdata->length_cm}}</td>
						  <td>{{$materialdata->width_cm}}</td>
						  <td>{{$materialdata->height_cm}}</td>
						  <td>{{$materialdata->net_weight_kg}}</td>
						  <td>{{$materialdata->gross_weight_kg}}</td>
						  <td>{{$materialdata->volume_cft}}</td>
						  <td>{{$materialdata->category}}</td>
						  <td>{{$materialdata->pallets}}</td>
						  <td>{{$materialdata->brand}}</td>
						  <td>{{$materialdata->sub_brand}}</td>
						  <td>{{$materialdata->thickness}}</td>
						  <td>{{$materialdata->load_sequence}}</td>
						  <td>{{$materialdata->associated}}</td>
						  <td>{{$materialdata->parent}}</td>
						  <td>{{$materialdata->child}}</td>
						  <td>{{$materialdata->created_at}}</td>
						  <td>{!! ($materialdata->status == 1)?"<span class='badge bg-success'>Active</span>":"<span class='badge bg-warning'>Inactive</span>" !!}</td>
						   @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <td><a class="btn btn-info btn-sm" href="{{url('admin/material/editmaterialdata/'.$materialdata->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="{{url('admin/deletematerialdata/'.$materialdata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a></td>
						  @endif
						</tr>
						  
             	  @endforeach
				  @endif
				  
               </tbody>
          </table>
		    
        </div>
       
		</div>
	  </div>
	</div>
    </div> 
	</div>	
		
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

  @endsection