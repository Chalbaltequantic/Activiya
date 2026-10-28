@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header"><div class="container-fluid"><div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0">Slab Master XLS Upload</h1></div>
    <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Slab Master XLS Upload</li></ol></div>
</div></div></div>
<div class="content"><div class="container-fluid">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(session('errorRows'))<div class="alert alert-warning"><b>Skipped Rows:</b><ul>@foreach(session('errorRows') as $err)<li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-header p-2"><ul class="nav nav-pills">
<li class="nav-item"><a class="nav-link active" href="{{ route('admin.slab-master.index') }}">XLS Upload</a></li>
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.manual') }}">Manual Upload</a></li>
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.list') }}">Slab Master List</a></li>
</ul></div><div class="card-body">
<form action="{{ route('admin.slab-master.import') }}" method="POST" enctype="multipart/form-data">@csrf
<div class="form-group"><label>Select XLS / XLSX File</label><input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx" required></div>
<button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Slab Master</button>
<a href="{{ route('admin.slab-master.template') }}" class="btn btn-secondary">Download Excel Template</a>
</form><hr><h5>XLS Column Format</h5><div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>Column</th><th>Field</th><th>Required</th></tr></thead><tbody>
<tr><td>A</td><td>From Code</td><td>Yes</td></tr>
<tr><td>B</td><td>From Name</td><td>No</td></tr>
<tr><td>C</td><td>Origin Region</td><td>No</td></tr>
<tr><td>D</td><td>To Name</td><td>No</td></tr>
<tr><td>E</td><td>To Code</td><td>Yes</td></tr>
<tr><td>F</td><td>Des Region</td><td>No</td></tr>
<tr><td>G</td><td>Vendor Code</td><td>Yes</td></tr>
<tr><td>H</td><td>Vendor Name</td><td>No</td></tr>
<tr><td>I</td><td>Mode</td><td>Yes</td></tr>
<tr><td>J</td><td>Truck Code</td><td>Yes</td></tr>
<tr><td>K</td><td>Truck Description</td><td>No</td></tr>
<tr><td>L</td><td>Slab 1</td><td>No</td></tr>
<tr><td>M</td><td>Slab 2</td><td>No</td></tr>
<tr><td>N</td><td>Slab 3</td><td>No</td></tr>
<tr><td>O</td><td>Slab 4</td><td>No</td></tr>
<tr><td>P</td><td>Slab 5</td><td>No</td></tr>
<tr><td>Q</td><td>Slab 6</td><td>No</td></tr>
<tr><td>R</td><td>Slab 7</td><td>No</td></tr>
<tr><td>S</td><td>Slab 8</td><td>No</td></tr>
<tr><td>T</td><td>Slab 9</td><td>No</td></tr>
<tr><td>U</td><td>Slab 10</td><td>No</td></tr>
<tr><td>V</td><td>Slab 1 Rate</td><td>No</td></tr>
<tr><td>W</td><td>Slab 2 Rate</td><td>No</td></tr>
<tr><td>X</td><td>Slab 3 Rate</td><td>No</td></tr>
<tr><td>Y</td><td>Slab 4 Rate</td><td>No</td></tr>
<tr><td>Z</td><td>Slab 5 Rate</td><td>No</td></tr>
<tr><td>AA</td><td>Slab 6 Rate</td><td>No</td></tr>
<tr><td>AB</td><td>Slab 7 Rate</td><td>No</td></tr>
<tr><td>AC</td><td>Slab 8 Rate</td><td>No</td></tr>
<tr><td>AD</td><td>Slab 9 Rate</td><td>No</td></tr>
<tr><td>AE</td><td>Slab 10 Rate</td><td>No</td></tr>
</tbody></table></div></div></div></div></div>
@endsection
