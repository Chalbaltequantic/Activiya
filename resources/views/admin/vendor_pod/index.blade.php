@extends('admin.admin')
@section('bodycontent')
<style>
    .vendor-pod-table-wrap { overflow-x:auto; max-height:600px; border:1px solid #ddd; }
    .vendor-pod-table { min-width:1150px; font-size:12px; }
    .vendor-pod-table th, .vendor-pod-table td { white-space:nowrap; vertical-align:middle!important; padding:6px!important; }
    .vendor-pod-table thead th { position:sticky; top:0; z-index:2; }
    .vendor-pod-table input[type=text] { min-width:155px; }
    .vendor-pod-table input[type=file] { width:195px; font-size:12px; }
    .vendor-pod-table .file-cell { min-width:220px; }
    .vendor-pod-table .camera-btn { margin-top:4px; }
    .vendor-pod-table .row-number { min-width:38px; }
</style>
<div class="content-header"><div class="container-fluid">
    <h1 class="m-0">{{ $pagetitle }}</h1>
</div></div>
<section class="content"><div class="container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if(session('rowErrors') && count(session('rowErrors')))
        <div class="alert alert-warning" style="max-height:220px;overflow-y:auto;">
            <strong>Rows skipped (other valid rows were saved):</strong>
            <ul class="mb-0">@foreach(session('rowErrors') as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills" id="vendorPodTabs">
                @unless($isAdmin)
                    <li class="nav-item"><a class="nav-link {{ request('tab') !== 'list' ? 'active' : '' }}" data-toggle="tab" href="#uploadTab">Vendor POD Manual Upload (20 Rows)</a></li>
                @endunless
                <li class="nav-item"><a class="nav-link {{ $isAdmin || request('tab') === 'list' ? 'active' : '' }}" data-toggle="tab" href="#listTab">Uploaded POD Listing</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                 @if(!$isAdmin)
                <div class="tab-pane {{ request('tab') !== 'list' ? 'active' : '' }}" id="uploadTab">
                    <p class="text-muted mb-2">Vendor code is automatically assigned from your login. Complete all five fields for each row. Blank or incomplete rows will be skipped; valid rows will be saved.</p>
                    <form method="POST" action="{{ route('admin.vendor-pods.store') }}" enctype="multipart/form-data" id="vendorPodBulkForm">
                        @csrf
                        <div class="table-responsive-fixed border rounded shadow-sm bg-white vendor-pod-table-wrap">
                            <table class="table table-bordered table-hover vendor-pod-table mb-0" id="table" style="table-layout:auto;width:auto;">
                                <thead><tr>
                                    <th style="background:#fce4d6;color:#0070c0;">#</th>
                                    <th style="background:#fce4d6;color:#0070c0;">Invoice Number *</th>
                                    <th style="background:#fce4d6;color:#0070c0;">LR/CN No *</th>
                                    <th style="background:#ddebf7;color:#0070c0;">Attach <br>POD (front) *</th>
                                    <th style="background:#ddebf7;color:#0070c0;">Attach <br>POD (back) *</th>
                                    <th style="background:#ddebf7;color:#0070c0;">Attach <br>Other Document *</th>
                                </tr></thead>
                                <tbody>
                                    @for($i = 0; $i < 20; $i++)
                                        <tr>
                                            <td class="row-number">{{ $i + 1 }}</td>
                                            <td><input type="text" class="form-control form-control-sm" name="data[{{ $i }}][invoice_no]" maxlength="100" placeholder="Invoice No."></td>
                                            <td><input type="text" class="form-control form-control-sm" name="data[{{ $i }}][lr_no]" maxlength="100" placeholder="LR/CN No."></td>
                                            {{-- POD FRONT --}}
											<td class="file-cell">
												<input type="file" id="pod_front_{{ $i }}" name="data[{{ $i }}][pod_front accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">

												<button type="button" class="btn btn-sm btn-primary camera-btn" onclick="captureVendorPodPhoto('pod_front_{{ $i }}')" title="Capture POD Front">
													<i class="fas fa-camera"></i>
												</button>
											</td>

											{{-- POD BACK --}}
											<td class="file-cell">
												<input type="file" id="pod_back_{{ $i }}" name="data[{{ $i }}][pod_back]" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">

												<button type="button" class="btn btn-sm btn-primary camera-btn" onclick="captureVendorPodPhoto('pod_back_{{ $i }}')" title="Capture POD Back">
													<i class="fas fa-camera"></i>
												</button>
											</td>

											{{-- OTHER DOCUMENT --}}
											<td class="file-cell">
												<input type="file" id="other_document_{{ $i }}" name="data[{{ $i }}][other_document]" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf">

												<button type="button" class="btn btn-sm btn-primary camera-btn" onclick="captureVendorPodPhoto('other_document_{{ $i }}')" title="Capture Other Document"><i class="fas fa-camera"></i>
												</button>
											</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex align-items-center flex-wrap">
                            <button type="submit" class="btn btn-success mr-3" id="submitVendorPOD"><i class="fas fa-save mr-1"></i> Submit POD Records</button>
                            <span class="text-muted">Maximum 20 rows per submission. JPG, PNG, WEBP, PDF; maximum 10 MB per file.</span>
                        </div>
                    </form>
                </div>
                @endif
                <div class="tab-pane {{ $isAdmin || request('tab') === 'list' ? 'active' : '' }}" id="listTab">
                    <form method="GET" action="{{ route('admin.vendor-pods.index') }}" class="form-inline mb-3">
                        <input type="hidden" name="tab" value="list">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2 mb-2" placeholder="Invoice / LR number">
                        @if($isAdmin)
                            <input type="text" name="vendor_code" value="{{ request('vendor_code') }}" class="form-control form-control-sm mr-2 mb-2" placeholder="Vendor code">
                        @endif
                        <button type="submit" class="btn btn-sm btn-primary mb-2 mr-2">Search</button>
                        <a class="btn btn-sm btn-secondary mb-2" href="{{ route('admin.vendor-pods.index', ['tab'=>'list']) }}">Reset</a>
                    </form>
                    <div class="table-responsive vendor-pod-table-wrap">
                        <table class="table table-bordered table-hover vendor-pod-table">
                            <thead><tr>
                                <th style="background:#fce4d6;color:#0070c0;">#</th>
                                <th style="background:#fce4d6;color:#0070c0;">Vendor Code</th>
                                <th style="background:#fce4d6;color:#0070c0;">Invoice Number</th>
                                <th style="background:#fce4d6;color:#0070c0;">LR/CN No</th>
                                <th style="background:#ddebf7;color:#0070c0;">POD Front</th>
                                <th style="background:#ddebf7;color:#0070c0;">POD Back</th>
                                <th style="background:#ddebf7;color:#0070c0;">Other Document</th>
                                <th style="background:#ddebf7;color:#0070c0;">Uploaded On</th>
                                @if($isAdmin)<th style="background:#ddebf7;color:#0070c0;">Action</th>@endif
                            </tr></thead>
                            <tbody>
                            @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entries->firstItem() + $loop->index }}</td>
                                    <td>{{ $entry->vendor_code }}</td>
                                    <td>{{ $entry->invoice_no }}</td>
                                    <td>{{ $entry->lr_no }}</td>
                                    @foreach(['front'=>'POD Front', 'back'=>'POD Back', 'other'=>'Other Document'] as $type=>$label)
                                        <td>
                                            @if($entry->{$type === 'other' ? 'other_document_path' : 'pod_'.$type.'_path'})
                                                <a href="{{ route('admin.vendor-pods.file', [$entry->id, $type]) }}" target="_blank" rel="noopener">View {{ $label }}</a>
                                            @else - @endif
                                        </td>
                                    @endforeach
                                    <td>{{ optional($entry->created_at)->format('d/m/Y H:i') }}</td>
                                    @if($isAdmin)
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPod{{ $entry->id }}">Edit</button>
                                            <form method="POST" action="{{ route('admin.vendor-pods.destroy', $entry->id) }}" style="display:inline;" onsubmit="return confirm('Delete this POD? This LR will remain blocked from re-upload.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center">No POD records found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>
    @if($isAdmin)
        @foreach($entries as $entry)
            <div class="modal fade" id="editPod{{ $entry->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
                    <form method="POST" action="{{ route('admin.vendor-pods.update', $entry->id) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-header"><h5 class="modal-title">Edit POD — LR {{ $entry->lr_no }}</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                        <div class="modal-body">
                            <div class="form-group"><label>Vendor Code</label><input class="form-control" value="{{ $entry->vendor_code }}" readonly></div>
                            <div class="form-group"><label>Invoice Number *</label><input class="form-control" name="invoice_no" value="{{ $entry->invoice_no }}" required maxlength="100"></div>
                            <div class="form-group"><label>LR/CN No. (locked to prevent duplicates)</label><input class="form-control" value="{{ $entry->lr_no }}" readonly></div>
                            @foreach(['pod_front'=>'POD Front', 'pod_back'=>'POD Back', 'other_document'=>'Other Document'] as $field=>$label)
                                <div class="form-group"><label>Replace {{ $label }}</label><input type="file" class="form-control-file" name="{{ $field }}" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf"></div>
                            @endforeach
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
                    </form>
                </div></div>
            </div>
        @endforeach
    @endif
</div></section>
<script>
function captureVendorPodPhoto(inputId) {
    var target = document.getElementById(inputId);
    if (!target) return;
    var camera = document.createElement('input');
    camera.type = 'file';
    camera.accept = 'image/*';
    camera.capture = 'environment';
    camera.onchange = function () {
        if (!camera.files || !camera.files.length) return;
        var dt = new DataTransfer();
        dt.items.add(camera.files[0]);
        target.files = dt.files;
    };
    camera.click();
}
(function () {
    var form = document.getElementById('vendorPodBulkForm');
    if (!form) return;
    form.addEventListener('submit', function (event) {
        var hasData = Array.from(form.querySelectorAll('tbody tr')).some(function (tr) {
            return Array.from(tr.querySelectorAll('input')).some(function (input) {
                return input.type === 'file' ? input.files.length > 0 : input.value.trim() !== '';
            });
        });
        if (!hasData) { event.preventDefault(); alert('Please enter at least one POD row.'); return; }
        document.getElementById('submitVendorPOD').disabled = true;
    });
})();
</script>
@endsection
