@extends('admin.admin')

@section('bodycontent')

    <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Transport Rate Manual Upload</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transport Rate Manual Upload</li>
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
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.transport-rate-master.index') }}">XLS Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.transport-rate-master.manual') }}">Manual Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.transport-rate-master.list') }}">Transport Rate List</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.transport-rate-master.manual.save') }}" method="POST" name="addfrm" id="postform">
                        @csrf

                        <!-- Select Mode once for all Transport Rate rows -->
                        <div class="row mb-3">
                            <div class="form-group col-md-4">
                                <label for="global_mode">Transport Mode <span class="text-danger">*</span></label>
                                <select id="global_mode" class="form-control" required>
                                    <option value="">Select Transport Mode</option>
                                    <option value="PTL" {{ old('mode.0') == 'PTL' ? 'selected' : '' }}>PTL</option>
                                    <option value="AIR" {{ old('mode.0') == 'AIR' ? 'selected' : '' }}>AIR</option>
                                    <option value="ROAD" {{ old('mode.0') == 'ROAD' ? 'selected' : '' }}>ROAD</option>
                                    <option value="RAIL" {{ old('mode.0') == 'RAIL' ? 'selected' : '' }}>RAIL</option>
                                    <option value="SEA" {{ old('mode.0') == 'SEA' ? 'selected' : '' }}>SEA</option>
                                    <option value="COURIER" {{ old('mode.0') == 'COURIER' ? 'selected' : '' }}>COURIER</option>
                                </select>
                                <small class="text-muted">The selected mode applies to every populated row below.</small>
                            </div>
                        </div>

                        <p class="text-muted">Enter multiple transport rates or paste rows directly from Excel. Only populated rows will be submitted.</p>

                        <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
                            <div class="excel-wrapper">
                                <table class="table table-bordered excel-table" id="table">
                                    <thead>
                                        <tr>
                                            
                                            <th style="background: #fce4d6; color: #0070c0;">Lane Name</th>
                                            <th style="background: #fce4d6; color: #0070c0;">From Code *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">From Name</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Origin Region</th>
                                            <th style="background: #fce4d6; color: #0070c0;">To Name</th>
                                            <th style="background: #fce4d6; color: #0070c0;">To Code *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Destination Region</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Vendor Code *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Chargeable Weight</th>
                                            <th style="background: #fce4d6; color: #0070c0;">CFT Value</th>
                                            <th style="background: #fce4d6; color: #0070c0;">CFT Weight</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Base Rate per KG / Case *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Pickup Charges</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Delivery Charges</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Detention Rate</th>
                                            <th style="background: #fce4d6; color: #0070c0;">FOV on Base Rate</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Fuel Surcharge on Base Rate</th>
                                            <th style="background: #fce4d6; color: #0070c0;">ODA</th>
                                            <th style="background: #fce4d6; color: #0070c0;">ODA 1</th>
                                            <th style="background: #fce4d6; color: #0070c0;">ODA 2</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Minimum Chargeable Weight / Case</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Validity Start Date *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Validity End Date *</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Docket Charges</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Custom</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Custom 1</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Custom 2</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Custom 3</th>
                                            <th style="background: #fce4d6; color: #0070c0;">Custom 4</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @for($i = 0; $i < 20; $i++)
                                            <tr>
<input type="hidden" name="mode[]" class="row_mode" value="{{ old('mode.'.$i, '') }}">
                                                <td class="char-10">
                                                    <input type="text" name="lane_name[]" id="lane_name{{ $i }}"
                                                           value="{{ old('lane_name.'.$i, '') }}" class="lane_name">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="from_code[]" id="from_code{{ $i }}"
                                                           value="{{ old('from_code.'.$i, '') }}" class="from_code from_code">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="from_name[]" id="from_name{{ $i }}"
                                                           value="{{ old('from_name.'.$i, '') }}" class="from_name" readonly>
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="origin_region[]" id="origin_region{{ $i }}"
                                                           value="{{ old('origin_region.'.$i, '') }}" class="origin_region">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="to_name[]" id="to_name{{ $i }}"
                                                           value="{{ old('to_name.'.$i, '') }}" class="to_name" readonly>
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="to_code[]" id="to_code{{ $i }}"
                                                           value="{{ old('to_code.'.$i, '') }}" class="to_code to_code">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="destination_region[]" id="destination_region{{ $i }}"
                                                           value="{{ old('destination_region.'.$i, '') }}" class="destination_region">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="vendor_code[]" id="vendor_code{{ $i }}"
                                                           value="{{ old('vendor_code.'.$i, '') }}" class="vendor_code">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="vendor_name[]" id="vendor_name{{ $i }}"
                                                           value="{{ old('vendor_name.'.$i, '') }}" class="vendor_name">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="chargeable_weight[]" id="chargeable_weight{{ $i }}"
                                                           value="{{ old('chargeable_weight.'.$i, '') }}" class="chargeable_weight" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="cft_value[]" id="cft_value{{ $i }}"
                                                           value="{{ old('cft_value.'.$i, '') }}" class="cft_value" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="cft_weight[]" id="cft_weight{{ $i }}"
                                                           value="{{ old('cft_weight.'.$i, '') }}" class="cft_weight" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="rate_logic[]" id="rate_logic{{ $i }}"
                                                           value="{{ old('rate_logic.'.$i, '') }}" class="rate_logic" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="pickup_charges[]" id="pickup_charges{{ $i }}"
                                                           value="{{ old('pickup_charges.'.$i, '') }}" class="pickup_charges" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="delivery_charges[]" id="delivery_charges{{ $i }}"
                                                           value="{{ old('delivery_charges.'.$i, '') }}" class="delivery_charges" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="detention_rate[]" id="detention_rate{{ $i }}"
                                                           value="{{ old('detention_rate.'.$i, '') }}" class="detention_rate" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="fov_on_base_rate[]" id="fov_on_base_rate{{ $i }}"
                                                           value="{{ old('fov_on_base_rate.'.$i, '') }}" class="fov_on_base_rate" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="fuel_surcharge_on_base_rate[]" id="fuel_surcharge_on_base_rate{{ $i }}"
                                                           value="{{ old('fuel_surcharge_on_base_rate.'.$i, '') }}" class="fuel_surcharge_on_base_rate" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="oda[]" id="oda{{ $i }}"
                                                           value="{{ old('oda.'.$i, '') }}" class="oda" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="oda_1[]" id="oda_1{{ $i }}"
                                                           value="{{ old('oda_1.'.$i, '') }}" class="oda_1" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="oda_2[]" id="oda_2{{ $i }}"
                                                           value="{{ old('oda_2.'.$i, '') }}" class="oda_2" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="minimum_chargeable_weight_case[]" id="minimum_chargeable_weight_case{{ $i }}"
                                                           value="{{ old('minimum_chargeable_weight_case.'.$i, '') }}" class="minimum_chargeable_weight_case" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="validity_start_date[]" id="validity_start_date{{ $i }}"
                                                           value="{{ old('validity_start_date.'.$i, '') }}" class="validity_start_date" placeholder="YYYY-MM-DD">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="validity_end_date[]" id="validity_end_date{{ $i }}"
                                                           value="{{ old('validity_end_date.'.$i, '') }}" class="validity_end_date" placeholder="YYYY-MM-DD">
                                                </td>
                                                <td class="char-10">
                                                    <input type="number" name="docket_charges[]" id="docket_charges{{ $i }}"
                                                           value="{{ old('docket_charges.'.$i, '') }}" class="docket_charges" step="0.01" min="0">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="custom[]" id="custom{{ $i }}"
                                                           value="{{ old('custom.'.$i, '') }}" class="custom">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="custom_1[]" id="custom_1{{ $i }}"
                                                           value="{{ old('custom_1.'.$i, '') }}" class="custom_1">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="custom_2[]" id="custom_2{{ $i }}"
                                                           value="{{ old('custom_2.'.$i, '') }}" class="custom_2">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="custom_3[]" id="custom_3{{ $i }}"
                                                           value="{{ old('custom_3.'.$i, '') }}" class="custom_3">
                                                </td>
                                                <td class="char-10">
                                                    <input type="text" name="custom_4[]" id="custom_4{{ $i }}"
                                                           value="{{ old('custom_4.'.$i, '') }}" class="custom_4">
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" id="saveRates">
                                    <i class="fas fa-save"></i> Submit Transport Rates
                                </button>
                            </div>
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

                // Apply the single selected mode to all rows before submitting.
                function updateRowModes() {
                    const selectedMode = $('#global_mode').val();
                    $('#table tbody .row_mode').val(selectedMode);
                }

                $('#global_mode').on('change', updateRowModes);
                updateRowModes();

                // Paste one or more rows/columns copied from Excel.
                $('#table').on('paste', 'input, select', function (e) {

                    const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                    const pastedData = clipboardData.getData('Text');

                    if (!pastedData.includes('\t') && !pastedData.includes('\n')) {
                        return;
                    }

                    e.preventDefault();

                    const rows = pastedData.replace(/\r/g, '').split('\n').filter(row => row.length > 0);
                    const startCell = this.closest('td');
                    const startRow = startCell.parentElement;
                    const table = document.getElementById('table');
                    const rowIndex = Array.from(table.tBodies[0].rows).indexOf(startRow);
                    const colIndex = Array.from(startRow.cells).indexOf(startCell);

                    rows.forEach(function (rowData, i) {

                        const tr = table.tBodies[0].rows[rowIndex + i];
                        if (!tr) return;

                        rowData.split('\t').forEach(function (value, j) {

                            const td = tr.cells[colIndex + j];
                            if (!td) return;

                            const input = td.querySelector('input, select');
                            if (!input || input.readOnly || input.disabled) return;

                            input.value = value.trim();
                            $(input).trigger('change');
                        });
                    });
                });

                // Look up location names from Siteplant after From/To Code changes.
                $('#table').on('change blur', '.from_code, .to_code', function () {

                    const input = $(this);
                    const row = input.closest('tr');
                    const code = $.trim(input.val());
                    const target = input.hasClass('from_code') ? '.from_name' : '.to_name';

                    row.find(target).val('');
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
                                return;
                            }

                            input.removeClass('is-invalid');
                            row.find(target).val(res.plant_name || res.name || '');
                        },
                        error: function () {
                            input.addClass('is-invalid');
                        }
                    });
                });

                $('#postform').on('submit', function (e) {

                    const selectedMode = $.trim($('#global_mode').val());

                    if (!selectedMode) {
                        e.preventDefault();
                        Swal.fire('Validation', 'Please select a Transport Mode before entering rates.', 'warning');
                        $('#global_mode').focus();
                        return;
                    }

                    updateRowModes();

                    let hasData = false;
                    let invalidRow = null;

                    $('#table tbody tr').each(function () {
                        const row = $(this);
                        const fromCode = $.trim(row.find('.from_code').val());
                        const toCode = $.trim(row.find('.to_code').val());
                        const vendorCode = $.trim(row.find('.vendor_code').val());
                        const rate = $.trim(row.find('.rate_logic').val());
                        const startDate = $.trim(row.find('.validity_start_date').val());
                        const endDate = $.trim(row.find('.validity_end_date').val());

                        const populated = row.find('input:not(.row_mode), select').filter(function () {
                            return $.trim($(this).val()) !== '';
                        }).length > 0;

                        if (!populated) return;
                        hasData = true;

                        if (!fromCode || !toCode || !vendorCode || !rate || !startDate || !endDate) {
                            invalidRow = row.index() + 1;
                            return false;
                        }
                    });

                    if (!hasData || invalidRow !== null) {
                        e.preventDefault();
                        Swal.fire('Validation', !hasData ? 'Please enter at least one rate.' : 'Complete required fields in row ' + invalidRow + '.', 'warning');
                        return;
                    }

                    $('#saveRates').prop('disabled', true).text('Saving...');
                });
            });
        </script>
    @endpush

@endsection
