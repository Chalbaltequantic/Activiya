@extends('admin.admin')
@section('bodycontent')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet"/>

<style>

/* ===== FORM LAYOUT FIX ===== */

.form-row-4 .form-group{
    display:flex;
    align-items:center;
    margin-bottom:10px;
}

.form-row-4 label{
    width:140px;
    font-weight:600;
    margin-bottom:0;
}

.form-row-4 .form-control,
.form-row-4 textarea,
.form-row-4 .select2-container{
    flex:1;
}



/* Select2 Fix */

.select2-container--bootstrap4 .select2-selection--single{
    height:38px;
    padding:6px 10px;
}

.select2-container--bootstrap4 .select2-selection__arrow{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    right:10px;
}

/* Annexure headings */

.annexure-heading{
    font-weight:700;
    background:#e5d798;
    padding:6px 10px;
    margin-bottom:10px;
    border-left:4px solid #007bff;
}
/* ===== ANNEXURE BLOCK DESIGN ===== */

.annexure-block{
    border:1px solid #ffc107;
    border-radius:6px;
    margin-bottom:20px;
    padding:10px;
    background:#fff;
}

/* Header inside each annexure */
.annexure-title{
    background:#e5d798;
    padding:8px 12px;
    font-weight:600;
    border-radius:4px;
    margin-bottom:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* Section headings (Loading / Unloading / Others) */
.annexure-subheading{
    font-weight:600;
    background:#f4f6f9;
    padding:6px 10px;
    margin:10px 0;
    border-left:4px solid #007bff;
}

/* Remove button */
.remove-annexure{
    font-size:12px;
}
</style>

<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1>Create Invoice</h1>
</div>
</div>
</div>
</div>

<div class="content">
<div class="container-fluid">
<div class="card">
<div class="card-body">

<form method="POST" action="{{ route('admin.invoice.store') }}">
@csrf


<div class="row form-row-4">

<div class="form-group col-md-4">
<label>Client</label>
<select name="site_plant_id" class="form-control select2">
<option value="">Select Client</option>
@foreach($plants as $p)
<option value="{{ $p->id }}">{{ $p->plant_site_name }} ({{ $p->plant_site_code }})</option>
@endforeach
</select>
</div>

<div class="form-group col-md-4">
<label>Indent Id</label>
<input type="text" name="indent_id" class="form-control">
</div>
<div class="form-group col-md-4">
<label>PO No.</label>
<input type="text" name="items[0][po_no]" class="form-control">
</div>
<div class="form-group col-md-4">
<label>From</label>
<input type="text" name="items[0][from]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>To</label>
<input type="text" name="items[0][to]" class="form-control">
</div>
<div class="form-group col-md-4">
<label>Invoice No</label>
<input type="text" name="invoice_no" value="{{ $invoiceNo }}" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Invoice Date</label>
<input type="date" name="bill_date" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Billing Address</label>
<select name="billing_address_id" class="form-control">
@foreach($vendor->addresses->where('address_type','Billing') as $b)
<option value="{{ $b->id }}">{{ $b->address_line1 }}, {{ $b->city }}</option>
@endforeach
</select>
</div>

<div class="form-group col-md-4">
<label>Branch Address</label>
<select name="branch_address_id" class="form-control">
@foreach($vendor->addresses->where('address_type','Branch') as $b)
<option value="{{ $b->id }}">{{ $b->address_line1 }}, {{ $b->city }}</option>
@endforeach
</select>
</div>

<div class="form-group col-md-4">
<label>LR No</label>
<input type="text" name="items[0][lr_no]" class="form-control" maxlength="10">
</div>

<div class="form-group col-md-4">
<label>LR Date</label>
<input type="date" name="items[0][lr_date]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Truck Type</label>
<input type="text" name="items[0][truck_type]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Truck No.</label>
<input type="text" name="items[0][vehicle_no]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Actual Weight</label>
<input type="text" name="items[0][actual_weight]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Charge Weight</label>
<input type="text" name="items[0][charge_weight]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Dispatch Date</label>
<input type="date" name="items[0][vehicle_dispatch_date]" class="form-control">
</div>



<div class="form-group col-md-4">
<label>Base Freight</label>
 <input type="number"  name="items[0][base_freight]" class="form-control">
</div>

<div class="form-group col-md-4">
<label>Taxable</label>
 <input type="text"  name="items[0][taxable]" class="form-control" readonly>
</div>
<div class="form-group col-md-4">

<label>GST</label>
 <input type="number" name="items[0][gst]" class="form-control">
</div>


</div>

{{-- ================= ANNEXURE ================= --}}

<div class="card card-warning mt-3">
	<div class="main-header navbar-white d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Annexure Details</h3>

    <button type="button" class="btn btn-success btn-sm" id="addAnnexure">
        + Add More Annexure
    </button>
	</div>

	<div class="card-body" id="annexureContainer">

		<div class="annexure-block" data-index="0">
		  <div class="annexure-title">
			<span>Annexure 1</span>
		</div>

		<div class="annexure-subheading">Loading Point</div>

		<div class="row form-row-4">

			<div class="form-group col-md-4">
			<label>Customer Ref / Indent Id</label>
			<input type="text" name="annexures[0][customer_ref_no]" class="form-control" maxlength="10">
			</div>

			<div class="form-group col-md-4">
			<label>OBD / LR No.</label>
			<input type="text" name="annexures[0][obd_po_no]" class="form-control" maxlength="10" >
			</div>


			<div class="form-group col-md-4">
			<label>Arrival Date</label>
			<input type="date" name="annexures[0][arrival_date]" class="form-control">
			</div>

			<div class="form-group col-md-4">
			<label>Dispatch Date</label>
			<input type="date" name="annexures[0][dispatch_date]" class="form-control">
			</div>
			<div class="form-group col-md-4">

			<label>Detention Days</label>
			<input type="text" name="annexures[0][loading_detention_days]" class="form-control" readonly>
			</div>


			<div class="form-group col-md-4">
			<label>Detention Charge</label>
			<input type="number" name="annexures[0][loading_detention_charge]" class="form-control">
			</div>

			<div class="form-group col-md-4">
			<label>Loading Charge</label>
			<input type="number" name="annexures[0][loading_charge]" class="form-control">
			</div>

			<div class="form-group col-md-4">
			<label>Two Point Loading Charge</label>
			<input type="number" name="annexures[0][two_point_loading_charge]" class="form-control">
			</div>


			


		</div>

		<div class="annexure-subheading mt-3">Unloading Point</div>

		<div class="row form-row-4">
			<div class="form-group col-md-4">
			<label>Reporting Date</label>
			<input type="date" name="annexures[0][reporting_date]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Unloading Date</label>
			<input type="date" name="annexures[0][unloading_date]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Detention Days</label>
			<input type="text" name="annexures[0][unloading_detention_days]" class="form-control" readonly>
			</div>
			<div class="form-group col-md-4">
			<label>Transit Days</label>
			<input type="number" name="annexures[0][transit_days]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Detention Charge</label>
			<input type="number" name="annexures[0][unloading_detention_charge]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Unloading Charge</label>
			<input type="number" name="annexures[0][unloading_charge]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Two Point Delivery Charge</label>
			<input type="number" name="annexures[0][two_point_delivery_charge]" class="form-control">
			</div>
			
			<div class="form-group col-md-4">
			<label>Freight</label>
			<input type="text" name="annexures[0][freight]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>GR Charges</label>
			<input type="number" name="annexures[0][gr_charges]" class="form-control">
			</div>
			


		</div>

		<div class="annexure-subheading mt-3">Others</div>

		<div class="row form-row-4">
			<div class="form-group col-md-4">
			<label>Fix Rental</label>
			<input type="number" name="annexures[0][fix_rental]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Green Tax</label>
			<input type="number" name="annexures[0][green_tax]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Toll Tax</label>
			<input type="number" name="annexures[0][toll_tax]" class="form-control">
			</div>


		</div>

	</div>
	</div>
</div>
<div class="mt-3">
    <button type="submit" name="action" value="draft" class="btn btn-warning">
        Save as Draft
    </button>

    <button type="submit" name="action" value="final" class="btn btn-success">
        Save Final
    </button>
</div>


</form>

</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    recalculateInvoiceAmounts();
});

let annexureIndex = 1;


$('#addAnnexure').click(function () {
    let newBlock = $('.annexure-block:first').clone();

    newBlock.attr('data-index', annexureIndex);

    newBlock.find('.annexure-title span').text('Annexure ' + (annexureIndex + 1));

    if (newBlock.find('.remove-annexure').length === 0) {
        newBlock.find('.annexure-title').append(`
            <button type="button" class="btn btn-danger btn-sm remove-annexure">
                Remove
            </button>
        `);
    }

    newBlock.find('input').each(function () {
        let oldName = $(this).attr('name');

        if (oldName) {
            let newName = oldName.replace(/\[\d+\]/, '[' + annexureIndex + ']');
            $(this).attr('name', newName);
        }

        $(this).val('');
    });

    $('#annexureContainer').append(newBlock);

    annexureIndex++;

    setTimeout(function () {
        recalculateInvoiceAmounts();
    }, 100);
});


$(document).on('click', '.remove-annexure', function () {
    $(this).closest('.annexure-block').remove();

    setTimeout(function () {
        recalculateInvoiceAmounts();
    }, 100);
});


function getNumber(value) {
    let num = parseFloat(value);
    return isNaN(num) ? 0 : num;
}


function parseDate(value) {
    if (!value) return null;

    value = value.trim();

    // yyyy-mm-dd
    if (value.indexOf('-') > -1) {
        let parts = value.split('-');
        if (parts.length === 3) {
            let year = parseInt(parts[0], 10);
            let month = parseInt(parts[1], 10) - 1;
            let day = parseInt(parts[2], 10);

            let date = new Date(year, month, day);
            return isNaN(date.getTime()) ? null : date;
        }
    }

    // mm/dd/yyyy
    if (value.indexOf('/') > -1) {
        let parts = value.split('/');
        if (parts.length === 3) {
            let month = parseInt(parts[0], 10) - 1;
            let day = parseInt(parts[1], 10);
            let year = parseInt(parts[2], 10);

            let date = new Date(year, month, day);
            return isNaN(date.getTime()) ? null : date;
        }
    }

    return null;
}

function getDayDifference(fromDate, toDate) {
    let startDate = parseDate(fromDate);
    let endDate = parseDate(toDate);

    if (!startDate || !endDate) {
        return 0;
    }

    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);

    let difference = endDate.getTime() - startDate.getTime();
    let days = difference / (1000 * 60 * 60 * 24);

    return days > 0 ? Math.floor(days) : 0;
}


function calculateDetentionDays() {
    $('#annexureContainer').find('.annexure-block').each(function () {
        let block = $(this);
        let index = block.attr('data-index');

        let arrivalDate = block.find('input[name="annexures[' + index + '][arrival_date]"]').val();
        let dispatchDate = block.find('input[name="annexures[' + index + '][dispatch_date]"]').val();

        let reportingDate = block.find('input[name="annexures[' + index + '][reporting_date]"]').val();
        let unloadingDate = block.find('input[name="annexures[' + index + '][unloading_date]"]').val();

        // Loading detention days = dispatch_date - arrival_date
        let loadingDetentionDays = getDayDifference(arrivalDate, dispatchDate);

        // Unloading detention days = unloading_date - reporting_date
        let unloadingDetentionDays = getDayDifference(reportingDate, unloadingDate);

        block.find('input[name="annexures[' + index + '][loading_detention_days]"]').val(loadingDetentionDays);
        block.find('input[name="annexures[' + index + '][unloading_detention_days]"]').val(unloadingDetentionDays);
    });
}


function calculateAnnexureTotals() {
    let gstApplicableTotal = 0;
    let nonGstTotal = 0;

    $('#annexureContainer').find('input').each(function () {
        let fieldName = $(this).attr('name');
        let fieldValue = getNumber($(this).val());

        if (!fieldName) return;

        // GST applicable fields
        if (
            fieldName.includes('[freight]') ||
            fieldName.includes('[loading_detention_charge]') ||
            fieldName.includes('[loading_charge]') ||
            fieldName.includes('[two_point_loading_charge]') ||
            fieldName.includes('[unloading_detention_charge]') ||
            fieldName.includes('[unloading_charge]') ||
            fieldName.includes('[two_point_delivery_charge]') ||
            fieldName.includes('[gr_charges]') ||
            fieldName.includes('[fix_rental]')
        ) {
            gstApplicableTotal += fieldValue;
        }

        // GST not applicable fields
        if (
            fieldName.includes('[toll_tax]') ||
            fieldName.includes('[green_tax]')
        ) {
            nonGstTotal += fieldValue;
        }
    });

    return {
        gstApplicableTotal: gstApplicableTotal,
        nonGstTotal: nonGstTotal
    };
}


function calculateInvoiceAmount() {
    let baseFreight = getNumber($('input[name="items[0][base_freight]"]').val());
    let gstPercent = getNumber($('input[name="items[0][gst]"]').val());

    let annexureTotals = calculateAnnexureTotals();

    // GST taxable amount
    let taxableAmount = baseFreight + annexureTotals.gstApplicableTotal;

    // GST amount
    let gstAmount = taxableAmount * gstPercent / 100;

    // Final payable amount
    let totalAmount = taxableAmount + gstAmount + annexureTotals.nonGstTotal;

    $('input[name="items[0][taxable]"]').val(taxableAmount.toFixed(2));

    // Optional hidden/display fields if you add them later
   /* if ($('input[name="items[0][gst_amount]"]').length) {
        $('input[name="items[0][gst_amount]"]').val(gstAmount.toFixed(2));
    }*/

    if ($('input[name="items[0][total]"]').length) {
        $('input[name="items[0][total]"]').val(totalAmount.toFixed(2));
    }
}


function recalculateInvoiceAmounts() {
    calculateDetentionDays();
    calculateInvoiceAmount();
}

$(document).on('keyup change', 'input[name="items[0][base_freight]"]', function () {
    calculateInvoiceAmount();
});

// Recalculate when GST percent changes
$(document).on('keyup change', 'input[name="items[0][gst]"]', function () {
    calculateInvoiceAmount();
});

// Recalculate when any annexure field changes
$(document).on('keyup change', '#annexureContainer input', function () {
    recalculateInvoiceAmounts();
});

// Recalculate specifically when date fields change
$(document).on(
    'change blur',
    '#annexureContainer input[name*="[arrival_date]"], ' +
    '#annexureContainer input[name*="[dispatch_date]"], ' +
    '#annexureContainer input[name*="[reporting_date]"], ' +
    '#annexureContainer input[name*="[unloading_date]"]',
    function () {
        calculateDetentionDays();
    }
);
</script>
@endsection