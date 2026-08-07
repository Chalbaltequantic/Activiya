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
<input type="text" name="items[0][lr_no]" class="form-control">
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
			<input type="text" name="annexures[0][customer_ref_no]" class="form-control">
			</div>

			<div class="form-group col-md-4">
			<label>OBD / PO No.</label>
			<input type="text" name="annexures[0][obd_po_no]" class="form-control">
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
			<input type="text" name="annexures[0][detention_days]" class="form-control" readonly>
			</div>


			<div class="form-group col-md-4">
			<label>Detention Charge</label>
			<input type="number" name="annexures[0][detention_charge]" class="form-control">
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
			<input type="text" name="annexures[0][detention_days]" class="form-control" readonly>
			</div>
			<div class="form-group col-md-4">
			<label>Transit Days</label>
			<input type="number" name="annexures[0][transit_days]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Detention Charge</label>
			<input type="number" name="annexures[0][detention_charge]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Unloading Charge</label>
			<input type="number" name="annexures[0][unloading_charge]" class="form-control">
			</div>
			<div class="form-group col-md-4">
			<label>Two Point Delivery Charge</label>
			<input type="number" name="annexures[0][two_point_delivery_charge]" class="form-control">
			</div>
			
			{{--<div class="form-group col-md-4">
			<label>Freight</label>
			<input type="text" name="annexures[0][freight]" class="form-control">
			</div>--}}
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
$(function(){
$('.select2').select2({
theme:'bootstrap4'
});
});
</script>
<script>
let annexureIndex = 1;

$('#addAnnexure').click(function(){

    let newBlock = $('.annexure-block:first').clone();

    newBlock.attr('data-index', annexureIndex);

    // Update heading text
    newBlock.find('.annexure-title span')
        .text('Annexure ' + (annexureIndex + 1));

    // ADD REMOVE BUTTON ONLY FOR NEW BLOCK
    if(newBlock.find('.remove-annexure').length === 0){
        newBlock.find('.annexure-title').append(`
            <button type="button" class="btn btn-danger btn-sm remove-annexure">
                Remove
            </button>
        `);
    }

    // Update input names & clear values
    newBlock.find('input').each(function(){
        let name = $(this).attr('name');

        if(name){
            let newName = name.replace(/\[\d+\]/, '['+annexureIndex+']');
            $(this).attr('name', newName);
        }

        $(this).val('');
    });

    $('#annexureContainer').append(newBlock);

    annexureIndex++;
});


/* REMOVE ANNEXURE */
$(document).on('click', '.remove-annexure', function(){
    $(this).closest('.annexure-block').remove();
});
</script>
<script>
/* ================= CALCULATE ANNEXURE TOTAL ================= */

function calculateAnnexureTotal(){

    let total = 0;

    $('#annexureContainer').find('input').each(function(){

        let name = $(this).attr('name');

        if(!name) return;

        // Only sum numeric charge fields
        if(
           
            name.includes('[loading_charge]') ||
            name.includes('[unloading_charge]') ||
            name.includes('[two_point_loading_charge]') ||
            name.includes('[detention_charge]') ||
            name.includes('[fix_rental]') ||
            name.includes('[green_tax]') ||
            name.includes('[toll_tax]') ||  
			name.includes('[base_freight]') ||
			name.includes('[gr_charges]') ||
			name.includes('[two_point_delivery_charge]') 
        ){
            let val = parseFloat($(this).val());
            if(!isNaN(val)){
                total += val;
            }
        }

    });
	let basefreight = $('input[name="items[0][base_freight]"]').val();
	
	let sumtotal = total ;
    // Set total to Taxable field
    $('input[name="items[0][taxable]"]').val(sumtotal.toFixed(2));
}


/* ================= EVENTS ================= */

// Trigger on typing
$(document).on('keyup change', '#annexureContainer input', function(){
    calculateAnnexureTotal();
});

// Also trigger after adding annexure
$('#addAnnexure').click(function(){
    setTimeout(function(){
        calculateAnnexureTotal();
    }, 200);
});

</script>
@endsection