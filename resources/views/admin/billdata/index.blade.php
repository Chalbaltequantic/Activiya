
@extends('admin.admin')

@section('bodycontent')

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Freight Shipments</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Freight Shipments</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">

        <!-- Common Success / Error Messages -->
        <div class="row">
            <div class="col-lg-12">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('errorRows'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Rows with errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('errorRows') as $error)
                                <li>
                                    Row {{ $error['row'] }}: {{ $error['reason'] }}
                                </li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <!-- FTL Freight Card -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-truck mr-2"></i>
                            FTL Freight Shipments
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <label for="ftl_excel_file">Import FTL Freight Data</label>

                                    <div class="d-flex align-items-center">
                                        <input type="file" name="excel_file" id="ftl_excel_file" class="form-control-file" accept=".xlsx,.xls,.csv" required>

                                        <button type="submit" class="btn btn-primary ml-2">
                                            Import
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="form-group col-md-4 border-left text-right">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('admin.manualupload') }}" class="btn btn-warning">
                                        Manual Upload Freight Data
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-0">
                                <a href="{{ URL::asset('consignmentapp_SampleDATA/billdata.xlsx') }}">
                                    <i class="fas fa-download mr-1"></i>
                                    Download FTL Sample Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Non-FTL Freight Card -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shipping-fast mr-2"></i>
                            Non-FTL Freight Shipments (PTL / AIR / RAIL / SEA / COURIER)
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <form action="{{ route('admin.excel.nonftlimport') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <label for="non_ftl_excel_file">Import Non-FTL Freight Data</label>

                                    <div class="d-flex align-items-center">
                                        <input type="file" name="non_ftl_excel_file" id="non_ftl_excel_file" class="form-control-file" accept=".xlsx,.xls,.csv" required>

                                        <button type="submit" class="btn btn-success ml-2">
                                            Import
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="form-group col-md-4 border-left text-right">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('admin.nonftlmanualupload') }}" class="btn btn-warning">
                                        Manual Upload Non-FTL Data
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-0">
                                <a href="{{ URL::asset('consignmentapp_SampleDATA/nonftl-freight-data.xlsx') }}">
                                    <i class="fas fa-download mr-1"></i>
                                    Download Non-FTL Sample Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
