@extends('admin.admin')
@section('bodycontent')
       
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
			<form action="{{route('admin.store.vendor')}}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  
			<div class="row">
			
			 <div class="form-group col-md-3">
                <label for="parent_id">Parent Vendor</label>
                <select name="parent_id" class="form-control" id="parent_id">
					<option value="">Select Vendor</option>
					@foreach($vendorlist as $vendordata)
						<option value="{{$vendordata->vendor_code}}">{{$vendordata->vendor_name}} ({{$vendordata->vendor_code}})</option>
					@endforeach	
				</select>
								
				@error('parent_id')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			
			  <div class="form-group col-md-2">
                <label for="vendor_code">Vendor code</label>
                <input type="text" id="vendor_code" name="vendor_code" class="form-control"  value="{{old('vendor_code')}}" placeholder=" vendor code" autocomplete=
				"off" required>
				
				@error('vendor_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group col-md-2">
                <label for="vendor_type">Vendor type</label>
                <input type="text" id="vendor_type" name="vendor_type" class="form-control"  value="{{old('vendor_type')}}" placeholder=" vendor type" autocomplete=
				"off" required>
              @error('vendor_type')
              <span class="text-danger">{{$message}}</span>
              @enderror
              </div>
			
			  <div class="form-group col-md-2">
                <label for="company_code">Company code</label>
                <input type="text" id="company_code" name="company_code" class="form-control" value="{{old('company_code')}}" placeholder=" company code" required>
				@error('company_code')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  
			   <div class="form-group col-md-2">
                <label for="vendor_name">Vendor name 1</label>
                <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="{{old('vendor_name')}}" placeholder=" vendor name" required>
				@error('vendor_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-2">
                <label for="vendor_short_name">Vendor name 2</label>
                <input type="text" id="vendor_short_name" name="vendor_short_name" class="form-control" value="{{old('vendor_short_name')}}" placeholder=" vendor name 2" required>
				@error('vendor_short_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>

			  <div class="form-group col-md-2">
                <label for="authorized_person_name">Authorized person name</label>
                <input type="text" name="authorized_person_name" id="authorized_person_name" class="form-control" placeholder=" Authrised person name" required>
				@error('authorized_person_name')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  
			  <div class="form-group col-md-2">
					<label for="authorized_person_phone">Authorized person phone</label>
					<input type="text" id="authorized_person_phone"  name="authorized_person_phone" value="{{old('authorized_person_phone')}}" class="form-control" placeholder=" authorized person phone">
					@error('authorized_person_phone')
					<span class="text-danger">{{$message}}</span>
					@enderror
				</div>
				<div class="form-group  col-md-2">
					<label for="authorized_person_mail">Authorized person mail</label>
					<input type="text" id="authorized_person_mail" name="authorized_person_mail" class="form-control" value="{{old('authorized_person_mail')}}" placeholder=" authorized person email">					
					@error('authorized_person_mail')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				 <div class="form-group col-md-2">
					<label for="withholding_tax_type">Withholding tax type</label>
					<input  type="text" id="withholding_tax_type" name="withholding_tax_type" value="{{old('withholding_tax_type')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Withholding tax type">
					
					@error('withholding_tax_type')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div> 
				  
				  <div class="form-group col-md-2">
					<label for="tds_section_1">TDS section 1</label>
					<input  type="text" id="tds_section_1" name="tds_section_1" value="{{old('tds_section_1')}}" autocomplete="off" class="form-control hide-on-parent" placeholder="TDS section 1">
					
					@error('tds_section_1')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				  <div class="form-group col-md-2">
					<label for="receipt_type_1">Receipt type 1</label>
					<input  type="text" id="receipt_type_1" name="receipt_type_1" value="{{old('receipt_type_1')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt type 1">
					@error('receipt_type_1')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				  
				  <div class="form-group col-md-2">
					<label for="receipt_name">Receipt name</label>
					<input  type="text" id="receipt_name" name="receipt_name" value="{{old('receipt_name')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('receipt_name')
					<span class="text-danger">{{$message}}</span>
					@enderror
				  </div>
				<div class="form-group col-md-2">
					<label for="withholding_tax_type_2">Withholding tax type_2</label>
					<input  type="text" id="withholding_tax_type_2" name="withholding_tax_type_2" value="{{old('withholding_tax_type_2')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('withholding_tax_type_2')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 <div class="form-group col-md-2">
					<label for="tds_section_2">TDS section 2</label>
					<input  type="text" id="tds_section_2" name="tds_section_2" value="{{old('tds_section_2')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('tds_section_2')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 
				 <div class="form-group col-md-2">
					<label for="pan_no">PAN no</label>
					<input  type="text" id="pan_no" name="pan_no" value="{{old('pan_no')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('pan_no')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 
				  <div class="form-group col-md-2">
					<label for="email">Email</label>
					<input  type="text" id="email" name="email" value="{{old('email')}}" autocomplete="off" class="form-control" placeholder=" Receipt name">
					@error('email')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 
				  <div class="form-group col-md-2">
					<label for="gstin_number">GSTIN number</label>
					<input  type="text" id="gstin_number" name="gstin_number" value="{{old('gstin_number')}}" autocomplete="off" class="form-control hide-on-parent">
					@error('gstin_number')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				  
				<div class="form-group col-md-2">
					<label for="pan_gstin_check">PAN GSTIN Check</label>
					<input  type="text" id="pan_gstin_check" name="pan_gstin_check" value="{{old('pan_gstin_check')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('pan_gstin_check')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 <div class="form-group col-md-2">
					<label for="terms_of_payment_key">Terms of Payment Key</label>
					<input  type="text" id="terms_of_payment_key" name="terms_of_payment_key" value="{{old('terms_of_payment_key')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('terms_of_payment_key')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 <div class="form-group col-md-2">
					<label for="account_group">Account Group</label>
					<input  type="text" id="account_group" name="account_group" value="{{old('account_group')}}" autocomplete="off" class="form-control hide-on-parent" placeholder=" Receipt name">
					@error('account_group')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 <div class="form-group col-md-2">
					<label for="posting_block_overall">Posting Block Overall</label>
					<input  type="text" id="posting_block_overall" name="posting_block_overall" value="{{old('posting_block_overall')}}" autocomplete="off" class="form-control" placeholder=" Receipt name">
					@error('posting_block_overall')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				 <div class="form-group col-md-2">
					<label for="purchase_block_overall">Purchase block overall</label>
					<input  type="text" id="purchase_block_overall" name="purchase_block_overall" value="{{old('purchase_block_overall')}}" autocomplete="off" class="form-control" placeholder=" Receipt name">
					@error('purchase_block_overall')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>
				
				<div class="form-group col-md-2">
					<label for="service_block">Service block</label>
					<input  type="text" id="service_block" name="service_block" value="{{old('service_block')}}" autocomplete="off" class="form-control" placeholder=" Receipt name">
					@error('service_block')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>	
				 
				 <div class="form-group col-md-2">
					<label for="purchase_block_org">Purchase Block-Purch Org</label>
					<input  type="text" id="purchase_block_org" name="purchase_block_org" value="{{old('purchase_block_org')}}" autocomplete="off" class="form-control" placeholder=" Purchase Block Org">
					@error('purchase_block_org')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>	
				<div class="form-group col-md-2">
					<label for="payment_block">Payment Block</label>
					<input  type="text" id="payment_block" name="payment_block" value="{{old('payment_block')}}" autocomplete="off" class="form-control" placeholder=" Payment Block">
					@error('payment_block')
					<span class="text-danger">{{$message}}</span>
					@enderror
				 </div>	

				  
              <div class="form-group col-md-2">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">             
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>               
                </select>
              </div>
              </div>
			  <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Add Vendor</button>
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
</script>
@endsection