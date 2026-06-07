@extends('admin.admin')

@section('bodycontent')
<link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">  
<style>

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

</style>
<div class="content">
<div class="container-fluid">

		@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
		@endif

		@if(session('error'))
		<div class="alert alert-danger">{{ session('error') }}</div>
		@endif

		@if(empty($header->id))
		<div class="card">
			<div class="card-header bg-info">
				<b>Enter following in case of Operation</b>
			</div>
		<div class="card-body">

		<form method="POST" action="{{ route('admin.digiwimpreloading.operation.storeHeader') }}">
		@csrf

		<div class="row form-row-4">

			
			<div class="form-group col-md-4">        
				<label>Select Type</label>
					<select name="operation_type" class="form-control" required>
						<option value="loading" {{ (($header->operation_type ?? '') == 'loading') ? 'selected' : '' }}>Loading</option>
					</select>
			</div>

			<div class="form-group col-md-4"> 
				<label>Invoice / Challan /OBD</label>
					<input type="text" name="invoice_challan_no" class="form-control"
							   value="{{ $header->invoice_challan_no ?? '' }}" required>
			</div>

			<div class="form-group col-md-4"> 
				<label>Invoice Date</label>
						<input type="date" name="invoice_date" class="form-control"
							   value="{{ $header->invoice_date ?? '' }}">
			</div>

			 <div class="form-group col-md-4"> 
					<label>Buyer Code & Name</label>          
						<input type="text" name="supplier_code_name" class="form-control"
							   value="{{ $header->supplier_code_name ?? '' }}">
			</div>

			 <div class="form-group col-md-4"> 
				<label>UOM</label>
					<select name="uom" class="form-control">
						<option value="">Select</option>
						<option value="case" {{ (($header->uom ?? '') == 'case') ? 'selected' : '' }}>Case</option>
						<option value="pcs" {{ (($header->uom ?? '') == 'pcs') ? 'selected' : '' }}>PCS</option>
						<option value="kg" {{ (($header->uom ?? '') == 'kg') ? 'selected' : '' }}>KG</option>
					</select>
			</div>


			 <div class="form-group col-md-4"> 
				
				<label>Transporter Name</label>
				<input type="text" name="transporter_name" class="form-control"
							   value="{{ $header->transporter_name ?? '' }}">
			</div>

			 <div class="form-group col-md-4"> 
					<label>Truck No.</label>
						<input type="text" name="truck_number" class="form-control"
							   value="{{ $header->truck_number ?? '' }}">
			</div>

			<div class="form-group col-md-4"> 
				<label>Truck Type</label>
						<select name="truck_type" class="form-control">
							<option value="">Select</option>
							<option value="Open" {{ (($header->truck_type ?? '') == 'Open') ? 'selected' : '' }}>Open</option>
							<option value="Container" {{ (($header->truck_type ?? '') == 'Container') ? 'selected' : '' }}>Container</option>
							<option value="Trailer" {{ (($header->truck_type ?? '') == 'Trailer') ? 'selected' : '' }}>Trailer</option>
						</select>
			</div>

			<div class="form-group col-md-4"> 
					<label>LR No.</label>
					<input type="text" name="lr_no" class="form-control" value="{{ $header->lr_no ?? '' }}">         
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
		@endif

		@if(!empty($header->id))

		<div class="card mb-3">
			<div class="card-header bg-success">
				<b>Header Information</b>
			</div>

			<div class="card-body">

				<div class="row">

					<div class="col-md-3">
						<b>Operation ID:</b>
						{{ $header->id }}
					</div>

					<div class="col-md-3">
						<b>Invoice No:</b>
						{{ $header->invoice_challan_no }}
					</div>

					<div class="col-md-3">
						<b>Invoice Date:</b>
						{{ $header->invoice_date }}
					</div>

					<div class="col-md-3">
						<b>Buyer:</b>
						{{ $header->supplier_code_name }}
					</div>

				</div>

				<div class="row mt-2">

					<div class="col-md-3">
						<b>Transporter:</b>
						{{ $header->transporter_name }}
					</div>

					<div class="col-md-3">
						<b>Truck No:</b>
						{{ $header->truck_number }}
					</div>

					<div class="col-md-3">
						<b>LR No:</b>
						{{ $header->lr_no }}
					</div>

					{{-- <div class="col-md-3">
						<a href="{{ route('admin.digiwimpreloading.operation.headerEdit',$header->id) }}"
						   class="btn btn-warning btn-sm">
							Edit Header
						</a>
					</div>
					--}}

				</div>

			</div>
		</div>

		@endif
		</div>

		</div>

@if(isset($header))
<div class="card" id="materialDetailsSection">
	<div class="card-header bg-primary text-white">
        <b>Manual Input</b>
    </div>

    <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
			 
	<table id="table" class="table table-bordered border-dark table-hover">
			
            <thead>
                <tr>
                    <th style="background: #fce4d6; color: #0070c0;">Product/Material Code</th>
                    <th style="background: #fce4d6; color: #0070c0;">Product Material Description</th>
                    <th style="background: #fce4d6; color: #0070c0;">Batch No.</th>
                    <th style="background: #fce4d6; color: #0070c0;">MFG Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Expiry Date</th>
                    <th style="background: #fce4d6; color: #0070c0;">Qty</th>
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
                    <td class="char-10">
                        <input type="hidden" class="operation_id" value="{{ $header->id }}">
                        <input type="hidden" class="invoice_challan_no" value="{{ $header->invoice_challan_no }}">
                        <input type="hidden" class="digi_wim_preloading_id" value="{{ $row->id }}">

                        <input type="text" class="material_code" value="{{ $row->m_code }}">
                    </td>

                    <td>
                        <input type="text" class="material_description" value="{{ $row->material_descriptions }}">
                    </td>

                    <td class="char-10">
                        <input type="text" class="batch_no" value="{{ $row->batch_no }}">
                    </td>

                    <td class="char-10">
                        <input type="date" class="mfg_date"
                               value="{{ !empty($row->mfg_date) ? \Carbon\Carbon::parse($row->mfg_date)->format('Y-m-d') : '' }}">
                    </td>

                    <td class="char-10">
                        <input type="date" class="expiry_date"
                               value="{{ !empty($row->expiry_date) ? \Carbon\Carbon::parse($row->expiry_date)->format('Y-m-d') : '' }}">
                    </td>

                    <td class="char-6">
                        <input type="text" class="qty" value="{{ $row->qty_units }}">
                    </td>

                    <td class="char-10">
                        <input type="text" class="bin_no">
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
                                data-url="{{ route('admin.digiwimpreloading.operation.storeItem') }}">
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
                        <input type="hidden" class="digi_wim_preloading_id" value="">

                        <input type="text" class="material_code">
                    </td>

                    <td>
                        <input type="text" class="material_description">
                    </td>

                    <td>
                        <input type="text" class="batch_no">
                    </td>

                    <td>
                        <input type="date" class="mfg_date">
                    </td>

                    <td>
                        <input type="date" class="expiry_date">
                    </td>

                    <td>
                        <input type="text" class="qty">
                    </td>

                    <td>
                        <input type="text" class="bin_no">
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
                                data-url="{{ route('admin.digiwimpreloading.operation.storeItem') }}">
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
        digi_wim_preloading_id: row.find('.digi_wim_preloading_id').val(),
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

@if(session('headerSubmitted'))
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