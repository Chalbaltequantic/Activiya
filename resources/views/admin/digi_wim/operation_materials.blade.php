@extends('admin.admin')

@section('bodycontent')

<div class="content-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <h1>Unloading Material Details</h1>

        <a href="{{ route('admin.digiwim.operation.list') }}"
           class="btn btn-secondary">

            <i class="fa fa-arrow-left"></i> Back

        </a>

    </div>
</div>

<div class="content">
<div class="container-fluid">

<div class="card">

<div class="card-header bg-info text-white">

    Invoice:
    {{ $header->invoice_challan_no }}

</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

    <th>Invoice No / Challan No</th>

    <th>Invoice Date</th>

    <th>Created By</th>

    <th>Material Code</th>

    <th>Description</th>

    <th>Batch</th>

    <th>MFG</th>

    <th>Expiry</th>

    <th>Qty</th>

    <th>BIN</th>

    <th>Status</th>

    <th>Remarks</th>

</tr>

</thead>

<tbody>

@foreach($header->items as $item)

<tr>

    <td>
        {{ $header->invoice_challan_no }}
    </td>

    <td>
        {{ $header->invoice_date }}
    </td>

    <td>
        {{ $header->creator->name ?? '' }}
    </td>

    <td>
        {{ $item->material_code }}
    </td>

    <td>
        {{ $item->material_description }}
    </td>

    <td>
        {{ $item->batch_no }}
    </td>

    <td>
        {{ $item->mfg_date }}
    </td>

    <td>
        {{ $item->expiry_date }}
    </td>

    <td>
        {{ $item->qty }}
    </td>

    <td>
        {{ $item->bin_no }}
    </td>

    <td>
        {{ $item->goods_status }}
    </td>

    <td>
        {{ $item->remarks }}
    </td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>
</div>

@endsection