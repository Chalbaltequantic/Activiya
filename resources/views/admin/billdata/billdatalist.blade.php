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
      left: 135px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 280px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 380px; /* Adjust based on col-1 width */
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
            <h1 class="m-0">Freight Shipment History</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Freight Shipment History</li>
				
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
							<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-1">S5 consignor short<br> name & location</th>
							<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-2">D5 consignor short<br> name & location</th>
							<th style="background: #fce4d6; color: #0070c0;z-index:999;width:90px;" class="sticky-col-3">Vendor Name</th>
							<th style="background: #ddebf7; color: #0070c0;z-index:999;" class="sticky-col-4">Truck type</th> 
							<th style="background: #fce4d6; color: #0070c0;">Consignor name</th>
							<th style="background: #fce4d6; color: #0070c0;">Consignor<br> code</th>
							<th style="background: #fce4d6; color: #0070c0;">Consignor location</th>
						  
						  
						  <th style="background: #fce4d6; color: #0070c0;">Consignee Name</th>
						  <th style="background: #fce4d6; color: #0070c0;">Consignee<br> Code</th>
						  <th style="background: #fce4d6; color: #0070c0;">Consignee Location</th>
						  
						<th style="background: #fce4d6; color: #0070c0;">Ref1</th>
						
						<th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
						 <th style="background: #ddebf7; color: #0070c0;">T code</th> 
						
						  <th style="background: #fce4d6; color: #0070c0;">LR/CN No.</th>
						  <th style="background: #fce4d6; color: #0070c0;">LR CN Date</th>
						  <th style="background: #fce4d6; color: #0070c0;">A amount </th>
						  <th style="background: #fce4d6; color: #0070c0;">Ref2</th>
						  <th style="background: #fce4d6; color: #0070c0;">Ref3</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Freight type</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Ap status</th>
						  <th style="background: #c6e0b4; color: #0070c0;">Created_date</th>
						  <th style="background: #c6e0b4; color: #0070c0;">status </th>
						  
						  <th style="background: #fce4d6; color: #0070c0;">Action</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($billdatalist) > 0)
						  @foreach($billdatalist as $billdata)
					  
					   <tr>
						<td class="sticky-col-1">{{$billdata->s5_consignor_short_name_and_location}}</td>
						<td class="sticky-col-2">{{$billdata->d5_consignor_short_name_and_location}}</td>
						<td class="sticky-col-3" class="sticky-col-2 col-width">{{$billdata->vendor_name}}</td>
						<td class="sticky-col-4">{{$billdata->truck_type}}</td>
						  <td>{{$billdata->consignor_name}}</td>
						  <td>{{$billdata->consignor_code}}</td>
						  <td>{{$billdata->consignor_location}}</td>
						  
						  <td>{{$billdata->consignee_name}}</td>
						  <td>{{$billdata->consignee_code}}</td>
						  <td>{{$billdata->consignee_location}}</td>
						  
						  <td>{{$billdata->ref1}}</td>
						  <td>{{$billdata->vendor_code}}</td>
						  
						  <td>{{$billdata->t_code}}</td>
						  
						  <td>{{$billdata->lr_no}}</td>
						  <td>{{$billdata->lr_cn_date}}</td>
						  <td>{{$billdata->a_amount}}</td>
						  <td>{{$billdata->ref2}}</td>
						  <td>{{$billdata->ref3}}</td>
						  <td>{{$billdata->freight_type}}</td>
						  <td>{{$billdata->ap_status}}</td>
						  <td>{{$billdata->created_at}}</td>
						  <td>{!! ($billdata->status == 1)?"<span class='badge bg-success'>Active</span>":"<span class='badge bg-warning'>Inactive</span>" !!}</td>
						  
						  <td>
							@if($user_role==1)
							  <a class="btn btn-info btn-sm" href="{{url('admin/billdata/editbilldata/'.$billdata->id)}}">
								  <i class="fas fa-pencil-alt">
								  </i>
								  Edit
							  </a>
							  <a class="btn btn-danger btn-sm" href="{{url('admin/deletebilldata/'.$billdata->id)}}" onclick="return confirm('Are your sure you want to delete this data');">
								  <i class="fas fa-trash">
								  </i>
								  Delete
							  </a>
							@endif
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
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

  @endsection

