@extends('admin.admin')
@section('title',$title)
@section('bodycontent')
<div class="container-fluid">
	<div class="d-flex justify-content-between mb-3"><div><h4>{{ $pagetitle }}</h4><small>Inventory Key: {{ $inventoryKey }}</small></div>
	<button onclick="window.print()" class="btn btn-primary">Print</button>
	</div>
	<div class="card mb-3">
		<div class="card-header"><strong>Inventory Identity</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<strong>Material Code</strong><br>{{ $identity->material_code??'-' }}
				</div>
				<div class="col-md-5"><strong>Description</strong><br>{{ $identity->material_description??'-' }}
				</div>
				<div class="col-md-2"><strong>Plant</strong><br>{{ $identity->storage_plant_code??'-' }}</div>
				<div class="col-md-2"><strong>Location</strong><br>{{ $identity->storage_plant_location??'-' }}</div>
				<div class="col-md-3 mt-3"><strong>Batch</strong><br>{{ $identity->batch_no??'-' }}</div>
				<div class="col-md-3 mt-3"><strong>MFG Date</strong><br>{{ !empty($identity->mfg_date)?\Illuminate\Support\Carbon::parse($identity->mfg_date)->format('d-m-Y'):'-' }}</div><div class="col-md-3 mt-3"><strong>Expiry Date</strong><br>{{ !empty($identity->expiry_date)?\Illuminate\Support\Carbon::parse($identity->expiry_date)->format('d-m-Y'):'-' }}</div>
				<div class="col-md-3 mt-3"><strong>UOM</strong><br>{{ $identity->uom??'-' }}</div>
			</div>
		</div>
	</div>
<div class="row mb-3">
	<div class="col-md-4">
		<div class="small-box bg-info"><div class="inner"><h3>{{ number_format($bookQty,3) }}</h3><p>Book Quantity</p></div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="small-box bg-secondary">
			<div class="inner"><h3>{{ number_format($iraQty,3) }}</h3><p>IRA Quantity</p></div>
			</div>
	</div>
	<div class="col-md-4">
		<div class="small-box {{ abs($differenceQty)<0.0005?'bg-success':'bg-danger' }}">
		<div class="inner"><h3>{{ number_format($differenceQty,3) }}</h3><p>Difference</p></div>
		</div>
	</div>
			
			</div>
<div class="card">
	<div class="card-header"><strong>IRA Activities</strong></div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-sm mb-0">
				<thead>
					<tr>
						<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Sl.</th>
						<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Qty Unit</th>
						<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Qty Case</th>
						<th style="background: #fce4d6; color: #0070c0;">BIN</th>
						<th style="background: #fce4d6; color: #0070c0;">Remarks</th>
						<th style="background: #fce4d6; color: #0070c0;">Activity By</th>
						<th style="background: #fce4d6; color: #0070c0;">Date/Time</th>
					</tr>
				</thead>
					<tbody>@forelse($ira?->activities??collect() as $index=>$activity)
						<tr>
							<td class="sticky-col-1">{{ $index+1 }}</td>
							<td class="sticky-col-2">{{ number_format((float)$activity->qty_unit,3) }}</td>
							<td class="sticky-col-3">{{ number_format((float)$activity->qty_case,3) }}</td>
							<td>{{ $activity->bin_no??'-' }}</td>
							<td>{{ $activity->remarks??'-' }}</td>
							<td>{{ $activity->activityByAdmin->name??$activity->activity_by??'-' }}</td>
							<td>{{ optional($activity->activity_at)->format('d-m-Y h:i A') }}</td>
						</tr>@empty
						<tr><td colspan="7" class="text-center">No IRA activities found.</td></tr>@endforelse
					</tbody>
			</table>
		</div>
	</div>
	</div>
</div>
@endsection
@push('styles')<style>@media print{.main-header,.main-sidebar,.content-header,.btn,footer{display:none!important}.content-wrapper{margin-left:0!important}}</style>@endpush
