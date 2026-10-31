@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Load/Unload Master Manual Upload</h1></div>
            <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Load/Unload Master Manual Upload</li></ol></div>
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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.load-unload-master.manual') }}">Manual Upload</a></li>
                    <li class="nav-item"><a class="nav-link " href="{{ route('admin.load-unload-master.list') }}">Load/Unload Master List</a></li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.load-unload-master.manual.save') }}" method="POST" id="postform">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="global_mode">Transport Mode *</label>
                            <select name="mode" id="global_mode" class="form-control" required>
                                <option value="">Select Mode</option>
                                <option value="FTL" {{ old('mode') == 'FTL' ? 'selected' : '' }}>FTL</option>
                                <option value="PTL" {{ old('mode') == 'PTL' ? 'selected' : '' }}>PTL</option>
                                <option value="AIR" {{ old('mode') == 'AIR' ? 'selected' : '' }}>AIR</option>
                                
                            </select>
                        </div>
                    </div>
                    <p class="text-muted">Select Mode once. Enter up to 20 rows or paste from Excel. Names and descriptions are fetched from master tables.</p>
                    <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
                        <div class="excel-wrapper">
                            <table class="table table-bordered excel-table" id="table">
                                <thead>
                                    <tr>
                                        <th style="background: #fce4d6; color: #0070c0;">From Code *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">From Name</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Origin Region</th>
                                        <th style="background: #fce4d6; color: #0070c0;">To Name</th>
                                        <th style="background: #fce4d6; color: #0070c0;">To Code *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Destination Region</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Vendor Code *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Truck Code *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Truck Description</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Charge Type (Load/Unload) *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Rate Type (Fix/Per Case/KG) *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Rate *</th>
                                        <th style="background: #fce4d6; color: #0070c0;">Load/Unload Limit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 20; $i++)
                                        <tr>
                                            <td class="char-10">
                                                <input type="text" name="from_code[]" id="from_code{{ $i }}" class="from_code" value="{{ old('from_code.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="from_name[]" id="from_name{{ $i }}" class="from_name" value="{{ old('from_name.'.$i, '') }}" readonly>
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="origin_region[]" id="origin_region{{ $i }}" class="origin_region" value="{{ old('origin_region.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="to_name[]" id="to_name{{ $i }}" class="to_name" value="{{ old('to_name.'.$i, '') }}" readonly>
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="to_code[]" id="to_code{{ $i }}" class="to_code" value="{{ old('to_code.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="destination_region[]" id="destination_region{{ $i }}" class="destination_region" value="{{ old('destination_region.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="vendor_code[]" id="vendor_code{{ $i }}" class="vendor_code" value="{{ old('vendor_code.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="vendor_name[]" id="vendor_name{{ $i }}" class="vendor_name" value="{{ old('vendor_name.'.$i, '') }}" readonly>
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="truck_code[]" id="truck_code{{ $i }}" class="truck_code" value="{{ old('truck_code.'.$i, '') }}">
                                            </td>
                                            <td class="char-10">
                                                <input type="text" name="truck_description[]" id="truck_description{{ $i }}" class="truck_description" value="{{ old('truck_description.'.$i, '') }}" readonly>
                                            </td>
                                            <td class="char-10">
                                                <select name="charge_type[]" id="charge_type{{ $i }}" class="charge_type form-control form-control-sm">
                                                    <option value="">Select</option>
                                                    <option value="Load" {{ old('charge_type.'.$i) == 'Load' ? 'selected' : '' }}>Load</option>
                                                    <option value="Unload" {{ old('charge_type.'.$i) == 'Unload' ? 'selected' : '' }}>Unload</option>
                                                </select>
                                            </td>
                                            <td class="char-10">
                                                <select name="rate_type[]" id="rate_type{{ $i }}" class="rate_type form-control form-control-sm">
                                                    <option value="">Select</option>
                                                    <option value="Fix" {{ old('rate_type.'.$i) == 'Fix' ? 'selected' : '' }}>Fix</option>
                                                    <option value="Per Case" {{ old('rate_type.'.$i) == 'Per Case' ? 'selected' : '' }}>Per Case</option>
                                                    <option value="KG" {{ old('rate_type.'.$i) == 'KG' ? 'selected' : '' }}>KG</option>
                                                </select>
                                            </td>
                                            <td class="char-10">
                                                <input type="number" name="rate[]" id="rate{{ $i }}" class="rate" value="{{ old('rate.'.$i, '') }}" step="0.0001" min="0">
                                            </td>
                                            <td class="char-10">
                                                <input type="number" name="load_unload_limit[]" id="load_unload_limit{{ $i }}" class="load_unload_limit" value="{{ old('load_unload_limit.'.$i, '') }}" step="0.0001" min="0">
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary" id="saveRates"><i class="fas fa-save"></i> Submit Load/Unload Rates</button>
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
        const lookupUrl = "{{ route('admin.load-unload-master.lookup') }}";

        function lookupCode(input) {
            const field = input.attr('class').split(' ')[0];
            const row = input.closest('tr');
            const code = $.trim(input.val());
            const target = {
                from_code: '.from_name',
                to_code: '.to_name',
                vendor_code: '.vendor_name',
                truck_code: '.truck_description'
            }[field];

            row.find(target).val('');
            input.removeClass('is-invalid');

            if (!code) {
                return;
            }

            $.ajax({
                url: lookupUrl,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    type: field,
                    code: code
                },
                success: function (response) {
                    row.find(target).val(response.name || '');
                },
                error: function () {
                    input.addClass('is-invalid');
                }
            });
        }

        $('#table').on('change blur', '.from_code, .to_code, .vendor_code, .truck_code', function () {
            lookupCode($(this));
        });

        $('#table').on('paste', 'input', function (event) {
            const clipboard = event.originalEvent.clipboardData || window.clipboardData;
            const pastedText = clipboard.getData('Text');

            if (!pastedText.includes('\t') && !pastedText.includes('\n')) {
                return;
            }

            event.preventDefault();

            const currentCell = this.closest('td');
            const currentRow = currentCell.parentElement;
            const table = document.getElementById('table');
            const rowIndex = Array.from(table.tBodies[0].rows).indexOf(currentRow);
            const columnIndex = Array.from(currentRow.cells).indexOf(currentCell);
            const rows = pastedText.replace(/\r/g, '').split('\n').filter(row => row.length > 0);

            rows.forEach(function (rowData, i) {
                const tr = table.tBodies[0].rows[rowIndex + i];

                if (!tr) {
                    return;
                }

                rowData.split('\t').forEach(function (value, j) {
                    const td = tr.cells[columnIndex + j];

                    if (!td) {
                        return;
                    }

                    const input = td.querySelector('input, select');

                    if (!input || input.readOnly || input.disabled) {
                        return;
                    }

                    input.value = value.trim();

                    if (input.matches('.from_code, .to_code, .vendor_code, .truck_code')) {
                        $(input).trigger('change');
                    }
                });
            });
        });

        $('#postform').on('submit', function (event) {
            if (!$('#global_mode').val()) {
                event.preventDefault();
                Swal.fire('Validation', 'Please select Transport Mode.', 'warning');
                return;
            }

            let hasData = false;
            let invalidRow = null;

            $('#table tbody tr').each(function () {
                const row = $(this);
                const populated = row.find('input:not([readonly]), select').filter(function () {
                    return $.trim($(this).val()) !== '';
                }).length > 0;

                if (!populated) {
                    return;
                }

                hasData = true;

                if (!row.find('.from_code').val() || !row.find('.to_code').val() || !row.find('.vendor_code').val() || !row.find('.truck_code').val() || !row.find('.charge_type').val() || !row.find('.rate_type').val() || row.find('.rate').val() === '') {
                    invalidRow = row.index() + 1;
                    return false;
                }
            });

            if (!hasData || invalidRow !== null) {
                event.preventDefault();
                Swal.fire('Validation', !hasData ? 'Please enter at least one rate.' : 'Complete required fields in row ' + invalidRow + '.', 'warning');
                return;
            }

            $('#saveRates').prop('disabled', true).text('Saving...');
        });
    });
</script>
@endpush
@endsection
