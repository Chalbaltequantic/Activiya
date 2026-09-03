@extends('admin.admin')

@section('bodycontent')

<link rel="stylesheet"
      href="{{ asset('backend/assets/manual_upload_setting.css') }}">


<!-- Content Header (Page header) -->
<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="m-0">
                    BIN Master
                </h1>

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">BIN Master</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->


<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('errorRows'))
                        <div class="alert alert-warning">
                            <b>Skipped Rows:</b>
                            <ul>
                                @foreach(session('errorRows') as $err)
                                    <li>
                                        Row {{ $err['row'] }}:
                                        {{ $err['reason'] }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        <div class="row">

            <div class="col-md-12">

                <div class="card">

                    <div class="card-header p-2">

                        <ul class="nav nav-pills">
							<li class="nav-item">
								<a class="nav-link active" href="{{ route('admin.digiwim.bin-master.manual-upload') }}">Manual Upload</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="{{ route('admin.digiwim.bin-master.datalist') }}">BIN Master List </a>
							</li>
                        </ul>

                    </div>


                    <div class="card-body">

                        <div class="tab-content">

                            <div class="active tab-pane"
                                 id="activity">


                                <form action="{{ route('admin.digiwim.bin-master.save-manual-upload') }}" method="post" name="addfrm" id="postform">
                                    @csrf
                                    <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
                                        <div class="excel-wrapper">
                                            <table class="table table-bordered excel-table" id="table">

                                                <thead>
                                                    <tr>
                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            Plant Code*
                                                        </th>
                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            Plant Name
                                                        </th>
                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN No.*
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Type
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Status
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            Storage<br>
                                                            Location<br>
                                                            (Virtual)
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            Storage<br>
                                                            Section<br>
                                                            (Floor)
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Location
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Length<br>
                                                            (Inch)*
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Width<br>
                                                            (Inch)*
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Height<br>
                                                            (Inch)*
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Volume<br>
                                                            (CFT) Cap
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Volume<br>
                                                            (CFT) Cap 2
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Weight<br>
                                                            (KG) Cap*
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            BIN Weight<br>
                                                            (KG) Cap 2
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            custom1
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            custom2
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            custom3
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            custom4
                                                        </th>

                                                        <th style="background:#fce4d6;color:#0070c0;">
                                                            custom5
                                                        </th>

                                                    </tr>

                                                </thead>


                                                <tbody>

                                                    @for ($i = 0; $i < 20; $i++)

                                                        <tr>

                                                            <td class="char-10">

                                                                <input
                                                                    type="text"
                                                                    name="plant_code[]"
                                                                    id="plant_code{{ $i }}"
                                                                    value="{{ old('plant_code.' . $i) }}"
                                                                    class="plant_code"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>

                                                            <td>

                                                                <input
                                                                    type="text"
                                                                    name="plant_name[]"
                                                                    id="plant_name{{ $i }}"
                                                                    value="{{ old('plant_name.' . $i) }}"
                                                                    class="plant_name"
                                                                    readonly>

                                                            </td>

                                                            <td class="char-10">

                                                                <input
                                                                    type="text"
                                                                    name="bin_no[]"
                                                                    id="bin_no{{ $i }}"
                                                                    value="{{ old('bin_no.' . $i) }}"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>

                                                            <td>

                                                                <input
                                                                    type="text"
                                                                    name="bin_type[]"
                                                                    id="bin_type{{ $i }}"
                                                                    value="{{ old('bin_type.' . $i) }}">

                                                            </td>

                                                            <td>

                                                                <select
                                                                    name="bin_status[]"
                                                                    id="bin_status{{ $i }}">

                                                                    <option
                                                                        value="Active"
                                                                        {{ old('bin_status.' . $i, 'Active') == 'Active' ? 'selected' : '' }}>
                                                                        Active
                                                                    </option>

                                                                    <option
                                                                        value="Inactive"
                                                                        {{ old('bin_status.' . $i) == 'Inactive' ? 'selected' : '' }}>
                                                                        Inactive
                                                                    </option>

                                                                </select>

                                                            </td>

                                                            <td>

                                                                <input
                                                                    type="text"
                                                                    name="storage_location[]"
                                                                    id="storage_location{{ $i }}"
                                                                    value="{{ old('storage_location.' . $i) }}">

                                                            </td>

                                                            <td>

                                                                <input
                                                                    type="text"
                                                                    name="storage_section[]"
                                                                    id="storage_section{{ $i }}"
                                                                    value="{{ old('storage_section.' . $i) }}">

                                                            </td>

                                                            <td>

                                                                <input
                                                                    type="text"
                                                                    name="bin_location[]"
                                                                    id="bin_location{{ $i }}"
                                                                    value="{{ old('bin_location.' . $i) }}">

                                                            </td>

                                                            <td class="char-6">

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    min="0"
                                                                    name="bin_length[]"
                                                                    id="bin_length{{ $i }}"
                                                                    value="{{ old('bin_length.' . $i) }}"
                                                                    class="bin_length dimension"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>

                                                            <td class="char-6">

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    min="0"
                                                                    name="bin_width[]"
                                                                    id="bin_width{{ $i }}"
                                                                    value="{{ old('bin_width.' . $i) }}"
                                                                    class="bin_width dimension"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>

                                                            <td class="char-6">

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    min="0"
                                                                    name="bin_height[]"
                                                                    id="bin_height{{ $i }}"
                                                                    value="{{ old('bin_height.' . $i) }}"
                                                                    class="bin_height dimension"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>


                                                            {{-- Volume CFT --}}
                                                            <td class="char-10">

                                                                <input
                                                                    type="text"
                                                                    name="bin_volume_cft_cap[]"
                                                                    id="bin_volume_cft_cap{{ $i }}"
                                                                    value="{{ old('bin_volume_cft_cap.' . $i) }}"
                                                                    class="bin_volume_cft_cap"
                                                                    readonly>

                                                            </td>


                                                            {{-- Volume CFT +30 --}}
                                                            <td class="char-10">

                                                                <input
                                                                    type="text"
                                                                    name="bin_volume_cft_cap_2[]"
                                                                    id="bin_volume_cft_cap_2{{ $i }}"
                                                                    value="{{ old('bin_volume_cft_cap_2.' . $i) }}"
                                                                    class="bin_volume_cft_cap_2"
                                                                    readonly>

                                                            </td>


                                                            {{-- Weight --}}
                                                            <td class="char-10">

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    min="0"
                                                                    name="bin_weight_kg_cap[]"
                                                                    id="bin_weight_kg_cap{{ $i }}"
                                                                    value="{{ old('bin_weight_kg_cap.' . $i) }}"
                                                                    class="bin_weight_kg_cap"
                                                                    {{ $i == 0 ? 'required' : '' }}>

                                                            </td>


                                                            {{-- Weight +30 --}}
                                                            <td class="char-10">

                                                                <input
                                                                    type="text"
                                                                    name="bin_weight_kg_cap_2[]"
                                                                    id="bin_weight_kg_cap_2{{ $i }}"
                                                                    value="{{ old('bin_weight_kg_cap_2.' . $i) }}"
                                                                    class="bin_weight_kg_cap_2"
                                                                    readonly>

                                                            </td>


                                                            <td>
                                                                <input
                                                                    type="text"
                                                                    name="custom1[]"
                                                                    value="{{ old('custom1.' . $i) }}">
                                                            </td>

                                                            <td>
                                                                <input
                                                                    type="text"
                                                                    name="custom2[]"
                                                                    value="{{ old('custom2.' . $i) }}">
                                                            </td>

                                                            <td>
                                                                <input
                                                                    type="text"
                                                                    name="custom3[]"
                                                                    value="{{ old('custom3.' . $i) }}">
                                                            </td>

                                                            <td>
                                                                <input
                                                                    type="text"
                                                                    name="custom4[]"
                                                                    value="{{ old('custom4.' . $i) }}">
                                                            </td>

                                                            <td>
                                                                <input
                                                                    type="text"
                                                                    name="custom5[]"
                                                                    value="{{ old('custom5.' . $i) }}">
                                                            </td>

                                                        </tr>

                                                    @endfor

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>


                                    <div class="row text-right mt-3">

                                        <div class="col-md-10">

                                            <button
                                                type="submit"
                                                class="btn btn-primary text-right"
                                                name="submit">

                                                Submit

                                            </button>

                                        </div>

                                    </div>


                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>

</div>


@push('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

$(document).ready(function () {

    const fetchPlantUrl =
        "{{ route('admin.digiwim.bin-master.fetch-plant') }}";

    function calculateRow(row) {

        let length =
            parseFloat(row.find('.bin_length').val()) || 0;

        let width =
            parseFloat(row.find('.bin_width').val()) || 0;

        let height =
            parseFloat(row.find('.bin_height').val()) || 0;

        let weight =
            parseFloat(row.find('.bin_weight_kg_cap').val()) || 0;


        if (length > 0 && width > 0 && height > 0) {

            let volume =
                (length / 12) *
                (width / 12) *
                (height / 12);

            let volumeCap2 =
                volume * 1.30;

            row.find('.bin_volume_cft_cap')
                .val(volume.toFixed(3));

            row.find('.bin_volume_cft_cap_2')
                .val(volumeCap2.toFixed(3));

        } else {

            row.find('.bin_volume_cft_cap').val('');

            row.find('.bin_volume_cft_cap_2').val('');
        }


        /*
         * Weight + 30%
         */

        if (weight > 0) {

            let weightCap2 =
                weight * 1.30;

            row.find('.bin_weight_kg_cap_2')
                .val(weightCap2.toFixed(2));

        } else {

            row.find('.bin_weight_kg_cap_2').val('');
        }
    }


    $('#table').on(
        'input change keyup',
        '.dimension, .bin_weight_kg_cap',
        function () {

            let row = $(this).closest('tr');

            calculateRow(row);
        }
    );


  
    function fetchPlantData(row, callback = null) {

        let plantCode =
            $.trim(row.find('.plant_code').val());

        if (plantCode === '') {

            row.find('.plant_name').val('');

            if (callback) {
                callback();
            }

            return;
        }


        $.ajax({

            url: fetchPlantUrl,

            type: "POST",

            data: {

                _token: "{{ csrf_token() }}",

                plant_code: plantCode
            },

            success: function (res) {

                if (res.error) {

                    row.find('.plant_name').val('');

                    Swal.fire({
                        icon: 'error',
                        title: 'Plant Not Found',
                        text: res.error
                    });

                    if (callback) {
                        callback();
                    }

                    return;
                }


                row.find('.plant_name')
                    .val(res.plant_name ?? '');


                if (callback) {
                    callback();
                }
            },

            error: function (xhr) {

                console.log(
                    'Plant AJAX Error:',
                    xhr.responseText
                );

                row.find('.plant_name').val('');

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Unable to fetch Plant details.'
                });


                if (callback) {
                    callback();
                }
            }
        });
    }


    $('#table').on(
        'change blur',
        '.plant_code',
        function () {

            let row =
                $(this).closest('tr');

            fetchPlantData(row);
        }
    );


    $('#table').on('paste', 'input', function (e) {

        e.preventDefault();

        const clipboardData =
            e.originalEvent.clipboardData ||
            window.clipboardData;

        const pastedData =
            clipboardData.getData('Text');

        const rows =
            pastedData
                .split(/\r\n|\n|\r/)
                .filter(row => row.length > 0);


        const startInput = this;

        const table =
            document.getElementById('table');

        const startCell =
            startInput.closest('td');

        const startRow =
            startCell.parentElement;


        const rowIndex =
            Array.from(table.rows)
                .indexOf(startRow);

        const colIndex =
            Array.from(startRow.cells)
                .indexOf(startCell);


        rows.forEach((rowData, i) => {

            const cols =
                rowData.split('\t');

            const tr =
                table.rows[rowIndex + i];

            if (!tr) {
                return;
            }


            cols.forEach((col, j) => {

                const td =
                    tr.cells[colIndex + j];

                if (!td) {
                    return;
                }


                const input =
                    td.querySelector('input, select');

                if (input) {

                    input.value =
                        col.trim();
                }
            });
        });


        /*After paste:
          Calculate dimensions/weight
          Fetch Plant Names
        */

        setTimeout(function () {

            let rowsToFetch = [];


            $('#table tbody tr').each(function () {

                let row =
                    $(this);


                /*
                 * Calculate every row having dimensions
                 */

                calculateRow(row);


                /*
                 * Plant AJAX only for rows with Plant Code
                 */

                if (
                    $.trim(
                        row.find('.plant_code').val()
                    ) !== ''
                ) {

                    rowsToFetch.push(row);
                }
            });


            if (rowsToFetch.length === 0) {
                return;
            }


            Swal.fire({

                title: 'Processing rows...',

                text: 'Please wait while Plant Names are fetched.',

                allowOutsideClick: false,

                allowEscapeKey: false,

                didOpen: () => {

                    Swal.showLoading();
                }
            });


            let completed = 0;


            rowsToFetch.forEach(function (row) {

                fetchPlantData(row, function () {

                    completed++;


                    if (
                        completed >=
                        rowsToFetch.length
                    ) {

                        Swal.close();
                    }
                });
            });

        }, 200);

    });


    /* Recalculate old input after validation */

    $('#table tbody tr').each(function () {

        calculateRow(
            $(this)
        );
    });

});

</script>

@endpush

@endsection