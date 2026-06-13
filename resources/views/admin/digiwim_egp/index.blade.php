@extends('admin.admin')
@section('bodycontent')

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
    <div class="card-header" style="background:#fce4d6;color:#0070c0;">
        <h3 class="card-title">DigiWIM EGP Records</h3>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Outward Date</th>
                    <th>Outward Time</th>
                    <th>Purpose</th>
                    <th>Customer Name</th>
                    <th>Customer Location</th>
                    <th>Invoice/Challan No.</th>
                    <th>Invoice/Challan Date</th>
                    <th>Vendor</th>
                    <th>Truck No.</th>
                    <th>LR/CN No.</th>
                    <th>Driver Mobile</th>
                    <th>Custom</th>
                    <th width="150">Action</th>
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
                <tr>
                    <td colspan="11" class="text-center">No record found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $records->links() }}
    </div>
</div>

</div>
</div>

@endsection