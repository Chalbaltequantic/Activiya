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
      left: 160px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
      min-width: 160px;
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
      }
    }
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Rate master Data Upload</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Rate Master Data </li>
				
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
			
              <div class="card-body">
				<div class="row">
					<div class="form-group col-md-6">
						 <form action="{{ route('admin.ratemasterexcel.import') }}" method="POST" enctype="multipart/form-data">
							@csrf	
							
							<label for="excel_file">Select Excel File to Upload Rate Master Data</label>
							<input type="file" name="excel_file" id="excel_file" required>
								<button type="submit" class="btn btn-primary">Import</button>
							
						</form>
					</div>
					<div class="form-group col-md-6 border-left text-right">						
							<label for="excel_file"> &nbsp; </label><br />
							<a href="{{route('admin.ratedatamanualupload')}}" target="" class="btn btn-warning">Manual Upload Rate Master Data</a>
						
					</div>
				</div>
				
				<div class="row">
				<div class="form-group col-md-6"><a href="{{ URL::asset('consignmentapp_SampleDATA/rate_upload.xlsx') }}">Download Sample Data</a></div>
				</div>
				
			  </div>
       
            </div>

           
          </div>
		  
		  
		</div>
        <!-- /.row -->
		
		
		 <div class="row">
          <div class="col-lg-12">
            <div class="card">
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
					<table class="table table-bordered border-dark table-hover" id="billDataTable">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Consignor name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Consignor code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor location</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">S5 consignor short<br>name & location</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">D5 consignor short<br>name & location</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Mode</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Logic</th>

						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
						 <th style="background: #ddebf7; color: #0070c0;" class="col-width">T code</th> 
						 <th style="background: #ddebf7; color: #0070c0;" class="col-width">Truck type</th> 
						 <th style="background: #fce4d6; color: #0070c0;" class="col-width">A amount </th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Validity start	</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Validity end	</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">TAT</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Rank</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Distance</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 1</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 2</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 3</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 4</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom 5</th>
						 
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created date</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">status </th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($ratedatalist) > 0)
						  @foreach($ratedatalist as $ratedata)
					  
					   <tr>
						  <td class="sticky-col-1 col-width">{{$ratedata->consignor_name}}</td>
						  <td class="sticky-col-2 col-width">{{$ratedata->consignor_code}}</td>
						  <td>{{$ratedata->consignor_location}}</td>
						  <td>{{$ratedata->s5_consignor_short_name_and_location}}</td>
						  <td>{{$ratedata->consignee_name}}</td>
						  <td>{{$ratedata->consignee_code}}</td>
						  <td>{{$ratedata->consignee_location}}</td>
						  <td>{{$ratedata->d5_consignor_short_name_and_location}}</td>
						  <td>{{$ratedata->mode}}</td>
						  <td>{{$ratedata->logic}}</td>
						   <td>{{$ratedata->vendor_code}}</td>
						  <td>{{$ratedata->vendor_name}}</td>
						 

						  <td>{{$ratedata->t_code}}</td>
						  <td>{{$ratedata->truck_type}}</td>
						  <td>{{$ratedata->a_amount}}</td>
						  <td>{{$ratedata->validity_start}}</td>
						  <td>{{$ratedata->validity_end}}</td>
						  <td>{{$ratedata->tat}}</td>
						  <td>{{$ratedata->rank}}</td>
						  <td>{{$ratedata->distance}}</td>
						  <td>{{$ratedata->custom1}}</td>
						  <td>{{$ratedata->custom2}}</td>
						  <td>{{$ratedata->custom3}}</td>
						  <td>{{$ratedata->custom4}}</td>
						  <td>{{$ratedata->custom5}}</td>
						  <td>{{$ratedata->created_at}}</td>
						    <td>{!! ($ratedata->status == 1)?"<span class='badge bg-success'>Active</span>":"<span class='badge bg-warning'>Inactive</span>" !!}</td>
						  
						  <td><a class="btn btn-info btn-sm" href="{{url('admin/ratedata/editratedata/'.$ratedata->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="{{url('admin/deleteratedata/'.$ratedata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a></td>
						  
						</tr>
						  
             	  @endforeach
				  @else
					  <tr><td colspan="7">No data found</td></tr>
				  @endif
				  
               </tbody>
          </table>
		    
        </div>
       
            </div>

           
          </div>
		  
		  
		    </div>
       
		</div>
		
		
		
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

  @endsection