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
            <div class="col-sm-6"><h1 class="m-0">Load/Unload Master List</h1></div>
            <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Load/Unload Master List</li></ol></div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('errorRows'))
            <div class="alert alert-warning">
                <strong>Skipped Rows:</strong>
                <ul class="mb-0">
                    @foreach(session('errorRows') as $err)
                        <li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.index') }}">XLS Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.manual') }}">Manual Upload</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.load-unload-master.list') }}">Load/Unload Master List</a></li>
                </ul>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.load-unload-master.list') }}" class="form-inline mb-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Search codes, vendor or truck">
                    <select name="mode" class="form-control mr-2">
                        <option value="">All Modes</option>
                        <option value="FTL" {{ request('mode') == 'FTL' ? 'selected' : '' }}>FTL</option>
                        <option value="PTL" {{ request('mode') == 'PTL' ? 'selected' : '' }}>PTL</option>
                        <option value="AIR" {{ request('mode') == 'AIR' ? 'selected' : '' }}>AIR</option>
                        
                    </select>
                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                    <a href="{{ route('admin.load-unload-master.export', request()->query()) }}" class="btn btn-success">Export Excel</a>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="loadUnloadList">
                        <thead>
                            <tr>
                                <th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">From Code</th>
                                <th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">From Name</th>
                                <th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Origin Region</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">To Name</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">To Code</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Destination Region</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Code</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Name</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Mode</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Truck Code</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Truck Description</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Charge Type (Load/Unload)</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Rate Type (Fix/Per Case/KG)</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Rate</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Load/Unload Limit</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Created At</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($datalist as $rate)
                                <tr>
                                    <td>{{ $datalist->firstItem() + $loop->index }}</td>
                                    <td>{{ $rate->from_code }}</td>
                                    <td>{{ $rate->from_name }}</td>
                                    <td>{{ $rate->origin_region }}</td>
                                    <td>{{ $rate->to_name }}</td>
                                    <td>{{ $rate->to_code }}</td>
                                    <td>{{ $rate->destination_region }}</td>
                                    <td>{{ $rate->vendor_code }}</td>
                                    <td>{{ $rate->vendor_name }}</td>
                                    <td>{{ $rate->mode }}</td>
                                    <td>{{ $rate->truck_code }}</td>
                                    <td>{{ $rate->truck_description }}</td>
                                    <td>{{ $rate->charge_type }}</td>
                                    <td>{{ $rate->rate_type }}</td>
                                    <td>{{ $rate->rate }}</td>
                                    <td>{{ $rate->load_unload_limit }}</td>
                                    <td>{{ $rate->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.load-unload-master.edit', $rate->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.load-unload-master.delete', $rate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="19" class="text-center">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    {{ $datalist->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
