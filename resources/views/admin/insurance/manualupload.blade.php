@extends('admin.admin')

@section('bodycontent')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
   .table-responsive-fixed {
      overflow-x: auto;
      position: relative;
    }

    table {
      min-width: max-content;
      font-size: 12px;
    }

    .table th, .table td {
      white-space: nowrap;
      vertical-align: middle;
	  padding: 0;
    }

    .table thead th {
      position: sticky;
      top: 0;
      background: #f8f9fa;
    }

     /* Sticky columns */
    .sticky-col-1 {
      position: sticky;
      left: 0;
      background: #fff;
      z-index: 99;
    }

    .sticky-col-2 {
      position: sticky;
      left: 100px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 190px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 290px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
      }
    }
	
.table-container {
    max-height: 400px;   /* Set your desired table height */
    overflow-y: auto;
    border: 1px solid #ccc;
}
.table-container thead th {
    position: sticky;
    top: 0;
    background: #fce4d6;
    color: #0070c0;
    z-index: 10;
}
#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}	
	
.lookup-note {
    display: block;
    font-size: 10px;
    margin-top: 2px;
}
.photo-box {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    min-width: 300px;
}
.photo-item {
    width: 85px;
    padding: 4px;
    border: 1px solid #ddd;
    background: #fff;
    text-align: center;
}
.photo-item img {
    width: 75px;
    height: 65px;
    object-fit: cover;
    border: 1px solid #ccc;
}
.photo-item .btn {
    margin-top: 3px;
}
.file-action-box {
    min-width: 180px;
}
.file-action-box .form-control-file {
    font-size: 11px;
}
.module-tabs .nav-link {
    font-size: 14px;
}
.step-tabs .nav-link {
    font-size: 13px;
}
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Insurance Manual Upload</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Insurance Manual Upload</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">

        <!-- Messages -->
        @if(session('success'))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="alert alert-success alert-dismissible fade show mb-0">
                        <strong>{{ session('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="alert alert-warning alert-dismissible fade show mb-0">
                        <strong>{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Module Navigation -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills module-tabs">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.insurance.index') }}">XLS Upload</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.insurance.manual-upload') }}">Manual Upload</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.insurance.datalist') }}">Insurance List</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Upload Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <!-- Step Tabs -->
                    <div class="card-header p-2">
                        <ul class="nav nav-pills step-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'details' ? 'active' : '' }}" href="#insuranceDetails" data-toggle="tab">
                                    Insurance Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab == 'files' ? 'active' : '' }}" href="#uploadFiles" data-toggle="tab">
                                    Upload Files
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content">

                            <!-- Insurance Details Tab -->
                            <div class="tab-pane {{ $activeTab == 'details' ? 'active' : '' }}" id="insuranceDetails">

                                <div class="p-3 pb-0">
                                    <div class="alert alert-info">
                                        Enter Insurance details below. Click <strong>Save & Continue</strong>. Records will first be saved in the database and the Upload Files tab will open automatically.
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.insurance.save-manual-upload') }}" id="insuranceForm">
                                    @csrf

                                    <div class="table-responsive-fixed border rounded shadow-sm bg-white table-container">
										<table id="table" class="table table-bordered border-dark table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Loss Date</th>
                                                    <th>Nature of Claim</th>
                                                    <th>From Location Code</th>
                                                    <th>From Location</th>
                                                    <th>To Location Code</th>
                                                    <th>To Location</th>
                                                    <th>Invoice No.</th>
                                                    <th>Invoice Date</th>
                                                    <th>Transporter Name</th>
                                                    <th>Truck No.</th>
                                                    <th>LR No.</th>
                                                    <th>LR Date</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($i = 0; $i < 20; $i++)
                                                <tr>
                                                    <td>
                                                      <input type="date" name="loss_date[{{ $i }}]" value="{{ old('loss_date.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <select name="nature_of_claim[{{ $i }}]" class="">
                                                            <option value="" {{ old('nature_of_claim.'.$i) == '' ? 'selected' : '' }}>Select</option>
                                                            <option value="Shortage" {{ old('nature_of_claim.'.$i) == 'Shortage' ? 'selected' : '' }}>Shortage</option>
                                                            <option value="Damage" {{ old('nature_of_claim.'.$i) == 'Damage' ? 'selected' : '' }}>Damage</option>
                                                            <option value="Shortage/Damage" {{ old('nature_of_claim.'.$i) == 'Shortage/Damage' ? 'selected' : '' }}>Shortage/Damage</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="from_location_code[{{ $i }}]" class="from-code" value="{{ old('from_location_code.'.$i) }}">
                                                        <small class="lookup-note from-note"></small>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="from_location[{{ $i }}]" class="from-location" value="{{ old('from_location.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="to_location_code[{{ $i }}]" class="to-code" value="{{ old('to_location_code.'.$i) }}">
                                                        <small class="lookup-note to-note"></small>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="to_location[{{ $i }}]" class="to-location" value="{{ old('to_location.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="invoice_no[{{ $i }}]" class="invoice-no" value="{{ old('invoice_no.'.$i) }}">
                                                        <small class="lookup-note invoice-note"></small>
                                                    </td>
                                                    <td>
                                                        <input type="date" name="invoice_date[{{ $i }}]" value="{{ old('invoice_date.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="transporter_name[{{ $i }}]" class="transporter" value="{{ old('transporter_name.'.$i) }}">
                                                    </td>
													<td>
                                                        <input type="text" name="truck_no[{{ $i }}]" class="truckno" value="{{ old('truck_no.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="lr_no[{{ $i }}]" class="lr-no" value="{{ old('lr_no.'.$i) }}">
                                                    </td>
                                                    <td>
                                                        <input type="date" name="lr_date[{{ $i }}]" class="lr-date" value="{{ old('lr_date.'.$i) }}">
                                                    </td>
                                                   
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-right p-3">
                                        <button type="submit" class="btn btn-success" id="saveButton">
                                            <i class="fas fa-save"></i> Save & Continue
                                        </button>
                                    </div>
                                </form>

                            </div>

                            <!-- Upload Files Tab -->
                            <div class="tab-pane {{ $activeTab == 'files' ? 'active' : '' }}" id="uploadFiles">

                                @if($savedInsurance->count() > 0)

                                <div class="p-3 pb-0">
                                    <div class="alert alert-info">
                                        Insurance records below are already saved. Invoice File, POD/LR Copy and photographs are uploaded immediately using AJAX. Maximum <strong>10 photographs</strong> per Insurance and each photograph must be <strong>1 MB or less</strong>.
                                    </div>
                                </div>

                                <div class="table-responsive-fixed border rounded bg-white insurance-data-table table-container">
                                    <table class="table table-bordered border-dark table-hover file-table">
                                        <thead>
                                            <tr>
                                               
                                                <th>Loss Date</th>
                                                <th>Nature</th>
                                                <th>From Code</th>
                                                <th>From Location</th>
                                                <th>To Code</th>
                                                <th>To Location</th>
                                                <th>Invoice No.</th>
                                                <th>Invoice Date</th>
                                                <th>Transporter</th>
                                                <th>Truck No.</th>
                                                <th>LR No.</th>
                                                <th>LR Date</th>
												<th style="background:#c6e0b4;color:#0070c0;">Product</th>
                                                <th style="background:#c6e0b4;color:#0070c0;">Invoice File</th>
                                                <th style="background:#c6e0b4;color:#0070c0;">POD/LR Copy</th>
                                                <th style="background:#c6e0b4;color:#0070c0;">Photographs</th>
												
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($savedInsurance as $index => $insurance)
                                            <tr>
                                              
                                                <td>{{ $insurance->loss_date ? $insurance->loss_date->format('Y-m-d') : '' }}</td>
                                                <td>{{ $insurance->nature_of_claim }}</td>
                                                <td>{{ $insurance->from_location_code }}</td>
                                                <td>{{ $insurance->from_location }}</td>
                                                <td>{{ $insurance->to_location_code }}</td>
                                                <td>{{ $insurance->to_location }}</td>
                                                <td>{{ $insurance->invoice_no }}</td>
                                                <td>{{ $insurance->invoice_date ? $insurance->invoice_date->format('Y-m-d') : '' }}</td>
                                                <td>{{ $insurance->transporter_name }}</td>
                                                <td>{{ $insurance->truck_no }}</td>
                                                <td>{{ $insurance->lr_no }}</td>
                                                <td>{{ $insurance->lr_date ? $insurance->lr_date->format('Y-m-d') : '' }}</td>
                                                
												<td>
													<a href="{{ route('admin.insurance.products', $insurance->id) }}" class="btn btn-primary btn-xs">
														<i class="fas fa-plus"></i> Add Product
													</a>
												</td>
                                                <!-- Invoice File -->
                                                <td>
                                                    <div class="file-action-box" id="invoiceBox{{ $insurance->id }}">
                                                        @if($insurance->invoice_file)
                                                        <a href="{{ asset($insurance->invoice_file) }}" target="_blank" class="btn btn-info btn-xs" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-xs delete-invoice" data-id="{{ $insurance->id }}">
                                                            <i class="fas fa-trash"></i> 
                                                        </button>
                                                        @else
                                                        <input type="file" class="form-control-file invoice-file" data-id="{{ $insurance->id }}" accept=".pdf,.jpg,.jpeg,.png">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- POD / LR -->
                                                <td>
                                                    <div class="file-action-box" id="podBox{{ $insurance->id }}">
                                                        @if($insurance->pod_lr_copy)
                                                        <a href="{{ asset($insurance->pod_lr_copy) }}" target="_blank" class="btn btn-info btn-xs" title="View">
                                                            <i class="fas fa-eye"></i></a>
                                                        <button type="button" class="btn btn-danger btn-xs delete-pod" data-id="{{ $insurance->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @else
                                                        <input type="file" class="form-control-file pod-file" data-id="{{ $insurance->id }}" accept=".pdf,.jpg,.jpeg,.png">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Photos -->
                                                <td>
                                                    <div class="mb-2">
                                                        <button type="button"
                                                            class="btn btn-info btn-xs view-photos"
                                                            data-id="{{ $insurance->id }}"
                                                            data-invoice="{{ $insurance->invoice_no }}"
                                                            data-lr="{{ $insurance->lr_no }}">
                                                            <i class="fas fa-images"></i>
                                                            View(<span id="photoButtonCount{{ $insurance->id }}">{{ $insurance->photographs->count() }}</span>)
                                                        </button>
                                                    </div>

                                                    <div id="photoUploadArea{{ $insurance->id }}">
                                                        <input type="file"
                                                            class="form-control-file photo-files"
                                                            data-id="{{ $insurance->id }}"
                                                            accept=".jpg,.jpeg,.png"
                                                            multiple>

                                                        <small class="text-muted">
                                                            <span id="photoCount{{ $insurance->id }}">{{ $insurance->photographs->count() }}</span> / 10 uploaded. Max 1 MB each.
                                                        </small>
                                                    </div>
                                                </td>
												
												
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right p-3">
                                    <a href="{{ route('admin.insurance.datalist') }}" class="btn btn-success">
                                        <i class="fas fa-check"></i> Finish & View Insurance List
                                    </a>
                                </div>

                                @else

                                <div class="p-3">
                                    <div class="alert alert-warning mb-0">
                                        No saved Insurance data is available for file upload. Please save Insurance Details first.
                                    </div>
                                </div>

                                @endif

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>


<!-- Photograph Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">
                    Insurance Photographs
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Invoice No.:</strong>
                        <span id="photoModalInvoice"></span>
                    </div>

                    <div class="col-md-6">
                        <strong>LR No.:</strong>
                        <span id="photoModalLR"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>File Name</th>
                                <th>Invoice No.</th>
                                <th>LR No.</th>
                                <th>View</th>
                                @if(Auth::guard('admin')->check())
                                <th>Delete</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody id="photoModalBody">
                            <tr>
                                <td colspan="7" class="text-center">No photographs found.</td>
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

    function fetchLocation(row, type) {
        let code = type === 'from' ? row.find('.from-code').val() : row.find('.to-code').val();
        let target = type === 'from' ? row.find('.from-location') : row.find('.to-location');
        let note = type === 'from' ? row.find('.from-note') : row.find('.to-note');

        code = $.trim(code);

        if (!code) {
            return;
        }

        note.removeClass().addClass('lookup-note text-info').text('Checking...');

        $.post("{{ route('admin.insurance.fetch-location') }}", {
            plant_code: code
        }, function(response) {
            if (response.success && response.location_name) {
                target.val(response.location_name);
                note.removeClass().addClass('lookup-note text-success').text('Found');
            } else {
                note.removeClass().addClass('lookup-note text-danger').text('Not found - enter manually');
            }
        }).fail(function() {
            note.removeClass().addClass('lookup-note text-danger').text('Lookup failed - enter manually');
        });
    }

    $(document).on('change', '.from-code', function() {
        fetchLocation($(this).closest('tr'), 'from');
    });

    $(document).on('change', '.to-code', function() {
        fetchLocation($(this).closest('tr'), 'to');
    });

    $(document).on('change', '.invoice-no', function() {
        let row = $(this).closest('tr');
        let invoice = $.trim($(this).val());
        let note = row.find('.invoice-note');

        if (!invoice) {
            return;
        }

        note.removeClass().addClass('lookup-note text-info').text('Checking...');

        $.post("{{ route('admin.insurance.fetch-invoice') }}", {
            invoice_no: invoice
        }, function(response) {
            if (!response.success) {
                note.removeClass().addClass('lookup-note text-danger').text('Not found - enter manually');
                return;
            }

            function setValue(selector, value) {
                if (value !== null && value !== undefined && $.trim(String(value)) !== '') {
                    row.find(selector).val(value);
                }
            }

            setValue('.from-code', response.from_location_code);
            setValue('.from-location', response.from_location);
            setValue('.to-code', response.to_location_code);
            setValue('.to-location', response.to_location);
            setValue('.transporter', response.transporter_name);
            setValue('.truckno', response.truck_no);
            setValue('.lr-no', response.lr_no);
            setValue('.lr-date', response.lr_date);

            note.removeClass().addClass('lookup-note text-success').text('Invoice found');
        }).fail(function() {
            note.removeClass().addClass('lookup-note text-danger').text('Lookup failed - enter manually');
        });
    });

    $('#insuranceForm').on('submit', function() {
        $('#saveButton')
            .prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    });

    function invoiceInput(id) {
        $('#invoiceBox' + id).html(
            '<input type="file" class="form-control-file invoice-file" data-id="' + id + '" accept=".pdf,.jpg,.jpeg,.png">'
        );
    }

    function podInput(id) {
        $('#podBox' + id).html(
            '<input type="file" class="form-control-file pod-file" data-id="' + id + '" accept=".pdf,.jpg,.jpeg,.png">'
        );
    }

    $(document).on('change', '.invoice-file', function() {
        let id = $(this).data('id');

        if (!this.files.length) {
            return;
        }

        let form = new FormData();
        form.append('file', this.files[0]);

        $('#invoiceBox' + id).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/invoice",
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to upload Invoice.');
                    invoiceInput(id);
                    return;
                }

                $('#invoiceBox' + id).html(
                    '<a href="' + response.url + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-eye"></i> View</a> ' +
                    '<button type="button" class="btn btn-danger btn-xs delete-invoice" data-id="' + id + '"><i class="fas fa-trash"></i> Delete</button>'
                );
            },
            error: function(xhr) {
                let message = 'Unable to upload Invoice.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
                invoiceInput(id);
            }
        });
    });

    $(document).on('click', '.delete-invoice', function() {
        let id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this Invoice file?')) {
            return;
        }

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/invoice",
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    invoiceInput(id);
                } else {
                    alert(response.message || 'Unable to delete Invoice.');
                }
            },
            error: function() {
                alert('Unable to delete Invoice.');
            }
        });
    });

    $(document).on('change', '.pod-file', function() {
        let id = $(this).data('id');

        if (!this.files.length) {
            return;
        }

        let form = new FormData();
        form.append('file', this.files[0]);

        $('#podBox' + id).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/pod",
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to upload POD/LR Copy.');
                    podInput(id);
                    return;
                }

                $('#podBox' + id).html(
                    '<a href="' + response.url + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-eye"></i> View</a> ' +
                    '<button type="button" class="btn btn-danger btn-xs delete-pod" data-id="' + id + '"><i class="fas fa-trash"></i> Delete</button>'
                );
            },
            error: function(xhr) {
                let message = 'Unable to upload POD/LR Copy.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
                podInput(id);
            }
        });
    });

    $(document).on('click', '.delete-pod', function() {
        let id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this POD/LR Copy?')) {
            return;
        }

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/pod",
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    podInput(id);
                } else {
                    alert(response.message || 'Unable to delete POD/LR Copy.');
                }
            },
            error: function() {
                alert('Unable to delete POD/LR Copy.');
            }
        });
    });

    let currentPhotoInsuranceId = null;

    function loadPhotos(id) {
        $.get("{{ url('/admin/insurance') }}/" + id + "/photographs", function(response) {
            if (!response.success) {
                return;
            }

            $('#photoCount' + id).text(response.photo_count);
            $('#photoButtonCount' + id).text(response.photo_count);

            if (response.remaining <= 0) {
                $('#photoUploadArea' + id).hide();
            } else {
                $('#photoUploadArea' + id).show();
            }
        });
    }

    function loadPhotoModal(id) {
        currentPhotoInsuranceId = id;

        $('#photoModalBody').html(
            '<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>'
        );

        $.get("{{ url('/admin/insurance') }}/" + id + "/photographs", function(response) {
            if (!response.success) {
                $('#photoModalBody').html(
                    '<tr><td colspan="7" class="text-center text-danger">Unable to load photographs.</td></tr>'
                );
                return;
            }

            $('#photoModalInvoice').text(response.invoice_no || '-');
            $('#photoModalLR').text(response.lr_no || '-');

            $('#photoCount' + id).text(response.photo_count);
            $('#photoButtonCount' + id).text(response.photo_count);

            if (response.remaining <= 0) {
                $('#photoUploadArea' + id).hide();
            } else {
                $('#photoUploadArea' + id).show();
            }

            let html = '';

            if (response.photographs.length === 0) {
                html = '<tr><td colspan="7" class="text-center text-muted">No photographs uploaded.</td></tr>';
            } else {
                $.each(response.photographs, function(index, photo) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td><img src="' + photo.url + '" style="width:80px;height:60px;object-fit:cover;border:1px solid #ccc;"></td>';
                    html += '<td>' + escapePhotoText(photo.original_name || '') + '</td>';
                    html += '<td>' + escapePhotoText(response.invoice_no || '') + '</td>';
                    html += '<td>' + escapePhotoText(response.lr_no || '') + '</td>';
                    html += '<td><a href="' + photo.url + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-eye"></i> View</a></td>';

                    @if(Auth::guard('admin')->check())
                    html += '<td><button type="button" class="btn btn-danger btn-xs delete-photo" data-id="' + photo.id + '" data-insurance="' + id + '"><i class="fas fa-trash"></i> Delete</button></td>';
                    @endif

                    html += '</tr>';
                });
            }

            $('#photoModalBody').html(html);
        }).fail(function() {
            $('#photoModalBody').html(
                '<tr><td colspan="7" class="text-center text-danger">Unable to load photographs.</td></tr>'
            );
        });
    }

    function escapePhotoText(value) {
        return $('<div>').text(value).html();
    }

    $(document).on('click', '.view-photos', function() {
        let id = $(this).data('id');

        $('#photoModalInvoice').text($(this).data('invoice') || '-');
        $('#photoModalLR').text($(this).data('lr') || '-');

        $('#photoModal').modal('show');

        loadPhotoModal(id);
    });

    @foreach($savedInsurance as $insurance)
    loadPhotos({{ $insurance->id }});
    @endforeach

    $(document).on('change', '.photo-files', function() {
        let id = $(this).data('id');
        let files = this.files;

        if (!files || files.length === 0) {
            return;
        }

        let current = parseInt($('#photoCount' + id).text()) || 0;
        let remaining = 10 - current;

        if (files.length > remaining) {
            alert('Only ' + remaining + ' more photograph(s) can be uploaded. Maximum 10 photographs are allowed.');
            $(this).val('');
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 1024 * 1024) {
                alert(files[i].name + ' is larger than 1 MB. Each photograph must be 1 MB or less.');
                $(this).val('');
                return;
            }
        }

        let form = new FormData();

        for (let i = 0; i < files.length; i++) {
            form.append('photographs[]', files[i]);
        }

        $.ajax({
            url: "{{ url('/admin/insurance') }}/" + id + "/photographs",
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to upload photographs.');
                    return;
                }

                $('.photo-files[data-id="' + id + '"]').val('');
                loadPhotos(id);

                if (currentPhotoInsuranceId == id && $('#photoModal').hasClass('show')) {
                    loadPhotoModal(id);
                }
            },
            error: function(xhr) {
                let message = 'Unable to upload photographs.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alert(message);
            }
        });
    });

    $(document).on('click', '.delete-photo', function() {
        let photoId = $(this).data('id');
        let insuranceId = $(this).data('insurance');

        if (!confirm('Are you sure you want to delete this photograph?')) {
            return;
        }

        $.ajax({
            url: "{{ url('/admin/insurance/photographs') }}/" + photoId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    loadPhotos(insuranceId);

                    if (currentPhotoInsuranceId == insuranceId) {
                        loadPhotoModal(insuranceId);
                    }
                } else {
                    alert(response.message || 'Unable to delete photograph.');
                }
            },
            error: function() {
                alert('Unable to delete photograph.');
            }
        });
    });
});
</script>

@endsection