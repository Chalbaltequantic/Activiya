@extends('admin.admin')
@section('bodycontent')
  <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     
    
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Vendor</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show ">
			<strong>{{session('success')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif	
              <div class="card-body p-0">
			
               <div class="card">
            <div class="card-header" style="background: #fce4d6; color: #0070c0;">
              <h3 class="card-title" style="color: #0070c0;">Add Vendor</h3>

            </div>
            <div class="card-body">
			
			<form action="{{route('admin.store.vendor')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;">Parent Vendor</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor code</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor type</th>
						
						<th style="background: #fce4d6; color: #0070c0;">Company code</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor name 1</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor name 2</th>
						<th style="background: #fce4d6; color: #0070c0;">Authorized person name</th>
						<th style="background: #fce4d6; color: #0070c0;">Authorized person phone</th>
						<th style="background: #fce4d6; color: #0070c0;">Authorized person Email</th>
						<th style="background: #fce4d6; color: #0070c0;">Email</th>
						<th style="background: #fce4d6; color: #0070c0;">Logo</th>
						<th style="background: #ddebf7; color: #0070c0;">Receipt type 1</th>
						
						{{-- <th style="background: #fce4d6; color: #0070c0;">Withholding tax type</th>
						<th style="background: #ddebf7; color: #0070c0;">TDS section 1</th> 
						 
						<th style="background: #fce4d6; color: #0070c0;">Receipt name</th>
						<th style="background: #fce4d6; color: #0070c0;">Withholding tax type_2</th>
						<th style="background: #fce4d6; color: #0070c0;">TDS section 2</th>
						<th style="background: #fce4d6; color: #0070c0;">PAN no</th>
						 <th style="background: #c6e0b4; color: #0070c0;">GSTIN number</th>
						<th style="background: #c6e0b4; color: #0070c0;">PAN GSTIN Check</th>
						<th style="background: #c6e0b4; color: #0070c0;">Terms of Payment Key</th>
						<th style="background: #c6e0b4; color: #0070c0;">Account Group</th>
						<th style="background: #c6e0b4; color: #0070c0;">Posting Block Overall</th>
						<th style="background: #c6e0b4; color: #0070c0;">Purchase block overall</th>
						<th style="background: #c6e0b4; color: #0070c0;">Service block</th>
						<th style="background: #c6e0b4; color: #0070c0;">Purchase Block-Purch Org</th>
						<th style="background: #c6e0b4; color: #0070c0;">Payment Block</th>
						--}}
						<th style="background: #c6e0b4; color: #0070c0;">Status</th>

					  
					</tr>
				  </thead>
				  <tbody>
				 
					<tr>
						<td class="">
							<select name="parent_id" class="" id="parent_id">
							<option value="">Select Vendor</option>
							@foreach($vendorlist as $vendordata)
							<option value="{{$vendordata->vendor_code}}">{{$vendordata->vendor_name}} ({{$vendordata->vendor_code}})</option>
							@endforeach	
							</select>
						</td>
						<td class="">
							<input type="text" id="vendor_code" name="vendor_code" class=""  value="{{old('vendor_code')}}" placeholder=" vendor code" autocomplete="off" required>
						</td>
						<td>
							<input type="text" id="vendor_type" name="vendor_type" class=""  value="{{old('vendor_type')}}" placeholder=" vendor type" autocomplete=
						"off" required>
						</td>
						  <td><input type="text" id="company_code" name="company_code" class="" value="{{old('company_code')}}" placeholder=" company code" required>
						  </td>
						  <td><input type="text" id="vendor_name" name="vendor_name" class="" value="{{old('vendor_name')}}" placeholder=" vendor name" required>
						  </td>
						  <td><input type="text" id="vendor_short_name" name="vendor_short_name" class="" value="{{old('vendor_short_name')}}" placeholder=" vendor name 2" required>
						  </td>
						  <td><input type="text" name="authorized_person_name" id="authorized_person_name" class="" placeholder=" Authrised person name" required>
						  </td>
						  <td><input type="text" id="authorized_person_phone"  name="authorized_person_phone" value="{{old('authorized_person_phone')}}" class="" placeholder=" authorized person phone">
						  </td>
						  <td><input type="text" id="authorized_person_mail" name="authorized_person_mail" class="" value="{{old('authorized_person_mail')}}" placeholder=" authorized person email">	</td>
						   <td>
							<input  type="text" id="email" name="email" value="{{old('email')}}" autocomplete="off" class="" placeholder="Email">
						  </td>
						   <td>
							<input  type="file" id="file" name="file" autocomplete="off">
						  </td>
						   <td>
							<input  type="text" id="receipt_type_1" name="receipt_type_1" value="{{old('receipt_type_1')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt type 1">
						  </td>
						  
						  {{--  <td><input  type="text" id="withholding_tax_type" name="withholding_tax_type" value="{{old('withholding_tax_type')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Withholding tax type"></td>
						  <td>
							<input  type="text" id="tds_section_1" name="tds_section_1" value="{{old('tds_section_1')}}" autocomplete="off" class=" hide-on-parent" placeholder="TDS section 1">
						  </td>
						 
						  <td>
							<input  type="text" id="receipt_name" name="receipt_name" value="{{old('receipt_name')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt name">
						  </td>
						  <td>
							<input  type="text" id="withholding_tax_type_2" name="withholding_tax_type_2" value="{{old('withholding_tax_type_2')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt name">
						  </td>
						  <td>
							<input  type="text" id="tds_section_2" name="tds_section_2" value="{{old('tds_section_2')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt name">
						  </td>
						  <td>
							<input  type="text" id="pan_no" name="pan_no" value="{{old('pan_no')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt name">
						  </td>
						 
						  <td>
							<input  type="text" id="gstin_number" name="gstin_number" value="{{old('gstin_number')}}" autocomplete="off" class=" hide-on-parent">
						  </td>
						  <td>
							<input  type="text" id="pan_gstin_check" name="pan_gstin_check" value="{{old('pan_gstin_check')}}" autocomplete="off" class=" hide-on-parent" placeholder=" Receipt name">
						  </td>
						  <td>
							<input  type="text" id="terms_of_payment_key" name="terms_of_payment_key" value="{{old('terms_of_payment_key')}}" autocomplete="off" class=" hide-on-parent" placeholder="Terms of payment key">
						  </td>
						  <td>
							<input  type="text" id="posting_block_overall" name="posting_block_overall" value="{{old('posting_block_overall')}}" autocomplete="off" class="" placeholder=" Posting Block Over all">
						  </td>
						  <td>
							<input  type="text" id="purchase_block_overall" name="purchase_block_overall" value="{{old('purchase_block_overall')}}" autocomplete="off" class="" placeholder="purchase block overall">
						  </td>
						  <td>
							<input  type="text" id="service_block" name="service_block" value="{{old('service_block')}}" autocomplete="off" class="" placeholder=" Receipt name">
						  </td>
						  <td>
						  <input  type="text" id="purchase_block_org" name="purchase_block_org" value="{{old('purchase_block_org')}}" autocomplete="off" class="" placeholder=" Purchase Block Org">
						  </td>
						  <td>
							<input  type="text" id="payment_block" name="payment_block" value="{{old('payment_block')}}" autocomplete="off" class="" placeholder=" Payment Block">
						  </td>
						  --}}
						   <td>
							<select id="inputStatus" name="status" class=" custom-select">             
							<option value="1">Active</option>
							<option value="0">Inactive</option>               
							</select>
						</td>
						</tr>  
				
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Submit</button>
				</div>
				</div>
			  
			</form>
			
			
			
			
            <!-- /.card-body -->
          </div>
        
		</div>       
		</div>           
	  </div>
	 </div>
	<!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
<script type="text/javascript">
   
 $(document).ready(function() {
	$("#title").on('blur', function(){
	var title = $(this).val();
	title = title.toLowerCase().replace(/\s+/g, '-').replace(/^-+/, '').replace(/-+$/, ''); //trim starting dash
	//trim ending dash //spaces to dashes;
	slug = title.replace('/\s/g','-');
	$("#slug").val(slug);    
	});
	
	$('#parent_id').on('change', function () {
        let parentVal = $(this).val();

        if (parentVal) {
            // Hide fields marked with X
            $('.hide-on-parent').each(function () {
                $(this).closest('.form-group').hide();   // hide wrapper
              //  $(this).removeAttr('required');          // remove required attr
            });
        } else {
            // Show fields again if no parent vendor selected
            $('.hide-on-parent').each(function () {
                $(this).closest('.form-group').show();
              //  $(this).attr('required', true);          // re-add required if needed
            });
        }
    });
	
	
});

document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('table');

    // Delegate paste event to table
    table.addEventListener('paste', function(e) {
        // Only handle paste when an input is focused
        if (document.activeElement.tagName !== 'INPUT') return;

        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text');

        // Split pasted data into rows and columns
        const rows = pastedData.split(/\r\n|\n|\r/).filter(row => row.length > 0);
        const startInput = document.activeElement;
        let startCell = startInput.closest('td');
        let startRow = startCell.parentElement;
        let rowIndex = Array.from(table.rows).indexOf(startRow);
        let colIndex = Array.from(startRow.cells).indexOf(startCell);

        // Loop through rows and columns and fill inputs
        rows.forEach((row, i) => {
            const cols = row.split('\t');
            const tr = table.rows[rowIndex + i];
            if (!tr) return;
            cols.forEach((col, j) => {
                const td = tr.cells[colIndex + j];
                if (!td) return;
                const input = td.querySelector('input');
                if (input) input.value = col;
            });
        });
    });
}); 
</script>
@endsection