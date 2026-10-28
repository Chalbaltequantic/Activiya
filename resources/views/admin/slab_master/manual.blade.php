@extends('admin.admin')
@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">
<div class="content-header"><div class="container-fluid"><div class="row mb-2">
    <div class="col-sm-6"><h1 class="m-0">Slab Master Manual Upload</h1></div>
    <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Slab Master Manual Upload</li></ol></div>
</div></div></div>
<div class="content"><div class="container-fluid">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if(session('errorRows'))<div class="alert alert-warning"><b>Skipped Rows:</b><ul>@foreach(session('errorRows') as $err)<li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-header p-2"><ul class="nav nav-pills">
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.index') }}">XLS Upload</a></li>
<li class="nav-item"><a class="nav-link active" href="{{ route('admin.slab-master.manual') }}">Manual Upload</a></li>
<li class="nav-item"><a class="nav-link " href="{{ route('admin.slab-master.list') }}">Slab Master List</a></li>
</ul></div><div class="card-body">
<form action="{{ route('admin.slab-master.manual.save') }}" method="POST" id="postform">@csrf
<div class="row mb-3"><div class="col-md-4"><label for="global_mode"><strong>Transport Mode *</strong></label>
<select name="mode" id="global_mode" class="form-control" required><option value="">Select Transport Mode</option>
<option value="PTL" {{ old('mode') == 'PTL' ? 'selected' : '' }}>PTL</option>
<option value="AIR" {{ old('mode') == 'AIR' ? 'selected' : '' }}>AIR</option>
<option value="ROAD" {{ old('mode') == 'ROAD' ? 'selected' : '' }}>ROAD</option>
<option value="RAIL" {{ old('mode') == 'RAIL' ? 'selected' : '' }}>RAIL</option>
<option value="SEA" {{ old('mode') == 'SEA' ? 'selected' : '' }}>SEA</option>
<option value="COURIER" {{ old('mode') == 'COURIER' ? 'selected' : '' }}>COURIER</option>
<option value="FTL" {{ old('mode') == 'FTL' ? 'selected' : '' }}>FTL</option>
</select></div></div>
<p class="text-muted">Select Mode once, then enter rows or paste data copied from Excel. Leave unused rows empty.</p>
<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container"><div class="excel-wrapper"><table class="table table-bordered excel-table" id="table"><thead><tr><th style="background:#fce4d6;color:#0070c0">Sl. No.</th>
<th style="background:#fce4d6;color:#0070c0">From Code *</th>
<th style="background:#fce4d6;color:#0070c0">From Name</th>
<th style="background:#fce4d6;color:#0070c0">Origin Region</th>
<th style="background:#fce4d6;color:#0070c0">To Name</th>
<th style="background:#fce4d6;color:#0070c0">To Code *</th>
<th style="background:#fce4d6;color:#0070c0">Des Region</th>
<th style="background:#fce4d6;color:#0070c0">Vendor Code *</th>
<th style="background:#fce4d6;color:#0070c0">Vendor Name</th>
<th style="background:#fce4d6;color:#0070c0">Truck Code *</th>
<th style="background:#fce4d6;color:#0070c0">Truck Description</th>
<th style="background:#fce4d6;color:#0070c0">Slab 1</th>
<th style="background:#fce4d6;color:#0070c0">Slab 2</th>
<th style="background:#fce4d6;color:#0070c0">Slab 3</th>
<th style="background:#fce4d6;color:#0070c0">Slab 4</th>
<th style="background:#fce4d6;color:#0070c0">Slab 5</th>
<th style="background:#fce4d6;color:#0070c0">Slab 6</th>
<th style="background:#fce4d6;color:#0070c0">Slab 7</th>
<th style="background:#fce4d6;color:#0070c0">Slab 8</th>
<th style="background:#fce4d6;color:#0070c0">Slab 9</th>
<th style="background:#fce4d6;color:#0070c0">Slab 10</th>
<th style="background:#fce4d6;color:#0070c0">Slab 1 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 2 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 3 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 4 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 5 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 6 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 7 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 8 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 9 Rate</th>
<th style="background:#fce4d6;color:#0070c0">Slab 10 Rate</th>
</tr></thead><tbody>
@for($i = 0; $i < 20; $i++)
<tr><td>{{ $i + 1 }}</td>
<td class="char-10"><input type="text" name="from_code[]" id="from_code{{ $i }}" class="from_code" value="{{ old('from_code.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="from_name[]" id="from_name{{ $i }}" class="from_name" value="{{ old('from_name.'.$i, '') }}" readonly></td>
<td class="char-10"><input type="text" name="origin_region[]" id="origin_region{{ $i }}" class="origin_region" value="{{ old('origin_region.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="to_name[]" id="to_name{{ $i }}" class="to_name" value="{{ old('to_name.'.$i, '') }}" readonly></td>
<td class="char-10"><input type="text" name="to_code[]" id="to_code{{ $i }}" class="to_code" value="{{ old('to_code.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="destination_region[]" id="destination_region{{ $i }}" class="destination_region" value="{{ old('destination_region.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="vendor_code[]" id="vendor_code{{ $i }}" class="vendor_code" value="{{ old('vendor_code.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="vendor_name[]" id="vendor_name{{ $i }}" class="vendor_name" value="{{ old('vendor_name.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="truck_code[]" id="truck_code{{ $i }}" class="truck_code" value="{{ old('truck_code.'.$i, '') }}"></td>
<td class="char-10"><input type="text" name="truck_description[]" id="truck_description{{ $i }}" class="truck_description" value="{{ old('truck_description.'.$i, '') }}"></td>
<td class="char-10"><input type="number" name="slab_1[]" id="slab_1{{ $i }}" class="slab_1" value="{{ old('slab_1.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_2[]" id="slab_2{{ $i }}" class="slab_2" value="{{ old('slab_2.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_3[]" id="slab_3{{ $i }}" class="slab_3" value="{{ old('slab_3.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_4[]" id="slab_4{{ $i }}" class="slab_4" value="{{ old('slab_4.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_5[]" id="slab_5{{ $i }}" class="slab_5" value="{{ old('slab_5.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_6[]" id="slab_6{{ $i }}" class="slab_6" value="{{ old('slab_6.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_7[]" id="slab_7{{ $i }}" class="slab_7" value="{{ old('slab_7.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_8[]" id="slab_8{{ $i }}" class="slab_8" value="{{ old('slab_8.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_9[]" id="slab_9{{ $i }}" class="slab_9" value="{{ old('slab_9.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_10[]" id="slab_10{{ $i }}" class="slab_10" value="{{ old('slab_10.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_1_rate[]" id="slab_1_rate{{ $i }}" class="slab_1_rate" value="{{ old('slab_1_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_2_rate[]" id="slab_2_rate{{ $i }}" class="slab_2_rate" value="{{ old('slab_2_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_3_rate[]" id="slab_3_rate{{ $i }}" class="slab_3_rate" value="{{ old('slab_3_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_4_rate[]" id="slab_4_rate{{ $i }}" class="slab_4_rate" value="{{ old('slab_4_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_5_rate[]" id="slab_5_rate{{ $i }}" class="slab_5_rate" value="{{ old('slab_5_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_6_rate[]" id="slab_6_rate{{ $i }}" class="slab_6_rate" value="{{ old('slab_6_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_7_rate[]" id="slab_7_rate{{ $i }}" class="slab_7_rate" value="{{ old('slab_7_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_8_rate[]" id="slab_8_rate{{ $i }}" class="slab_8_rate" value="{{ old('slab_8_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_9_rate[]" id="slab_9_rate{{ $i }}" class="slab_9_rate" value="{{ old('slab_9_rate.'.$i, '') }}" step="0.0001" min="0"></td>
<td class="char-10"><input type="number" name="slab_10_rate[]" id="slab_10_rate{{ $i }}" class="slab_10_rate" value="{{ old('slab_10_rate.'.$i, '') }}" step="0.0001" min="0"></td>
</tr>
@endfor
</tbody></table></div></div>
<div class="text-right mt-3"><button type="submit" class="btn btn-primary" id="saveSlabs"><i class="fas fa-save"></i> Submit Slab Master</button></div></form>
</div></div></div></div>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    const fetchRowUrl = "{{ route('admin.slab-master.lookup') }}";

    $('#table').on('paste', 'input', function (e) {
        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text');
        if (!pastedData.includes('\t') && !pastedData.includes('\n')) return;
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
                const input = td.querySelector('input');
                if (!input || input.readOnly) return;
                input.value = value.trim();
                $(input).trigger('change');
            });
        });
    });

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
            data: { _token: "{{ csrf_token() }}", plant_code: code },
            success: function (res) {
                input.removeClass('is-invalid');
                row.find(target).val(res.plant_name || '');
            },
            error: function () { input.addClass('is-invalid'); }
        });
    });

    $('#postform').on('submit', function (e) {
        if (!$('#global_mode').val()) {
            e.preventDefault();
            Swal.fire('Validation', 'Please select Transport Mode.', 'warning');
            return;
        }
        let hasData = false;
        let invalidRow = null;
        $('#table tbody tr').each(function () {
            const row = $(this);
            const fromCode = $.trim(row.find('.from_code').val());
            const toCode = $.trim(row.find('.to_code').val());
            const vendorCode = $.trim(row.find('.vendor_code').val());
            const truckCode = $.trim(row.find('.truck_code').val());
            if (!fromCode && !toCode && !vendorCode && !truckCode) return;
            hasData = true;
            if (!fromCode || !toCode || !vendorCode || !truckCode) {
                invalidRow = row.index() + 1;
                return false;
            }
        });
        if (!hasData || invalidRow !== null) {
            e.preventDefault();
            Swal.fire('Validation', !hasData ? 'Enter at least one slab record.' : 'Complete required fields in row ' + invalidRow + '.', 'warning');
            return;
        }
        $('#saveSlabs').prop('disabled', true).text('Saving...');
    });
});
</script>
@endpush
@endsection
