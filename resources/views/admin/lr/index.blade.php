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

.table-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ccc;
}
</style>
  
<!-- Content Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">LR</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
          <li class="breadcrumb-item active">LR List</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="content">
<div class="container-fluid">

<div class="row">
<div class="col-lg-12">
<div class="card">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

</div>
</div>
</div>

<div class="row">          	  
<div class="col-md-12">
<div class="card">

<div class="card-header p-2">
<ul class="nav nav-pills">
<li class="nav-item">
<a class="nav-link active" href="javascript:void(0);">Invoice List</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{route('admin.lr.create')}}">Generate Invoice</a>
</li>				
</ul>
</div>

<div class="card-body">
<div class="tab-content">
<div class="active tab-pane">

		<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
			<table class="table table-bordered table-hover border-dark" id="billDataTable" width="100%">

			<thead>
			<tr>
			<th style="background:#fce4d6;color:#0070c0;">#</th>
			<th style="background:#fce4d6;color:#0070c0;">Invoice No</th>
			<th style="background:#fce4d6;color:#0070c0;">Bill Date</th>
			<th style="background:#fce4d6;color:#0070c0;">Consignor</th>
			<th style="background:#fce4d6;color:#0070c0;">Consignee</th>

			<th style="background:#fce4d6;color:#0070c0;">LR No</th>
			<th style="background:#fce4d6;color:#0070c0;">From</th>
			<th style="background:#fce4d6;color:#0070c0;">To</th>
			<th style="background:#fce4d6;color:#0070c0;">Vehicle no.</th>
			<th style="background:#fce4d6;color:#0070c0;">Insurance</th>
			<th style="background:#fce4d6;color:#0070c0;">Packages</th>
			<th style="background:#fce4d6;color:#0070c0;">Description</th>
			<th style="background:#fce4d6;color:#0070c0;">Actual Weight</th>
			<th style="background:#fce4d6;color:#0070c0;">Charged</th>
			
			<th style="background:#fce4d6;color:#0070c0;">Invoice Value</th>
			<th style="background:#fce4d6;color:#0070c0;">Surcharge</th>
			<th style="background:#fce4d6;color:#0070c0;">Hamali</th>
			<th style="background:#fce4d6;color:#0070c0;">Risk Charge</th>
			
			<th style="background:#fce4d6;color:#0070c0;">B Charge</th>
			<th style="background:#fce4d6;color:#0070c0;">Other Charge</th>
			<th style="background:#fce4d6;color:#0070c0;">Grand Total</th>
			<th style="background:#fce4d6;color:#0070c0;">Reg. Add</th>
			

			<th style="background:#fce4d6;color:#0070c0;" width="120">Action</th>
			</tr>
			</thead>

			<tbody>
			@forelse($invoices as $invoice)

			<tr>
			<td>{{ $loop->iteration }}</td>

			<td>
			<span class="badge badge-primary">
			{{ $invoice->invoice_no }}
			</span>
			</td>

			<td>{{ \Carbon\Carbon::parse($invoice->bill_date)->format('d-m-Y') }}</td>

			<td>{{ $invoice->consignor ?? '-' }}</td>
			<td>{{ $invoice->consignee ?? '-' }}</td>
			<td>{{ $invoice->lr_no ?? '' }}</td>
			<td>{{ $invoice->origin ?? '' }}</td>
			<td>{{ $invoice->destination ?? '' }}</td>
			<td>{{ $invoice->vehicle_no ?? '' }}</td>
			<td>{{ $invoice->insurance ?? '' }}</td>
			<td>{{ $invoice->packages ?? '' }}</td>
			<td>{{ $invoice->description ?? '' }}</td>
			<td>{{ $invoice->actual_weight ?? '' }}</td>
			<td>{{ $invoice->charged ?? '' }}</td>
			<td>{{ $invoice->invoice_value ?? '' }}</td>
			<td>{{ $invoice->surcharge ?? '' }}</td>
			<td>{{ $invoice->hamali ?? '' }}</td>
			<td>{{ $invoice->risk_charge ?? '' }}</td>
			<td>{{ $invoice->b_charge ?? '' }}</td>
			<td>{{ $invoice->other_charge ?? '' }}</td>
			<td>{{ number_format($invoice->total_amount ,2) }}</td>
			
			<td>{{ $invoice->registeredAddress->address_line1 ?? '' }} {{ $invoice->registeredAddress->address_line2 ?? ''  }} {{ $invoice->registeredAddress->city ?? ''  }} {{ $invoice->registeredAddress->state ?? ''  }} - {{ $invoice->registeredAddress->zip_code ?? ''  }}</td>
			

			<td>
			<a href="{{ route('admin.lr.pdf',$invoice->id) }}"
			   class="btn btn-sm btn-danger"
			   target="_blank">
			<i class="fas fa-file-pdf"></i> PDF
			</a>
			</td>
			</tr>

			@empty
			<tr>
			<td colspan="17" class="text-center text-muted">
			No invoices found
			</td>
			</tr>
			@endforelse
			</tbody>

			</table>
		</div>

		<div class="card-footer clearfix">
		{{ $invoices->links() }}
		</div>

	</div>
</div>
</div>

</div>
</div>
</div>
</div>

@endsection