@extends('admin.admin')

@section('bodycontent')

    <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Transport Rate Master</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Transport Rate Master</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
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
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Transport Rate Master</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.transport-rate-master.update', $rate->id) }}" method="POST" name="addfrm" id="editRateForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" value="{{ $rate->id }}">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="lane_name">Lane Name</label>
                                <input type="text" name="lane_name" id="lane_name" class="form-control @error('lane_name') is-invalid @enderror"
                                       value="{{ old('lane_name', $rate->lane_name) }}">
                                @error('lane_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="from_code">From Code *</label>
                                <input type="text" name="from_code" id="from_code" class="form-control @error('from_code') is-invalid @enderror"
                                       value="{{ old('from_code', $rate->from_code) }}" required>
                                @error('from_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="from_name">From Name</label>
                                <input type="text" name="from_name" id="from_name" class="form-control @error('from_name') is-invalid @enderror"
                                       value="{{ old('from_name', $rate->from_name) }}" readonly>
                                @error('from_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="origin_region">Origin Region</label>
                                <input type="text" name="origin_region" id="origin_region" class="form-control @error('origin_region') is-invalid @enderror"
                                       value="{{ old('origin_region', $rate->origin_region) }}">
                                @error('origin_region')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="to_name">To Name</label>
                                <input type="text" name="to_name" id="to_name" class="form-control @error('to_name') is-invalid @enderror"
                                       value="{{ old('to_name', $rate->to_name) }}" readonly>
                                @error('to_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="to_code">To Code *</label>
                                <input type="text" name="to_code" id="to_code" class="form-control @error('to_code') is-invalid @enderror"
                                       value="{{ old('to_code', $rate->to_code) }}" required>
                                @error('to_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="destination_region">Destination Region</label>
                                <input type="text" name="destination_region" id="destination_region" class="form-control @error('destination_region') is-invalid @enderror"
                                       value="{{ old('destination_region', $rate->destination_region) }}">
                                @error('destination_region')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="vendor_code">Vendor Code *</label>
                                <input type="text" name="vendor_code" id="vendor_code" class="form-control @error('vendor_code') is-invalid @enderror"
                                       value="{{ old('vendor_code', $rate->vendor_code) }}" required>
                                @error('vendor_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="vendor_name">Vendor Name</label>
                                <input type="text" name="vendor_name" id="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror"
                                       value="{{ old('vendor_name', $rate->vendor_name) }}">
                                @error('vendor_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="mode">Mode *</label>
                                <select name="mode" id="mode" class="form-control @error('mode') is-invalid @enderror" required>
                                    <option value="">Select Mode</option>
                                    <option value="PTL" {{ old('mode', $rate->mode) == 'PTL' ? 'selected' : '' }}>PTL</option>
                                    <option value="AIR" {{ old('mode', $rate->mode) == 'AIR' ? 'selected' : '' }}>AIR</option>
                                    <option value="ROAD" {{ old('mode', $rate->mode) == 'ROAD' ? 'selected' : '' }}>ROAD</option>
                                    <option value="RAIL" {{ old('mode', $rate->mode) == 'RAIL' ? 'selected' : '' }}>RAIL</option>
                                    <option value="SEA" {{ old('mode', $rate->mode) == 'SEA' ? 'selected' : '' }}>SEA</option>
                                    <option value="COURIER" {{ old('mode', $rate->mode) == 'COURIER' ? 'selected' : '' }}>COURIER</option>
                                </select>
                                @error('mode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="chargeable_weight">Chargeable Weight</label>
                                <input type="number" name="chargeable_weight" id="chargeable_weight" class="form-control @error('chargeable_weight') is-invalid @enderror"
                                       value="{{ old('chargeable_weight', $rate->chargeable_weight) }}" step="0.01" min="0">
                                @error('chargeable_weight')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="cft_value">CFT Value</label>
                                <input type="number" name="cft_value" id="cft_value" class="form-control @error('cft_value') is-invalid @enderror"
                                       value="{{ old('cft_value', $rate->cft_value) }}" step="0.01" min="0">
                                @error('cft_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="cft_weight">CFT Weight</label>
                                <input type="number" name="cft_weight" id="cft_weight" class="form-control @error('cft_weight') is-invalid @enderror"
                                       value="{{ old('cft_weight', $rate->cft_weight) }}" step="0.01" min="0">
                                @error('cft_weight')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="rate_logic">Base Rate per KG / Case *</label>
                                <input type="number" name="rate_logic" id="rate_logic" class="form-control @error('rate_logic') is-invalid @enderror"
                                       value="{{ old('rate_logic', $rate->rate_logic) }}" required step="0.01" min="0">
                                @error('rate_logic')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="pickup_charges">Pickup Charges</label>
                                <input type="number" name="pickup_charges" id="pickup_charges" class="form-control @error('pickup_charges') is-invalid @enderror"
                                       value="{{ old('pickup_charges', $rate->pickup_charges) }}" step="0.01" min="0">
                                @error('pickup_charges')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="delivery_charges">Delivery Charges</label>
                                <input type="number" name="delivery_charges" id="delivery_charges" class="form-control @error('delivery_charges') is-invalid @enderror"
                                       value="{{ old('delivery_charges', $rate->delivery_charges) }}" step="0.01" min="0">
                                @error('delivery_charges')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="detention_rate">Detention Rate</label>
                                <input type="number" name="detention_rate" id="detention_rate" class="form-control @error('detention_rate') is-invalid @enderror"
                                       value="{{ old('detention_rate', $rate->detention_rate) }}" step="0.01" min="0">
                                @error('detention_rate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="fov_on_base_rate">FOV on Base Rate</label>
                                <input type="number" name="fov_on_base_rate" id="fov_on_base_rate" class="form-control @error('fov_on_base_rate') is-invalid @enderror"
                                       value="{{ old('fov_on_base_rate', $rate->fov_on_base_rate) }}" step="0.01" min="0">
                                @error('fov_on_base_rate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="fuel_surcharge_on_base_rate">Fuel Surcharge on Base Rate</label>
                                <input type="number" name="fuel_surcharge_on_base_rate" id="fuel_surcharge_on_base_rate" class="form-control @error('fuel_surcharge_on_base_rate') is-invalid @enderror"
                                       value="{{ old('fuel_surcharge_on_base_rate', $rate->fuel_surcharge_on_base_rate) }}" step="0.01" min="0">
                                @error('fuel_surcharge_on_base_rate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="oda">ODA</label>
                                <input type="number" name="oda" id="oda" class="form-control @error('oda') is-invalid @enderror"
                                       value="{{ old('oda', $rate->oda) }}" step="0.01" min="0">
                                @error('oda')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="oda_1">ODA 1</label>
                                <input type="number" name="oda_1" id="oda_1" class="form-control @error('oda_1') is-invalid @enderror"
                                       value="{{ old('oda_1', $rate->oda_1) }}" step="0.01" min="0">
                                @error('oda_1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="oda_2">ODA 2</label>
                                <input type="number" name="oda_2" id="oda_2" class="form-control @error('oda_2') is-invalid @enderror"
                                       value="{{ old('oda_2', $rate->oda_2) }}" step="0.01" min="0">
                                @error('oda_2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="minimum_chargeable_weight_case">Minimum Chargeable Weight / Case</label>
                                <input type="number" name="minimum_chargeable_weight_case" id="minimum_chargeable_weight_case" class="form-control @error('minimum_chargeable_weight_case') is-invalid @enderror"
                                       value="{{ old('minimum_chargeable_weight_case', $rate->minimum_chargeable_weight_case) }}" step="0.01" min="0">
                                @error('minimum_chargeable_weight_case')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="validity_start_date">Validity Start Date *</label>
                                <input type="date" name="validity_start_date" id="validity_start_date" class="form-control @error('validity_start_date') is-invalid @enderror"
                                       value="{{ old('validity_start_date', $rate->validity_start_date ? \Illuminate\Support\Carbon::parse($rate->validity_start_date)->format('Y-m-d') : '') }}" required>
                                @error('validity_start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="validity_end_date">Validity End Date *</label>
                                <input type="date" name="validity_end_date" id="validity_end_date" class="form-control @error('validity_end_date') is-invalid @enderror"
                                       value="{{ old('validity_end_date', $rate->validity_end_date ? \Illuminate\Support\Carbon::parse($rate->validity_end_date)->format('Y-m-d') : '') }}" required>
                                @error('validity_end_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="docket_charges">Docket Charges</label>
                                <input type="number" name="docket_charges" id="docket_charges" class="form-control @error('docket_charges') is-invalid @enderror"
                                       value="{{ old('docket_charges', $rate->docket_charges) }}" step="0.01" min="0">
                                @error('docket_charges')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="custom">Custom</label>
                                <input type="text" name="custom" id="custom" class="form-control @error('custom') is-invalid @enderror"
                                       value="{{ old('custom', $rate->custom) }}">
                                @error('custom')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="custom_1">Custom 1</label>
                                <input type="text" name="custom_1" id="custom_1" class="form-control @error('custom_1') is-invalid @enderror"
                                       value="{{ old('custom_1', $rate->custom_1) }}">
                                @error('custom_1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="custom_2">Custom 2</label>
                                <input type="text" name="custom_2" id="custom_2" class="form-control @error('custom_2') is-invalid @enderror"
                                       value="{{ old('custom_2', $rate->custom_2) }}">
                                @error('custom_2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="custom_3">Custom 3</label>
                                <input type="text" name="custom_3" id="custom_3" class="form-control @error('custom_3') is-invalid @enderror"
                                       value="{{ old('custom_3', $rate->custom_3) }}">
                                @error('custom_3')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="custom_4">Custom 4</label>
                                <input type="text" name="custom_4" id="custom_4" class="form-control @error('custom_4') is-invalid @enderror"
                                       value="{{ old('custom_4', $rate->custom_4) }}">
                                @error('custom_4')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('admin.transport-rate-master.list') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="updateRate">
                                <i class="fas fa-save"></i> Update Transport Rate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function () {

                const fetchRowUrl = "{{ route('admin.transport-rate-master.lookup') }}";

                $('#from_code, #to_code').on('change blur', function () {

                    const input = $(this);
                    const code = $.trim(input.val());
                    const nameField = input.attr('id') === 'from_code' ? '#from_name' : '#to_name';

                    $(nameField).val('');
                    if (!code) return;

                    $.ajax({
                        url: fetchRowUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            plant_code: code
                        },
                        success: function (res) {
                            if (res.error) {
                                input.addClass('is-invalid');
                                Swal.fire('Invalid Code', res.error, 'warning');
                                return;
                            }

                            input.removeClass('is-invalid');
                            $(nameField).val(res.plant_name || res.name || '');
                        },
                        error: function () {
                            Swal.fire('Error', 'Unable to fetch plant details.', 'error');
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
