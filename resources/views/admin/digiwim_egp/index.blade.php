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
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Digi WIM EGP List</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.digiwim-egp.create') }}" class="btn btn-primary">
                    Add New EGP
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
<div class="container-fluid">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <strong>{{ session('success') }}</strong>
    <button type="button" class="close" data-dismiss="alert"></button>
</div>
@endif

<div class="card">
   
     <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
			 
		<table id="billDataTable" class="table table-bordered border-dark table-hover">
            <thead>
                <tr>
                    <th style="background: #fce4d6; color: #0070c0;">#</th>
                    <th style="background: #fce4d6; color: #0070c0;">Outward Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Outward Time</th>
                    <th style="background: #fce4d6; color: #0070c0;">Purpose</th>
                    <th style="background: #fce4d6; color: #0070c0;">Customer Name</th>
                    <th style="background: #fce4d6; color: #0070c0;">Customer Location</th>
                    <th style="background: #fce4d6; color: #0070c0;">Invoice/Challan No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">Invoice/Challan Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Vendor</th>
                    <th style="background: #fce4d6; color: #0070c0;">Truck No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">LR/CN No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">Driver Mobile</th>
                    <th style="background: #fce4d6; color: #0070c0;">Custom</th>
                    <th style="background: #c6e0b4; color: #0070c0;" width="150">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $key => $row)
                <tr>
                    <td>{{ $records->firstItem() + $key }}</td>
                    <td>{{ $row->outward_date }}</td>
                    <td>{{ $row->outward_time }}</td>
                    <td>{{ $row->purpose_of_entry }}</td>
                    <td>{{ $row->supplier_name }}</td>
                    <td>{{ $row->invoice_challan_no }}</td>
                    <td>{{ $row->invoice_challan_date }}</td>
                    <td>{{ $row->vendor_name }}</td>
                    <td>{{ $row->truck_no }}</td>
                    <td>{{ $row->lr_cn_no }}</td>
                    <td>{{ $row->driver_mobile_no }}</td>
                    <td>{{ $row->custom }}</td>
                    <td>
                        <a href="{{ route('admin.digiwim-egp.edit', $row->id) }}" class="btn btn-sm btn-info">
                            Edit
                        </a>

                        <form action="{{ route('admin.digiwim-egp.destroy', $row->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
               
                @endforelse
            </tbody>
        </table>

        {{ $records->links() }}
    </div>
</div>

</div>
</div>

@endsection