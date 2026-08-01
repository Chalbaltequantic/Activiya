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


    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
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
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $pagetitle }}</h1>
                </div>

                <div class="col-sm-6 text-right">
                    <a
                        href="{{ route('admin.freight-bill-processing.export-xls', request()->query()) }}"
                        class="btn btn-success"
                    >
                        <i class="fas fa-file-excel"></i>
                        Download XLS
                    </a>
                </div>
            </div>

        </div>
    </section>


    <section class="content">

        <div class="container-fluid">

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif


            <div class="card card-outline card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        Freight Bill Processing Summary
                    </h3>
                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="summary-box">

                                <div class="summary-title">
                                    Total Shipments
                                </div>

                                <div class="summary-value-row">

                                    <div>
                                        <span class="summary-label">
                                            Count
                                        </span>

                                        <strong>
                                            {{ number_format($report['total_count']) }}
                                        </strong>
                                    </div>

                                    <div>
                                        <span class="summary-label">
                                            Value
                                        </span>

                                        <strong>
                                            ₹{{ number_format($report['total_value'], 2) }}
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="summary-box">

                                <div class="summary-title">
                                    Shipments Mode
                                </div>

                                <div class="summary-value-row">

                                    <div>
                                        <span class="summary-label">
                                            Count
                                        </span>

                                        <strong>
                                            {{ number_format($report['mode_count']) }}
                                        </strong>
                                    </div>

                                    <div>
                                        <span class="summary-label">
                                            Value
                                        </span>

                                        <strong>
                                            ₹{{ number_format($report['mode_value'], 2) }}
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card">

                <div class="card-header filter-header">
                    Filters
                </div>

                <div class="card-body">

                    <form
                        method="GET"
                        action="{{ route('admin.freight-bill-processing.index') }}"
                    >

                        <div class="row">

                            <div class="col-md-3">

                                <label>Select Mode</label>

                                <select
                                    name="mode"
                                    class="form-control"
                                    {{ $columns['mode'] ? '' : 'disabled' }}
                                >
                                    <option value="">
                                        All Mode
                                    </option>

                                    @foreach($modes as $mode)
                                        <option
                                            value="{{ $mode }}"
                                            {{ request('mode') == $mode ? 'selected' : '' }}
                                        >
                                            {{ $mode }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>


                            <div class="col-md-3">

                                <label>Select Vendor</label>

                                <select
                                    name="vendor"
                                    class="form-control"
                                    {{ $columns['vendor'] ? '' : 'disabled' }}
                                >
                                    <option value="">
                                        All Vendor
                                    </option>

                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{ $vendor }}"
                                            {{ request('vendor') == $vendor ? 'selected' : '' }}
                                        >
                                            {{ $vendor }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>


                            <div class="col-md-3">

                                <label>Select Plant</label>

                                <select
                                    name="plant"
                                    class="form-control"
                                    {{ $columns['plant'] ? '' : 'disabled' }}
                                >
                                    <option value="">
                                        All Plant
                                    </option>

                                    @foreach($plants as $plant)
                                        <option
                                            value="{{ $plant }}"
                                            {{ request('plant') == $plant ? 'selected' : '' }}
                                        >
                                            {{ $plant }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>


                            <div class="col-md-3">

                                <label>From Date</label>

                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">

                            </div>


                            <div class="col-md-3 mt-3">

                                <label>To Date</label>

                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">

                            </div>


                            <div class="col-md-4 mt-3">

                                <label>&nbsp;</label>

                                <div>

                                    <button type="submit" class="btn btn-primary">
                                        Apply Filter
                                    </button>

                                    <a href="{{ route('admin.freight-bill-processing.index') }}" class="btn btn-secondary">Reset</a>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <div class="card">

                <div class="card-body table-responsive p-0 table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
	

							<div class="excel-wrapper">
                    <table class="table table-bordered dashboard-table">

                        <thead>

                            <tr class="count-header">

                                <th class="row-title" style="background: #fce4d6; color: #0070c0;">
                                    Count
                                </th>

                                @foreach($report['buckets'] as $bucketKey => $bucketLabel)

                                    <th class="bucket bucket-{{ $bucketKey }}" style="background: #fce4d6; color: #0070c0;">
                                        {{ $bucketLabel }}
                                    </th>

                                @endforeach

                            </tr>

                        </thead>


                        <tbody>

                            @php
                                $statusRows = [
                                    'received' => 'Invoice Received',
                                    'validated' => 'Invoice Validated',
                                    'returned' => 'Invoice Returned',
                                    'pending' => 'Invoices Pending',
                                    'paid' => 'Invoices Paid',
                                ];
                            @endphp


                            @foreach($statusRows as $statusKey => $statusLabel)

                                <tr>

                                    <td class="row-title">
                                        {{ $statusLabel }}
                                    </td>

                                    @foreach($report['buckets'] as $bucketKey => $bucketLabel)

                                        <td class="text-center">
                                            {{ number_format(
                                                $report['count_matrix'][$statusKey][$bucketKey]
                                            ) }}
                                        </td>

                                    @endforeach

                                </tr>

                            @endforeach


                            <tr class="separator-row">
                                <td colspan="9"></td>
                            </tr>


                            <tr class="value-header">

                                <th class="row-title" style="background: #fce4d6; color: #0070c0;">
                                    Value
                                </th>

                                @foreach($report['buckets'] as $bucketKey => $bucketLabel)

                                    <th class="bucket bucket-{{ $bucketKey }}" style="background: #fce4d6; color: #0070c0;">
                                        {{ $bucketLabel }}
                                    </th>

                                @endforeach

                            </tr>


                            @foreach($statusRows as $statusKey => $statusLabel)

                                <tr>

                                    <td class="row-title">
                                        {{ $statusLabel }}
                                    </td>

                                    @foreach($report['buckets'] as $bucketKey => $bucketLabel)

                                        <td class="text-right">
                                            ₹{{ number_format(
                                                $report['value_matrix'][$statusKey][$bucketKey],
                                                2
                                            ) }}
                                        </td>

                                    @endforeach

                                </tr>

                            @endforeach

                        </tbody>

                    </table>
					</div>
                </div>


                <div class="card-footer">

                    <strong>
                        Base date for counting:
                    </strong>

                    Invoice Receiving Date

                    <span class="ml-3 text-muted">
                        Records older than 180 days are included in summary totals
                        but are not shown in the ageing matrix.
                    </span>

                </div>

            </div>

        </div>

    </section>

</div>

@endsection


@section('styles')

<style>

.summary-box {
    border: 1px solid #d8dee6;
    border-radius: 6px;
    background: #ffffff;
    padding: 18px;
    margin-bottom: 15px;
}

.summary-title {
    font-size: 18px;
    font-weight: 700;
    color: #25364b;
    margin-bottom: 15px;
}

.summary-value-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.summary-value-row > div {
    flex: 1;
    border-left: 4px solid #40566f;
    padding-left: 12px;
}

.summary-label {
    display: block;
    font-size: 13px;
    color: #6c757d;
}

.summary-value-row strong {
    display: block;
    font-size: 22px;
    margin-top: 3px;
}

.filter-header {
    background: #40566f;
    color: #ffffff;
    font-weight: 700;
}

.dashboard-table {
    min-width: 1180px;
    margin-bottom: 0;
}

.dashboard-table th,
.dashboard-table td {
    vertical-align: middle;
    padding: 10px 8px;
}

.dashboard-table .row-title {
    min-width: 270px;
    font-weight: 700;
    background: #edf4fa;
}

.dashboard-table .bucket {
    min-width: 110px;
    text-align: center;
    white-space: nowrap;
}

.dashboard-table .count-header,
.dashboard-table .value-header {
    background: #dcebf7;
}

.dashboard-table .bucket-0_15,
.dashboard-table .bucket-16_30,
.dashboard-table .bucket-31_45 {
    color: #00a83d;
}

.dashboard-table .bucket-46_60,
.dashboard-table .bucket-61_90 {
    color: #222222;
    background: #f8dfcf;
}

.dashboard-table .bucket-91_120,
.dashboard-table .bucket-121_150,
.dashboard-table .bucket-151_180 {
    color: #e00000;
    background: #fde9dd;
}

.dashboard-table .separator-row td {
    height: 22px;
    background: #ffffff;
    border-left: 0;
    border-right: 0;
}

@media (max-width: 767px) {

    .summary-value-row {
        display: block;
    }

    .summary-value-row > div {
        margin-bottom: 12px;
    }

}

</style>

@endsection
