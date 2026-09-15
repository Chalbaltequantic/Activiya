@extends('admin.admin')

@section('bodycontent')

<style>
.insurance-list{/*min-width:2200px;*/font-size:12px}
.insurance-list th,.insurance-list td{white-space:nowrap;padding:5px;vertical-align:middle}
.insurance-list thead th{position:sticky;top:0;background:#fce4d6;color:#0070c0;z-index:2}
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">Insurance Claim Bill Log</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/admin/dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Claim Bill Log
                    </li>
                </ol>
            </div>

        </div>

    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>{{ session('success') }}</strong>

            <button type="button"
                class="close"
                data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>{{ session('error') }}</strong>

            <button type="button"
                class="close"
                data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        <div class="card card-primary">

            <div class="card-header">
                <h3 class="card-title">
                    Claim Bill Log
                </h3>
            </div>

            <div class="card-body">

                <form method="GET"
                    action="{{ route('admin.insurance.claim-bills') }}">

                    <div class="row">

                        <div class="col-md-5">

                            <div class="form-group">

                                <input type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="form-control"
                                    placeholder="Search Claim No., Invoice, LR, Transporter or Location">

                            </div>

                        </div>

                        <div class="col-md-4">

                            <button type="submit"
                                class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Search
                            </button>

                            <a href="{{ route('admin.insurance.claim-bills') }}"
                                class="btn btn-secondary">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover insurance-list">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Claim No.</th>
                                <th>Loss Date</th>
                                <th>Nature</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Invoice No.</th>
                                <th>Invoice Date</th>
                                <th>Transporter</th>
                                <th>LR No.</th>
                                <th>LR Date</th>
                                <th>Products</th>
                                <th>Claim Amount</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($claims as $index => $insurance)

                            @php
                                $claimAmount = $insurance->products->sum('total_value');
                            @endphp

                            <tr>

                                <td>
                                    {{ $claims->firstItem() + $index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $insurance->claim_no ?: '-' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $insurance->loss_date ? $insurance->loss_date->format('d/m/Y') : '' }}
                                </td>

                                <td>
                                    {{ $insurance->nature_of_claim }}
                                </td>

                                <td>
                                    {{ $insurance->from_location }}
                                </td>

                                <td>
                                    {{ $insurance->to_location }}
                                </td>

                                <td>
                                    {{ $insurance->invoice_no }}
                                </td>

                                <td>
                                    {{ $insurance->invoice_date ? $insurance->invoice_date->format('d/m/Y') : '' }}
                                </td>

                                <td>
                                    {{ $insurance->transporter_name }}
                                </td>

                                <td>
                                    {{ $insurance->lr_no }}
                                </td>

                                <td>
                                    {{ $insurance->lr_date ? $insurance->lr_date->format('d/m/Y') : '' }}
                                </td>

                                <td class="text-center">
                                    {{ $insurance->products_count }}
                                </td>

                                <td class="text-right">
                                    {{ number_format((float)$claimAmount, 2) }}
                                </td>

                                <td>

                                    <a href="{{ route('admin.insurance.claim-bill', $insurance->id) }}"
                                        class="btn btn-primary btn-sm">

                                        <i class="fas fa-eye"></i>
                                        View Claim

                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="14"
                                    class="text-center text-muted">

                                    No Insurance claim records found.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">

                    {{ $claims->links('pagination::bootstrap-4') }}

                </div>

            </div>

        </div>

    </div>
</div>
<!-- /.content -->

@endsection