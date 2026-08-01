@extends('admin.admin')

@section('bodycontent')

@php
    /*
    |--------------------------------------------------------------------------
    | Workflow Count Totals
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


    /*
    |--------------------------------------------------------------------------
    | Workflow Value Totals
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Report Row Labels
    |--------------------------------------------------------------------------
    */

    $statusRows = [
        'received'  => 'Invoice Received',
        'validated' => 'Invoice Validated',
        'returned'  => 'Invoice Returned',
        'pending'   => 'Invoices Pending',
        'paid'      => 'Invoices Paid',
    ];
@endphp


<style>
    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

    .freight-dashboard-page {
        padding-bottom: 30px;
    }

    .dashboard-page-title {
        margin: 0;
        color: #17263c;
        font-size: 28px;
        font-weight: 700;
    }

    .dashboard-page-subtitle {
        margin-top: 4px;
        color: #7a8492;
        font-size: 14px;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter Card
    |--------------------------------------------------------------------------
    */

    .freight-filter-card {
        overflow: hidden;
        border: 1px solid #e4e9ef;
        border-top: 3px solid #007bff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 45, 62, 0.07);
    }

    .freight-filter-card .card-header {
        padding: 14px 18px;
        border-bottom: 1px solid #edf0f4;
        background: #ffffff;
    }

    .freight-filter-card .card-title {
        float: none;
        margin: 0;
        color: #25364b;
        font-size: 17px;
        font-weight: 700;
    }

    .freight-filter-card label {
        margin-bottom: 6px;
        color: #455365;
        font-size: 13px;
        font-weight: 700;
    }


    /*
    |--------------------------------------------------------------------------
    | Modern KPI Cards
    |--------------------------------------------------------------------------
    */

    .modern-stat-card {
        position: relative;
        min-height: 148px;
        overflow: hidden;
        border: 1px solid #e5eaf0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 5px 18px rgba(31, 45, 61, 0.08);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .modern-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 11px 28px rgba(31, 45, 61, 0.14);
    }

    .modern-stat-card::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        content: "";
    }

    .stat-blue::before {
        background: #007bff;
    }

    .stat-green::before {
        background: #28a745;
    }

    .stat-orange::before {
        background: #f39c12;
    }

    .stat-red::before {
        background: #dc3545;
    }

    .stat-card-content {
        position: relative;
        z-index: 2;
        padding: 22px 84px 17px 23px;
    }

    .stat-card-label {
        margin-bottom: 7px;
        color: #7a8492;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.45px;
        text-transform: uppercase;
    }

    .stat-card-value {
        overflow: hidden;
        margin-bottom: 15px;
        color: #17263c;
        font-size: 31px;
        font-weight: 750;
        line-height: 1.18;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .stat-card-money {
        font-size: 25px;
    }

    .stat-card-footer {
        padding-top: 11px;
        border-top: 1px solid #edf0f4;
        color: #8c96a4;
        font-size: 12px;
    }

    .stat-card-icon {
        position: absolute;
        top: 28px;
        right: 20px;
        display: flex;
        width: 58px;
        height: 58px;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        font-size: 25px;
    }

    .stat-blue .stat-card-icon {
        color: #007bff;
        background: rgba(0, 123, 255, 0.10);
    }

    .stat-green .stat-card-icon {
        color: #28a745;
        background: rgba(40, 167, 69, 0.10);
    }

    .stat-orange .stat-card-icon {
        color: #e39100;
        background: rgba(243, 156, 18, 0.13);
    }

    .stat-red .stat-card-icon {
        color: #dc3545;
        background: rgba(220, 53, 69, 0.10);
    }


    /*
    |--------------------------------------------------------------------------
    | Chart Cards
    |--------------------------------------------------------------------------
    */

    .modern-chart-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid #e4e9ef;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 5px 18px rgba(31, 45, 61, 0.08);
    }

    .modern-chart-card .card-header {
        min-height: 78px;
        padding: 16px 19px;
        border-bottom: 1px solid #edf0f4;
        background: #ffffff;
    }

    .modern-chart-card .card-title {
        display: flex;
        float: none;
        align-items: center;
        margin: 0;
        color: #25364b;
        font-size: 16px;
        font-weight: 700;
    }

    .chart-title-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        border-radius: 10px;
    }

    .bg-primary-soft {
        color: #007bff;
        background: rgba(0, 123, 255, 0.10);
    }

    .bg-success-soft {
        color: #28a745;
        background: rgba(40, 167, 69, 0.10);
    }

    .bg-warning-soft {
        color: #d88a00;
        background: rgba(243, 156, 18, 0.14);
    }

    .chart-subtitle {
        display: block;
        margin-top: 4px;
        margin-left: 46px;
        color: #929ba8;
        font-size: 12px;
    }

    .modern-chart-card .card-body {
        padding: 18px;
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 315px;
    }

    .count-chart-card {
        border-top: 3px solid #007bff;
    }

    .value-chart-card {
        border-top: 3px solid #28a745;
    }

    .ageing-chart-card {
        border-top: 3px solid #f39c12;
    }


    /*
    |--------------------------------------------------------------------------
    | Compact Report Analysis
    |--------------------------------------------------------------------------
    */

    .report-analysis-card {
        overflow: hidden;
        border: 1px solid #e4e9ef;
        border-top: 3px solid #17a2b8;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(31, 45, 61, 0.08);
    }

    .report-analysis-card .card-header {
        padding: 14px 18px;
        background: #ffffff;
    }

    .report-analysis-card .card-title {
        float: none;
        margin: 0;
        color: #25364b;
        font-size: 17px;
        font-weight: 700;
    }

    .analysis-item {
        position: relative;
        min-height: 105px;
        overflow: hidden;
        border: 1px solid #e7ebf0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 3px 10px rgba(31, 45, 61, 0.06);
        transition: transform 0.2s ease;
    }

    .analysis-item:hover {
        transform: translateY(-2px);
    }

    .analysis-item-body {
        display: flex;
        align-items: center;
        padding: 15px;
    }

    .analysis-icon {
        display: flex;
        flex: 0 0 50px;
        width: 50px;
        height: 50px;
        align-items: center;
        justify-content: center;
        margin-right: 13px;
        border-radius: 12px;
        color: #ffffff;
        font-size: 21px;
    }

    .analysis-content {
        flex: 1;
        min-width: 0;
    }

    .analysis-label {
        overflow: hidden;
        margin-bottom: 3px;
        color: #687485;
        font-size: 12px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .analysis-count {
        color: #1e2d42;
        font-size: 22px;
        font-weight: 750;
        line-height: 1.15;
    }

    .analysis-value {
        overflow: hidden;
        margin-top: 5px;
        color: #8c96a4;
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Ageing Table
    |--------------------------------------------------------------------------
    */

    .freight-report-card {
        overflow: hidden;
        border: 1px solid #e4e9ef;
        border-top: 3px solid #6c757d;
        border-radius: 13px;
        box-shadow: 0 5px 18px rgba(31, 45, 61, 0.08);
    }

    .freight-report-card .card-header {
        padding: 14px 18px;
        background: #ffffff;
    }

    .freight-report-card .card-title {
        float: none;
        margin: 0;
        color: #25364b;
        font-size: 17px;
        font-weight: 700;
    }

    .freight-report-table-wrapper {
        max-height: 650px;
        overflow: auto;
    }

    .freight-report-table {
        min-width: 1250px;
        margin-bottom: 0;
        font-size: 13px;
    }

    .freight-report-table th,
    .freight-report-table td {
        padding: 10px 11px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .freight-report-table .status-column {
        position: sticky;
        left: 0;
        z-index: 3;
        min-width: 230px;
        background: #f5f8fb;
        font-weight: 700;
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
        border-right: 0;
        border-left: 0;
        background: #edf1f5;
    }

    code {
        padding: 2px 5px;
        border-radius: 3px;
        color: #c7254e;
        background: #f9f2f4;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767px) {
        .dashboard-page-title {
            font-size: 22px;
        }

        .modern-stat-card {
            min-height: 132px;
        }

        .stat-card-content {
            padding-right: 70px;
        }

        .stat-card-value {
            font-size: 25px;
        }

        .stat-card-money {
            font-size: 20px;
        }

        .stat-card-icon {
            right: 14px;
            width: 48px;
            height: 48px;
            font-size: 20px;
        }

        .chart-wrapper {
            height: 280px;
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

                    <h1 class="dashboard-page-title">
                        {{ $pagetitle ?? 'Freight Bill Processing Dashboard' }}
                    </h1>

                    <div class="dashboard-page-subtitle">
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


    <section class="content">

        <div class="container-fluid">

            {{-- =================================================
                 MESSAGES
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

                        Showing records only for:

                        <strong>
                            {{ $loggedInVendorName
                                ?? $loggedInVendorCode
                                ?? 'Logged-in Vendor' }}
                        </strong>

                        @if(!empty($loggedInVendorCode))

                            <span class="ml-2">

                                Vendor Code:

                                <strong>{{ $loggedInVendorCode }}</strong>

                            </span>

                        @endif

                    </p>

                </div>

            @endif


            {{-- =================================================
                 FILTERS
            ================================================== --}}
            <div class="card freight-filter-card">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-filter mr-1"></i>

                        Report Filters

                    </h3>

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

                                <label>Mode</label>

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

                                <label>Vendor</label>

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

                                <label>Plant</label>

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

                                <label>From Date</label>

                                <input
                                    type="date"
                                    name="from_date"
                                    class="form-control"
                                    value="{{ request('from_date') }}"
                                >

                            </div>


                            {{-- To Date --}}
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">

                                <label>To Date</label>

                                <input
                                    type="date"
                                    name="to_date"
                                    class="form-control"
                                    value="{{ request('to_date') }}"
                                >

                            </div>


                            {{-- Search --}}
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


                        <a
                            href="{{ route(
                                'admin.freight-bill-processing.index'
                            ) }}"
                            class="btn btn-secondary btn-sm"
                        >
                            <i class="fas fa-undo mr-1"></i>
                            Reset Filters
                        </a>

                    </form>

                </div>

            </div>


            {{-- =================================================
                 MODERN SUMMARY CARDS
            ================================================== --}}
            <div class="row">

                {{-- Total Shipments --}}
                <div class="col-xl-3 col-md-6 mb-3">

                    <div class="modern-stat-card stat-blue">

                        <div class="stat-card-content">

                            <div class="stat-card-label">
                                Total Shipments
                            </div>

                            <div class="stat-card-value">
                                {{ number_format(
                                    $report['total_count'] ?? 0
                                ) }}
                            </div>

                            <div class="stat-card-footer">

                                <i class="fas fa-boxes mr-1"></i>

                                All freight records

                            </div>

                        </div>

                        <div class="stat-card-icon">
                            <i class="fas fa-truck-loading"></i>
                        </div>

                    </div>

                </div>


                {{-- Total Freight Value --}}
                <div class="col-xl-3 col-md-6 mb-3">

                    <div class="modern-stat-card stat-green">

                        <div class="stat-card-content">

                            <div class="stat-card-label">
                                Total Freight Value
                            </div>

                            <div class="stat-card-value stat-card-money">

                                ₹{{ number_format(
                                    $report['total_value'] ?? 0,
                                    2
                                ) }}

                            </div>

                            <div class="stat-card-footer">

                                <i class="fas fa-chart-line mr-1"></i>

                                Total processed value

                            </div>

                        </div>

                        <div class="stat-card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>

                    </div>

                </div>


                {{-- Shipment Mode --}}
                <div class="col-xl-3 col-md-6 mb-3">

                    <div class="modern-stat-card stat-orange">

                        <div class="stat-card-content">

                            <div class="stat-card-label">
                                Shipments With Mode
                            </div>

                            <div class="stat-card-value">

                                {{ number_format(
                                    $report['mode_count'] ?? 0
                                ) }}

                            </div>

                            <div class="stat-card-footer">

                                <i class="fas fa-route mr-1"></i>

                                Mode-tagged shipments

                            </div>

                        </div>

                        <div class="stat-card-icon">
                            <i class="fas fa-route"></i>
                        </div>

                    </div>

                </div>


                {{-- Pending --}}
                <div class="col-xl-3 col-md-6 mb-3">

                    <div class="modern-stat-card stat-red">

                        <div class="stat-card-content">

                            <div class="stat-card-label">
                                Pending Invoices
                            </div>

                            <div class="stat-card-value">
                                {{ number_format($pendingCount) }}
                            </div>

                            <div class="stat-card-footer">

                                <i class="fas fa-clock mr-1"></i>

                                Awaiting invoice action

                            </div>

                        </div>

                        <div class="stat-card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CHARTS
            ================================================== --}}
            <div class="row">

                {{-- Count Chart --}}
                <div class="col-xl-4 col-lg-6 col-md-12 mb-3">

                    <div class="card modern-chart-card count-chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <span class="chart-title-icon bg-primary-soft">
                                    <i class="fas fa-chart-bar"></i>
                                </span>

                                Workflow Count

                            </h3>

                            <small class="chart-subtitle">
                                Stage-wise invoice quantity
                            </small>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">
                                <canvas id="workflowCountChart"></canvas>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Value Chart --}}
                <div class="col-xl-4 col-lg-6 col-md-12 mb-3">

                    <div class="card modern-chart-card value-chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <span class="chart-title-icon bg-success-soft">
                                    <i class="fas fa-chart-bar"></i>
                                </span>

                                Workflow Value

                            </h3>

                            <small class="chart-subtitle">
                                Stage-wise freight amount
                            </small>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">
                                <canvas id="workflowValueChart"></canvas>
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Pending Ageing --}}
                <div class="col-xl-4 col-lg-12 col-md-12 mb-3">

                    <div class="card modern-chart-card ageing-chart-card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <span class="chart-title-icon bg-warning-soft">
                                    <i class="fas fa-chart-pie"></i>
                                </span>

                                Pending Ageing

                            </h3>

                            <small class="chart-subtitle">
                                Pending invoices by ageing bucket
                            </small>

                        </div>

                        <div class="card-body">

                            <div class="chart-wrapper">
                                <canvas id="pendingAgeingChart"></canvas>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 REPORT ANALYSIS
            ================================================== --}}
            <div class="card report-analysis-card">

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

                            <div class="analysis-item">

                                <div class="analysis-item-body">

                                    <div class="analysis-icon bg-primary">

                                        <i class="fas fa-file-invoice"></i>

                                    </div>

                                    <div class="analysis-content">

                                        <div class="analysis-label">
                                            Invoice Received
                                        </div>

                                        <div class="analysis-count">
                                            {{ number_format($receivedCount) }}
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


                        {{-- Validated --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="analysis-item">

                                <div class="analysis-item-body">

                                    <div class="analysis-icon bg-success">

                                        <i class="fas fa-check-circle"></i>

                                    </div>

                                    <div class="analysis-content">

                                        <div class="analysis-label">
                                            Invoice Validated
                                        </div>

                                        <div class="analysis-count">
                                            {{ number_format($validatedCount) }}
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


                        {{-- Returned --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="analysis-item">

                                <div class="analysis-item-body">

                                    <div class="analysis-icon bg-danger">

                                        <i class="fas fa-undo-alt"></i>

                                    </div>

                                    <div class="analysis-content">

                                        <div class="analysis-label">
                                            Invoice Returned
                                        </div>

                                        <div class="analysis-count">
                                            {{ number_format($returnedCount) }}
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


                        {{-- Pending --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="analysis-item">

                                <div class="analysis-item-body">

                                    <div class="analysis-icon bg-warning">

                                        <i class="fas fa-clock"></i>

                                    </div>

                                    <div class="analysis-content">

                                        <div class="analysis-label">
                                            Invoice Pending
                                        </div>

                                        <div class="analysis-count">
                                            {{ number_format($pendingCount) }}
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


                        {{-- Paid --}}
                        <div class="col-xl col-lg-4 col-md-6 mb-3">

                            <div class="analysis-item">

                                <div class="analysis-item-body">

                                    <div class="analysis-icon bg-secondary">

                                        <i class="fas fa-money-check-alt"></i>

                                    </div>

                                    <div class="analysis-content">

                                        <div class="analysis-label">
                                            Invoice Paid
                                        </div>

                                        <div class="analysis-count">
                                            {{ number_format($paidCount) }}
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


            {{-- =================================================
                 AGEING TABLE
            ================================================== --}}
            <div class="card freight-report-card">

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

                                {{-- Count --}}
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


                                {{-- Value Heading --}}
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


                                {{-- Value --}}
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

                    <div class="mt-2 text-muted">

                        Records older than 180 days are not displayed
                        inside the ageing matrix.

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>


{{-- =============================================================
     ADMINLTE LOCAL CHART.JS
============================================================== --}}
<script src="{{ asset('backend/assets/plugins/chart.js/chart.js') }}"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Confirm Chart.js Loaded
    |--------------------------------------------------------------------------
    */

    if (typeof Chart === 'undefined') {

        console.error(
            'Chart.js was not loaded from: ' +
            "{{ asset('backend/assets/plugins/chart.js/chart.js') }}"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Chart Data
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
    | Workflow Count Chart
    |--------------------------------------------------------------------------
    */

    var countCanvas = document.getElementById(
        'workflowCountChart'
    );

    if (countCanvas) {

        new Chart(countCanvas.getContext('2d'), {

            type: 'bar',

            data: {

                labels: workflowLabels,

                datasets: [
                    {
                        label: 'Invoice Count',
                        data: workflowCountData,
                        backgroundColor: barBackgroundColors,
                        borderColor: barBorderColors,
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

                                    return Number(value)
                                        .toLocaleString('en-IN');
                                }
                            }
                        }
                    ]
                }
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Workflow Value Chart
    |--------------------------------------------------------------------------
    */

    var valueCanvas = document.getElementById(
        'workflowValueChart'
    );

    if (valueCanvas) {

        new Chart(valueCanvas.getContext('2d'), {

            type: 'bar',

            data: {

                labels: workflowLabels,

                datasets: [
                    {
                        label: 'Freight Value',
                        data: workflowValueData,
                        backgroundColor: barBackgroundColors,
                        borderColor: barBorderColors,
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
                                                value / 10000000
                                            ).toFixed(1) +
                                            ' Cr';
                                    }

                                    if (value >= 100000) {

                                        return '₹' +
                                            (
                                                value / 100000
                                            ).toFixed(1) +
                                            ' L';
                                    }

                                    if (value >= 1000) {

                                        return '₹' +
                                            (
                                                value / 1000
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
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Pending Ageing Doughnut Chart
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

        new Chart(pendingCanvas.getContext('2d'), {

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
                cutoutPercentage: 58,

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
                            var itemIndex = tooltipItem.index;

                            var label =
                                chartData.labels[itemIndex];

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
                                            return Number(totalValue) +
                                                Number(currentValue);
                                        },
                                        0
                                    );

                            var percentage = total > 0
                                ? (
                                    value / total * 100
                                ).toFixed(1)
                                : 0;

                            return label +
                                ': ' +
                                value.toLocaleString('en-IN') +
                                ' (' +
                                percentage +
                                '%)';
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection