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
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Transport Rate Master List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transport Rate Master List</li>
                </ol>
            </div>
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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transport Rate Master Records</h3>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('admin.transport-rate-master.list') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control"
                                       value="{{ request('search') }}" placeholder="Lane, code, vendor, location">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Mode</label>
                                <select name="mode" class="form-control">
                                    <option value="">All Modes</option>
                                    @foreach(['PTL', 'AIR'] as $mode)
                                        <option value="{{ $mode }}" {{ request('mode') == $mode ? 'selected' : '' }}>
                                            {{ $mode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Vendor Code</label>
                                <input type="text" name="vendor_code" class="form-control"
                                       value="{{ request('vendor_code') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Rows Per Page</label>
                                <select name="per_page" class="form-control">
                                    @foreach([10, 25, 50, 100] as $pageSize)
                                        <option value="{{ $pageSize }}" {{ (int) request('per_page', 25) === $pageSize ? 'selected' : '' }}>
                                            {{ $pageSize }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('admin.transport-rate-master.list') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm text-nowrap">
                        <thead>
                            <tr>
                                <th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Lane Name</th>
                                <th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">From Code</th>
                                <th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">From Name</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Origin Region</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">To Name</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">To Code</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Destination Region</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Code</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Vendor Name</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Mode</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Chargeable Weight</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">CFT Value</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">CFT Weight</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Rate Logic / Base Rate per KG/Case</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Pickup Charges</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Delivery Charges</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Detention Rate</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">FOV on Base Rate</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Fuel Surcharge on Base Rate</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">ODA</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">ODA 1</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">ODA 2</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Minimum Chargeable Weight/Case</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Validity Start Date</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Validity End Date</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Docket Charges</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Custom</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Custom 1</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Custom 2</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Custom 3</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Custom 4</th>
                                <th style="background: #fce4d6; color: #0070c0;z-index:999;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($datalist as $key => $rate)
                                <tr>
                                    <td>{{ $datalist->firstItem() + $key }}</td>
                                    <td>{{ $rate->lane_name }}</td>
                                    <td>{{ $rate->from_code }}</td>
                                    <td>{{ $rate->from_name }}</td>
                                    <td>{{ $rate->origin_region }}</td>
                                    <td>{{ $rate->to_name }}</td>
                                    <td>{{ $rate->to_code }}</td>
                                    <td>{{ $rate->destination_region }}</td>
                                    <td>{{ $rate->vendor_code }}</td>
                                    <td>{{ $rate->vendor_name }}</td>
                                    <td>{{ $rate->mode }}</td>
                                    <td>{{ $rate->chargeable_weight }}</td>
                                    <td>{{ $rate->cft_value }}</td>
                                    <td>{{ $rate->cft_weight }}</td>
                                    <td>{{ $rate->rate_logic }}</td>
                                    <td>{{ $rate->pickup_charges }}</td>
                                    <td>{{ $rate->delivery_charges }}</td>
                                    <td>{{ $rate->detention_rate }}</td>
                                    <td>{{ $rate->fov_on_base_rate }}</td>
                                    <td>{{ $rate->fuel_surcharge_on_base_rate }}</td>
                                    <td>{{ $rate->oda }}</td>
                                    <td>{{ $rate->oda_1 }}</td>
                                    <td>{{ $rate->oda_2 }}</td>
                                    <td>{{ $rate->minimum_chargeable_weight_case }}</td>
                                    <td>{{ $rate->validity_start_date }}</td>
                                    <td>{{ $rate->validity_end_date }}</td>
                                    <td>{{ $rate->docket_charges }}</td>
                                    <td>{{ $rate->custom }}</td>
                                    <td>{{ $rate->custom_1 }}</td>
                                    <td>{{ $rate->custom_2 }}</td>
                                    <td>{{ $rate->custom_3 }}</td>
                                    <td>{{ $rate->custom_4 }}</td>
                                    <td>
                                        {{-- Add Edit/Delete links here using your registered route names and role permissions. --}}
                                        <span class="text-muted">—</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="36" class="text-center">No Transport Rate Master records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $datalist->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
