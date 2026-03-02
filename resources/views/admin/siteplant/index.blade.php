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
      left: 70px; /* Adjust based on col-1 width */
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
							<label for="excel_file"> upload Site Master</label>
							<input type="file" name="excel_file" id="excel_file" required>
							<button type="submit" class="btn btn-primary">Import</button>
						</form>
						<a href="{{ URL::asset('consignmentapp_SampleDATA/locationmaster.xlsx') }}">Download Sample Data</a>
					</div>
					<div class="form-group col-md-4 border-left text-right">						
						
						<a href="{{route('admin.siteplantmanualupload')}}" class="btn btn-warning">Manual Upload Site Master</a>
						
					</div>
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
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white table-container">
					<table class="table table-bordered border-dark table-hover" id="billDataTable">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 ">PLANT <br>Site Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 ">Plant/Site<br>Location Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Code</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="">Plant/Site<br> Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">S5 & D5</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Street/House<br> number</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Street1</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Street2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">City</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Post Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">State</th>
						 <th style="background: #ddebf7; color: #0070c0;" class="">State Desc</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="">PAN NO</th> 
						  <th style="background: #fce4d6; color: #0070c0;" class="">Food<br>License No.</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Food<br>License Expiry</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">GSTIN Number </th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site<br>Executive Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Executive<br> Contact No.</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Executive <br>Mail ID	</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Incharge Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Incharge Contact No.	</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Incharge Mail ID</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Manager Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Manager Contact No.</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">Site Manager Mail ID</th>
						   <th style="background: #fce4d6; color: #0070c0;" class="">Site Region</th>
						    <th style="background: #fce4d6; color: #0070c0;" class="">Company Code</th>
							 <th style="background: #fce4d6; color: #0070c0;" class="">Company Type</th>
						   
						  <th style="background: #fce4d6; color: #0070c0;" class="">Created date</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="">status </th>
						 @if(Auth::user() && (Auth::user()->role_id == 1))			
						  <th style="background: #c6e0b4; color: #0070c0;" class="">Action</th>
					    @endif
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($siteplantlist) > 0)
						  @foreach($siteplantlist as $siteplantdata)
					  
					   <tr>
						  <td class="sticky-col-1 ">{{$siteplantdata->plant_site_code}}</td>
						  <td class="sticky-col-2 ">{{$siteplantdata->plant_site_location_name}}</td>
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