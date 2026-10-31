@extends('admin.admin')
@section('bodycontent')
@push('style')
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
      left: 71px; /* Adjust based on col-1 width */
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
 @endpush
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><h1 class="m-0">Slab Master List</h1></div>
			<div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Slab Master List</li></ol></div>
		</div>
	</div>
</div>
<div class="content">
	<div class="container-fluid">
		@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
		@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
		@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
		@if(session('errorRows'))<div class="alert alert-warning"><b>Skipped Rows:</b><ul>@foreach(session('errorRows') as $err)<li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>@endforeach</ul></div>@endif
<div class="card">
	<div class="card-header p-2">
		<ul class="nav nav-pills">
			<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.index') }}">XLS Upload</a></li>
			<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.manual') }}">Manual Upload</a></li>
			<li class="nav-item"><a class="nav-link active" href="{{ route('admin.slab-master.list') }}">Slab Master List</a></li>
		</ul>
	</div>
	<div class="card-body">
		<form method="GET" action="{{ route('admin.slab-master.list') }}" class="row mb-3">
			<div class="col-md-4"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="From / To / Vendor / Truck Code"></div>
			<div class="col-md-2">
				<select name="mode" class="form-control"><option value="">All Modes</option>
					<option value="PTL" {{ request('mode') == 'PTL' ? 'selected' : '' }}>PTL</option>
					<option value="AIR" {{ request('mode') == 'AIR' ? 'selected' : '' }}>AIR</option>
					<option value="ROAD" {{ request('mode') == 'ROAD' ? 'selected' : '' }}>ROAD</option>
					<option value="RAIL" {{ request('mode') == 'RAIL' ? 'selected' : '' }}>RAIL</option>
					<option value="SEA" {{ request('mode') == 'SEA' ? 'selected' : '' }}>SEA</option>
					<option value="COURIER" {{ request('mode') == 'COURIER' ? 'selected' : '' }}>COURIER</option>
					<option value="FTL" {{ request('mode') == 'FTL' ? 'selected' : '' }}>FTL</option>
				</select>
			</div>
			<div class="col-md-2"><button class="btn btn-primary" type="submit">Search</button></div>
			<div class="col-md-4 text-right"><a href="{{ route('admin.slab-master.export', request()->query()) }}" class="btn btn-success">Export Excel</a></div>
		</form>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-sm">
		<thead>
		<tr>
			<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">From Code</th>
			<th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">From Name</th>
			<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Origin Region</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">To Name</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">To Code</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Des Region</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Code</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Name</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Mode</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Truck Code</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Truck Description</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 1</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 2</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 3</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 4</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 5</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 6</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 7</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 8</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 9</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 10</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 1 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 2 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 3 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 4 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 5 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 6 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 7 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 8 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 9 Rate</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Slab 10 Rate</th>

			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Created At</th>
			<th style="background: #fce4d6; color: #0070c0;z-index:999;">Action</th>
		</tr>
		</thead>
		<tbody>
			@forelse($datalist as $rate)
			<tr>
				<td class="sticky-col-1">{{ $rate->from_code }}</td>
				<td class="sticky-col-2">{{ $rate->from_name }}</td>
				<td class="sticky-col-3">{{ $rate->origin_region }}</td>
				<td>{{ $rate->to_name }}</td>
				<td>{{ $rate->to_code }}</td>
				<td>{{ $rate->destination_region }}</td>
				<td>{{ $rate->vendor_code }}</td>
				<td>{{ $rate->vendor_name }}</td>
				<td>{{ $rate->mode }}</td>
				<td>{{ $rate->truck_code }}</td>
				<td>{{ $rate->truck_description }}</td>
				<td>{{ $rate->slab_1 }}</td>
				<td>{{ $rate->slab_2 }}</td>
				<td>{{ $rate->slab_3 }}</td>
				<td>{{ $rate->slab_4 }}</td>
				<td>{{ $rate->slab_5 }}</td>
				<td>{{ $rate->slab_6 }}</td>
				<td>{{ $rate->slab_7 }}</td>
				<td>{{ $rate->slab_8 }}</td>
				<td>{{ $rate->slab_9 }}</td>
				<td>{{ $rate->slab_10 }}</td>
				<td>{{ $rate->slab_1_rate }}</td>
				<td>{{ $rate->slab_2_rate }}</td>
				<td>{{ $rate->slab_3_rate }}</td>
				<td>{{ $rate->slab_4_rate }}</td>
				<td>{{ $rate->slab_5_rate }}</td>
				<td>{{ $rate->slab_6_rate }}</td>
				<td>{{ $rate->slab_7_rate }}</td>
				<td>{{ $rate->slab_8_rate }}</td>
				<td>{{ $rate->slab_9_rate }}</td>
				<td>{{ $rate->slab_10_rate }}</td>
				<td>{{ $rate->created_at }}</td>
				<td><a href="{{ route('admin.slab-master.edit', $rate->id) }}" class="btn btn-sm btn-primary">Edit</a>
				<button type="button" class="btn btn-sm btn-danger delete-slab" data-id="{{ $rate->id }}">Delete</button>
				</td>
				</tr>
				@empty<tr><td colspan="35" class="text-center">No records found</td></tr>@endforelse
			</tbody>
		</table>
	</div>
    {{ $datalist->appends(request()->query())->links() }}
</div>
</div>
</div>
</div>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('.delete-slab').on('click', function () {
        const button = $(this);
        const url = "{{ url('/admin/slab-master') }}/" + button.data('id');
        Swal.fire({title:'Delete Slab Master?', text:'This cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Delete'}).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({url:url, type:'POST', data:{_token:"{{ csrf_token() }}", _method:'DELETE'},
                success:function(res) { if (res.status) location.reload(); else Swal.fire('Error',res.message || 'Unable to delete','error'); },
                error:function() { Swal.fire('Error','Unable to delete this record.','error'); }
            });
        });
    });
});
</script>
@endpush
@endsection
