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
            <h1 class="m-0">Site Plant Data Upload</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Site Plant Data </li>
				
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
						 <form action="{{ route('admin.siteplantexcel.import') }}" method="POST" enctype="multipart/form-data">
							@csrf								
							<label for="excel_file">Select Excel file to upload Site plant Data</label>
							<input type="file" name="excel_file" id="excel_file" required>
							<button type="submit" class="btn btn-primary">Import</button>
						</form>
					</div>
					<div class="form-group col-md-6 border-left text-right">						
						<label for="excel_file"> &nbsp; </label><br />
						<a href="{{route('admin.siteplantmanualupload')}}" class="btn btn-warning">Manual Upload Site plant Data</a>
						
					</div>
				</div>
				
				<div class="row">
				<div class="form-group col-md-6"><a href="{{ URL::asset('consignmentapp_SampleDATA/locationmaster.xlsx') }}">Download Sample Data</a></div>
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
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">PLANT Site Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Plant/Site Location Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Code</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Plant/Site Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">S5 & D5</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Street/House number</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">STREET1</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">STREET2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">CITY</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">POST CODE</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">STATE</th>
						 <th style="background: #ddebf7; color: #0070c0;" class="col-width">STATE DESC</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="col-width">PAN NO</th> 
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Food License No.</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Food License Expiry</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">GSTIN Number </th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Executive Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Executive Contact No.</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Executive Mail ID	</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Name</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Contact No.	</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Mail ID</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Name</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Contact No.</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Mail ID</th>
						   <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Region</th>
						    <th style="background: #fce4d6; color: #0070c0;" class="col-width">Company Code</th>
							 <th style="background: #fce4d6; color: #0070c0;" class="col-width">Company Type</th>
						   
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created date</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">status </th>
						 @if(Auth::user() && (Auth::user()->role_id == 1))			
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
					    @endif
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($siteplantlist) > 0)
						  @foreach($siteplantlist as $siteplantdata)
					  
					   <tr>
						  <td class="sticky-col-1 col-width">{{$siteplantdata->plant_site_code}}</td>
						  <td class="sticky-col-2 col-width">{{$siteplantdata->plant_site_location_name}}</td>
						  <td>{{$siteplantdata->site_code}}</td>
						  <td>{{$siteplantdata->plant_site_name}}</td>
						  <td>{{$siteplantdata->s5_d5_short_name}}</td>
						  <td>{{$siteplantdata->street_house_number}}</td>
						  <td>{{$siteplantdata->street1}}</td>
						  <td>{{$siteplantdata->street2}}</td>
						  <td>{{$siteplantdata->city}}</td>
						  <td>{{$siteplantdata->post_code}}</td>
						  <td>{{$siteplantdata->state_code}}</td>
						  <td>{{$siteplantdata->state_name}}</td>
						  <td>{{$siteplantdata->pan_no}}</td>
						  <td>{{$siteplantdata->food_license_no}}</td>
						  <td>{{$siteplantdata->food_license_expiry}}</td>
						  <td>{{$siteplantdata->gstin_number}}</td>
						  <td>{{$siteplantdata->site_executive_name}}</td>
						  <td>{{$siteplantdata->site_executive_contact_no}}</td>
						  <td>{{$siteplantdata->site_executive_mail_id}}</td>
						  <td>{{$siteplantdata->site_incharge_name}}</td>
						  <td>{{$siteplantdata->site_incharge_contact_no}}</td>
						  <td>{{$siteplantdata->site_incharge_mail_id}}</td>
						  <td>{{$siteplantdata->site_manager_name}}</td>
						  <td>{{$siteplantdata->site_manager_contact_no}}</td>
						  <td>{{$siteplantdata->site_manager_mail_id}}</td>
						  <td>{{$siteplantdata->region}}</td>
						   <td>{{$siteplantdata->company_code}}</td>
						    <td>{{$siteplantdata->company_type}}</td>
						  <td>{{$siteplantdata->created_at}}</td>
						  <td>{!! ($siteplantdata->status == 1)?"<span class='badge bg-success'>Active</span>":"<span class='badge bg-warning'>Inactive</span>" !!}</td>
						   @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <td><a class="btn btn-info btn-sm" href="{{url('admin/siteplant/editsiteplantdata/'.$siteplantdata->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="{{url('admin/deletesiteplantdata/'.$siteplantdata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a></td>
						  @endif
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