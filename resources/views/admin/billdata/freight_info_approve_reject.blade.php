@extends('admin.admin')

@section('bodycontent')

<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}
.table-container {
    max-height: 520px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
}
.freight-approval-table {
    min-width: 1850px;
    font-size: 12px;
    margin-bottom: 0;
}
.freight-approval-table th,
.freight-approval-table td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 7px 10px;
}
.freight-approval-table thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #fce4d6;
    color: #0070c0;
}
.freight-approval-table thead th.action-header {
    background: #c6e0b4;
}
.approval-action-area {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-top: 0;
    padding: 15px;
}
.approval-action-area label {
    font-weight: 600;
}
.badge-approved {
    font-size: 12px;
    padding: 6px 10px;
}
.nav-pills .nav-link {
    margin-right: 5px;
}
.approval-count {
    margin-left: 5px;
}
.file-link {
    white-space: nowrap;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Freight Bill Approve / Reject</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Freight Bill Approve / Reject</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#pendingApproval" data-toggle="tab">
                                    Pending Approval
                                    <span class="badge badge-warning approval-count">{{ $entries->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#approvedBills" data-toggle="tab">
                                    Approved Bills
                                    <span class="badge badge-success approval-count">{{ $approvedentries->count() }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('error') }}
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

                        <div class="tab-content">
                            {{-- Pending Approval Tab --}}
                            <div class="active tab-pane" id="pendingApproval">
                                <form method="POST" action="{{ route('admin.freight.approve.reject.store') }}" id="approveRejectForm">
                                    @csrf

                                    <div class="table-responsive-fixed table-container">
                                        <table class="table table-bordered table-hover freight-approval-table" id="pendingApprovalTable">
                                            <thead>
                                                <tr>                                                   
                                                    <th style="background: #fce4d6; color: #0070c0;">S5 consignor short<br>name & location</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">D5 consignor short<br>name & location</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Order Ref No.<br>(Indent ID)</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">LR/CN No.</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">LR/CN Date</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Freight PO</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Freight<br>Invoice No.</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Invoice Dt.</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Amount</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Custom 5</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Freight Invoice</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">POD</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Approvals</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Validation Status</th>
                                                    <th style="background: #fce4d6; color: #0070c0;">Validation Remark</th>
													 <th class="action-header text-center" style="background: #ddebf7; color: #0070c0;">
                                                        <input type="checkbox" id="selectAll" title="Select All">
                                                    </th>
													<th style="background: #ddebf7; color: #0070c0;">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($entries->count() > 0)
                                                    @foreach($entries as $billdata)
                                                        <tr>
                                                           
                                                            <td>{{ $billdata->s5_consignor_short_name_and_location }}</td>
                                                            <td>{{ $billdata->d5_consignor_short_name_and_location }}</td>
                                                            <td>{{ $billdata->ref1 }}</td>
                                                            <td>{{ $billdata->vendor_name }}</td>
                                                            <td>{{ $billdata->lr_no }}</td>
                                                            <td>{{ $billdata->lr_cn_date }}</td>
                                                            <td>{{ $billdata->truck_type }}</td>
                                                            <td>{{ $billdata->ref2 }}</td>
                                                            <td>{{ $billdata->freight_invoice_no }}</td>
                                                            <td>{{ $billdata->freight_invoice_date }}</td>
                                                            <td>
                                                                @if(is_numeric($billdata->freight_amount))
                                                                    {{ number_format($billdata->freight_amount) }}
                                                                @else
                                                                    {{ $billdata->freight_amount }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $billdata->rate_custom5 ?? 'NA' }}</td>
                                                            <td>
                                                                @if(!empty($billdata->freight_invoice_file))
                                                                    <a href="{{ asset($billdata->freight_invoice_file) }}" target="_blank" class="file-link">View Invoice File</a>
                                                                @else
                                                                    NA
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(!empty($billdata->pod_file))
                                                                    <a href="{{ asset($billdata->pod_file) }}" target="_blank" class="file-link">View POD</a>
                                                                @else
                                                                    NA
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(!empty($billdata->approval_file))
                                                                    <a href="{{ asset($billdata->approval_file) }}" target="_blank" class="file-link">View Approval</a>
                                                                @else
                                                                    NA
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($billdata->validated_status == 'submitted')
                                                                    <span class="badge badge-success">Validated</span>
                                                                @else
                                                                    {{ ucfirst($billdata->validated_status ?? 'Validated') }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $billdata->validation_remark ?? '' }}</td>
															 <td class="text-center">
																<input type="checkbox" name="selected_ids[]" value="{{ $billdata->id }}" class="approval-checkbox">
															</td>
															<td>
																<input type="text" name="remarks[{{ $billdata->id }}]" class="form-control form-control-sm approval-remark" maxlength="2000" placeholder="Enter remark">
															</td>
															
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="19" class="text-center">No freight bills are pending for approval.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>  
									@if($entries->count() > 0)
										<div class="approval-action-area text-right">
											<button type="button" class="btn btn-success" id="approveSelected">Approve</button>
											<button type="button" class="btn btn-danger" id="rejectSelected">Reject</button>
										</div>
									@endif									

                                    <input type="hidden" name="action" id="approvalAction" value="">
                                </form>
                            </div>

                            {{-- Approved Bills Tab --}}
                            <div class="tab-pane" id="approvedBills">
                                <div class="table-responsive-fixed table-container">
                                    <table class="table table-bordered table-hover freight-approval-table" id="approvedBillsTable">
                                        <thead>
                                            <tr>
                                                <th style="background: #fce4d6; color: #0070c0;">S5 consignor short<br>name & location</th>
                                                <th style="background: #fce4d6; color: #0070c0;">D5 consignor short<br>name & location</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Order Ref No.<br>(Indent ID)</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
                                                <th style="background: #fce4d6; color: #0070c0;">LR/CN No.</th>
                                                <th style="background: #fce4d6; color: #0070c0;">LR/CN Date</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Freight PO</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Freight<br>Invoice No.</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Invoice Dt.</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Amount</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Custom 5</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Freight Invoice</th>
                                                <th style="background: #fce4d6; color: #0070c0;">POD</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Approvals</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Status</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Approval Remark</th>
                                                <th style="background: #fce4d6; color: #0070c0;">Approved At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($approvedentries->count() > 0)
                                                @foreach($approvedentries as $billdata)
                                                    <tr>
                                                        <td>{{ $billdata->s5_consignor_short_name_and_location }}</td>
                                                        <td>{{ $billdata->d5_consignor_short_name_and_location }}</td>
                                                        <td>{{ $billdata->ref1 }}</td>
                                                        <td>{{ $billdata->vendor_name }}</td>
                                                        <td>{{ $billdata->lr_no }}</td>
                                                        <td>{{ $billdata->lr_cn_date }}</td>
                                                        <td>{{ $billdata->truck_type }}</td>
                                                        <td>{{ $billdata->ref2 }}</td>
                                                        <td>{{ $billdata->freight_invoice_no }}</td>
                                                        <td>{{ $billdata->freight_invoice_date }}</td>
                                                        <td>
                                                            @if(is_numeric($billdata->freight_amount))
                                                                {{ number_format($billdata->freight_amount) }}
                                                            @else
                                                                {{ $billdata->freight_amount }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $billdata->rate_custom5 ?? 'NA' }}</td>
                                                        <td>
                                                            @if(!empty($billdata->freight_invoice_file))
                                                                <a href="{{ asset($billdata->freight_invoice_file) }}" target="_blank">View Invoice File</a>
                                                            @else
                                                                NA
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($billdata->pod_file))
                                                                <a href="{{ asset($billdata->pod_file) }}" target="_blank">View POD</a>
                                                            @else
                                                                NA
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!empty($billdata->approval_file))
                                                                <a href="{{ asset($billdata->approval_file) }}" target="_blank">View Approval</a>
                                                            @else
                                                                NA
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-success badge-approved">Approved</span>
                                                        </td>
                                                        <td>{{ $billdata->approval_remark ?? '--' }}</td>
                                                        <td>{{ $billdata->approved_at }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="18" class="text-center">No approved freight bills found.</td>
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
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const form = document.getElementById('approveRejectForm');
    const approveButton = document.getElementById('approveSelected');
    const rejectButton = document.getElementById('rejectSelected');
    const actionInput = document.getElementById('approvalAction');
    let isSubmitting = false;

    // Select or deselect all freight bills.
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.approval-checkbox').forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    }

    // Update Select All when individual checkboxes change.
    document.querySelectorAll('.approval-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const all = document.querySelectorAll('.approval-checkbox');
            const checked = document.querySelectorAll('.approval-checkbox:checked');

            if (selectAll) {
                selectAll.checked = all.length > 0 && all.length === checked.length;
            }
        });
    });

    function processFreightApproval(action) {
        if (isSubmitting || !form || !actionInput) {
            return;
        }

        const selected = document.querySelectorAll('.approval-checkbox:checked');

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least one freight bill.'
            });
            return;
        }

        // Every selected freight bill must have a remark when rejecting.
        if (action === 'reject') {
            for (const checkbox of selected) {
                const row = checkbox.closest('tr');
                const remarkInput = row ? row.querySelector('.approval-remark') : null;

                if (!remarkInput || remarkInput.value.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remark Required',
                        text: 'Please enter a rejection remark for every selected freight bill.'
                    }).then(function () {
                        if (remarkInput) {
                            remarkInput.focus();
                        }
                    });
                    return;
                }
            }
        }

        Swal.fire({
            title: action === 'approve' ? 'Approve selected freight bills?' : 'Reject selected freight bills?',
            text: selected.length + ' freight bill(s) will be ' + (action === 'approve' ? 'approved.' : 'rejected.'),
            icon: action === 'approve' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: action === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
            cancelButtonText: 'Cancel',
            confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545'
        }).then(function (result) {
            if (result.isConfirmed && !isSubmitting) {
                isSubmitting = true;
                actionInput.value = action;

                if (approveButton) {
                    approveButton.disabled = true;
                }

                if (rejectButton) {
                    rejectButton.disabled = true;
                }

                form.submit();
            }
        });
    }

    // Approve selected freight bills.
    if (approveButton) {
        approveButton.addEventListener('click', function () {
            processFreightApproval('approve');
        });
    }

    // Reject selected freight bills.
    if (rejectButton) {
        rejectButton.addEventListener('click', function () {
            processFreightApproval('reject');
        });
    }
});
</script>

@endsection