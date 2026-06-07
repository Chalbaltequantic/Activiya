@extends('admin.admin')

@section('bodycontent')

<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">

<style>


    .filter-box {
        background: #fff;
        border: 1px solid #ddd;
        padding: 12px;
        margin-bottom: 15px;
    }
</style>
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
      left: 90px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 185px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 260px; /* Adjust based on col-1 width */
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
        <h1>DigiWim Inventory</h1>
    </div>
</div>

<div class="content">
<div class="container-fluid">

<div class="filter-box">
    <form method="GET" action="{{ route('admin.digiwim.inventory') }}">
        <div class="row">

            <div class="col-md-4">
                <label>Search Based on Location</label>
                <input type="text"
                       name="location"
                       class="form-control"
                       value="{{ request('location') }}"
                       placeholder="Plant code / plant name / location">
            </div>

            <div class="col-md-3">
                <label>Date</label>
                <input type="date"
                       name="date"
                       class="form-control"
                       value="{{ request('date') }}">
            </div>

            <div class="col-md-3 mt-4">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="{{ route('admin.digiwim.inventory') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </div>
    </form>
</div>

 <div class="row">
          <div class="col-lg-12">
            <div class="card">
			
              <div class="card-body p-0">
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table id="billDataTable" class="table table-bordered border-dark table-hover">

				<thead>
				<tr>
					<th style="background: #fce4d6; color: #0070c0;">Material Code</th>
					<th style="background: #fce4d6; color: #0070c0;">Material Description</th>

					<th style="background: #fce4d6; color: #0070c0;">Division</th>
					<th style="background: #fce4d6; color: #0070c0;">Brand</th>
					<th style="background: #fce4d6; color: #0070c0;">Sub Brand</th>
					<th style="background: #fce4d6; color: #0070c0;">UOM</th>
					<th style="background: #fce4d6; color: #0070c0;">Piece<br>per<br>Box</th>
					<th style="background: #fce4d6; color: #0070c0;">MRP</th>
					<th style="background: #fce4d6; color: #0070c0;">Weight<br>(KG)</th>
					<th style="background: #fce4d6; color: #0070c0;">Volume<br>(CFT)</th>

					<th style="background: #fce4d6; color: #0070c0;">Storage<br>Plant<br>Code</th>
					<th style="background: #fce4d6; color: #0070c0;">Storage<br>Plant Name</th>
					<th style="background: #fce4d6; color: #0070c0;">Storage<br>Plant Loc**</th>

					<th style="background: #fce4d6; color: #0070c0;">Safety<br>Stock<br>Level</th>

					<th style="background: #fce4d6; color: #0070c0;">Batch</th>
					<th style="background: #fce4d6; color: #0070c0;">MFG Date</th>
					<th style="background: #fce4d6; color: #0070c0;">Expiry Date</th>

					<th style="background: #fce4d6; color: #0070c0;">Qty.<br>(Unit)</th>
					<th style="background: #fce4d6; color: #0070c0;">Qty.<br>(Case)</th>
					<th style="background: #fce4d6; color: #0070c0;">BIN No.</th>
				</tr>
				</thead>

				<tbody>

				@forelse($records as $row)

				@php
					$piecePerBox = (float) ($row->piece_per_box ?? 0);
					$availableQty = (float) ($row->available_qty ?? 0);

					$caseQty = '';
					if ($piecePerBox > 0) {
						$caseQty = round($availableQty / $piecePerBox, 2);
					}
				@endphp

				<tr>
					<td>{{ $row->material_code }}</td>
					<td>{{ $row->material_description }}</td>

					<td>{{ $row->division }}</td>
					<td>{{ $row->brand }}</td>
					<td>{{ $row->sub_brand }}</td>
					<td>{{ $row->uom }}</td>
					<td>{{ $row->piece_per_box }}</td>
					<td>{{ $row->mrp }}</td>
					<td>{{ $row->weight }}</td>
					<td>{{ $row->volume }}</td>

					<td>{{ $row->storage_plant_code }}</td>
					<td>{{ $row->storage_plant_name }}</td>
					<td>{{ $row->storage_plant_location }}</td>

					<td>{{ $row->safety_stock_level ?? '' }}</td>

					<td>{{ $row->batch_no }}</td>
					<td>{{ $row->mfg_date }}</td>
					<td>{{ $row->expiry_date }}</td>

					<td>{{ number_format($availableQty, 2) }}</td>
					<td>{{ $caseQty !== '' ? number_format($caseQty, 2) : '' }}</td>
					<td>{{ $row->bin_no }}</td>
				</tr>

				@empty

				
				@endforelse

				</tbody>

			</table>
		</div>
	</div>
</div>
</div>
</div>
</div>
</div>

@endsection