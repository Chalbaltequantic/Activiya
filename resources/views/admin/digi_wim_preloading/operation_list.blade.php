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
      left: 132px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 242px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 332px; /* Adjust based on col-1 width */
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
.bulk-delete-footer-wrap {
    overflow-x: auto;
    background: #fff;
    border-top: 1px solid #dee2e6;
}

.bulk-delete-footer-inner {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 12px 15px;
    background: #fff;
}

.bulk-delete-footer-inner .btn {
    min-width: 180px;
}
	
  </style>
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
{{-- <th style="background: #fce4d6; color: #0070c0;">Operation</th> --}}
    <th style="background: #fce4d6; color: #0070c0;">Invoice No</th>
    <th style="background: #fce4d6; color: #0070c0;">Invoice Date</th>
    
    <th style="background: #fce4d6; color: #0070c0;">Supplier</th>
    <th style="background: #fce4d6; color: #0070c0;">Transporter</th>
    <th style="background: #fce4d6; color: #0070c0;">Truck</th>
    <th style="background: #fce4d6; color: #0070c0;">Created By</th>
    <th style="background: #fce4d6; color: #0070c0;">Material</th>
    <th style="background: #fce4d6; color: #0070c0;">PDF</th>
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