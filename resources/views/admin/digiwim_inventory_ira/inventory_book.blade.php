@extends('admin.admin')
@section('title',$title)
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
      left: 51px; /* Adjust based on col-1 width */
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
<div class="container-fluid">
	<div class="d-flex justify-content-between mb-3"><h4>{{ $pagetitle }}</h4>
		<div><a href="{{ route('admin.digiwim-inventory-ira.index') }}" class="btn btn-outline-primary">Pending IRA</a> <a href="{{ route('admin.digiwim-inventory-ira.history') }}" class="btn btn-outline-secondary">History</a></div></div><div class="alert alert-info"><strong>Difference = Book Qty - IRA Unit Qty</strong></div>
		<div class="card mb-3">
			<div class="card-body">
				<form method="GET">
					<div class="row">
						<div class="col-md-2">
							<select name="plant_code" class="form-control">
								<option value="">All Plants</option>
								@foreach($plants as $plant)
								<option value="{{ $plant }}" @selected(request('plant_code')==$plant)>{{ $plant }}</option>
								@endforeach</select>
						</div>
						<div class="col-md-2">
							<select name="plant_location" class="form-control">
								<option value="">All Locations</option>
									@foreach($locations as $location)
								<option value="{{ $location }}" @selected(request('plant_location')==$location)>{{ $location }}</option>@endforeach</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-control">
								<option value="">All Status</option>
								<option value="not_started" @selected(request('status')==='not_started')>Not Started</option>
								<option value="pending" @selected(request('status')==='pending')>Pending</option>
								<option value="completed" @selected(request('status')==='completed')>Completed</option>
							</select>
						</div>
						<div class="col-md-4"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search"></div>
						<div class="col-md-2"><button class="btn btn-primary">Search</button> <a href="{{ route('admin.digiwim-inventory-ira.inventory-book') }}" class="btn btn-secondary">Reset</a></div>
					</div>
				</form>
			</div>
		</div>
<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-sm mb-0">
				<thead>
					<tr>
						<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Sl.</th>
						<th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">Material</th>
						<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Description</th>
						<th style="background: #fce4d6; color: #0070c0;">Plant</th>
						<th style="background: #fce4d6; color: #0070c0;">Location</th>
						<th style="background: #fce4d6; color: #0070c0;">Batch</th>
						<th style="background: #fce4d6; color: #0070c0;">Book Qty</th>
						<th style="background: #fce4d6; color: #0070c0;">IRA Unit</th>
						<th style="background: #fce4d6; color: #0070c0;">IRA Case</th>
						<th style="background: #fce4d6; color: #0070c0;">Difference</th>
						<th style="background: #fce4d6; color: #0070c0;">Activities</th>
						<th style="background: #fce4d6; color: #0070c0;">Status</th>
						<th style="background: #fce4d6; color: #0070c0;">Report</th>
					</tr>
				</thead>
				<tbody>@forelse($inventoryList as $index=>$item)@php($difference=(float)$item->difference_qty)
				<tr>
					<td class="sticky-col-1">{{ $inventoryList->firstItem()+$index }}</td>
					<td class="sticky-col-2">{{ $item->material_code }}</td>
					<td class="sticky-col-3">{{ $item->material_description }}</td>
					<td>{{ $item->storage_plant_code }}</td>
					<td>{{ $item->storage_plant_location }}</td>
					<td>{{ $item->batch_no }}</td>
					<td>{{ number_format((float)$item->inventory_qty,3) }}</td>
					<td>{{ number_format((float)$item->total_ira_qty,3) }}</td>
					<td>{{ number_format((float)$item->total_ira_case_qty,3) }}</td>
					<td class="font-weight-bold {{ abs($difference)<0.0005?'text-success':'text-danger' }}">{{ number_format($difference,3) }}</td>
					<td>{{ $item->activity_count }}</td>
					<td>@if(!$item->ira_id)<span class="badge badge-secondary">Not Started</span>@elseif($item->status==='pending')<span class="badge badge-warning">Pending</span>@else<span class="badge badge-success">Completed</span>@endif</td>
					<td><a target="_blank" href="{{ route('admin.digiwim-inventory-ira.report',$item->inventory_key) }}" class="btn btn-info btn-sm">View</a>
					</td>
				</tr>@empty
				<tr>
					<td colspan="13" class="text-center">No records found.</td></tr>@endforelse
			</tbody>
		</table>
	</div>
</div>@if($inventoryList->hasPages())<div class="card-footer">{{ $inventoryList->links() }}</div>@endif</div>
</div>
@endsection
