@extends('admin.admin')

@section('bodycontent')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}
.insurance-header-table,
.product-entry-table,
.saved-product-table {
    font-size: 12px;
    margin-bottom: 0;
}
.insurance-header-table th,
.insurance-header-table td,
.product-entry-table th,
.product-entry-table td,
.saved-product-table th,
.saved-product-table td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 5px 8px;
}
.insurance-header-table th,
.product-entry-table thead th,
.saved-product-table thead th {
    background: #fce4d6;
    color: #0070c0;
}
.product-entry-table {
    min-width: 1800px;
}
.product-entry-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}
.product-table-container {
    max-height: 520px;
    overflow-y: auto;
    border: 1px solid #ccc;
}
.product-entry-table input {
    min-width: 115px;
    height: 31px;
    padding: 3px 6px;
    font-size: 12px;
}
.product-entry-table .description-input {
    min-width: 220px;
}
.lookup-note {
    display: block;
    font-size: 10px;
    margin-top: 2px;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Insurance Product Entry</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="/admin/dashboard">Dashboard</a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.insurance.manual-upload', ['tab' => 'files']) }}">
                            Insurance Manual Upload
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Product Entry
                    </li>
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

                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Insurance Details</strong>
                            </div>

                            <div class="col-md-6 text-right">
                                <a href="{{ route('admin.insurance.manual-upload', ['tab' => 'files']) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-bordered insurance-header-table">

                                <tr>
                                    <th>Loss Date</th>
                                    <td>
                                        {{ $insurance->loss_date ? $insurance->loss_date->format('Y-m-d') : '' }}
                                    </td>

                                    <th>Nature of Claim</th>
                                    <td>
                                        {{ $insurance->nature_of_claim }}
                                    </td>

                                    <th>Invoice No.</th>
                                    <td>
                                        {{ $insurance->invoice_no }}
                                    </td>

                                    <th>Invoice Date</th>
                                    <td>
                                        {{ $insurance->invoice_date ? $insurance->invoice_date->format('Y-m-d') : '' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>From Location Code</th>
                                    <td>
                                        {{ $insurance->from_location_code }}
                                    </td>

                                    <th>From Location</th>
                                    <td>
                                        {{ $insurance->from_location }}
                                    </td>

                                    <th>To Location Code</th>
                                    <td>
                                        {{ $insurance->to_location_code }}
                                    </td>

                                    <th>To Location</th>
                                    <td>
                                        {{ $insurance->to_location }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>Transporter Name</th>
                                    <td>
                                        {{ $insurance->transporter_name }}
                                    </td>

                                    <th>LR No.</th>
                                    <td>
                                        {{ $insurance->lr_no }}
                                    </td>

                                    <th>LR Date</th>
                                    <td>
                                        {{ $insurance->lr_date ? $insurance->lr_date->format('Y-m-d') : '' }}
                                    </td>

                                    <th></th>
                                    <td></td>
                                </tr>

                            </table>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12">

                <div class="card">

                    <div class="card-header">

                        <div class="row">

                            <div class="col-md-6">
                                <strong>Add Insurance Products</strong>
                                <br>
                                <small class="text-muted">
                                    Initial 20 rows. Add More will add 20 rows each time up to 100 rows.
                                </small>
                            </div>

                            <div class="col-md-6 text-right">

                                <button type="button" class="btn btn-info btn-sm" id="addMoreRows">
                                    <i class="fas fa-plus"></i> Add More
                                </button>

                                <span class="ml-2">
                                    Rows:
                                    <strong id="rowCount">20</strong>
                                    / 100
                                </span>

                            </div>

                        </div>

                    </div>

                    <form method="POST" action="{{ route('admin.insurance.products.store', $insurance->id) }}" id="productForm">

                        @csrf

                        <div class="card-body p-0">

                            <div class="table-responsive-fixed product-table-container">

                                <table class="table table-bordered table-hover product-entry-table" id="productTable">

                                    <thead>

                                        <tr>
                                            <th>#</th>
                                            <th>LR No.</th>
                                            <th>Invoice No.</th>
                                            <th>Product Code</th>
                                            <th>Product Description</th>
                                            <th>Batch No.</th>
                                            <th>Damage Quantity</th>
                                            <th>Shortage Quantity</th>
                                            <th>Damage Value</th>
                                            <th>Shortage Value</th>
                                            <th>Total Value</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        @for($i = 0; $i < 20; $i++)

                                        <tr data-row="{{ $i }}">

                                            <td class="row-number">
                                                {{ $i + 1 }}
                                            </td>

                                            <td>
                                                <input type="text" name="lr_no[{{ $i }}]" class="form-control lr-no" value="{{ $insurance->lr_no }}">
                                            </td>

                                            <td>
                                                <input type="text" name="invoice_no[{{ $i }}]" class="form-control invoice-no" value="{{ $insurance->invoice_no }}">
                                            </td>

                                            <td>
                                                <input type="text" name="product_code[{{ $i }}]" class="form-control product-code">
                                                <small class="lookup-note product-note"></small>
                                            </td>

                                            <td>
                                                <input type="text" name="product_description[{{ $i }}]" class="form-control description-input product-description">
                                            </td>

                                            <td>
                                                <input type="text" name="batch_no[{{ $i }}]" class="form-control batch-no">
                                            </td>

                                            <td>
                                                <input type="number" step="0.001" min="0" name="damage_quantity[{{ $i }}]" class="form-control damage-quantity" value="0">
                                            </td>

                                            <td>
                                                <input type="number" step="0.001" min="0" name="shortage_quantity[{{ $i }}]" class="form-control shortage-quantity" value="0">
                                            </td>

                                            <td>
                                                <input type="number" step="0.01" min="0" name="damage_value[{{ $i }}]" class="form-control damage-value" value="0.00">
                                            </td>

                                            <td>
                                                <input type="number" step="0.01" min="0" name="shortage_value[{{ $i }}]" class="form-control shortage-value" value="0.00">
                                            </td>

                                            <td>
                                                <input type="number" step="0.01" min="0" name="total_value[{{ $i }}]" class="form-control total-value" value="0.00" readonly>

                                                <input type="hidden" name="recovery_mrp[{{ $i }}]" class="recovery-mrp" value="0">
                                            </td>

                                        </tr>

                                        @endfor

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        <div class="card-footer text-right">

                            <a href="{{ route('admin.insurance.manual-upload', ['tab' => 'files']) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

                            <button type="submit" class="btn btn-success" id="saveProducts">
                                <i class="fas fa-save"></i> Save Products
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        @if($products->count() > 0)

        <div class="row">

            <div class="col-lg-12">

                <div class="card">

                    <div class="card-header">
                        <strong>Saved Products</strong>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover saved-product-table">

                                <thead>

                                    <tr>
                                        <th>#</th>
                                        <th>LR No.</th>
                                        <th>Invoice No.</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Batch No.</th>
                                        <th>Damage Qty</th>
                                        <th>Shortage Qty</th>
                                        <th>Recovery MRP</th>
                                        <th>Damage Value</th>
                                        <th>Shortage Value</th>
                                        <th>Total Value</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($products as $index => $product)

                                    <tr>

                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->lr_no }}</td>
                                        <td>{{ $product->invoice_no }}</td>
                                        <td>{{ $product->product_code }}</td>
                                        <td>{{ $product->product_description }}</td>
                                        <td>{{ $product->batch_no }}</td>
                                        <td>{{ number_format((float)$product->damage_quantity, 3) }}</td>
                                        <td>{{ number_format((float)$product->shortage_quantity, 3) }}</td>
                                        <td>{{ number_format((float)$product->recovery_mrp, 2) }}</td>
                                        <td>{{ number_format((float)$product->damage_value, 2) }}</td>
                                        <td>{{ number_format((float)$product->shortage_value, 2) }}</td>
                                        <td>{{ number_format((float)$product->total_value, 2) }}</td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @endif

    </div>
</div>

<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let totalRows = 20;
    const maxRows = 100;

    function calculateRow(row) {

        let damageQty = parseFloat(row.find('.damage-quantity').val()) || 0;
        let shortageQty = parseFloat(row.find('.shortage-quantity').val()) || 0;
        let mrp = parseFloat(row.find('.recovery-mrp').val()) || 0;

        let damageValue = parseFloat(row.find('.damage-value').val()) || 0;
        let shortageValue = parseFloat(row.find('.shortage-value').val()) || 0;

        if (mrp > 0) {
            damageValue = damageQty * mrp;
            shortageValue = shortageQty * mrp;

            row.find('.damage-value').val(damageValue.toFixed(2));
            row.find('.shortage-value').val(shortageValue.toFixed(2));
        }

        let total = damageValue + shortageValue;

        row.find('.total-value').val(total.toFixed(2));
    }

    $(document).on('input change', '.damage-quantity, .shortage-quantity, .damage-value, .shortage-value', function() {

        calculateRow(
            $(this).closest('tr')
        );

    });

    function lookupProduct(row) {

        let productCode = $.trim(
            row.find('.product-code').val()
        );

        let invoiceNo = $.trim(
            row.find('.invoice-no').val()
        );

        let note = row.find('.product-note');

        if (!productCode) {
            return;
        }

        note
            .removeClass()
            .addClass('lookup-note text-info')
            .text('Checking...');

        $.ajax({

            url: "{{ route('admin.insurance.products.lookup', $insurance->id) }}",

            type: 'POST',

            data: {
                product_code: productCode,
                invoice_no: invoiceNo
            },

            success: function(response) {

                if (!response.success) {

                    note
                        .removeClass()
                        .addClass('lookup-note text-danger')
                        .text('Not found - enter manually');

                    return;

                }

                if (
                    response.product_description !== null &&
                    response.product_description !== ''
                ) {
                    row.find('.product-description').val(response.product_description);
                }

                if (
                    response.batch_no !== null &&
                    response.batch_no !== ''
                ) {
                    row.find('.batch-no').val(response.batch_no);
                }

                if (
                    parseFloat(response.damage_quantity) > 0
                ) {
                    row.find('.damage-quantity').val(response.damage_quantity);
                }

                if (
                    parseFloat(response.shortage_quantity) > 0
                ) {
                    row.find('.shortage-quantity').val(response.shortage_quantity);
                }

                if (
                    parseFloat(response.recovery_mrp) > 0
                ) {
                    row.find('.recovery-mrp').val(response.recovery_mrp);
                } else {
                    row.find('.recovery-mrp').val(0);
                }

                if (
                    parseFloat(response.damage_value) > 0
                ) {
                    row.find('.damage-value').val(
                        parseFloat(response.damage_value).toFixed(2)
                    );
                }

                if (
                    parseFloat(response.shortage_value) > 0
                ) {
                    row.find('.shortage-value').val(
                        parseFloat(response.shortage_value).toFixed(2)
                    );
                }

                calculateRow(row);

                if (
                    response.found_operation_item &&
                    response.found_material
                ) {

                    note
                        .removeClass()
                        .addClass('lookup-note text-success')
                        .text('Product and MRP found');

                } else if (response.found_operation_item) {

                    note
                        .removeClass()
                        .addClass('lookup-note text-warning')
                        .text('Product found. MRP not found - enter values manually');

                } else if (response.found_material) {

                    note
                        .removeClass()
                        .addClass('lookup-note text-warning')
                        .text('MRP found. Product details can be entered manually');

                } else {

                    note
                        .removeClass()
                        .addClass('lookup-note text-danger')
                        .text('Not found - enter manually');

                }

            },

            error: function() {

                note
                    .removeClass()
                    .addClass('lookup-note text-danger')
                    .text('Not found - enter manually');

            }

        });

    }

    $(document).on('change', '.product-code', function() {

        lookupProduct(
            $(this).closest('tr')
        );

    });

    $(document).on('paste', '.product-code', function() {

        let input = this;

        setTimeout(function() {

            lookupProduct(
                $(input).closest('tr')
            );

        }, 100);

    });

    function createRow(index) {

        let rowNumber = index + 1;

        return `
        <tr data-row="${index}">

            <td class="row-number">
                ${rowNumber}
            </td>

            <td>
                <input type="text"
                    name="lr_no[${index}]"
                    class="form-control lr-no"
                    value="{{ addslashes($insurance->lr_no ?? '') }}">
            </td>

            <td>
                <input type="text"
                    name="invoice_no[${index}]"
                    class="form-control invoice-no"
                    value="{{ addslashes($insurance->invoice_no ?? '') }}">
            </td>

            <td>
                <input type="text"
                    name="product_code[${index}]"
                    class="form-control product-code">

                <small class="lookup-note product-note"></small>
            </td>

            <td>
                <input type="text"
                    name="product_description[${index}]"
                    class="form-control description-input product-description">
            </td>

            <td>
                <input type="text"
                    name="batch_no[${index}]"
                    class="form-control batch-no">
            </td>

            <td>
                <input type="number"
                    step="0.001"
                    min="0"
                    name="damage_quantity[${index}]"
                    class="form-control damage-quantity"
                    value="0">
            </td>

            <td>
                <input type="number"
                    step="0.001"
                    min="0"
                    name="shortage_quantity[${index}]"
                    class="form-control shortage-quantity"
                    value="0">
            </td>

            <td>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="damage_value[${index}]"
                    class="form-control damage-value"
                    value="0.00">
            </td>

            <td>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="shortage_value[${index}]"
                    class="form-control shortage-value"
                    value="0.00">
            </td>

            <td>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="total_value[${index}]"
                    class="form-control total-value"
                    value="0.00"
                    readonly>

                <input type="hidden"
                    name="recovery_mrp[${index}]"
                    class="recovery-mrp"
                    value="0">
            </td>

        </tr>
        `;
    }

    $('#addMoreRows').click(function() {

        if (totalRows >= maxRows) {

            alert(
                'Maximum 100 product rows are allowed.'
            );

            return;

        }

        let addUntil = Math.min(
            totalRows + 20,
            maxRows
        );

        for (
            let i = totalRows;
            i < addUntil;
            i++
        ) {

            $('#productTable tbody').append(
                createRow(i)
            );

        }

        totalRows = addUntil;

        $('#rowCount').text(
            totalRows
        );

        if (totalRows >= maxRows) {

            $('#addMoreRows').prop(
                'disabled',
                true
            );

        }

    });

    $('#productForm').submit(function() {

        $('#saveProducts')
            .prop('disabled', true)
            .html(
                '<i class="fas fa-spinner fa-spin"></i> Saving...'
            );

    });

});
</script>

@endsection