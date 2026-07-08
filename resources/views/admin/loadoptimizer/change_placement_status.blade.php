@extends('admin.admin')
@section('bodycontent')

<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}
table {
    min-width: max-content;
    font-size: 12px;
}
.consign-data-table th,
.consign-data-table td {
    white-space: nowrap;
    vertical-align: middle;
}
.consign-data-table thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
}
.consign-data-table .table th,
.consign-data-table .table td {
    padding: 5px 10px;
}
.table-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ccc;
}
#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}
.box-modal-img {
    height: 170px;
    object-fit: cover;
    width: 100%;
}
.box-card-count {
    font-size: 14px;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">V_Placement</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">V_Placement</li>
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
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.vendor.loads') }}">V_Placement</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
                        <table id="appointdataTable" class="table table-bordered border-dark table-hover">
                            <thead>
                                <tr>
                                    <th style="background:#fce4d6;color:#0070c0;">Reference<br>No</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Origin</th>
                                    <th style="background:#fce4d6;color:#0070c0;">Destination</th>
                                    <th style="background:#fce4d6;color:#0070c0;">Mode</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Truck Type</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Total<br>Wt</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Total<br>Vol</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Vendor<br>Name</th>
                                    <th style="background:#fce4d6;color:#0070c0;" class="mobile-hide">Sent<br>Date</th>
                                    <th style="background:#fce4d6;color:#0070c0;">Last<br>Status</th>
                                    <th style="background:#c6e0b4;color:#0070c0;">Placement<br>Status</th>
                                    <th style="background:#c6e0b4;color:#0070c0;">Camera</th>
                                    <th style="background:#c6e0b4;color:#0070c0;">Box Count</th>
                                    <th style="background:#c6e0b4;color:#0070c0;">Remarks</th>
                                    <th style="background:#c6e0b4;color:#0070c0;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(count($loads) > 0)
                                    @foreach($loads as $row)
                                        @php
                                            $boxKey = $row->id . '_' . $row->source_type;
                                            $boxData = $boxCounts[$boxKey] ?? null;
                                            $lastStatus = $row->latestPlacement->placement_status ?? '';
                                        @endphp

                                        <tr>
                                            <td>{{ $row->reference_no }}</td>
                                            <td class="mobile-hide">{{ $row->origin_name_code }} {{ $row->origin_name }}</td>
                                            <td>{{ $row->destination_name_code }} {{ $row->destination_name ?? $row->destination_city ?? '' }}</td>
                                            <td>{{ $row->t_mode }}</td>
                                            <td class="mobile-hide">{{ $row->truck->description ?? $row->truck_name ?? $row->truck_code ?? 'NA' }}</td>
                                            <td class="mobile-hide">{{ $row->total_weight }}</td>
                                            <td class="mobile-hide">{{ $row->total_volume }}</td>
                                            <td class="mobile-hide">{{ $row->vendor_name }}</td>
                                            <td class="mobile-hide">{{ $row->sent_at }}</td>

                                            <td>
                                                @if($lastStatus)
                                                    <span class="badge bg-info">
                                                        {{ ucfirst(str_replace('_',' ', $lastStatus)) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">--</span>
                                                @endif
                                            </td>

                                            <td>
                                                <select class="placement-status"
                                                        data-load="{{ $row->id }}"
                                                        data-last-status="{{ $lastStatus }}"
                                                        {{ $lastStatus === 'Dispatch' ? 'disabled' : '' }}>
                                                    <option value="">Select</option>
                                                    <option value="Reported">Reported</option>
                                                    <option value="Loading_Start">Loading Start</option>
                                                    <option value="Loading_End">Loading End</option>
                                                    <option value="Dispatch" {{ $lastStatus === 'Dispatch' ? 'selected' : '' }}>Dispatch</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                            </td>

                                            <td>
                                                <button type="button"
                                                        class="btn btn-info btn-sm camera-btn d-none"
                                                        data-load-id="{{ $row->id }}"
                                                        data-reference-no="{{ $row->reference_no }}"
                                                        data-source-type="{{ $row->source_type }}"
                                                        title="Take Photo">
                                                    <i class="fa fa-camera"></i>
                                                </button>

                                                <input type="file"
                                                       class="camera-input d-none"
                                                       accept="image/*"
                                                       capture="environment">
                                            </td>

                                            <td>
                                                <span class="box-count-result">
                                                    @if($boxData)
                                                        Images: {{ $boxData->total_images }} |
                                                        Boxes: {{ $boxData->total_boxes }}
                                                    @else
                                                        --
                                                    @endif
                                                </span>

                                                <br>

                                                <button type="button"
                                                        class="btn btn-xs btn-secondary view-box-images"
                                                        data-load-id="{{ $row->id }}"
                                                        data-source-type="{{ $row->source_type }}"
                                                        data-reference-no="{{ $row->reference_no }}"
                                                        data-origin="{{ $row->origin_name_code }} {{ $row->origin_name }}"
                                                        data-destination="{{ $row->destination_name_code }} {{ $row->destination_name ?? $row->destination_city ?? '' }}"
                                                        data-vendor="{{ $row->vendor_name }}"
                                                        data-truck="{{ $row->truck->description ?? $row->truck_name ?? $row->truck_code ?? 'NA' }}">
                                                    View
                                                </button>
                                            </td>

                                            <td>
                                                <input type="text" class="lr-no d-none" placeholder="Enter LR No">
                                                <input type="text" class="remarks" placeholder="Enter remark">
                                            </td>

                                            <td>
                                                <button class="btn btn-success btn-sm submit-placement"
                                                        data-load-id="{{ $row->id }}"
                                                        data-reference_no="{{ $row->reference_no }}"
                                                        data-source_type="{{ $row->source_type }}">
                                                    Submit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="15" class="text-center">No data found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
</div>

<div class="modal fade" id="boxImageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Box Count Images</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Reference No</th>
                        <td id="modal_reference_no"></td>
                        <th>Source</th>
                        <td id="modal_source_type"></td>
                    </tr>
                    <tr>
                        <th>Origin</th>
                        <td id="modal_origin"></td>
                        <th>Destination</th>
                        <td id="modal_destination"></td>
                    </tr>
                    <tr>
                        <th>Vendor</th>
                        <td id="modal_vendor"></td>
                        <th>Truck</th>
                        <td id="modal_truck"></td>
                    </tr>
                </table>

                <div id="boxImageList" class="row"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('change', '.placement-status', function () {
    let row = $(this).closest('tr');
    let status = $(this).val();
    let lrInput = row.find('.lr-no');

    if (status === 'Dispatch') {
        lrInput.removeClass('d-none');
    } else {
        lrInput.addClass('d-none').val('');
    }

    if (status === 'Loading_End') {
        row.find('.camera-btn').removeClass('d-none');
    } else {
        row.find('.camera-btn').addClass('d-none');
    }
});

$(document).on('click', '.camera-btn', function () {
    let row = $(this).closest('tr');
    let input = row.find('.camera-input');

    input.data('load-id', $(this).data('load-id'));
    input.data('reference-no', $(this).data('reference-no'));
    input.data('source-type', $(this).data('source-type'));
    input.data('placement-status', row.find('.placement-status').val());

    input.val('');
    input.click();
});

$(document).on('change', '.camera-input', function () {
    let file = this.files[0];

    if (!file) {
        return;
    }

    let input = $(this);
    let row = input.closest('tr');

    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('load_summary_id', input.data('load-id'));
    formData.append('reference_no', input.data('reference-no'));
    formData.append('source_type', input.data('source-type'));
    formData.append('placement_status', input.data('placement-status'));
    formData.append('image', file);

    row.find('.box-count-result').html('Uploading & Counting...');

    $.ajax({
        url: "{{ route('admin.load.boxcount.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (res) {
            if (res.success) {
                row.find('.box-count-result').html('Latest Boxes: ' + res.count);

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            } else {
                row.find('.box-count-result').html('--');
                Swal.fire('Error', res.message, 'error');
            }
        },

        error: function (xhr) {
            row.find('.box-count-result').html('--');

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Image upload/count failed.'
            });
        }
    });
});

$(document).on('click', '.view-box-images', function () {
    let btn = $(this);

    $('#modal_reference_no').text(btn.data('reference-no'));
    $('#modal_source_type').text(btn.data('source-type'));
    $('#modal_origin').text(btn.data('origin'));
    $('#modal_destination').text(btn.data('destination'));
    $('#modal_vendor').text(btn.data('vendor'));
    $('#modal_truck').text(btn.data('truck'));

    $('#boxImageList').html('<div class="col-md-12 text-center">Loading...</div>');

    let loadId = btn.data('load-id');
    let sourceType = btn.data('source-type');

    $.ajax({
        url: "{{ url('admin/load-box-count/list') }}/" + loadId + "/" + sourceType,
        type: "GET",
        success: function (res) {
            let html = '';

            if (!res.records || res.records.length === 0) {
                html = '<div class="col-md-12 text-center text-muted">No photos found.</div>';
            }

            res.records.forEach(function (item) {
                let finalCount = item.manual_box_count ?? item.box_count;

                html += `
                    <div class="col-md-4 mb-3" id="box-photo-${item.id}">
                        <div class="card">
                            <a href="/${item.image_path}" target="_blank">
                                <img src="/${item.image_path}" class="box-modal-img">
                            </a>

                            <div class="card-body p-2">
                                <p class="mb-1 box-card-count"><b>AI Count:</b> ${item.box_count}</p>
                                <p class="mb-1 box-card-count"><b>Manual Count:</b>
                                    <input type="number"
                                           min="0"
                                           class="manual-count-input"
                                           data-id="${item.id}"
                                           value="${item.manual_box_count ?? item.box_count}"
                                           style="width:80px;">
                                    <button type="button"
                                            class="btn btn-xs btn-success update-manual-count"
                                            data-id="${item.id}">
                                        Save
                                    </button>
                                </p>

                                <p class="mb-1"><b>Final Count:</b> <span id="final-count-${item.id}">${finalCount}</span></p>
                                <p class="mb-1"><b>Confidence:</b> ${item.confidence_score ?? '-'}</p>
                                <p class="mb-1"><b>Size:</b> ${item.image_size_kb ?? '-'} KB</p>
                                <p class="mb-1"><b>Status:</b> ${item.placement_status ?? '-'}</p>

                                <input type="text"
                                       class="form-control form-control-sm box-remark-input"
                                       data-id="${item.id}"
                                       value="${item.remarks ?? ''}"
                                       placeholder="Remark">

                                <button type="button"
                                        class="btn btn-xs btn-primary mt-1 update-box-remark"
                                        data-id="${item.id}">
                                    Save Remark
                                </button>

                                <button type="button"
                                        class="btn btn-danger btn-xs mt-1 delete-box-photo"
                                        data-id="${item.id}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#boxImageList').html(html);
            $('#boxImageModal').modal('show');
        },
        error: function () {
            $('#boxImageList').html('<div class="col-md-12 text-danger">Unable to load photos.</div>');
            $('#boxImageModal').modal('show');
        }
    });
});

$(document).on('click', '.update-manual-count', function () {
    let id = $(this).data('id');
    let value = $('.manual-count-input[data-id="' + id + '"]').val();

    $.ajax({
        url: "{{ url('admin/load-box-count/update-count') }}/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            manual_box_count: value
        },
        success: function (res) {
            if (res.success) {
                $('#final-count-' + id).text(res.manual_box_count);
                Swal.fire('Updated', res.message, 'success');
            }
        },
        error: function (xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Count update failed.', 'error');
        }
    });
});

$(document).on('click', '.update-box-remark', function () {
    let id = $(this).data('id');
    let remark = $('.box-remark-input[data-id="' + id + '"]').val();

    $.ajax({
        url: "{{ url('admin/load-box-count/update-remark') }}/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            remarks: remark
        },
        success: function (res) {
            if (res.success) {
                Swal.fire('Updated', res.message, 'success');
            }
        },
        error: function (xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Remark update failed.', 'error');
        }
    });
});

$(document).on('click', '.delete-box-photo', function () {
    let id = $(this).data('id');

    if (!confirm('Delete this photo?')) {
        return;
    }

    $.ajax({
        url: "{{ url('admin/load-box-count/delete') }}/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            _method: "DELETE"
        },
        success: function (res) {
            if (res.success) {
                $('#box-photo-' + id).remove();
                Swal.fire('Deleted', res.message, 'success');
            }
        },
        error: function () {
            Swal.fire('Error', 'Photo delete failed.', 'error');
        }
    });
});

$(document).on('click', '.submit-placement', function () {
    let row = $(this).closest('tr');

    let loadId = $(this).data('load-id');
    let reference_no = $(this).data('reference_no');
    let source_type = $(this).data('source_type');
    let status = row.find('.placement-status').val();
    let lrNo = row.find('.lr-no').val();
    let remarks = row.find('.remarks').val();

    if (!status) {
        Swal.fire('Validation Error', 'Please select placement status', 'warning');
        return;
    }

    if (status === 'Dispatch' && (!lrNo || lrNo.trim() === '')) {
        Swal.fire('Validation Error', 'LR Number is mandatory when status is Dispatch', 'warning');
        return;
    }

    $.post("{{ route('admin.vendor.placement.status') }}", {
        _token: "{{ csrf_token() }}",
        load_id: loadId,
        placement_status: status,
        lr_no: lrNo,
        remarks: remarks,
        reference_no: reference_no,
        source_type: source_type,
    })
    .done(function (res) {
        Swal.fire('Success', res.message || 'Placement status updated successfully', 'success')
            .then(() => location.reload());
    })
    .fail(function (xhr) {
        Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
    });
});
</script>

@endsection