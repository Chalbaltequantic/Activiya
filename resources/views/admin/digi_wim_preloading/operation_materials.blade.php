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
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <h1>Preloading Material Details</h1>

        <a href="{{ route('admin.digiwimpreloading.operation.list') }}"
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

    <th style="background: #fce4d6; color: #0070c0;">Invoice No / Challan No</th>

    <th style="background: #fce4d6; color: #0070c0;">Invoice Date</th>

    <th style="background: #fce4d6; color: #0070c0;">Created By</th>

    <th style="background: #fce4d6; color: #0070c0;">Material Code</th>

    <th style="background: #fce4d6; color: #0070c0;">Description</th>

    <th style="background: #fce4d6; color: #0070c0;">Batch</th>

    <th style="background: #fce4d6; color: #0070c0;">MFG</th>

    <th style="background: #fce4d6; color: #0070c0;">Expiry</th>

    <th style="background: #fce4d6; color: #0070c0;">Qty</th>

    <th style="background: #fce4d6; color: #0070c0;">BIN</th>

    <th style="background: #fce4d6; color: #0070c0;">Status</th>

    <th style="background: #fce4d6; color: #0070c0;">Remarks</th>

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