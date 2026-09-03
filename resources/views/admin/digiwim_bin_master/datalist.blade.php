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
.consign-data-table th,
.consign-data-table td {
    white-space: nowrap;
    vertical-align: middle;
}

.consign-data-table thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
}

.consign-data-table .table th,
.consign-data-table .table td {
    padding: 5px 10px;
}

.sticky-col-1 {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 99;
}

.sticky-col-2 {
    position: sticky;
    left: 100px;
    background: #fff;
    z-index: 99;
}

.sticky-col-3 {
    position: sticky;
    left: 200px;
    background: #fff;
    z-index: 99;
}

.table-container {
    max-height: 550px;
    overflow-y: auto;
    border: 1px solid #ccc;
}

#binMasterTable {
    border-collapse: collapse;
    width: 100%;
    min-width: 2100px;
}

#binMasterTable th {
    position: sticky;
    top: 0;
    z-index: 2;
}

#binMasterTable th.sticky-col-1,
#binMasterTable th.sticky-col-2,
#binMasterTable th.sticky-col-3 {
    z-index: 999;
}

.sort-heading {
    color: #0070c0 !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.sort-heading:hover {
    color: #004b87 !important;
    text-decoration: none !important;
}

.sort-heading .sort-icon {
    color: #888;
    font-size: 10px;
}

.sort-heading.active-sort .sort-icon {
    color: #0070c0;
}

.filter-box {
    background: #f8f9fa;
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 12px;
}

.filter-box label {
    font-size: 12px;
    margin-bottom: 3px;
}

.filter-box .form-control {
    height: 34px;
    font-size: 12px;
}
.pagination-area {
    margin-top: 15px;
}

.pagination-info {
    font-size: 12px;
    color: #666;
    padding-top: 8px;
}

.action-column {
    min-width: 90px;
    text-align: center;
}
@media (max-width: 768px) {

    .sticky-col-2 {
        left: 80px;
    }

}

</style>


<!-- Content Header -->
<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="m-0">
                    BIN Master Data
                </h1>

            </div>

            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">

                        <a href="/admin/dashboard">
                            Dashboard
                        </a>

                    </li>

                    <li class="breadcrumb-item active">
                        BIN Master
                    </li>

                </ol>

            </div>

        </div>

    </div>

</div>


<!-- Main Content -->
<div class="content">

<div class="container-fluid">


    <!-- Success / Error -->
    <div class="row">

        <div class="col-lg-12">

            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert">

                        &times;

                    </button>

                </div>

            @endif


            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    {{ session('error') }}

                    <button
                        type="button"
                        class="close"
                        data-dismiss="alert">

                        &times;

                    </button>

                </div>

            @endif

        </div>

    </div>


    <div class="row">

        <div class="col-md-12">

            <div class="card">


                <!-- Tabs -->
                <div class="card-header p-2">

                    <ul class="nav nav-pills">

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="{{ route('admin.digiwim.bin-master.index') }}">

                                XLS Upload

                            </a>

                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.digiwim.bin-master.manual-upload') }}">Manual Upload</a>
                        </li>
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.digiwim.bin-master.datalist') }}">BIN Master List</a>
                        </li>

                    </ul>

                </div>


                <div class="card-body">

                    <div class="tab-content">

                        <div class="active tab-pane"
                             id="activity">


                            <!-- Search / Filter -->
                            <form method="GET" action="{{ route('admin.digiwim.bin-master.datalist') }}" id="binFilterForm">
                                <div class="filter-box">
                                    <div class="row">
                                        <!-- Search -->
                                        <div class="col-md-3">

                                            <div class="form-group mb-2">

                                                <label>
                                                    Search
                                                </label>

                                                <input
                                                    type="text"
                                                    name="search"
                                                    value="{{ request('search') }}"
                                                    class="form-control"
                                                    placeholder="Plant / BIN / Type / Location">

                                            </div>

                                        </div>


                                        <!-- Plant -->
                                        <div class="col-md-2">

                                            <div class="form-group mb-2">

                                                <label>
                                                    Plant
                                                </label>

                                                <select
                                                    name="plant_code"
                                                    class="form-control">

                                                    <option value="">
                                                        All Plants
                                                    </option>


                                                    @foreach($plants as $plant)

                                                        <option
                                                            value="{{ $plant->plant_code }}"
                                                            {{ request('plant_code') == $plant->plant_code ? 'selected' : '' }}>

                                                            {{ $plant->plant_code }}
                                                            -
                                                            {{ $plant->plant_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>


                                        <!-- Status -->
                                        <div class="col-md-2">

                                            <div class="form-group mb-2">

                                                <label>
                                                    BIN Status
                                                </label>

                                                <select
                                                    name="bin_status"
                                                    class="form-control">

                                                    <option value="">
                                                        All
                                                    </option>

                                                    <option
                                                        value="Active"
                                                        {{ request('bin_status') == 'Active' ? 'selected' : '' }}>

                                                        Active

                                                    </option>

                                                    <option
                                                        value="Inactive"
                                                        {{ request('bin_status') == 'Inactive' ? 'selected' : '' }}>

                                                        Inactive

                                                    </option>

                                                </select>

                                            </div>

                                        </div>


                                        <!-- Per Page -->
                                        <div class="col-md-1">
                                            <div class="form-group mb-2">
                                                <label>Rows</label>
                                                <select name="per_page" id="per_page" class="form-control">
                                                    @foreach([10,25,50,100] as $pageSize)
                                                        <option value="{{ $pageSize }}" {{ $perPage == $pageSize ? 'selected' : '' }}>
                                                            {{ $pageSize }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <!-- Keep Current Sort -->
                                        <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                                        <input type="hidden" name="sort_direction"value="{{ $sortDirection }}">


                                        <!-- Buttons -->
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit"class="btn btn-primary btn-sm"><i class="fas fa-search"></i>Search</button>
                                                    <a href="{{ route('admin.digiwim.bin-master.datalist') }}" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i>Reset</a>
                                                    <a href="{{ route('admin.digiwim.bin-master.export',request()->except(['page', 'per_page'])) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i>Download XLS </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>


                            <!-- Table -->
                            <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
                                <div class="excel-wrapper">
                                    <table class="table table-bordered excel-table" id="binMasterTable"> <thead>
                                        <tr>
                                            <th class="sticky-col-1" style="background:#fce4d6;color:#0070c0;z-index:999;">
                                                  <a
                                                    class="sort-heading {{ $sortBy == 'plant_code' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'plant_code',

                                                                'sort_direction' =>
                                                                    $sortBy == 'plant_code' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    Plant Code

                                                    <span class="sort-icon">

                                                        @if($sortBy == 'plant_code')

                                                            @if($sortDirection == 'asc')
                                                                ▲
                                                            @else
                                                                ▼
                                                            @endif

                                                        @else
                                                            ⇅
                                                        @endif

                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Plant Name --}}
                                            <th
                                                class="sticky-col-2"
                                                style="background:#fce4d6;color:#0070c0;z-index:999;">

                                                <a class="sort-heading {{ $sortBy == 'plant_name' ? 'active-sort' : '' }}" href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'plant_name',

                                                                'sort_direction' =>
                                                                    $sortBy == 'plant_name' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">Plant Name
                                                    <span class="sort-icon">
                                                        @if($sortBy == 'plant_name')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif

                                                    </span>

                                                </a>

                                            </th>


                                            {{-- BIN No --}}
                                            <th class="sticky-col-3" style="background:#fce4d6;color:#0070c0;z-index:999;">

                                                <a class="sort-heading {{ $sortBy == 'bin_no' ? 'active-sort' : '' }}" href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_no',

                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_no' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">BIN No.
                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_no')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>
                                                </a>
                                            </th>


                                            {{-- BIN Type --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_type' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_type',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_type' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Type

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_type')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Status --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_status' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_status',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_status' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Status

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_status')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Storage Location --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'storage_location' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'storage_location',
                                                                'sort_direction' =>
                                                                    $sortBy == 'storage_location' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    Storage Location<br>
                                                    (Virtual)

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'storage_location')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Storage Section --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'storage_section' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'storage_section',
                                                                'sort_direction' =>
                                                                    $sortBy == 'storage_section' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    Storage Section<br>
                                                    (Floor)

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'storage_section')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- BIN Location --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_location' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_location',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_location' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Location

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_location')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Length --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_length' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_length',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_length' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Length<br>(Inch)

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_length')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Width --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_width' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_width',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_width' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Width<br>(Inch)

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_width')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Height --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_height' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_height',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_height' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Height<br>(Inch)

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_height')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Volume --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_volume_cft_cap' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_volume_cft_cap',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_volume_cft_cap' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Volume<br>(CFT) Cap

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_volume_cft_cap')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Volume Cap2 --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_volume_cft_cap_2' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_volume_cft_cap_2',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_volume_cft_cap_2' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Volume<br>(CFT) Cap 2

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_volume_cft_cap_2')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Weight --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_weight_kg_cap' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_weight_kg_cap',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_weight_kg_cap' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Weight<br>(KG) Cap

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_weight_kg_cap')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            {{-- Weight Cap2 --}}
                                            <th style="background:#fce4d6;color:#0070c0;">

                                                <a
                                                    class="sort-heading {{ $sortBy == 'bin_weight_kg_cap_2' ? 'active-sort' : '' }}"
                                                    href="{{ route(
                                                        'admin.digiwim.bin-master.datalist',
                                                        array_merge(
                                                            request()->except(['page']),
                                                            [
                                                                'sort_by' => 'bin_weight_kg_cap_2',
                                                                'sort_direction' =>
                                                                    $sortBy == 'bin_weight_kg_cap_2' &&
                                                                    $sortDirection == 'asc'
                                                                    ? 'desc'
                                                                    : 'asc'
                                                            ]
                                                        )
                                                    ) }}">

                                                    BIN Weight<br>(KG) Cap 2

                                                    <span class="sort-icon">
                                                        @if($sortBy == 'bin_weight_kg_cap_2')
                                                            {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                        @else
                                                            ⇅
                                                        @endif
                                                    </span>

                                                </a>

                                            </th>


                                            @foreach([
                                                'custom1' => 'Custom1',
                                                'custom2' => 'Custom2',
                                                'custom3' => 'Custom3',
                                                'custom4' => 'Custom4',
                                                'custom5' => 'Custom5'
                                            ] as $column => $heading)

                                                <th style="background:#fce4d6;color:#0070c0;">

                                                    <a
                                                        class="sort-heading {{ $sortBy == $column ? 'active-sort' : '' }}"
                                                        href="{{ route(
                                                            'admin.digiwim.bin-master.datalist',
                                                            array_merge(
                                                                request()->except(['page']),
                                                                [
                                                                    'sort_by' => $column,
                                                                    'sort_direction' =>
                                                                        $sortBy == $column &&
                                                                        $sortDirection == 'asc'
                                                                        ? 'desc'
                                                                        : 'asc'
                                                                ]
                                                            )
                                                        ) }}">

                                                        {{ $heading }}

                                                        <span class="sort-icon">
                                                            @if($sortBy == $column)
                                                                {{ $sortDirection == 'asc' ? '▲' : '▼' }}
                                                            @else
                                                                ⇅
                                                            @endif
                                                        </span>

                                                    </a>

                                                </th>

                                            @endforeach


                                            @if((int)$userRole === 1)

                                                <th
                                                    class="action-column"
                                                    style="background:#fce4d6;color:#0070c0;">

                                                    Action

                                                </th>

                                            @endif


                                        </tr>

                                        </thead>


                                        <tbody>


                                        @if($datalist->count() > 0)


                                            @foreach($datalist as $bin)

                                                <tr>


                                                    <td class="sticky-col-1">
                                                        {{ $bin->plant_code }}
                                                    </td>


                                                    <td class="sticky-col-2">
                                                        {{ $bin->plant_name }}
                                                    </td>


                                                    <td class="sticky-col-3">
                                                        {{ $bin->bin_no }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_type }}
                                                    </td>


                                                    <td>

                                                        @if($bin->bin_status == 'Active')

                                                            <span class="badge badge-success">
                                                                Active
                                                            </span>

                                                        @else

                                                            <span class="badge badge-secondary">
                                                                Inactive
                                                            </span>

                                                        @endif

                                                    </td>


                                                    <td>
                                                        {{ $bin->storage_location }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->storage_section }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_location }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_length }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_width }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_height }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_volume_cft_cap }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_volume_cft_cap_2 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_weight_kg_cap }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->bin_weight_kg_cap_2 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->custom1 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->custom2 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->custom3 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->custom4 }}
                                                    </td>


                                                    <td>
                                                        {{ $bin->custom5 }}
                                                    </td>


                                                    @if((int)$userRole === 1)

                                                        <td class="action-column">


                                                            <a
                                                                href="{{ route(
                                                                    'admin.digiwim.bin-master.edit',
                                                                    $bin->id
                                                                ) }}"
                                                                class="btn btn-primary btn-xs"
                                                                title="Edit">

                                                                <i class="fas fa-edit"></i>

                                                            </a>


                                                            <button
                                                                type="button"
                                                                class="btn btn-danger btn-xs delete-bin"
                                                                data-id="{{ $bin->id }}"
                                                                title="Delete">

                                                                <i class="fas fa-trash"></i>

                                                            </button>


                                                        </td>

                                                    @endif


                                                </tr>

                                            @endforeach


                                        @else


                                            <tr>

                                                <td
                                                    colspan="{{ (int)$userRole === 1 ? 21 : 20 }}"
                                                    class="text-center">

                                                    No BIN Master records found.

                                                </td>

                                            </tr>


                                        @endif


                                        </tbody>


                                    </table>


                                </div>

                            </div>


                            <!-- Pagination -->
                            <div class="pagination-area">

                                <div class="row">


                                    <div class="col-md-6">

                                        <div class="pagination-info">

                                            Showing

                                            <strong>
                                                {{ $datalist->firstItem() ?? 0 }}
                                            </strong>

                                            to

                                            <strong>
                                                {{ $datalist->lastItem() ?? 0 }}
                                            </strong>

                                            of

                                            <strong>
                                                {{ $datalist->total() }}
                                            </strong>

                                            records

                                        </div>

                                    </div>


                                    <div class="col-md-6">

                                        <div class="float-right">

                                            {{ $datalist->links() }}

                                        </div>

                                    </div>


                                </div>

                            </div>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>


@push('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Automatically change page size
    |--------------------------------------------------------------------------
    */

    $('#per_page').on(
        'change',
        function () {

            $('#binFilterForm').submit();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete BIN
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.delete-bin',
        function () {


            let binId = $(this).data('id');


            Swal.fire({

                title: 'Are you sure?',

                text:
                    'This BIN Master record will be deleted.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Delete',

                cancelButtonText:
                    'Cancel'

            }).then((result) => {


                if (!result.isConfirmed) {
                    return;
                }


                let deleteUrl =
                    "{{ route(
                        'admin.digiwim.bin-master.delete',
                        ':id'
                    ) }}";


                deleteUrl =
                    deleteUrl.replace(
                        ':id',
                        binId
                    );


                $.ajax({

                    url:
                        deleteUrl,

                    type:
                        'POST',

                    data: {

                        _token:
                            "{{ csrf_token() }}",

                        _method:
                            'DELETE'
                    },


                    success:
                        function (response) {


                            if (
                                response.status === true
                            ) {


                                Swal.fire({

                                    icon:
                                        'success',

                                    title:
                                        'Deleted',

                                    text:
                                        response.message,

                                    timer:
                                        1200,

                                    showConfirmButton:
                                        false

                                }).then(function () {

                                    window.location.reload();

                                });


                            } else {


                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );

                            }

                        },


                    error:
                        function (xhr) {


                            let message =
                                'Unable to delete BIN Master.';


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.message
                            ) {

                                message =
                                    xhr.responseJSON.message;

                            }


                            Swal.fire(
                                'Error',
                                message,
                                'error'
                            );

                        }

                });

            });

        }

    );

});

</script>

@endpush
@endsection