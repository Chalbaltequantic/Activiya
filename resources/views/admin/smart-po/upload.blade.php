@extends('admin.admin')
@section('bodycontent')

<style>
.po-upload-box {
    border: 2px dashed #ced4da;
    border-radius: 6px;
    background: #f8f9fa;
    min-height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.po-upload-box:hover {
    border-color: #007bff;
    background: #f1f7ff;
}

.po-upload-content {
    padding: 35px 20px;
}

.po-upload-icon {
    font-size: 58px;
    color: #007bff;
    line-height: 1;
    margin-bottom: 20px;
}

.po-upload-content h4 {
    margin-bottom: 10px;
    font-weight: 500;
}

.selected-file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    margin-bottom: 7px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.selected-file-item i {
    color: #dc3545;
    margin-right: 8px;
}

.selected-file-size {
    color: #6c757d;
    font-size: 13px;
}
</style>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">Upload Purchase Orders</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">Dashboard</a>
                    </li>

                    <li class="breadcrumb-item active">
                        <a class="btn btn-primary"
                           href="{{ route('admin.smart-po.index') }}">
                            PO Data List
                        </a>
                    </li>
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

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>{{ session('success') }}</strong>

                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <strong>{{ session('error') }}</strong>

                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Please correct the following:</strong>

                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                        <button type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="card card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            Upload PO Files
                        </h3>
                    </div>

                    <form action="{{ route('admin.smart-po.process') }}"
                          method="post"
                          enctype="multipart/form-data">

                        @csrf

                        <div class="card-body">

                            <div class="po-info-box mb-4">
                                <strong>
                                    <i class="fas fa-magic mr-1"></i>
                                    Automatic PO Processing
                                </strong>

                                <div class="text-muted mt-1">
                                    Upload one or multiple purchase orders.
                                    The system will automatically identify the PO format
                                    and select the correct parser.
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-12">
									<div class="form-group">
										<label>Purchase Order Files <span class="text-danger">*</span></label>

										<div class="po-upload-box" id="poUploadBox">
											<input type="file" name="po_files[]" id="poFiles" multiple accept=".pdf,.jpg,.jpeg,.png,.bmp,.tif,.tiff" required style="display:none;">

											<div class="po-upload-content">
												<div class="po-upload-icon">
													<i class="fas fa-cloud-upload-alt"></i>
												</div>

												<h4>Select Purchase Order Files</h4>

												<p class="text-muted mb-1">
													Click here to browse and select one or multiple files
												</p>

												<small class="text-muted">
													PDF, JPG, JPEG, PNG, BMP, TIF or TIFF
												</small>
											</div>
										</div>

										<div id="selectedFiles" class="mt-3"></div>
										@error('po_files')
											<div class="text-danger mt-2">{{ $message }}</div>
										@enderror
										@error('po_files.*')
											<div class="text-danger mt-2">{{ $message }}</div>
										@enderror
									</div>                                    
                                </div>
                            </div>

                            <div id="selectedFilesBox" class="card card-outline card-info mt-3" style="display:none;">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Selected Files
                                        <span id="selectedFileCount" class="badge badge-info ml-2">0</span>
                                    </h3>
                                </div>

                                <div class="card-body po-file-list" id="selectedFileList"></div>

                            </div>

                        </div>

                        <div class="card-footer">

                            <button type="submit" name="submit" id="processPoButton" class="btn btn-primary">
                                <i class="fas fa-cogs mr-1"></i>Upload & Process PO
                            </button>

                            <a href="{{ route('admin.smart-po.index') }}" class="btn btn-default ml-2"><i class="fas fa-list mr-1"></i>PO Data List</a>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>
</div>
<!-- /.content -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('po_files');
    var box = document.getElementById('selectedFilesBox');
    var count = document.getElementById('selectedFileCount');
    var list = document.getElementById('selectedFileList');

    input.addEventListener('change', function () {
        list.innerHTML = '';

        if (!this.files || this.files.length === 0) {
            box.style.display = 'none';
            count.innerHTML = '0';
            return;
        }

        count.innerHTML = this.files.length;
        box.style.display = 'block';

        for (var i = 0; i < this.files.length; i++) {
            var file = this.files[i];

            var row = document.createElement('div');
            row.className = 'border-bottom py-2';

            var size = file.size / 1024 / 1024;

            row.innerHTML =
                '<i class="fas fa-file text-primary mr-2"></i>' +
                '<strong>' + file.name + '</strong>' +
                '<span class="text-muted ml-2">(' +
                size.toFixed(2) +
                ' MB)</span>';

            list.appendChild(row);
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var uploadBox = document.getElementById('poUploadBox');
    var fileInput = document.getElementById('poFiles');
    var selectedFiles = document.getElementById('selectedFiles');

    uploadBox.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        selectedFiles.innerHTML = '';

        if (!this.files.length) {
            return;
        }

        var heading = document.createElement('div');
        heading.className = 'font-weight-bold mb-2';
        heading.textContent = this.files.length + ' file(s) selected';
        selectedFiles.appendChild(heading);

        Array.from(this.files).forEach(function (file) {
            var size = file.size / 1024;

            if (size >= 1024) {
                size = (size / 1024).toFixed(2) + ' MB';
            } else {
                size = size.toFixed(2) + ' KB';
            }

            var item = document.createElement('div');
            item.className = 'selected-file-item';

            var name = document.createElement('div');

            var icon = document.createElement('i');
            icon.className = 'fas fa-file-pdf';

            var text = document.createElement('span');
            text.textContent = file.name;

            name.appendChild(icon);
            name.appendChild(text);

            var fileSize = document.createElement('div');
            fileSize.className = 'selected-file-size';
            fileSize.textContent = size;

            item.appendChild(name);
            item.appendChild(fileSize);

            selectedFiles.appendChild(item);
        });
    });
});
</script>
@endsection