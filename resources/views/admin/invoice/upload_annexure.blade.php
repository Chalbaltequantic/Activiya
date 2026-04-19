@extends('admin.admin')
@section('bodycontent')

<div class="content-header">
    <div class="container-fluid">
        <h1>Upload Annexure XLS</h1>
    </div>
</div>

<div class="content">
<div class="container-fluid">
<div class="card">
<div class="card-body">

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
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

<div class="mb-3">
    <strong>Invoice No:</strong> {{ $invoice->invoice_no }}<br>
    <strong>Existing Annexures:</strong> {{ $invoice->annexures->count() }}
</div>

<form method="POST" action="{{ route('admin.invoice.upload_annexure_store', $invoice->id) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Upload XLS / XLSX File</label>
        <input type="file" name="annexure_file" class="form-control" accept=".xls,.xlsx" required>
    </div>

    <div class="alert alert-info">
        <strong>Expected column order in XLS:</strong><br>
         customer_ref_no,obd_no,arrival_date,dispatch_date,loading_detention_days, loading_detention_charge, loading_charge, loading_pt_det_charge, reporting_date, 
		unloading_date, unloading_detention_days, transit_days, unloading_detention_charge,
		unloading_charge, unloading_pt_det_charge, 
      gr_charges, fix_rental, toll_tax, green_tax

    </div>

    <div class="mt-3">
        <button class="btn btn-primary">Upload Annexure</button>
        <a href="{{ route('admin.invoice.list') }}" class="btn btn-secondary">Back</a>
    </div>
</form>

</div>
</div>
</div>
</div>

@endsection