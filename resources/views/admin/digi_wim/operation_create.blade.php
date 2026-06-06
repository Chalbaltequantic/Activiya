@extends('admin.admin')

@section('bodycontent')

<div class="content">
<div class="container-fluid">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header bg-info">
        <b>Enter following in case of Operation</b>
    </div>
<div class="card-body">

<form method="POST" action="{{ route('admin.digiwim.operation.storeHeader') }}">
@csrf

<div class="row">
<div class="form-group col-md-4">
			<label>Indent / Ref</label>
			<input type="text" name="indent_no" class="form-control">
			</div>	

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Select Type</label>
            <div class="col-sm-8">
                <select name="operation_type" class="form-control" required>
                    <option value="unloading" {{ (($header->operation_type ?? '') == 'unloading') ? 'selected' : '' }}>Unloading</option>
                 
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Invoice / Challan /OBD</label>
            <div class="col-sm-8">
                <input type="text" name="invoice_challan_no" class="form-control"
                       value="{{ $header->invoice_challan_no ?? '' }}" required>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Invoice Date</label>
            <div class="col-sm-8">
                <input type="date" name="invoice_date" class="form-control"
                       value="{{ $header->invoice_date ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">PO/Order No.</label>
            <div class="col-sm-8">
                <input type="text" name="po_order_no" class="form-control"
                       value="{{ $header->po_order_no ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">PO/Order Date</label>
            <div class="col-sm-8">
                <input type="date" name="po_order_date" class="form-control"
                       value="{{ $header->po_order_date ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Supplier Code & Name</label>
            <div class="col-sm-8">
                <input type="text" name="supplier_code_name" class="form-control"
                       value="{{ $header->supplier_code_name ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Transporter Name</label>
            <div class="col-sm-8">
                <input type="text" name="transporter_name" class="form-control"
                       value="{{ $header->transporter_name ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Truck No.</label>
            <div class="col-sm-8">
                <input type="text" name="truck_number" class="form-control"
                       value="{{ $header->truck_number ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">Truck Type</label>
            <div class="col-sm-8">
                <select name="truck_type" class="form-control">
                    <option value="">Select</option>
                    <option value="Open" {{ (($header->truck_type ?? '') == 'Open') ? 'selected' : '' }}>Open</option>
                    <option value="Container" {{ (($header->truck_type ?? '') == 'Container') ? 'selected' : '' }}>Container</option>
                    <option value="Trailer" {{ (($header->truck_type ?? '') == 'Trailer') ? 'selected' : '' }}>Trailer</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">LR No.</label>
            <div class="col-sm-8">
                <input type="text" name="lr_no" class="form-control"
                       value="{{ $header->lr_no ?? '' }}">
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group row align-items-center">
            <label class="col-sm-4 col-form-label font-weight-bold">UOM</label>
            <div class="col-sm-8">
                <select name="uom" class="form-control">
                    <option value="">Select</option>
                    <option value="case" {{ (($header->uom ?? '') == 'case') ? 'selected' : '' }}>Case</option>
                    <option value="pcs" {{ (($header->uom ?? '') == 'pcs') ? 'selected' : '' }}>PCS</option>
                    <option value="kg" {{ (($header->uom ?? '') == 'kg') ? 'selected' : '' }}>KG</option>
                </select>
            </div>
        </div>
    </div>

</div>

@if(empty($header->id))

<button type="submit" class="btn btn-primary">
    Submit Header
</button>

@else

<button type="button" class="btn btn-success" disabled>
    Header Submitted
</button>

@endif
</form>

</div>

</div>

@if(isset($header))
<div class="card mt-3" id="materialDetailsSection">
    <div class="card-header bg-primary text-white">
        <b>Manual Input</b>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="background: #fce4d6; color: #0070c0;">Product/Material Code</th>
                    <th style="background: #fce4d6; color: #0070c0;">Product Material Description</th>
                    <th style="background: #fce4d6; color: #0070c0;">Batch No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">MFG Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Expiry Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Qty.</th>
                    <th style="background: #fce4d6; color: #0070c0;">BIN No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">Goods Status</th>
                    <th style="background: #fce4d6; color: #0070c0;">Remarks</th>
                    <th style="background: #fce4d6; color: #0070c0;">Post Button</th>
                </tr>
            </thead>

            <tbody>
            @if(isset($digiRows) && $digiRows->count() > 0)

                @foreach($digiRows as $row)
                <tr>
                    <td>
                        <input type="hidden" class="operation_id" value="{{ $header->id }}">
                        <input type="hidden" class="invoice_challan_no" value="{{ $header->invoice_challan_no }}">
                        <input type="hidden" class="digi_wim_id" value="{{ $row->id }}">

                        <input type="text" class="material_code char-10" value="{{ $row->m_code }}">
                    </td>

                    <td>
                        <input type="text" class="material_description" value="{{ $row->material_descriptions }}">
                    </td>

                    <td>
                        <input type="text" class="batch_no char-10" value="{{ $row->batch_no }}">
                    </td>

                    <td>
                        <input type="date" class="mfg_date char-10"
                               value="{{ !empty($row->mfg_date) ? \Carbon\Carbon::parse($row->mfg_date)->format('Y-m-d') : '' }}">
                    </td>

                    <td>
                        <input type="date" class="expiry_date char-10"
                               value="{{ !empty($row->expiry_date) ? \Carbon\Carbon::parse($row->expiry_date)->format('Y-m-d') : '' }}">
                    </td>

                    <td>
                        <input type="text" class="qty char-6" value="{{ $row->qty_units }}">
                    </td>

                    <td>
                        <input type="text" class="bin_no char-10">
                    </td>

                    <td>
                        <select class="goods_status">
                            <option value="">Select</option>
                            <option value="Good">Good</option>
                            <option value="Damage">Damage</option>
                            <option value="Short">Short</option>
                            <option value="Excess">Excess</option>
                        </select>
                    </td>

                    <td>
                        <input type="text" class="remarks">
                    </td>

                    <td>
                        <button type="button"
                                class="btn btn-success btn-sm post-row-btn"
                                data-url="{{ route('admin.digiwim.operation.storeItem') }}">
                            Post
                        </button>
                    </td>
                </tr>
                @endforeach

            @else

                @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td>
                        <input type="hidden" class="operation_id" value="{{ $header->id }}">
                        <input type="hidden" class="invoice_challan_no" value="{{ $header->invoice_challan_no }}">
                        <input type="hidden" class="digi_wim_id" value="">

                        <input type="text" class="material_code char-10">
                    </td>

                    <td>
                        <input type="text" class="material_description">
                    </td>

                    <td>
                        <input type="text" class="batch_no  char-10">
                    </td>

                    <td>
                        <input type="date" class="mfg_date char-10">
                    </td>

                    <td>
                        <input type="date" class="expiry_date char-10">
                    </td>

                    <td>
                        <input type="text" class="qty char-6">
                    </td>

                    <td>
                        <input type="text" class="bin_no char-6">
                    </td>

                    <td>
                        <select class="goods_status">
                            <option value="">Select</option>
                            <option value="Good">Good</option>
                            <option value="Damage">Damage</option>
                            <option value="Short">Short</option>
                            <option value="Excess">Excess</option>
                        </select>
                    </td>

                    <td>
                        <input type="text" class="remarks">
                    </td>

                    <td>
                        <button type="button"
                                class="btn btn-success btn-sm post-row-btn"
                                data-url="{{ route('admin.digiwim.operation.storeItem') }}">
                            Post
                        </button>
                    </td>
                </tr>
                @endfor

            @endif
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('click', '.post-row-btn', function () {

    let button = $(this);
    let row = button.closest('tr');
    let url = button.data('url');

    let data = {
        _token: "{{ csrf_token() }}",
        operation_id: row.find('.operation_id').val(),
        invoice_challan_no: row.find('.invoice_challan_no').val(),
        digi_wim_id: row.find('.digi_wim_id').val(),
        material_code: row.find('.material_code').val(),
        material_description: row.find('.material_description').val(),
        batch_no: row.find('.batch_no').val(),
        mfg_date: row.find('.mfg_date').val(),
        expiry_date: row.find('.expiry_date').val(),
        qty: row.find('.qty').val(),
        bin_no: row.find('.bin_no').val(),
        goods_status: row.find('.goods_status').val(),
        remarks: row.find('.remarks').val()
    };

    button.prop('disabled', true).text('Posting...');

    $.ajax({
        url: url,
        type: 'POST',
        data: data,

        success: function (res) {

            if (res.status === true) {

                Swal.fire({
                    icon: 'success',
                    title: 'Posted',
                    text: res.message
                });

                button.removeClass('btn-success')
                    .addClass('btn-secondary')
                    .text('Posted')
                    .prop('disabled', true);

                row.addClass('table-success');

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Unable to post row.'
                });

                button.prop('disabled', false).text('Post');
            }
        },

        error: function (xhr) {

            let msg = 'Something went wrong.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: msg
            });

            button.prop('disabled', false).text('Post');
        }
    });
});
</script>

@if(!empty($headerSubmitted))
<script>
$(document).ready(function () {
    Swal.fire({
        icon: 'success',
        title: 'Header Submitted Successfully',
        text: 'Now please add/post material details below.',
        confirmButtonText: 'OK'
    }).then(() => {
        $('html, body').animate({
            scrollTop: $('#materialDetailsSection').offset().top - 80
        }, 700);
    });
});
</script>
@endif

@endpush