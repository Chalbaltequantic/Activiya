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
      z-index: 9999;
    }

    .sticky-col-2 {
      position: sticky;
      left: 160px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 9999;
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
            <h1 class="m-0"> <h3>Bank Account for : {{ $vendor->name }}</h3>
    <a href="{{ route('admin.vendor-bank-accounts.create', $vendor->id) }}" class="btn btn-success mb-3">Add Bank Account</a></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item"><a href="/admin/vendor">Vendors</a></li>
             <li class="breadcrumb-item active">Vendor Bank Accounts</li>
				
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
					<table class="table table-bordered border-dark table-hover">
					  <thead>
						<tr>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Bank name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="sticky-col-2 col-width">Branch name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Account holder name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Account number</th>
						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">IFSC code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Account type</th>
						
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Created Date</th>
						  <th style="background: #c6e0b4; color: #0070c0;" class="col-width">Status </th>						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Action</th>
						</tr>
					  </thead>
					  <tbody>
				  
						  @php($i=1)
						 @if (!empty($bankAccounts) && $bankAccounts->count() > 0)

						   @foreach ($bankAccounts as $bankAccount)
					  
					   <tr>
							<td class="sticky-col-1 col-width">{{ $bankAccount->bank_name }}</td>
							<td class="sticky-col-2 col-width">{{ $bankAccount->branch_name }}</td>
							<td>{{ $bankAccount->account_holder_name }}</td>
							<td>{{ $bankAccount->account_number }}</td>
							<td>{{ $bankAccount->ifsc_code }}</td>
							<td>{{ $bankAccount->account_type }}</td>

							<td>{{$bankAccount->created_at}}</td>
							<td>@if($bankAccount->status == 1)
								<span class="badge bg-success">Active</span>
								@else
								<span class="badge bg-secondary">Inactive</span>
								@endif
							</td>

							<td>
							 <a href="{{ route('admin.vendor-bank-accounts.edit', [$vendor->id, $bankAccount->id]) }}" class="btn btn-warning btn-sm">Edit</a>
							<form method="POST" action="{{ route('admin.vendor-bank-accounts.destroy', [$vendor->id, $bankAccount->id]) }}" style="display:inline;">
								@csrf @method('DELETE')
								<button class="btn btn-danger btn-sm" onclick="return confirm('Delete Account?')">Delete</button>
							</form>
													 
							</td>						  
						</tr>						  
						  @endforeach
						  @else
							  <tr><td colspan="9">No data found</td></tr>
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