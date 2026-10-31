@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Load/Unload Master XLS Upload</h1></div>
            <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Load/Unload Master XLS Upload</li></ol></div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('errorRows'))
            <div class="alert alert-warning">
                <strong>Skipped Rows:</strong>
                <ul class="mb-0">
                    @foreach(session('errorRows') as $err)
                        <li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.load-unload-master.index') }}">XLS Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.manual') }}">Manual Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.list') }}">Load/Unload Master List</a></li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.load-unload-master.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-6 px-0">
                        <label>Select XLS / XLSX File</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                    <a href="{{ route('admin.load-unload-master.template') }}" class="btn btn-secondary">Download Excel Template</a>
                </form>
                <hr>
                <h5>Excel Column Order</h5>
                <div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>Column</th><th>Field</th><th>Required</th></tr></thead><tbody>
                    <tr><td>A</td><td>From Code</td><td>Yes</td></tr>
                    <tr><td>B</td><td>From Name</td><td>Auto</td></tr>
                    <tr><td>C</td><td>Origin Region</td><td>No</td></tr>
                    <tr><td>D</td><td>To Name</td><td>Auto</td></tr>
                    <tr><td>E</td><td>To Code</td><td>Yes</td></tr>
                    <tr><td>F</td><td>Destination Region</td><td>No</td></tr>
                    <tr><td>G</td><td>Vendor Code</td><td>Yes</td></tr>
                    <tr><td>H</td><td>Vendor Name</td><td>Auto</td></tr>
                    <tr><td>I</td><td>Mode</td><td>Yes</td></tr>
                    <tr><td>J</td><td>Truck Code</td><td>Yes</td></tr>
                    <tr><td>K</td><td>Truck Description</td><td>Auto</td></tr>
                    <tr><td>L</td><td>Charge Type (Load/Unload)</td><td>Yes</td></tr>
                    <tr><td>M</td><td>Rate Type (Fix/Per Case/KG)</td><td>Yes</td></tr>
                    <tr><td>N</td><td>Rate</td><td>Yes</td></tr>
                    <tr><td>O</td><td>Load/Unload Limit</td><td>No</td></tr>
                </tbody></table></div>
            </div>
        </div>
    </div>
</div>
@endsection
