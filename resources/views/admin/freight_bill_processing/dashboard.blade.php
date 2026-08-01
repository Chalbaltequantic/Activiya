@extends('admin.admin')

@section('bodycontent')

<div class="content-wrapper">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2 align-items-center">

                <div class="col-sm-7">

                    <h1 class="m-0">
                        {{ $pagetitle ?? 'Freight Bill Processing Dashboard' }}
                    </h1>

                    <small class="text-muted">
                        Freight invoice count, value and ageing analysis
                    </small>

                </div>

                <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">

                    <a
                        href="{{ route(
                            'admin.freight-bill-processing.export-xls',
                            request()->query()
                        ) }}"
                        class="btn btn-success"
                    >
                        <i class="fas fa-file-excel mr-1"></i>
                        Download XLS
                    </a>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
    <section class="content">

        <div class="container-fluid">


            {{-- =================================================
                 SESSION AND VALIDATION MESSAGES
            ================================================== --}}
            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                    >
                        &times;
                    </button>

                    <i class="fas fa-check-circle mr-1"></i>

                    {{ session('success') }}

                </div>

            @endif


            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert"
                    >
                        &times;
                    </button>

                    <i class="fas fa-exclamation-circle mr-1"></i>

                    {{ session('error') }}

                </div>

            @endif


            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        Please correct the following:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =================================================
                 REPORT TOTALS USED IN CARDS AND CHARTS
            ================================================== --}}
            @php

                $receivedCount = array_sum(
                    $report['count_matrix']['received'] ?? []
                );

                $validatedCount = array_sum(
                    $report['count_matrix']['validated'] ?? []
                );

                $returnedCount = array_sum(
                    $report['count_matrix']['returned'] ?? []
                );

                $pendingCount = array_sum(
                    $report['count_matrix']['pending'] ?? []
                );

                $paidCount = array_sum(
                    $report['count_matrix']['paid'] ?? []
                );


                $receivedValue = array_sum(
                    $report['value_matrix']['received'] ?? []
                );

                $validatedValue = array_sum(
                    $report['value_matrix']['validated'] ?? []
                );

                $returnedValue = array_sum(
                    $report['value_matrix']['returned'] ?? []
                );

                $pendingValue = array_sum(
                    $report['value_matrix']['pending'] ?? []
                );

                $paidValue = array_sum(
                    $report['value_matrix']['paid'] ?? []
                );


                $statusRows = [

                    'received' => 'Invoice Received',

                    'validated' => 'Invoice Validated',

                    'returned' => 'Invoice Returned',

                    'pending' => 'Invoices Pending',

                    'paid' => 'Invoices Paid',

                ];

            @endphp


            {{-- =================================================
                 LOGGED-IN VENDOR INFORMATION
            ================================================== --}}
            @if(
                isset($canViewAllVendors) &&
                !$canViewAllVendors
            )

                <div class="callout callout-info">

                    <h5>

                        <i class="fas fa-user-tag mr-1"></i>

                        Vendor Report

                    </h5>

                    <p class="mb-0">

                        This dashboard contains data only for:

                        <strong>

                            {{ $loggedInVendorName
                                ?? $loggedInVendorCode
                                ?? 'Logged-in Vendor' }}

                        </strong>

                        @if(!empty($loggedInVendorCode))

                            <span class="ml-2">

                                Vendor Code:

                                <strong>
                                    {{ $loggedInVendorCode }}
                                </strong>

                            </span>

                        @endif

                    </p>

                </div>

            @endif


            {{-- =================================================
                 FILTER SECTION
            ================================================== --}}
            <div class="card card-outline card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-filter mr-1"></i>

                        Report Filters

                    </h3>

                    <div class="card-tools">

                        <button
                            type="button"
                            class="btn btn-tool"
                            data-card-widget="collapse"
                        >
                            <i class="fas fa-minus"></i>
                        </button>

                    </div>

                </div>

                <div class="card-body">

                    <form
                        method="GET"
                        action="{{ route(
                            'admin.freight-bill-processing.index'
                        ) }}"
                    >

                        <div class="row">


                            {{-- Mode --}}
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Select Mode
                                </label>

                                <select
                                    name="mode"
                                    class="form-control"
                                    {{ empty($columns['mode'])
                                        ? 'disabled'
                                        : '' }}
                                >

                                    <option value="">
                                        All Modes
                                    </option>

                                    @foreach($modes as $mode)

                                        <option
                                            value="{{ $mode }}"
                                            {{ request('mode') == $mode
                                                ? 'selected'
                                                : '' }}
                                        >
                                            {{ $mode }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- Vendor --}}
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Select Vendor
                                </label>

                                @if(
                                    isset($canViewAllVendors) &&
                                    $canViewAllVendors
                                )

                                    <select
                                        name="vendor"
                                        class="form-control"
                                        {{ empty($columns['vendor_code'])
                                            ? 'disabled'
                                            : '' }}
                                    >

                                        <option value="">
                                            All Vendors
                                        </option>

                                        @foreach($vendors as $vendor)

                                            <option
                                                value="{{ $vendor->vendor_code }}"
                                                {{ request('vendor') ==
                                                    $vendor->vendor_code
                                                    ? 'selected'
                                                    : '' }}
                                            >

                                                {{ $vendor->vendor_name }}

                                                @if(
                                                    !empty(
                                                        $vendor->vendor_name
                                                    ) &&
                                                    $vendor->vendor_name !=
                                                    $vendor->vendor_code
                                                )

                                                    ({{ $vendor->vendor_code }})

                                                @endif

                                            </option>

                                        @endforeach

                                    </select>

                                @else

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $loggedInVendorName
                                            ?? $loggedInVendorCode
                                            ?? '' }}"
                                        readonly
                                    >

                                @endif

                            </div>


                            {{-- Plant --}}
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Select Plant
                                </label>

                                <select
                                    name="plant"
                                    class="form-control"
                                    {{ empty($columns['plant'])
                                        ? 'disabled'
                                        : '' }}
                                >

                                    <option value="">
                                        All Plants
                                    </option>

                                    @foreach($plants as $plant)

                                        <option
                                            value="{{ $plant }}"
                                            {{ request('plant') == $plant
                                                ? 'selected'
                                                : '' }}
                                        >
                                            {{ $plant }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- From Date --}}
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">

                                <label>
                                    From Date
                                </label>

                                <input
                                    type="date"
                                    name="from_date"
                                    class="form-control"
                                    value="{{ request('from_date') }}"
                                >

                            </div>


                            {{-- To Date --}}
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">

                                <label>
                                    To Date
                                </label>

                                <input
                                    type="date"
                                    name="to_date"
                                    class="form-control"
                                    value="{{ request('to_date') }}"
                                >

                            </div>


                            {{-- Filter Buttons --}}
                            <div class="col-lg-1 col-md-4 col-sm-6 mb-3">

                                <label class="d-block">
                                    &nbsp;
                                </label>

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-block"
                                    title="Apply Filter"
                                >
                                    <i class="fas fa-search"></i>
                                </button>

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-12">

                                <a
                                    href="{{ route(
                                        'admin.freight-bill-processing.index'
                                    ) }}"
                                    class="btn btn-secondary"
                                >
                                    <i class="fas fa-undo mr-1"></i>
                                    Reset Filter
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            {{-- =================================================
                 SUMMARY SMALL BOXES
            ================================================== --}}
            <div class="row">


                {{-- Total Shipments --}}
                <div class="col-lg-3 col-6">

                    <div class="small-box bg-info">

                        <div class="inner">

                            <h3>
                                {{ number_format(
                                    $report['total_count'] ?? 0
                                ) }}
                            </h3>

                            <p>
                                Total Shipments
                            </p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-truck"></i>

                        </div>

                    </div>

                </div>


                {{-- Total Freight Value --}}
                <div class="col-lg-3 col-6">

                    <div class="small-box bg-success">

                        <div class="inner">

                            <h3 class="summary-money">

                                ₹{{ number_format(
                                    $report['total_value'] ?? 0,
                                    2
                                ) }}

                            </h3>

                            <p>
                                Total Freight Value
                            </p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-rupee-sign"></i>

                        </div>

                    </div>

                </div>


                {{-- Mode Count --}}
                <div class="col-lg-3 col-6">

                    <div class="small-box bg-warning">

                        <div class="inner">

                            <h3>
                                {{ number_format(
                                    $report['mode_count'] ?? 0
                                ) }}
                            </h3>

                            <p>
                                Shipments with Mode
                            </p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-route"></i>

                        </div>

                    </div>

                </div>


                {{-- Pending Count --}}
                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger">

                        <div class="inner">

                            <h3>
                                {{ number_format($pendingCount) }}
                            </h3>

                            <p>
                                Pending Invoices
                            </p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-clock"></i>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CHART ROW 1
            ================================================== --}}
            <div class="row">


                {{-- Workflow Count Bar Chart --}}
                <div class="col-lg-6 col-md-12">

                    <div class="card card-outline card-primary">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-bar mr-1"></i>

                                Workflow Count Comparison

                            </h3>

                            <div class="card-tools">

                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="chart">

                                <canvas
                                    id="workflowCountChart"
                                    class="dashboard-chart"
                                ></canvas>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Workflow Value Bar Chart --}}
                <div class="col-lg-6 col-md-12">

                    <div class="card card-outline card-success">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-bar mr-1"></i>

                                Workflow Value Comparison

                            </h3>

                            <div class="card-tools">

                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="chart">

                                <canvas
                                    id="workflowValueChart"
                                    class="dashboard-chart"
                                ></canvas>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CHART ROW 2
            ================================================== --}}
            <div class="row">


                {{-- Pending Ageing Pie Chart --}}
                <div class="col-lg-6 col-md-12">

                    <div class="card card-outline card-warning">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-pie mr-1"></i>

                                Pending Invoice Ageing Distribution

                            </h3>

                            <div class="card-tools">

                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>

                        </div>

                        <div class="card-body">

                            <div class="chart">

                                <canvas
                                    id="pendingAgeingChart"
                                    class="dashboard-chart"
                                ></canvas>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Workflow Analysis --}}
                <div class="col-lg-6 col-md-12">

                    <div class="card card-outline card-info">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-analytics mr-1"></i>

                                Report Analysis

                            </h3>

                            <div class="card-tools">

                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>

                        </div>

                        <div class="card-body">


                            {{-- Received --}}
                            <div class="info-box mb-2">

                                <span class="info-box-icon bg-primary">

                                    <i class="fas fa-file-invoice"></i>

                                </span>

                                <div class="info-box-content">

                                    <span class="info-box-text">
                                        Invoice Received
                                    </span>

                                    <span class="info-box-number">

                                        {{ number_format($receivedCount) }}

                                        <small class="float-right">

                                            ₹{{ number_format(
                                                $receivedValue,
                                                2
                                            ) }}

                                        </small>

                                    </span>

                                </div>

                            </div>


                            {{-- Validated --}}
                            <div class="info-box mb-2">

                                <span class="info-box-icon bg-success">

                                    <i class="fas fa-check-circle"></i>

                                </span>

                                <div class="info-box-content">

                                    <span class="info-box-text">
                                        Invoice Validated
                                    </span>

                                    <span class="info-box-number">

                                        {{ number_format($validatedCount) }}

                                        <small class="float-right">

                                            ₹{{ number_format(
                                                $validatedValue,
                                                2
                                            ) }}

                                        </small>

                                    </span>

                                </div>

                            </div>


                            {{-- Returned --}}
                            <div class="info-box mb-2">

                                <span class="info-box-icon bg-danger">

                                    <i class="fas fa-undo-alt"></i>

                                </span>

                                <div class="info-box-content">

                                    <span class="info-box-text">
                                        Invoice Returned
                                    </span>

                                    <span class="info-box-number">

                                        {{ number_format($returnedCount) }}

                                        <small class="float-right">

                                            ₹{{ number_format(
                                                $returnedValue,
                                                2
                                            ) }}

                                        </small>

                                    </span>

                                </div>

                            </div>


                            {{-- Pending --}}
                            <div class="info-box mb-2">

                                <span class="info-box-icon bg-warning">

                                    <i class="fas fa-clock"></i>

                                </span>

                                <div class="info-box-content">

                                    <span class="info-box-text">
                                        Invoices Pending
                                    </span>

                                    <span class="info-box-number">

                                        {{ number_format($pendingCount) }}

                                        <small class="float-right">

                                            ₹{{ number_format(
                                                $pendingValue,
                                                2
                                            ) }}

                                        </small>

                                    </span>

                                </div>

                            </div>


                            {{-- Paid --}}
                            <div class="info-box mb-0">

                                <span class="info-box-icon bg-secondary">

                                    <i class="fas fa-money-check-alt"></i>

                                </span>

                                <div class="info-box-content">

                                    <span class="info-box-text">
                                        Invoices Paid
                                    </span>

                                    <span class="info-box-number">

                                        {{ number_format($paidCount) }}

                                        <small class="float-right">

                                            ₹{{ number_format(
                                                $paidValue,
                                                2
                                            ) }}

                                        </small>

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 COUNT AND VALUE AGEING TABLE
            ================================================== --}}
            <div class="card card-outline card-secondary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-table mr-1"></i>

                        Freight Bill Ageing Report

                    </h3>

                    <div class="card-tools">

                        <button
                            type="button"
                            class="btn btn-tool"
                            data-card-widget="collapse"
                        >
                            <i class="fas fa-minus"></i>
                        </button>

                    </div>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive freight-report-table-wrapper">

                        <table
                            class="
                                table
                                table-bordered
                                table-hover
                                table-sm
                                freight-report-table
                                mb-0
                            "
                        >

                            {{-- =========================================
                                 COUNT HEADER
                            ========================================== --}}
                            <thead>

                                <tr class="count-table-header">

                                    <th class="status-column">

                                        Count

                                    </th>

                                    @foreach(
                                        $report['buckets'] ?? []
                                        as $bucketKey => $bucketLabel
                                    )

                                        <th
                                            class="
                                                text-center
                                                ageing-heading
                                                ageing-{{ $bucketKey }}
                                            "
                                        >
                                            {{ $bucketLabel }}
                                        </th>

                                    @endforeach

                                </tr>

                            </thead>


                            <tbody>

                                {{-- Count Rows --}}
                                @foreach(
                                    $statusRows
                                    as $statusKey => $statusLabel
                                )

                                    <tr>

                                        <td class="status-column">

                                            {{ $statusLabel }}

                                        </td>

                                        @foreach(
                                            $report['buckets'] ?? []
                                            as $bucketKey => $bucketLabel
                                        )

                                            <td class="text-center">

                                                <strong>

                                                    {{ number_format(
                                                        $report[
                                                            'count_matrix'
                                                        ][
                                                            $statusKey
                                                        ][
                                                            $bucketKey
                                                        ] ?? 0
                                                    ) }}

                                                </strong>

                                            </td>

                                        @endforeach

                                    </tr>

                                @endforeach


                                {{-- Divider --}}
                                <tr class="report-divider">

                                    <td colspan="9"></td>

                                </tr>


                                {{-- =====================================
                                     VALUE HEADER
                                ====================================== --}}
                                <tr class="value-table-header">

                                    <th class="status-column">

                                        Value

                                    </th>

                                    @foreach(
                                        $report['buckets'] ?? []
                                        as $bucketKey => $bucketLabel
                                    )

                                        <th
                                            class="
                                                text-center
                                                ageing-heading
                                                ageing-{{ $bucketKey }}
                                            "
                                        >
                                            {{ $bucketLabel }}
                                        </th>

                                    @endforeach

                                </tr>


                                {{-- Value Rows --}}
                                @foreach(
                                    $statusRows
                                    as $statusKey => $statusLabel
                                )

                                    <tr>

                                        <td class="status-column">

                                            {{ $statusLabel }}

                                        </td>

                                        @foreach(
                                            $report['buckets'] ?? []
                                            as $bucketKey => $bucketLabel
                                        )

                                            <td class="text-right">

                                                ₹{{ number_format(
                                                    $report[
                                                        'value_matrix'
                                                    ][
                                                        $statusKey
                                                    ][
                                                        $bucketKey
                                                    ] ?? 0,
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


                {{-- Ageing Logic Footer --}}
                <div class="card-footer">

                    <div class="row">

                        <div class="col-md-9">

                            <strong>
                                Ageing logic:
                            </strong>

                            <span class="ml-2">

                                Pending:
                                <code>created_at</code>

                            </span>

                            <span class="ml-3">

                                Received:
                                <code>freight_info_updated_at</code>

                            </span>

                            <span class="ml-3">

                                Validated:
                                <code>validated_at</code>

                            </span>

                            <span class="ml-3">

                                Returned:
                                <code>returned_at</code>

                            </span>

                        </div>

                        <div class="col-md-3 text-md-right">

                            <small class="text-muted">

                                Data beyond 180 days is excluded from
                                the ageing grid.

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

@endsection



{{-- =============================================================
     PAGE STYLES
============================================================== --}}
@section('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Summary Boxes
    |--------------------------------------------------------------------------
    */

    .summary-money {
        font-size: 25px !important;
        word-break: break-word;
    }


    /*
    |--------------------------------------------------------------------------
    | Chart Size
    |--------------------------------------------------------------------------
    */

    .dashboard-chart {
        min-height: 330px;
        height: 330px;
        max-height: 330px;
        max-width: 100%;
    }


    /*
    |--------------------------------------------------------------------------
    | Analysis Boxes
    |--------------------------------------------------------------------------
    */

    .info-box {
        min-height: 82px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.10);
    }

    .info-box .info-box-icon {
        width: 75px;
    }

    .info-box .info-box-number small {
        font-size: 13px;
        font-weight: 600;
        padding-top: 4px;
    }


    /*
    |--------------------------------------------------------------------------
    | Report Table
    |--------------------------------------------------------------------------
    */

    .freight-report-table-wrapper {
        max-height: 620px;
        overflow: auto;
    }

    .freight-report-table {
        min-width: 1250px;
        font-size: 13px;
    }

    .freight-report-table th,
    .freight-report-table td {
        padding: 9px 10px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .freight-report-table thead th {
        position: sticky;
        top: 0;
        z-index: 4;
    }

    .freight-report-table .status-column {
        position: sticky;
        left: 0;
        z-index: 3;
        min-width: 230px;
        font-weight: 700;
        background: #edf4fa;
    }

    .freight-report-table thead .status-column {
        z-index: 5;
    }

    .count-table-header th,
    .value-table-header th {
        background: #fce4d6;
        color: #0070c0;
        font-weight: 700;
    }

    .ageing-heading {
        min-width: 115px;
    }

    .ageing-0_15,
    .ageing-16_30,
    .ageing-31_45 {
        color: #008c3a !important;
    }

    .ageing-46_60,
    .ageing-61_90 {
        color: #b36b00 !important;
    }

    .ageing-91_120,
    .ageing-121_150,
    .ageing-151_180 {
        color: #d40000 !important;
    }

    .report-divider td {
        height: 20px;
        padding: 0;
        border-left: 0;
        border-right: 0;
        background: #f4f6f9;
    }


    /*
    |--------------------------------------------------------------------------
    | Code Labels
    |--------------------------------------------------------------------------
    */

    code {
        padding: 2px 5px;
        border-radius: 3px;
        background: #f9f2f4;
        color: #c7254e;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {

        .content-header h1 {
            font-size: 22px;
        }

        .dashboard-chart {
            min-height: 280px;
            height: 280px;
            max-height: 280px;
        }

        .small-box h3 {
            font-size: 22px;
        }

        .summary-money {
            font-size: 18px !important;
        }

        .info-box .info-box-number small {
            display: block;
            float: none !important;
        }

    }

</style>

@endsection



{{-- =============================================================
     PAGE JAVASCRIPT
============================================================== --}}
@section('scripts')

{{-- AdminLTE 3 Local Chart.js Asset --}}
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>

<script>

$(function () {

    /*
    |--------------------------------------------------------------------------
    | Confirm That AdminLTE Chart.js Is Loaded
    |--------------------------------------------------------------------------
    */

    if (typeof Chart === 'undefined') {

        console.error(
            'Chart.js is not loaded. Check the AdminLTE Chart.js asset path.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Workflow Labels
    |--------------------------------------------------------------------------
    */

    var workflowLabels = [

        'Invoice Received',

        'Invoice Validated',

        'Invoice Returned',

        'Invoices Pending',

        'Invoices Paid'

    ];


    /*
    |--------------------------------------------------------------------------
    | Workflow Count Data
    |--------------------------------------------------------------------------
    */

    var workflowCountData = [

        {{ (int) $receivedCount }},

        {{ (int) $validatedCount }},

        {{ (int) $returnedCount }},

        {{ (int) $pendingCount }},

        {{ (int) $paidCount }}

    ];


    /*
    |--------------------------------------------------------------------------
    | Workflow Value Data
    |--------------------------------------------------------------------------
    */

    var workflowValueData = [

        {{ (float) $receivedValue }},

        {{ (float) $validatedValue }},

        {{ (float) $returnedValue }},

        {{ (float) $pendingValue }},

        {{ (float) $paidValue }}

    ];


    /*
    |--------------------------------------------------------------------------
    | AdminLTE Theme Colours
    |--------------------------------------------------------------------------
    */

    var chartBackgroundColours = [

        'rgba(60, 141, 188, 0.80)',

        'rgba(0, 166, 90, 0.80)',

        'rgba(221, 75, 57, 0.80)',

        'rgba(243, 156, 18, 0.80)',

        'rgba(96, 92, 168, 0.80)'

    ];


    var chartBorderColours = [

        'rgba(60, 141, 188, 1)',

        'rgba(0, 166, 90, 1)',

        'rgba(221, 75, 57, 1)',

        'rgba(243, 156, 18, 1)',

        'rgba(96, 92, 168, 1)'

    ];


    /*
    |--------------------------------------------------------------------------
    | Common Bar Chart Options
    |--------------------------------------------------------------------------
    */

    var commonBarChartOptions = {

        maintainAspectRatio: false,

        responsive: true,

        legend: {

            display: false

        },

        scales: {

            xAxes: [

                {

                    gridLines: {

                        display: false

                    },

                    ticks: {

                        autoSkip: false,

                        maxRotation: 25,

                        minRotation: 0

                    }

                }

            ],

            yAxes: [

                {

                    ticks: {

                        beginAtZero: true

                    }

                }

            ]

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Workflow Count Chart
    |--------------------------------------------------------------------------
    */

    var workflowCountCanvas = document.getElementById(
        'workflowCountChart'
    );

    if (workflowCountCanvas) {

        new Chart(
            workflowCountCanvas.getContext('2d'),
            {

                type: 'bar',

                data: {

                    labels: workflowLabels,

                    datasets: [

                        {

                            label: 'Invoice Count',

                            data: workflowCountData,

                            backgroundColor:
                                chartBackgroundColours,

                            borderColor:
                                chartBorderColours,

                            borderWidth: 1

                        }

                    ]

                },

                options: {

                    maintainAspectRatio:
                        commonBarChartOptions
                            .maintainAspectRatio,

                    responsive:
                        commonBarChartOptions
                            .responsive,

                    legend: {

                        display: false

                    },

                    tooltips: {

                        callbacks: {

                            label: function (
                                tooltipItem
                            ) {

                                return 'Count: ' +
                                    Number(
                                        tooltipItem.yLabel
                                    ).toLocaleString(
                                        'en-IN'
                                    );

                            }

                        }

                    },

                    scales: {

                        xAxes: [

                            {

                                gridLines: {

                                    display: false

                                },

                                ticks: {

                                    autoSkip: false,

                                    maxRotation: 25,

                                    minRotation: 0

                                }

                            }

                        ],

                        yAxes: [

                            {

                                ticks: {

                                    beginAtZero: true,

                                    precision: 0,

                                    callback: function (
                                        value
                                    ) {

                                        return Number(
                                            value
                                        ).toLocaleString(
                                            'en-IN'
                                        );

                                    }

                                },

                                scaleLabel: {

                                    display: true,

                                    labelString:
                                        'Invoice Count'

                                }

                            }

                        ]

                    }

                }

            }

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Workflow Value Chart
    |--------------------------------------------------------------------------
    */

    var workflowValueCanvas = document.getElementById(
        'workflowValueChart'
    );

    if (workflowValueCanvas) {

        new Chart(
            workflowValueCanvas.getContext('2d'),
            {

                type: 'bar',

                data: {

                    labels: workflowLabels,

                    datasets: [

                        {

                            label: 'Freight Value',

                            data: workflowValueData,

                            backgroundColor:
                                chartBackgroundColours,

                            borderColor:
                                chartBorderColours,

                            borderWidth: 1

                        }

                    ]

                },

                options: {

                    maintainAspectRatio: false,

                    responsive: true,

                    legend: {

                        display: false

                    },

                    tooltips: {

                        callbacks: {

                            label: function (
                                tooltipItem
                            ) {

                                var value = Number(
                                    tooltipItem.yLabel
                                );

                                return 'Value: ₹' +
                                    value.toLocaleString(
                                        'en-IN',
                                        {

                                            minimumFractionDigits: 2,

                                            maximumFractionDigits: 2

                                        }
                                    );

                            }

                        }

                    },

                    scales: {

                        xAxes: [

                            {

                                gridLines: {

                                    display: false

                                },

                                ticks: {

                                    autoSkip: false,

                                    maxRotation: 25,

                                    minRotation: 0

                                }

                            }

                        ],

                        yAxes: [

                            {

                                ticks: {

                                    beginAtZero: true,

                                    callback: function (
                                        value
                                    ) {

                                        if (
                                            value >= 10000000
                                        ) {

                                            return '₹' +
                                                (
                                                    value /
                                                    10000000
                                                ).toFixed(1) +
                                                ' Cr';

                                        }

                                        if (
                                            value >= 100000
                                        ) {

                                            return '₹' +
                                                (
                                                    value /
                                                    100000
                                                ).toFixed(1) +
                                                ' L';

                                        }

                                        if (
                                            value >= 1000
                                        ) {

                                            return '₹' +
                                                (
                                                    value /
                                                    1000
                                                ).toFixed(1) +
                                                ' K';

                                        }

                                        return '₹' +
                                            value;

                                    }

                                },

                                scaleLabel: {

                                    display: true,

                                    labelString:
                                        'Freight Value'

                                }

                            }

                        ]

                    }

                }

            }

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Pending Ageing Chart Data
    |--------------------------------------------------------------------------
    */

    var pendingAgeingLabels = @json(
        array_values(
            $report['buckets'] ?? []
        )
    );


    var pendingAgeingData = @json(
        array_values(
            $report['count_matrix']['pending'] ?? []
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Pending Ageing Pie Chart
    |--------------------------------------------------------------------------
    */

    var pendingAgeingCanvas = document.getElementById(
        'pendingAgeingChart'
    );

    if (pendingAgeingCanvas) {

        new Chart(
            pendingAgeingCanvas.getContext('2d'),
            {

                type: 'pie',

                data: {

                    labels: pendingAgeingLabels,

                    datasets: [

                        {

                            data: pendingAgeingData,

                            backgroundColor: [

                                '#00a65a',

                                '#39cccc',

                                '#3c8dbc',

                                '#f39c12',

                                '#ff851b',

                                '#dd4b39',

                                '#d33724',

                                '#85144b'

                            ],

                            borderColor: '#ffffff',

                            borderWidth: 2

                        }

                    ]

                },

                options: {

                    maintainAspectRatio: false,

                    responsive: true,

                    legend: {

                        position: 'right',

                        labels: {

                            boxWidth: 14,

                            padding: 12

                        }

                    },

                    tooltips: {

                        callbacks: {

                            label: function (
                                tooltipItem,
                                chartData
                            ) {

                                var itemIndex =
                                    tooltipItem.index;

                                var label =
                                    chartData.labels[
                                        itemIndex
                                    ];

                                var value = Number(

                                    chartData
                                        .datasets[0]
                                        .data[
                                            itemIndex
                                        ] || 0

                                );

                                var total = chartData
                                    .datasets[0]
                                    .data
                                    .reduce(

                                        function (
                                            totalValue,
                                            currentValue
                                        ) {

                                            return Number(
                                                totalValue
                                            ) +
                                            Number(
                                                currentValue
                                            );

                                        },

                                        0

                                    );

                                var percentage = total > 0
                                    ? (
                                        value /
                                        total *
                                        100
                                    ).toFixed(1)
                                    : 0;

                                return label +
                                    ': ' +
                                    value.toLocaleString(
                                        'en-IN'
                                    ) +
                                    ' (' +
                                    percentage +
                                    '%)';

                            }

                        }

                    }

                }

            }

        );

    }

});

</script>

@endsection