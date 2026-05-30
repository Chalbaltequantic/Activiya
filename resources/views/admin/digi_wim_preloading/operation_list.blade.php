@extends('admin.admin')

@section('bodycontent')

<div class="content-header">
    <div class="container-fluid">
        <h1>DigiWim Preloading List</h1>
    </div>
</div>

<div class="content">
<div class="container-fluid">

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if($headers->count() == 0)

<div class="alert alert-warning">
    No unloading data found.
</div>

@endif

<div class="card">

<div class="card-body table-responsive">

<table class="table table-bordered table-striped">

<thead class="bg-info">

<tr>
{{-- <th>Operation</th> --}}
    <th>Invoice No</th>
    <th>Invoice Date</th>
    
    <th>Supplier</th>
    <th>Transporter</th>
    <th>Truck</th>
    <th>Created By</th>
    <th>Material</th>
    <th>PDF</th>
</tr>

</thead>

<tbody>

@foreach($headers as $header)

<tr>

{{-- <td>
        {{ ucfirst($header->operation_type) }}
    </td>
--}}
    <td>
        {{ $header->invoice_challan_no }}
    </td>

    <td>
        {{ $header->invoice_date }}
    </td>

    

    <td>
        {{ $header->supplier_code_name }}
    </td>

    <td>
        {{ $header->transporter_name }}
    </td>

    <td>
        {{ $header->truck_number }}
    </td>

    <td>
        {{ $header->creator->name ?? '' }}
    </td>

    <td>

        <a href="{{ route('admin.digiwimpreloading.operation.materials', $header->id) }}"
           class="btn btn-primary btn-sm">

            View Material

        </a>

    </td>

    <td>

        <a href="{{ route('admin.digiwimpreloading.operation.pdf', $header->id) }}"
           class="btn btn-danger btn-sm">

            Download PDF

        </a>

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