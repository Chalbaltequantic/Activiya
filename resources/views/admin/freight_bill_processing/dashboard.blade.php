@extends('admin.admin')

@section('bodycontent')

@php

    /*
    |--------------------------------------------------------------------------
    | Calculate Summary Values
    |--------------------------------------------------------------------------
    */

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
        'received'  => 'Invoice Received',
        'validated' => 'Invoice Validated',
        'returned'  => 'Invoice Returned',
        'pending'   => 'Invoices Pending',
        'paid'      => 'Invoices Paid',
    ];


    /*
    |--------------------------------------------------------------------------
    | Find AdminLTE Chart.js Asset
    |--------------------------------------------------------------------------
    |
    | Common AdminLTE installations use one of these folders:
    |
    | public/admin/plugins/chart.js/Chart.min.js
    | public/plugins/chart.js/Chart.min.js
    |
    */

    if (
        file_exists(
            public_path('admin/plugins/chart.js/Chart.min.js')
        )
    ) {
        $chartJsAsset = asset(
            'admin/plugins/chart.js/Chart.min.js'
        );
    } else {
        $chartJsAsset = asset(
            'plugins/chart.js/Chart.min.js'
        );
    }

@endphp


<style>

    /*
    |--------------------------------------------------------------------------
    | General Page Design
    |--------------------------------------------------------------------------
    */

    .freight-dashboard-page {
        padding-bottom: 25px;
    }

    .dashboard-subtitle {
        color: #6c757d;
        font-size: 14px;
        margin-top: 3px;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter Card
    |--------------------------------------------------------------------------
    */

    .filter-card {
        border-top: 3px solid #007bff;
        border-radius: 7px;
    }

    .filter-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e8edf2;
    }

    .filter-card label {
        font-size: 13px;
        font-weight: 700;
        color: #34495e;
    }


    /*
    |--------------------------------------------------------------------------
    | Top Summary Cards
    |--------------------------------------------------------------------------
    */

    .summary-card {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 9px;
        min-height: 130px;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.14);
        transition: transform 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-card .card-body {
        position: relative;
        z-index: 2;
        padding: 20px;
    }

    .summary-card .summary-number {
        font-size: 32px;
        line-height: 1.15;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .summary-card .summary-label {
        font-size: 16px;
        font-weight: 500;
    }

    .summary-card .summary-icon {
        position: absolute;
        right: 18px;
        bottom: 10px;
        font-size: 68px;
        opacity: 0.18;
        z-index: 1;
    }

    .summary-card-blue {
        background: linear-gradient(
            135deg,
            #17a2b8,
            #138496
        );
    }

    .summary-card-green {
        background: linear-gradient(
            135deg,
            #28a745,
            #1e7e34
        );
    }

    .summary-card-yellow {
        color: #212529;
        background: linear-gradient(
            135deg,
            #ffc107,
            #e0a800
        );
    }

    .summary-card-red {
        background: linear-gradient(
            135deg,
            #dc3545,
            #bd2130
        );
    }

    .summary-money {
        font-size: 28px !important;
        word-break: break-word;
    }


    /*
    |--------------------------------------------------------------------------
    | Chart Cards
    |--------------------------------------------------------------------------
    */

    .chart-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.10);
    }

    .chart-card .card-header {
        background: #ffffff;
        padding: 14px 18px;
    }

    .chart-card .card-title {
        font-size: 17px;
        font-weight: 600;
    }

    .chart-card .card-body {
        padding: 16px;
        background: #ffffff;
    }

    .chart-wrapper {
        position: relative;
        height: 315px;
        width: 100%;
    }

    .chart-loading-message {
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 1;
        color: #6c757d;
        transform: translate(-50%, -50%);
    }


    /*
    |--------------------------------------------------------------------------
    | Compact Analysis Section
    |--------------------------------------------------------------------------
    */

    .analysis-card {
        border: 0;
        border-radius: 8px;
        min-height: 116px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.09);
        background: #ffffff;
    }

    .analysis-card .card-body {
        padding: 15px;
    }

    .analysis-card-inner {
        display: flex;
        align-items: center;
    }

    .analysis-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 55px;
        width: 55px;
        height: 55px;
        color: #ffffff;
        border-radius: 8px;
        font-size: 24px;
        margin-right: 13px;
    }

    .analysis-content {
        flex: 1;
        min-width: 0;
    }

    .analysis-title {
        color: #59636e;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .analysis-count {
        font-size: 23px;
        line-height: 1.1;
        font-weight: 700;
        color: #222222;
    }

    .analysis-value {
        color: #6c757d;
        font-size: 12px;
        margin-top: 5px;
        word-break: break-word;
    }


    /*
    |--------------------------------------------------------------------------
    | Report Table
    |--------------------------------------------------------------------------
    */

    .report-card {
        border-radius: 8px;
        overflow: hidden;
    }

    .freight-report-table-wrapper {
        overflow: auto;
        max-height: 650px;
    }

    .freight-report-table {
        min-width: 1250px;
        font-size: 13px;
        margin-bottom: 0;
    }

    .freight-report-table th,
    .freight-report-table td {
        padding: 9px 11px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .freight-report-table .status-column {
        position: sticky;
        left: 0;
        z-index: 3;
        min-width: 230px;
        font-weight: 700;
        background: #f4f7fa;
    }

    .count-heading th,
    .value-heading th {
        color: #0070c0;
        background: #fce4d6;
        font-weight: 700;
    }

    .count-heading .status-column,
    .value-heading .status-column {
        z-index: 4;
        background: #fce4d6;
    }

    .ageing-column {
        min-width: 115px;
        text-align: center;
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
        color: #d50000 !important;
    }

    .report-divider td {
        height: 18px;
        padding: 0;
        background: #edf1f5;
        border-left: 0;
        border-right: 0;
    }

    code {
        padding: 2px 5px;
        color: #c7254e;
        background: #f9f2f4;
        border-radius: 3px;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive Design
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {

        .summary-card {
            min-height: 115px;
        }

        .summary-card .summary-number {
            font-size: 25px;
        }

        .summary-money {
            font-size: 20px !important;
        }

        .summary-card .summary-icon {
            font-size: 55px;
        }

        .chart-wrapper {
            height: 280px;
        }

        .analysis-card {
            min-height: auto;
        }

    }

</style>


<div class="content-wrapper freight-dashboard-page">

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

                    <div class="dashboard-subtitle">

                        Freight invoice count, value and ageing analysis

                    </div>

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
                 SESSION MESSAGES
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
                        Please correct the following errors:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- =================================================
                 VENDOR INFORMATION
            ================================================== --}}
            @if(
                isset($canViewAllVendors) &&
                !$canViewAllVendors
            )

                <div class="callout callout-info">

                    <h5 class="mb-1">

                        <i class="fas fa-user-tag mr-1"></i>

                        Vendor-specific report

                    </h5>

                    <p class="mb-0">

                        Showing data only for:

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
                 FILTERS
            ================================================== --}}
            <div class="card filter-card">

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

                        <div class="row align-items-end">


                            {{-- Mode --}}
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Mode
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
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Vendor
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
                                                    !empty($vendor->vendor_name) &&
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
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">

                                <label>
                                    Plant
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
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">

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
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">

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


                            {{-- Buttons --}}
                            <div class="col-xl-1 col-lg-3 col-md-4 col-sm-6 mb-3">

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
                                    class="btn btn-secondary btn-sm"
                                >

                                    <i class="fas fa-undo mr-1"></i>

                                    Reset Filters

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            {{-- =================================================
                 TOP SUMMARY CARDS
            ================================================== --}}
            <div class="row">


                {{-- Total Shipments --}}
                <div class="col-xl-3 col-md-6">

                    <div class="card summary-card summary-card-blue">

                        <div class="card-body">

                            <div class="summary-number">

                                {{ number_format(
                                    $report['total_count'] ?? 0
                                ) }}

                            </div>

                            <div class="summary-label">

                                Total Shipments

                            </div>

                        </div>

                        <i class="fas fa-truck summary-icon"></i>

                    </div>

                </div>


                {{-- Total Freight Value --}}
                <div class="col-xl-3 col-md-6">

                    <div class="card summary-card summary-card-green">

                        <div class="card-body">

                            <div class="summary-number summary-money">

                                ₹{{ number_format(
                                    $report['total_value'] ?? 0,
                                    2
                                ) }}

                            </div>

                            <div class="summary-label">

                                Total Freight Value

                            </div>

                        </div>

                        <i class="fas fa-rupee-sign summary-icon"></i>

                    </div>

                </div>


                {{-- Mode Count --}}
                <div class="col-xl-3 col-md-6">

                    <div class="card summary-card summary-card-yellow">

                        <div class="card-body">

                            <div class="summary-number">

                                {{ number_format(
                                    $report['mode_count'] ?? 0
                                ) }}

                            </div>

                            <div class="summary-label">

                                Shipments with Mode

                            </div>

                        </div>

                        <i class="fas fa-route summary-icon"></i>

                    </div>

                </div>


                {{-- Pending --}}
                <div class="col-xl-3 col-md-6">

                    <div class="card summary-card summary-card-red">

                        <div class="card-body">

                            <div class="summary-number">

                                {{ number_format($pendingCount) }}

                            </div>

                            <div class="summary-label">

                                Pending Invoices

                            </div>

                        </div>

                        <i class="fas fa-clock summary-icon"></i>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CHARTS
            ================================================== --}}
            <div class="row">


                {{-- Count Bar Chart --}}
                <div class="col-xl-4 col-lg-6 col-md-12">

                    <div class="card card-outline card-primary chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-bar mr-1"></i>

                                Workflow Count

                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">

                                <div
                                    class="chart-loading-message"
                                    id="workflowCountLoading"
                                >
                                    Loading chart...
                                </div>

                                <canvas id="workflowCountChart"></canvas>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Value Bar Chart --}}
                <div class="col-xl-4 col-lg-6 col-md-12">

                    <div class="card card-outline card-success chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-bar mr-1"></i>

                                Workflow Value

                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">

                                <div
                                    class="chart-loading-message"
                                    id="workflowValueLoading"
                                >
                                    Loading chart...
                                </div>

                                <canvas id="workflowValueChart"></canvas>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Pending Pie Chart --}}
                <div class="col-xl-4 col-lg-12 col-md-12">

                    <div class="card card-outline card-warning chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-pie mr-1"></i>

                                Pending Ageing

                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">

                                <div
                                    class="chart-loading-message"
                                    id="pendingAgeingLoading"
                                >
                                    Loading chart...
                                </div>

                                <canvas id="pendingAgeingChart"></canvas>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 COMPACT REPORT ANALYSIS
            ================================================== --}}
            <div class="card card-outline card-info">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-chart-line mr-1"></i>

                        Report Analysis

                    </h3>

                </div>


                <div class="card-body">

                    <div class="row">


                        {{-- Received --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="card analysis-card mb-0">

                                <div class="card-body">

                                    <div class="analysis-card-inner">

                                        <div class="analysis-icon bg-primary">

                                            <i class="fas fa-file-invoice"></i>

                                        </div>

                                        <div class="analysis-content">

                                            <div class="analysis-title">

                                                Invoice Received

                                            </div>

                                            <div class="analysis-count">

                                                {{ number_format(
                                                    $receivedCount
                                                ) }}

                                            </div>

                                            <div class="analysis-value">

                                                ₹{{ number_format(
                                                    $receivedValue,
                                                    2
                                                ) }}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Validated --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="card analysis-card mb-0">

                                <div class="card-body">

                                    <div class="analysis-card-inner">

                                        <div class="analysis-icon bg-success">

                                            <i class="fas fa-check-circle"></i>

                                        </div>

                                        <div class="analysis-content">

                                            <div class="analysis-title">

                                                Invoice Validated

                                            </div>

                                            <div class="analysis-count">

                                                {{ number_format(
                                                    $validatedCount
                                                ) }}

                                            </div>

                                            <div class="analysis-value">

                                                ₹{{ number_format(
                                                    $validatedValue,
                                                    2
                                                ) }}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Returned --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="card analysis-card mb-0">

                                <div class="card-body">

                                    <div class="analysis-card-inner">

                                        <div class="analysis-icon bg-danger">

                                            <i class="fas fa-undo-alt"></i>

                                        </div>

                                        <div class="analysis-content">

                                            <div class="analysis-title">

                                                Invoice Returned

                                            </div>

                                            <div class="analysis-count">

                                                {{ number_format(
                                                    $returnedCount
                                                ) }}

                                            </div>

                                            <div class="analysis-value">

                                                ₹{{ number_format(
                                                    $returnedValue,
                                                    2
                                                ) }}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Pending --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="card analysis-card mb-0">

                                <div class="card-body">

                                    <div class="analysis-card-inner">

                                        <div class="analysis-icon bg-warning">

                                            <i class="fas fa-clock"></i>

                                        </div>

                                        <div class="analysis-content">

                                            <div class="analysis-title">

                                                Invoice Pending

                                            </div>

                                            <div class="analysis-count">

                                                {{ number_format(
                                                    $pendingCount
                                                ) }}

                                            </div>

                                            <div class="analysis-value">

                                                ₹{{ number_format(
                                                    $pendingValue,
                                                    2
                                                ) }}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Paid --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="card analysis-card mb-0">

                                <div class="card-body">

                                    <div class="analysis-card-inner">

                                        <div class="analysis-icon bg-secondary">

                                            <i class="fas fa-money-check-alt"></i>

                                        </div>

                                        <div class="analysis-content">

                                            <div class="analysis-title">

                                                Invoice Paid

                                            </div>

                                            <div class="analysis-count">

                                                {{ number_format(
                                                    $paidCount
                                                ) }}

                                            </div>

                                            <div class="analysis-value">

                                                ₹{{ number_format(
                                                    $paidValue,
                                                    2
                                                ) }}

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 AGEING TABLE
            ================================================== --}}
            <div class="card card-outline card-secondary report-card">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-table mr-1"></i>

                        Freight Bill Ageing Report

                    </h3>

                </div>


                <div class="card-body p-0">

                    <div class="freight-report-table-wrapper">

                        <table
                            class="
                                table
                                table-bordered
                                table-hover
                                table-sm
                                freight-report-table
                            "
                        >

                            <thead>

                                <tr class="count-heading">

                                    <th class="status-column">

                                        Count

                                    </th>

                                    @foreach(
                                        $report['buckets'] ?? []
                                        as $bucketKey => $bucketLabel
                                    )

                                        <th
                                            class="
                                                ageing-column
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


                                <tr class="report-divider">

                                    <td colspan="9"></td>

                                </tr>


                                {{-- Value Header --}}
                                <tr class="value-heading">

                                    <th class="status-column">

                                        Value

                                    </th>

                                    @foreach(
                                        $report['buckets'] ?? []
                                        as $bucketKey => $bucketLabel
                                    )

                                        <th
                                            class="
                                                ageing-column
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


                <div class="card-footer">

                    <div class="row">

                        <div class="col-lg-9">

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

                        <div class="col-lg-3 text-lg-right">

                            <small class="text-muted">

                                Records beyond 180 days are excluded
                                from the ageing grid.

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>


{{-- =============================================================
     ADMINLTE LOCAL CHART.JS
============================================================== --}}

<script src="{{ asset('backend/assets/plugins/chart.js/chart.min.js') }}"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Check Chart.js
    |--------------------------------------------------------------------------
    */

    if (typeof Chart === 'undefined') {

        console.error(
            'Chart.js could not be loaded from: {{ $chartJsAsset }}'
        );

        document.querySelectorAll(
            '.chart-loading-message'
        ).forEach(function (messageBox) {

            messageBox.innerHTML =
                '<span class="text-danger">' +
                'Chart.js asset was not loaded.' +
                '</span>';

        });

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Loading Messages
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(
        '.chart-loading-message'
    ).forEach(function (messageBox) {

        messageBox.style.display = 'none';

    });


    /*
    |--------------------------------------------------------------------------
    | Chart Data
    |--------------------------------------------------------------------------
    */

    var workflowLabels = [
        'Received',
        'Validated',
        'Returned',
        'Pending',
        'Paid'
    ];


    var workflowCountData = [
        {{ (int) $receivedCount }},
        {{ (int) $validatedCount }},
        {{ (int) $returnedCount }},
        {{ (int) $pendingCount }},
        {{ (int) $paidCount }}
    ];


    var workflowValueData = [
        {{ (float) $receivedValue }},
        {{ (float) $validatedValue }},
        {{ (float) $returnedValue }},
        {{ (float) $pendingValue }},
        {{ (float) $paidValue }}
    ];


    var barBackgroundColors = [
        'rgba(0, 123, 255, 0.78)',
        'rgba(40, 167, 69, 0.78)',
        'rgba(220, 53, 69, 0.78)',
        'rgba(255, 193, 7, 0.82)',
        'rgba(108, 117, 125, 0.78)'
    ];


    var barBorderColors = [
        'rgba(0, 123, 255, 1)',
        'rgba(40, 167, 69, 1)',
        'rgba(220, 53, 69, 1)',
        'rgba(255, 193, 7, 1)',
        'rgba(108, 117, 125, 1)'
    ];


    /*
    |--------------------------------------------------------------------------
    | Workflow Count Bar Chart
    |--------------------------------------------------------------------------
    */

    var countCanvas = document.getElementById(
        'workflowCountChart'
    );

    if (countCanvas) {

        new Chart(
            countCanvas.getContext('2d'),
            {
                type: 'bar',

                data: {
                    labels: workflowLabels,

                    datasets: [
                        {
                            label: 'Invoice Count',

                            data: workflowCountData,

                            backgroundColor:
                                barBackgroundColors,

                            borderColor:
                                barBorderColors,

                            borderWidth: 1
                        }
                    ]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    legend: {
                        display: false
                    },

                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {

                                return 'Count: ' +
                                    Number(
                                        tooltipItem.yLabel
                                    ).toLocaleString('en-IN');

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

                                    callback: function (value) {

                                        return Number(
                                            value
                                        ).toLocaleString('en-IN');

                                    }
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
    | Workflow Value Bar Chart
    |--------------------------------------------------------------------------
    */

    var valueCanvas = document.getElementById(
        'workflowValueChart'
    );

    if (valueCanvas) {

        new Chart(
            valueCanvas.getContext('2d'),
            {
                type: 'bar',

                data: {
                    labels: workflowLabels,

                    datasets: [
                        {
                            label: 'Freight Value',

                            data: workflowValueData,

                            backgroundColor:
                                barBackgroundColors,

                            borderColor:
                                barBorderColors,

                            borderWidth: 1
                        }
                    ]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    legend: {
                        display: false
                    },

                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {

                                var amount = Number(
                                    tooltipItem.yLabel
                                );

                                return 'Value: ₹' +
                                    amount.toLocaleString(
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

                                    callback: function (value) {

                                        if (value >= 10000000) {
                                            return '₹' +
                                                (
                                                    value /
                                                    10000000
                                                ).toFixed(1) +
                                                ' Cr';
                                        }

                                        if (value >= 100000) {
                                            return '₹' +
                                                (
                                                    value /
                                                    100000
                                                ).toFixed(1) +
                                                ' L';
                                        }

                                        if (value >= 1000) {
                                            return '₹' +
                                                (
                                                    value /
                                                    1000
                                                ).toFixed(1) +
                                                ' K';
                                        }

                                        return '₹' + value;

                                    }
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
    | Pending Ageing Pie Chart
    |--------------------------------------------------------------------------
    */

    var pendingLabels = @json(
        array_values(
            $report['buckets'] ?? []
        )
    );


    var pendingData = @json(
        array_values(
            $report['count_matrix']['pending'] ?? []
        )
    );


    var pendingCanvas = document.getElementById(
        'pendingAgeingChart'
    );

    if (pendingCanvas) {

        new Chart(
            pendingCanvas.getContext('2d'),
            {
                type: 'doughnut',

                data: {
                    labels: pendingLabels,

                    datasets: [
                        {
                            data: pendingData,

                            backgroundColor: [
                                '#28a745',
                                '#20c997',
                                '#17a2b8',
                                '#ffc107',
                                '#fd7e14',
                                '#dc3545',
                                '#bd2130',
                                '#721c24'
                            ],

                            borderColor: '#ffffff',

                            borderWidth: 2
                        }
                    ]
                },

                options: {
                    responsive: true,

                    maintainAspectRatio: false,

                    cutoutPercentage: 56,

                    legend: {
                        position: 'bottom',

                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            fontSize: 11
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
                                    chartData.datasets[0]
                                        .data[itemIndex] || 0
                                );

                                var total =
                                    chartData.datasets[0]
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