@extends('admin.admin')

@section('bodycontent')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}
.insurance-data-table {
    font-size: 12px;
}
.insurance-data-table table {
    min-width: 3000px;
    font-size: 12px;
    margin-bottom: 0;
}
.insurance-data-table th,
.insurance-data-table td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 5px 10px;
}
.insurance-data-table thead th {
    position: sticky;
    top: 0;
    background: #fce4d6;
    color: #0070c0;
    z-index: 10;
}
.insurance-data-table thead th.file-column {
    background: #c6e0b4;
    color: #0070c0;
}
.table-container {
    max-height: 550px;
    overflow-y: auto;
    border: 1px solid #ccc;
}
.file-action-box {
    min-width: 190px;
}
.file-action-box input {
    font-size: 11px;
    max-width: 190px;
}
.btn-xs {
    padding: 2px 5px;
    font-size: 11px;
}
.history-table th,
.history-table td {
    font-size: 12px;
    vertical-align: middle;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Insurance - T Vendor</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">T Vendor</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.insurance.t-vendor') }}">T Vendor</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('admin.insurance.t-vendor') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Invoice / LR / Location / Transporter">
                                </div>

                                <div class="col-md-2">
                                    <select name="per_page" class="form-control">
                                        @foreach([10,25,50,100] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>

                                    <a href="{{ route('admin.insurance.t-vendor') }}" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive-fixed border rounded bg-white insurance-data-table table-container">
                            <table class="table table-bordered border-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Loss Date</th>
                                        <th>Nature of Claim</th>
                                        <th>From Code</th>
                                        <th>From Location</th>
                                        <th>To Code</th>
                                        <th>To Location</th>
                                        <th>Invoice No.</th>
                                        <th>Invoice Date</th>
                                        <th>Transporter</th>
                                        <th>LR No.</th>
                                        <th>LR Date</th>
                                        <th>Damage Value</th>
                                        <th>Shortage Value</th>
                                        <th>Total Value</th>
                                        <th class="file-column">Invoice File</th>
                                        <th class="file-column">POD/LR Copy</th>
                                        <th class="file-column">Photographs</th>
                                        <th class="file-column">COF</th>
                                        <th class="file-column">FIR / Police Report</th>
                                        <th class="file-column">Fire Report<br>(If Applicable)</th>
                                        <th class="file-column">FIR Closure Report<br>(If Applicable)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($datalist as $index => $insurance)

                                    <tr>

                                        <td>{{ $datalist->firstItem() + $index }}</td>

                                        <td>
                                            {{ $insurance->loss_date ? $insurance->loss_date->format('Y-m-d') : '' }}
                                        </td>

                                        <td>
                                            {{ $insurance->nature_of_claim }}
                                        </td>

                                        <td>
                                            {{ $insurance->from_location_code }}
                                        </td>

                                        <td>
                                            {{ $insurance->from_location }}
                                        </td>

                                        <td>
                                            {{ $insurance->to_location_code }}
                                        </td>

                                        <td>
                                            {{ $insurance->to_location }}
                                        </td>

                                        <td>
                                            {{ $insurance->invoice_no }}
                                        </td>

                                        <td>
                                            {{ $insurance->invoice_date ? $insurance->invoice_date->format('Y-m-d') : '' }}
                                        </td>

                                        <td>
                                            {{ $insurance->transporter_name }}
                                        </td>

                                        <td>
                                            {{ $insurance->lr_no }}
                                        </td>

                                        <td>
                                            {{ $insurance->lr_date ? $insurance->lr_date->format('Y-m-d') : '' }}
                                        </td>

                                        <td>
                                            {{ number_format((float)$insurance->damage_value, 2) }}
                                        </td>

                                        <td>
                                            {{ number_format((float)$insurance->shortage_value, 2) }}
                                        </td>

                                        <td>
                                            {{ number_format((float)$insurance->total_value, 2) }}
                                        </td>

                                        <td>
                                            @if($insurance->invoice_file)
                                            <a href="{{ asset($insurance->invoice_file) }}" target="_blank" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @else
                                            <span class="text-muted">Not Uploaded</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($insurance->pod_lr_copy)
                                            <a href="{{ asset($insurance->pod_lr_copy) }}" target="_blank" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @else
                                            <span class="text-muted">Not Uploaded</span>
                                            @endif
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-info btn-xs view-photos" data-id="{{ $insurance->id }}" data-invoice="{{ $insurance->invoice_no }}">
                                                <i class="fas fa-images"></i>
                                                View Photos ({{ $insurance->photographs->count() }})
                                            </button>
                                        </td>

                                        <td>
                                            <div class="file-action-box" id="docBox{{ $insurance->id }}_cof">

                                                @if($insurance->cof_file)

                                                <a href="{{ asset($insurance->cof_file) }}" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                <button type="button" class="btn btn-danger btn-xs delete-tvendor-file" data-id="{{ $insurance->id }}" data-type="cof">
                                                    Delete
                                                </button>

                                                @else

                                                <input type="file" class="form-control-file tvendor-file" data-id="{{ $insurance->id }}" data-type="cof" accept=".pdf,.jpg,.jpeg,.png">

                                                @endif

                                                <button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="{{ $insurance->id }}" data-type="cof">
                                                    History
                                                </button>

                                            </div>
                                        </td>

                                        <td>
                                            <div class="file-action-box" id="docBox{{ $insurance->id }}_fir_police_report">

                                                @if($insurance->fir_police_report_file)

                                                <a href="{{ asset($insurance->fir_police_report_file) }}" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                <button type="button" class="btn btn-danger btn-xs delete-tvendor-file" data-id="{{ $insurance->id }}" data-type="fir_police_report">
                                                    Delete
                                                </button>

                                                @else

                                                <input type="file" class="form-control-file tvendor-file" data-id="{{ $insurance->id }}" data-type="fir_police_report" accept=".pdf,.jpg,.jpeg,.png">

                                                @endif

                                                <button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="{{ $insurance->id }}" data-type="fir_police_report">
                                                    History
                                                </button>

                                            </div>
                                        </td>

                                        <td>
                                            <div class="file-action-box" id="docBox{{ $insurance->id }}_fire_report">

                                                @if($insurance->fire_report_file)

                                                <a href="{{ asset($insurance->fire_report_file) }}" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                <button type="button" class="btn btn-danger btn-xs delete-tvendor-file" data-id="{{ $insurance->id }}" data-type="fire_report">
                                                    Delete
                                                </button>

                                                @else

                                                <input type="file" class="form-control-file tvendor-file" data-id="{{ $insurance->id }}" data-type="fire_report" accept=".pdf,.jpg,.jpeg,.png">

                                                @endif

                                                <button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="{{ $insurance->id }}" data-type="fire_report">
                                                    History
                                                </button>

                                            </div>
                                        </td>

                                        <td>
                                            <div class="file-action-box" id="docBox{{ $insurance->id }}_fir_closure_report">

                                                @if($insurance->fir_closure_report_file)

                                                <a href="{{ asset($insurance->fir_closure_report_file) }}" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                <button type="button" class="btn btn-danger btn-xs delete-tvendor-file" data-id="{{ $insurance->id }}" data-type="fir_closure_report">
                                                    Delete
                                                </button>

                                                @else

                                                <input type="file" class="form-control-file tvendor-file" data-id="{{ $insurance->id }}" data-type="fir_closure_report" accept=".pdf,.jpg,.jpeg,.png">

                                                @endif

                                                <button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="{{ $insurance->id }}" data-type="fir_closure_report">
                                                    History
                                                </button>

                                            </div>
                                        </td>

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="22" class="text-center">
                                            No Insurance records found.
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">

                            <div class="col-md-6">
                                Showing {{ $datalist->firstItem() ?? 0 }} to {{ $datalist->lastItem() ?? 0 }} of {{ $datalist->total() }}
                            </div>

                            <div class="col-md-6">
                                <div class="float-right">
                                    {{ $datalist->onEachSide(1)->links('pagination::bootstrap-4') }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Photograph Modal -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Insurance Photographs
                    <span id="photoInvoice"></span>
                </h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row" id="photoModalBody">
                    <div class="col-md-12 text-center">
                        Loading...
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<!-- File History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="historyTitle">
                    File History
                </h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover history-table">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>File Name</th>

                                <th>Action</th>

                                <th>User ID</th>

                                <th>Date & Time</th>

                                <th>File</th>

                            </tr>

                        </thead>

                        <tbody id="historyTableBody">

                            <tr>
                                <td colspan="6" class="text-center">
                                    Loading...
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>

            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function documentLabel(type) {

        let labels = {
            cof: 'COF',
            fir_police_report: 'FIR / Police Report',
            fire_report: 'Fire Report',
            fir_closure_report: 'FIR Closure Report'
        };

        return labels[type] || type;
    }

    function createUploadInput(id, type) {

        return '<input type="file" class="form-control-file tvendor-file" data-id="' + id + '" data-type="' + type + '" accept=".pdf,.jpg,.jpeg,.png">' +
            '<button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="' + id + '" data-type="' + type + '">History</button>';
    }

    function createUploadedButtons(id, type, url) {

        return '<a href="' + url + '" target="_blank" class="btn btn-info btn-xs">' +
            '<i class="fas fa-eye"></i> View' +
            '</a> ' +
            '<button type="button" class="btn btn-danger btn-xs delete-tvendor-file" data-id="' + id + '" data-type="' + type + '">' +
            'Delete' +
            '</button> ' +
            '<button type="button" class="btn btn-secondary btn-xs mt-1 view-history" data-id="' + id + '" data-type="' + type + '">' +
            'History' +
            '</button>';
    }

    $(document).on('change', '.tvendor-file', function() {

        let input = this;
        let id = $(this).data('id');
        let type = $(this).data('type');

        if (!input.files.length) {
            return;
        }

        let file = input.files[0];

        if (file.size > 10 * 1024 * 1024) {

            alert('Maximum file size allowed is 10 MB.');

            $(input).val('');

            return;
        }

        let formData = new FormData();

        formData.append('document_type', type);
        formData.append('file', file);

        $('#docBox' + id + '_' + type).html(
            '<i class="fas fa-spinner fa-spin"></i> Uploading...'
        );

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/t-vendor-document",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {

                if (!response.success) {

                    alert(response.message || 'Unable to upload file.');

                    $('#docBox' + id + '_' + type).html(
                        createUploadInput(id, type)
                    );

                    return;
                }

                $('#docBox' + id + '_' + type).html(
                    createUploadedButtons(
                        id,
                        type,
                        response.url
                    )
                );

            },
            error: function(xhr) {

                let message = 'Unable to upload file.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);

                $('#docBox' + id + '_' + type).html(
                    createUploadInput(id, type)
                );

            }
        });

    });

    $(document).on('click', '.delete-tvendor-file', function() {

        let id = $(this).data('id');
        let type = $(this).data('type');

        if (!confirm('Are you sure you want to remove this ' + documentLabel(type) + '? The file will remain available in History.')) {
            return;
        }

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/t-vendor-document",
            type: 'DELETE',
            data: {
                document_type: type
            },
            success: function(response) {

                if (!response.success) {

                    alert(response.message || 'Unable to remove file.');

                    return;
                }

                $('#docBox' + id + '_' + type).html(
                    createUploadInput(id, type)
                );

            },
            error: function(xhr) {

                let message = 'Unable to remove file.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);

            }
        });

    });

    $(document).on('click', '.view-history', function() {

        let id = $(this).data('id');
        let type = $(this).data('type');

        $('#historyTitle').text(
            documentLabel(type) + ' - File History'
        );

        $('#historyTableBody').html(
            '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>'
        );

        $('#historyModal').modal('show');

        $.get(
            "{{ url('/admin/insurance') }}/" + id + "/t-vendor-history/" + type,
            function(response) {

                if (!response.success) {

                    $('#historyTableBody').html(
                        '<tr><td colspan="6" class="text-center text-danger">Unable to load history.</td></tr>'
                    );

                    return;
                }

                let html = '';

                if (response.history.length === 0) {

                    html = '<tr><td colspan="6" class="text-center text-muted">No file history found.</td></tr>';

                } else {

                    $.each(
                        response.history,
                        function(index, row) {

                            let actionClass = 'badge-info';

                            if (row.action === 'Replaced') {
                                actionClass = 'badge-warning';
                            }

                            if (row.action === 'Deleted') {
                                actionClass = 'badge-danger';
                            }

                            html += '<tr>';

                            html += '<td>' + (index + 1) + '</td>';

                            html += '<td>' + escapeHtml(row.file_name || '') + '</td>';

                            html += '<td><span class="badge ' + actionClass + '">' + escapeHtml(row.action) + '</span></td>';

                            html += '<td>' + (row.uploaded_by || '') + '</td>';

                            html += '<td>' + escapeHtml(row.created_at) + '</td>';

                            if (row.file_url) {

                                html += '<td><a href="' + row.file_url + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-eye"></i> View</a></td>';

                            } else {

                                html += '<td>-</td>';

                            }

                            html += '</tr>';

                        }
                    );

                }

                $('#historyTableBody').html(html);

            }
        ).fail(function() {

            $('#historyTableBody').html(
                '<tr><td colspan="6" class="text-center text-danger">Unable to load history.</td></tr>'
            );

        });

    });

    $(document).on('click', '.view-photos', function() {

        let id = $(this).data('id');
        let invoice = $(this).data('invoice');

        $('#photoInvoice').text(
            invoice ? ' - Invoice ' + invoice : ''
        );

        $('#photoModalBody').html(
            '<div class="col-md-12 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
        );

        $('#photoModal').modal('show');

        $.get(
            "{{ url('/admin/insurance') }}/" + id + "/photographs",
            function(response) {

                if (!response.success) {
                    return;
                }

                let html = '';

                if (response.photographs.length === 0) {

                    html = '<div class="col-md-12 text-center text-muted">No photographs uploaded.</div>';

                } else {

                    $.each(
                        response.photographs,
                        function(index, photo) {

                            html += '<div class="col-md-3 mb-3">';

                            html += '<div class="card">';

                            html += '<a href="' + photo.url + '" target="_blank">';

                            html += '<img src="' + photo.url + '" class="card-img-top" style="height:140px;object-fit:cover;">';

                            html += '</a>';

                            html += '<div class="card-body p-2 text-center">';

                            html += '<a href="' + photo.url + '" target="_blank" class="btn btn-info btn-xs">View</a>';

                            html += '</div>';

                            html += '</div>';

                            html += '</div>';

                        }
                    );

                }

                $('#photoModalBody').html(html);

            }
        );

    });

    function escapeHtml(value) {

        return $('<div>')
            .text(value)
            .html();

    }

});
</script>

@endsection