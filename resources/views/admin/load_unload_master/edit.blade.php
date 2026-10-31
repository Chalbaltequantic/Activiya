@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Edit Load/Unload Master</h1></div>
            <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Edit Load/Unload Master</li></ol></div>
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
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.index') }}">XLS Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.manual') }}">Manual Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.list') }}">Load/Unload Master List</a></li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.load-unload-master.update', $rate->id) }}" method="POST" id="editRateForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="from_code">From Code *</label>
                            <input type="text" name="from_code" id="from_code" class="form-control" value="{{ old('from_code', $rate->from_code) }}" required>
                            @error('from_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="from_name">From Name</label>
                            <input type="text" name="from_name" id="from_name" class="form-control" value="{{ old('from_name', $rate->from_name) }}" readonly>
                            @error('from_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="origin_region">Origin Region</label>
                            <input type="text" name="origin_region" id="origin_region" class="form-control" value="{{ old('origin_region', $rate->origin_region) }}">
                            @error('origin_region')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="to_name">To Name</label>
                            <input type="text" name="to_name" id="to_name" class="form-control" value="{{ old('to_name', $rate->to_name) }}" readonly>
                            @error('to_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="to_code">To Code *</label>
                            <input type="text" name="to_code" id="to_code" class="form-control" value="{{ old('to_code', $rate->to_code) }}" required>
                            @error('to_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="destination_region">Destination Region</label>
                            <input type="text" name="destination_region" id="destination_region" class="form-control" value="{{ old('destination_region', $rate->destination_region) }}">
                            @error('destination_region')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="vendor_code">Vendor Code *</label>
                            <input type="text" name="vendor_code" id="vendor_code" class="form-control" value="{{ old('vendor_code', $rate->vendor_code) }}" required>
                            @error('vendor_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="vendor_name">Vendor Name</label>
                            <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name', $rate->vendor_name) }}" readonly>
                            @error('vendor_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="mode">Mode *</label>
                            <select name="mode" id="mode" class="form-control" required>
                                <option value="">Select</option>
                                <option value="FTL" {{ old('mode', $rate->mode) == 'FTL' ? 'selected' : '' }}>FTL</option>
                                <option value="PTL" {{ old('mode', $rate->mode) == 'PTL' ? 'selected' : '' }}>PTL</option>
                                <option value="AIR" {{ old('mode', $rate->mode) == 'AIR' ? 'selected' : '' }}>AIR</option>
                                <option value="RAIL" {{ old('mode', $rate->mode) == 'RAIL' ? 'selected' : '' }}>RAIL</option>
                                <option value="SEA" {{ old('mode', $rate->mode) == 'SEA' ? 'selected' : '' }}>SEA</option>
                                <option value="COURIER" {{ old('mode', $rate->mode) == 'COURIER' ? 'selected' : '' }}>COURIER</option>
                            </select>
                            @error('mode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="truck_code">Truck Code *</label>
                            <input type="text" name="truck_code" id="truck_code" class="form-control" value="{{ old('truck_code', $rate->truck_code) }}" required>
                            @error('truck_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="truck_description">Truck Description</label>
                            <input type="text" name="truck_description" id="truck_description" class="form-control" value="{{ old('truck_description', $rate->truck_description) }}" readonly>
                            @error('truck_description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="charge_type">Charge Type (Load/Unload) *</label>
                            <select name="charge_type" id="charge_type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Load" {{ old('charge_type', $rate->charge_type) == 'Load' ? 'selected' : '' }}>Load</option>
                                <option value="Unload" {{ old('charge_type', $rate->charge_type) == 'Unload' ? 'selected' : '' }}>Unload</option>
                            </select>
                            @error('charge_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="rate_type">Rate Type (Fix/Per Case/KG) *</label>
                            <select name="rate_type" id="rate_type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Fix" {{ old('rate_type', $rate->rate_type) == 'Fix' ? 'selected' : '' }}>Fix</option>
                                <option value="Per Case" {{ old('rate_type', $rate->rate_type) == 'Per Case' ? 'selected' : '' }}>Per Case</option>
                                <option value="KG" {{ old('rate_type', $rate->rate_type) == 'KG' ? 'selected' : '' }}>KG</option>
                            </select>
                            @error('rate_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="rate">Rate *</label>
                            <input type="number" name="rate" id="rate" class="form-control" value="{{ old('rate', $rate->rate) }}" required step="0.0001" min="0">
                            @error('rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="load_unload_limit">Load/Unload Limit</label>
                            <input type="number" name="load_unload_limit" id="load_unload_limit" class="form-control" value="{{ old('load_unload_limit', $rate->load_unload_limit) }}" step="0.0001" min="0">
                            @error('load_unload_limit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('admin.load-unload-master.list') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="updateRate">Update Load/Unload Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function () {
        const lookupUrl = "{{ route('admin.load-unload-master.lookup') }}";
        const targetFields = {
            from_code: '#from_name',
            to_code: '#to_name',
            vendor_code: '#vendor_name',
            truck_code: '#truck_description'
        };

        $('#from_code, #to_code, #vendor_code, #truck_code').on('change blur', function () {
            const input = $(this);
            const type = input.attr('id');
            const code = $.trim(input.val());
            const target = $(targetFields[type]);

            target.val('');
            input.removeClass('is-invalid');

            if (!code) {
                return;
            }

            $.ajax({
                url: lookupUrl,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    type: type,
                    code: code
                },
                success: function (response) {
                    target.val(response.name || '');
                },
                error: function () {
                    input.addClass('is-invalid');
                }
            });
        });

        $('#editRateForm').on('submit', function () {
            $('#updateRate').prop('disabled', true).text('Updating...');
        });
    });
</script>
@endpush
@endsection
