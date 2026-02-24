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
            <h1 class="m-0">Vendor List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Vendor </li>
				
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
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
					<table class="table table-bordered border-dark table-hover" id="billDataTable">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Vendor Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Vendor Desc</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Company Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name 1</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name 2</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Authorized Person Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Authorized Person Phone</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Authorized Person Mail</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Withholding Tax Type</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">TDS Section 1</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Receipt Type 1</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Receipt Name</th>
						 <th style="background: #ddebf7; color: #0070c0;" class="col-width">Withholding Tax Type 2</th> 
						 <th style="background: #ddebf7; color: #0070c0;" class="col-width">TDS Section 2</th> 
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Pan no</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Email</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">GSTin number </th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Pan GSTin Check</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">terms of Payment Key</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Account Group</th>
						 
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Posting Block Overall</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Purchase Block Overall</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Service Block</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Purchase Shipment Block</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created Date</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Status </th>						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						  @if(count($vendorlist) > 0)
						  @foreach($vendorlist as $vendordata)
					  
					   <tr>
						  <td class="sticky-col-1 col-width">{{$vendordata->vendor_code}}</td>
						  <td class="sticky-col-2 col-width">{{$vendordata->vendor_type}}</td>
						  <td>{{$vendordata->company_code}}</td>
						  <td>{{$vendordata->vendor_name}}</td>
						  <td>{{$vendordata->vendor_short_name}}</td>
						  <td>{{$vendordata->authorized_person_name}}</td>
						  <td>{{$vendordata->authorized_person_phone}}</td>
						  <td>{{$vendordata->authorized_person_mail}}</td>
						  <td>{{$vendordata->withholding_tax_type}}</td>
						  <td>{{$vendordata->tds_section_1}}</td>
						  <td>{{$vendordata->receipt_type_1}}</td>
						  <td>{{$vendordata->receipt_name}}</td>
						  <td>{{$vendordata->withholding_tax_type_2}}</td>
						  <td>{{$vendordata->tds_section_2}}</td>
						  <td>{{$vendordata->pan_no}}</td>
						  <td>{{$vendordata->email}}</td>
						  <td>{{$vendordata->gstin_number}}</td>
						  <td>{{$vendordata->pan_gstin_check}}</td>
						  <td>{{$vendordata->terms_of_payment_key}}</td>
						  <td>{{$vendordata->account_group}}</td>
						  <td>{{$vendordata->posting_block_overall}}</td>
						  <td>{{$vendordata->purchase_block_overall}}</td>
						  <td>{{$vendordata->service_block}}</td>
						  <td>{{$vendordata->purchase_shipment_block}}</td>
						  <td>{{$vendordata->created_at}}</td>
						  <td>@if($vendordata->status == 1)
									<span class="badge bg-success">Active</span>
								@else
									<span class="badge bg-secondary">Inactive</span>
								@endif</td>
						  
						  <td>
						   @if(Auth::user() && (Auth::user()->role_id == 1))	
						  <a class="btn btn-info btn-sm" href="{{url('admin/vendor/editvendor/'.$vendordata->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          <a class="btn btn-danger btn-sm" href="{{url('admin/deletevendor/'.$vendordata->id)}}" onclick="return confirm('Are your sure you want to delete this data? This will delete its address and bank details as well');">
                              <i class="fas fa-trash">
                              </i>
                              Delete
                          </a>
						  <br /><br />
						  @endif
						   <a href="{{ route('admin.vendor-addresses.index', $vendordata->id) }}" class="btn btn-sm btn-primary">Addresses</a>
							<a href="{{ route('admin.vendor-bank-accounts.index', $vendordata->id) }}" class="btn btn-sm btn-warning">Bank Accounts</a>
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
       
		
		</div>
		
		
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->


	 

  @endsection