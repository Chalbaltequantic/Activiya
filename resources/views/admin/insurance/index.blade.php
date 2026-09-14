@extends('admin.admin')

@section('bodycontent')
<div class="content-header">
    <div class="container-fluid">
        <h1>Insurance</h1>
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

<div class="card">
<div class="card-header p-2">
<ul class="nav nav-pills">
<li class="nav-item"><a class="nav-link active" href="{{ route('admin.insurance.index') }}">XLS Upload</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('admin.insurance.manual-upload') }}">Manual Upload</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('admin.insurance.datalist') }}">Insurance List</a></li>
</ul>
</div>

<div class="card-body">
<form method="POST" action="{{ route('admin.insurance.import') }}" enctype="multipart/form-data">
@csrf
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label>Select XLS / XLSX File</label>
<input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx" required>
</div>
</div>
<div class="col-md-3">
<label>&nbsp;</label>
<div><button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Upload XLS</button></div>
</div>
</div>
</form>

<div class="table-responsive mt-3">
<table class="table table-bordered table-sm">
<thead>
<tr>
<th>Column</th>
<th>Field</th>
</tr>
</thead>
<tbody>
<tr><td>A</td><td>Loss Date</td></tr>
<tr><td>B</td><td>Nature of Claim</td></tr>
<tr><td>C</td><td>From Location Code</td></tr>
<tr><td>D</td><td>From Location</td></tr>
<tr><td>E</td><td>To Location Code</td></tr>
<tr><td>F</td><td>To Location</td></tr>
<tr><td>G</td><td>Invoice No.</td></tr>
<tr><td>H</td><td>Invoice Date</td></tr>
<tr><td>I</td><td>Transporter Name</td></tr>
<tr><td>J</td><td>LR No.</td></tr>
<tr><td>K</td><td>LR Date</td></tr>
<tr><td>L</td><td>Damage Value</td></tr>
<tr><td>M</td><td>Shortage Value</td></tr>
<tr><td>N</td><td>Total Value - recalculated by system</td></tr>
</tbody>
</table>
</div>
</div>
</div>

</div>
</div>
@endsection