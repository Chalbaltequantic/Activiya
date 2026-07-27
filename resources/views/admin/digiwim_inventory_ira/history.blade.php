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
<div class="d-flex justify-content-between mb-3"><h4>{{ $pagetitle }}</h4><div><a href="{{ route('admin.digiwim-inventory-ira.index') }}" class="btn btn-outline-primary">Pending IRA</a> <a href="{{ route('admin.digiwim-inventory-ira.inventory-book') }}" class="btn btn-outline-success">Book Vs IRA</a></div></div>
<div class="card mb-3"><div class="card-body"><form method="GET"><div class="row"><div class="col-md-2"><select name="plant_code" class="form-control"><option value="">All Plants</option>@foreach($plants as $plant)<option value="{{ $plant }}" @selected(request('plant_code')==$plant)>{{ $plant }}</option>@endforeach</select></div><div class="col-md-2"><select name="plant_location" class="form-control"><option value="">All Locations</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('plant_location')==$location)>{{ $location }}</option>@endforeach</select></div><div class="col-md-3"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search"></div><div class="col-md-2"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div><div class="col-md-2"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div><div class="col-md-1"><button class="btn btn-primary btn-block">Go</button></div></div></form></div></div>
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
						<th style="background: #fce4d6; color: #0070c0;">Activities</th>
						<th style="background: #fce4d6; color: #0070c0;">Ended</th>
						<th>Action</th>
						</tr>
						</thead>
						<tbody>@forelse($historyList as $index=>$item)
						<tr>
							<td class="sticky-col-1">{{ $historyList->firstItem()+$index }}</td>
							<td class="sticky-col-2">{{ $item->material_code }}</td>
							<td class="sticky-col-3">{{ $item->material_description }}</td>
							<td>{{ $item->storage_plant_code }}</td>
							<td>{{ $item->storage_plant_location }}</td>
							<td>{{ $item->batch_no }}</td>
							<td>{{ number_format((float)$item->inventory_qty,3) }}</td>
							<td>{{ number_format((float)($item->total_qty_unit??0),3) }}</td>
							<td>{{ number_format((float)($item->total_qty_case??0),3) }}</td>
							<td>{{ $item->activities_count }}</td>
							<td>{{ optional($item->ended_at)->format('d-m-Y h:i A') }}</td>
							<td><button class="btn btn-info btn-sm view-btn" data-id="{{ $item->id }}">View Activity</button> <a target="_blank" href="{{ route('admin.digiwim-inventory-ira.report',$item->inventory_key) }}" class="btn btn-secondary btn-sm">Report</a></td></tr>@empty<tr><td colspan="12" class="text-center">No completed IRA found.</td>
						</tr>@endforelse
							</tbody>
						</table>
						</div>
						</div>@if($historyList->hasPages())
							<div class="card-footer">{{ $historyList->links() }}</div>@endif
						</div>
</div>
<div class="modal fade" id="activityModal"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header"><h5>IRA Activities</h5><button class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><div id="activityInfo"></div><div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>Sl.</th><th>Qty Unit</th><th>Qty Case</th><th>BIN</th><th>Remarks</th><th>By</th><th>Date/Time</th></tr></thead><tbody id="activityRows"></tbody></table></div></div></div></div></div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{const tpl=@json(route('admin.digiwim-inventory-ira.view-activities','__ID__'));const esc=v=>{const d=document.createElement('div');d.textContent=v??'';return d.innerHTML};document.querySelectorAll('.view-btn').forEach(b=>b.addEventListener('click',async()=>{$('#activityModal').modal('show');const res=await fetch(tpl.replace('__ID__',b.dataset.id),{headers:{Accept:'application/json'}});const d=await res.json();document.getElementById('activityInfo').innerHTML='<p><strong>Material:</strong> '+esc(d.ira.material_code)+' &nbsp; <strong>Plant:</strong> '+esc(d.ira.plant_code)+' &nbsp; <strong>Batch:</strong> '+esc(d.ira.batch_no)+'</p>';document.getElementById('activityRows').innerHTML=d.activities.length?d.activities.map((x,i)=>`<tr><td>${i+1}</td><td>${esc(x.qty_unit??'-')}</td><td>${esc(x.qty_case??'-')}</td><td>${esc(x.bin_no??'-')}</td><td>${esc(x.remarks??'-')}</td><td>${esc(x.activity_by??'-')}</td><td>${esc(x.activity_at??'-')}</td></tr>`).join(''):'<tr><td colspan="7" class="text-center">No activities found.</td></tr>'}))});
</script>
@endpush
