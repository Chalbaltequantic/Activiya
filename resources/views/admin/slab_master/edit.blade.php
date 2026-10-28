@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header"><div class="container-fluid"><div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0">Edit Slab Master</h1></div>
    <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Edit Slab Master</li></ol></div>
</div></div></div>
<div class="content"><div class="container-fluid">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(session('errorRows'))<div class="alert alert-warning"><b>Skipped Rows:</b><ul>@foreach(session('errorRows') as $err)<li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-header p-2"><ul class="nav nav-pills">
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.index') }}">XLS Upload</a></li>
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.manual') }}">Manual Upload</a></li>
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.list') }}">Slab Master List</a></li>
</ul></div><div class="card-body">
<form action="{{ route('admin.slab-master.update', $rate->id) }}" method="POST" id="editSlabForm">@csrf @method('PUT')
<div class="row">
<div class="form-group col-md-6"><label for="from_code">From Code *</label>
<input type="text" name="from_code" id="from_code" class="form-control" value="{{ old('from_code', $rate->from_code) }}" required>
@error('from_code')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="from_name">From Name</label>
<input type="text" name="from_name" id="from_name" class="form-control" value="{{ old('from_name', $rate->from_name) }}" readonly>
@error('from_name')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="origin_region">Origin Region</label>
<input type="text" name="origin_region" id="origin_region" class="form-control" value="{{ old('origin_region', $rate->origin_region) }}">
@error('origin_region')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="to_name">To Name</label>
<input type="text" name="to_name" id="to_name" class="form-control" value="{{ old('to_name', $rate->to_name) }}" readonly>
@error('to_name')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="to_code">To Code *</label>
<input type="text" name="to_code" id="to_code" class="form-control" value="{{ old('to_code', $rate->to_code) }}" required>
@error('to_code')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="destination_region">Des Region</label>
<input type="text" name="destination_region" id="destination_region" class="form-control" value="{{ old('destination_region', $rate->destination_region) }}">
@error('destination_region')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="vendor_code">Vendor Code *</label>
<input type="text" name="vendor_code" id="vendor_code" class="form-control" value="{{ old('vendor_code', $rate->vendor_code) }}" required>
@error('vendor_code')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="vendor_name">Vendor Name</label>
<input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name', $rate->vendor_name) }}">
@error('vendor_name')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="mode">Mode *</label>
<select name="mode" id="mode" class="form-control" required><option value="">Select Mode</option>
<option value="PTL" {{ old('mode', $rate->mode) == 'PTL' ? 'selected' : '' }}>PTL</option>
<option value="AIR" {{ old('mode', $rate->mode) == 'AIR' ? 'selected' : '' }}>AIR</option>
<option value="ROAD" {{ old('mode', $rate->mode) == 'ROAD' ? 'selected' : '' }}>ROAD</option>
<option value="RAIL" {{ old('mode', $rate->mode) == 'RAIL' ? 'selected' : '' }}>RAIL</option>
<option value="SEA" {{ old('mode', $rate->mode) == 'SEA' ? 'selected' : '' }}>SEA</option>
<option value="COURIER" {{ old('mode', $rate->mode) == 'COURIER' ? 'selected' : '' }}>COURIER</option>
<option value="FTL" {{ old('mode', $rate->mode) == 'FTL' ? 'selected' : '' }}>FTL</option>
</select>
@error('mode')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="truck_code">Truck Code *</label>
<input type="text" name="truck_code" id="truck_code" class="form-control" value="{{ old('truck_code', $rate->truck_code) }}" required>
@error('truck_code')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="truck_description">Truck Description</label>
<input type="text" name="truck_description" id="truck_description" class="form-control" value="{{ old('truck_description', $rate->truck_description) }}">
@error('truck_description')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_1">Slab 1</label>
<input type="number" name="slab_1" id="slab_1" class="form-control" value="{{ old('slab_1', $rate->slab_1) }}" step="0.0001" min="0">
@error('slab_1')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_2">Slab 2</label>
<input type="number" name="slab_2" id="slab_2" class="form-control" value="{{ old('slab_2', $rate->slab_2) }}" step="0.0001" min="0">
@error('slab_2')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_3">Slab 3</label>
<input type="number" name="slab_3" id="slab_3" class="form-control" value="{{ old('slab_3', $rate->slab_3) }}" step="0.0001" min="0">
@error('slab_3')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_4">Slab 4</label>
<input type="number" name="slab_4" id="slab_4" class="form-control" value="{{ old('slab_4', $rate->slab_4) }}" step="0.0001" min="0">
@error('slab_4')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_5">Slab 5</label>
<input type="number" name="slab_5" id="slab_5" class="form-control" value="{{ old('slab_5', $rate->slab_5) }}" step="0.0001" min="0">
@error('slab_5')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_6">Slab 6</label>
<input type="number" name="slab_6" id="slab_6" class="form-control" value="{{ old('slab_6', $rate->slab_6) }}" step="0.0001" min="0">
@error('slab_6')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_7">Slab 7</label>
<input type="number" name="slab_7" id="slab_7" class="form-control" value="{{ old('slab_7', $rate->slab_7) }}" step="0.0001" min="0">
@error('slab_7')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_8">Slab 8</label>
<input type="number" name="slab_8" id="slab_8" class="form-control" value="{{ old('slab_8', $rate->slab_8) }}" step="0.0001" min="0">
@error('slab_8')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_9">Slab 9</label>
<input type="number" name="slab_9" id="slab_9" class="form-control" value="{{ old('slab_9', $rate->slab_9) }}" step="0.0001" min="0">
@error('slab_9')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_10">Slab 10</label>
<input type="number" name="slab_10" id="slab_10" class="form-control" value="{{ old('slab_10', $rate->slab_10) }}" step="0.0001" min="0">
@error('slab_10')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_1_rate">Slab 1 Rate</label>
<input type="number" name="slab_1_rate" id="slab_1_rate" class="form-control" value="{{ old('slab_1_rate', $rate->slab_1_rate) }}" step="0.0001" min="0">
@error('slab_1_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_2_rate">Slab 2 Rate</label>
<input type="number" name="slab_2_rate" id="slab_2_rate" class="form-control" value="{{ old('slab_2_rate', $rate->slab_2_rate) }}" step="0.0001" min="0">
@error('slab_2_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_3_rate">Slab 3 Rate</label>
<input type="number" name="slab_3_rate" id="slab_3_rate" class="form-control" value="{{ old('slab_3_rate', $rate->slab_3_rate) }}" step="0.0001" min="0">
@error('slab_3_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_4_rate">Slab 4 Rate</label>
<input type="number" name="slab_4_rate" id="slab_4_rate" class="form-control" value="{{ old('slab_4_rate', $rate->slab_4_rate) }}" step="0.0001" min="0">
@error('slab_4_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_5_rate">Slab 5 Rate</label>
<input type="number" name="slab_5_rate" id="slab_5_rate" class="form-control" value="{{ old('slab_5_rate', $rate->slab_5_rate) }}" step="0.0001" min="0">
@error('slab_5_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_6_rate">Slab 6 Rate</label>
<input type="number" name="slab_6_rate" id="slab_6_rate" class="form-control" value="{{ old('slab_6_rate', $rate->slab_6_rate) }}" step="0.0001" min="0">
@error('slab_6_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_7_rate">Slab 7 Rate</label>
<input type="number" name="slab_7_rate" id="slab_7_rate" class="form-control" value="{{ old('slab_7_rate', $rate->slab_7_rate) }}" step="0.0001" min="0">
@error('slab_7_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_8_rate">Slab 8 Rate</label>
<input type="number" name="slab_8_rate" id="slab_8_rate" class="form-control" value="{{ old('slab_8_rate', $rate->slab_8_rate) }}" step="0.0001" min="0">
@error('slab_8_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_9_rate">Slab 9 Rate</label>
<input type="number" name="slab_9_rate" id="slab_9_rate" class="form-control" value="{{ old('slab_9_rate', $rate->slab_9_rate) }}" step="0.0001" min="0">
@error('slab_9_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
<div class="form-group col-md-6"><label for="slab_10_rate">Slab 10 Rate</label>
<input type="number" name="slab_10_rate" id="slab_10_rate" class="form-control" value="{{ old('slab_10_rate', $rate->slab_10_rate) }}" step="0.0001" min="0">
@error('slab_10_rate')<span class="text-danger">{{ $message }}</span>@enderror</div>
</div><div class="text-right"><a href="{{ route('admin.slab-master.list') }}" class="btn btn-secondary">Cancel</a>
<button type="submit" class="btn btn-primary" id="updateSlab">Update Slab Master</button></div></form>
</div></div></div></div>
@push('js')
<script>
$(document).ready(function () {
    $('#from_code, #to_code').on('change blur', function () {
        const input = $(this);
        const code = $.trim(input.val());
        const target = input.attr('id') === 'from_code' ? '#from_name' : '#to_name';
        $(target).val('');
        if (!code) return;
        $.ajax({
            url: "{{ route('admin.slab-master.lookup') }}",
            type: 'POST',
            data: {_token: "{{ csrf_token() }}", plant_code: code},
            success: function (res) { input.removeClass('is-invalid'); $(target).val(res.plant_name || ''); },
            error: function () { input.addClass('is-invalid'); }
        });
    });
    $('#editSlabForm').on('submit', function () { $('#updateSlab').prop('disabled', true).text('Updating...'); });
});
</script>
@endpush
@endsection
