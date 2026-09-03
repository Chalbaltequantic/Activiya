@extends('admin.admin')

@section('bodycontent')

<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">BIN Master Upload</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">BIN Master Upload</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if(session('errorRows'))
            <div class="alert alert-warning">
                <strong> Skipped Rows:</strong>
                <ul class="mb-0">
                    @foreach(session('errorRows') as $err )

                        <li>
                            Row {{ $err['row'] }}:
                            {{ $err['reason'] }}
                        </li>
                    @endforeach

                </ul>

            </div>

        @endif


        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.digiwim.bin-master.index') }}">XLS Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.digiwim.bin-master.manual-upload') }}">Manual Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.digiwim.bin-master.datalist') }}">BIN Master List </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.digiwim.bin-master.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select XLS / XLSX File</label>
                                <input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        Upload BIN Master
                    </button>
                </form>
                <hr>
                <h5> XLS Column Format</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
						<tr>    
						<th>Column</th>
						<th>Field</th>
						<th>Required</th>
                        </tr>

                        </thead>

                        <tbody>

                            <tr>
                                <td>A</td>
                                <td>Plant Code</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>B</td>
                                <td>Plant Name</td>
                                <td>Auto</td>
                            </tr>

                            <tr>
                                <td>C</td>
                                <td>BIN No.</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>D</td>
                                <td>BIN Type</td>
                                <td>No</td>
                            </tr>

                            <tr>
                                <td>E</td>
                                <td>BIN Status</td>
                                <td>Active / Inactive</td>
                            </tr>

                            <tr>
                                <td>F</td>
                                <td>Storage Location</td>
                                <td>No</td>
                            </tr>

                            <tr>
                                <td>G</td>
                                <td>Storage Section</td>
                                <td>No</td>
                            </tr>

                            <tr>
                                <td>H</td>
                                <td>BIN Location</td>
                                <td>No</td>
                            </tr>

                            <tr>
                                <td>I</td>
                                <td>BIN Length (Inch)</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>J</td>
                                <td>BIN Width (Inch)</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>K</td>
                                <td>BIN Height (Inch)</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>L</td>
                                <td>BIN Volume CFT Cap</td>
                                <td>Auto Calculated</td>
                            </tr>

                            <tr>
                                <td>M</td>
                                <td>BIN Volume CFT Cap 2</td>
                                <td>Auto Calculated</td>
                            </tr>

                            <tr>
                                <td>N</td>
                                <td>BIN Weight KG Cap</td>
                                <td>Yes</td>
                            </tr>

                            <tr>
                                <td>O</td>
                                <td>BIN Weight KG Cap 2</td>
                                <td>Auto Calculated</td>
                            </tr>

                            <tr>
                                <td>P-T</td>
                                <td>Custom1 - Custom5</td>
                                <td>No</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection