@extends('admin.admin')
@section('bodycontent')
  <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     
     
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Upload appoinment data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/preappointmentdata/appointment-history')}}" class="btn btn-info">View Appointment data</a></li>
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
			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show ">
			<strong>{{session('error')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif
			@if(session('errorRows'))
				<div class="alert alert-warning">
					<strong>Rows with errors:</strong>
					<ul>
						@foreach(session('errorRows') as $error)
							<li>Row {{ $error['row'] }}: {{ $error['reason'] }}</li>
						@endforeach
					</ul>
				</div>
			@endif		
              <div class="card-body p-0">
			<form action="{{ route('admin.preappointment_save_manual') }}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Inv Number</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Inv Doc Date</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">PO No.</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">PO Date</th>
						  				  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Code</th>						  
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor Location</th>
						 
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Code</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
						
						<th style="background: #fce4d6; color: #0070c0;">V Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">No of <br>
						Cases sale</th>
						
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Shipment <br />Inv Value </th>
						  <th style="background: #fce4d6; color: #0070c0;" class="col-width">Delivery<br />Gross weight </th>	
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Company Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-1 col-width">Remarks</th>						
					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 0; $i <= 10; $i++)				  
					<tr>
					 <td class=""><input type="text" name="inv_number[]" id="inv_number{{$i}}" value="{{ old('inv_number')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
					  <td class=""><input type="text" name="inv_doc_date[]" id="inv_doc_date{{$i}}" value="{{ old('inv_doc_date')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
					  <td class=""><input type="text" name="po_no[]" id="po_no{{$i}}" value="{{ old('po_no')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
					  
					  <td class=""><input type="text" name="po_date[]" id="po_date{{$i}}" value="{{ old('po_date')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
					  
					  <td class="char-6"><input type="text" name="consignor_code[]" id="" value="{{ old('consignor_code')[$i] ?? '' }}"></td>
					  <td><input type="text" name="consignor_name[]" id="" value="{{ old('consignor_name')[$i] ?? '' }}"></td>
					  <td><input type="text" name="consignor_location[]" id="" value="{{ old('consignor_location')[$i] ?? '' }}"></td>
					
					  <td class="char-6"><input type="text" name="consignee_code[]" id="" value="{{ old('consignee_code')[$i] ?? '' }}"></td>
					  <td><input type="text" name="consignee_name[]" id="" value="{{ old('consignee_name')[$i] ?? '' }}"></td>
					  
					  <td><input type="text" name="consignee_location[]" id="" value="{{ old('consignee_location')[$i] ?? '' }}"></td>
					
					  <td class="char-10"><input type="text" name="v_code[]" id="" value="{{ old('v_code')[$i] ?? '' }}"></td>
					  <td><input type="text" name="vendor_name[]" id="" value="{{ old('vendor_name')[$i] ?? '' }}"></td>
					  <td class="char-6"><input type="text" name="no_of_cases_sale[]" id="" value="{{ old('no_of_cases_sale')[$i] ?? '' }}"></td>
					  
					  <td class="char-6"><input type="text" name="shipment_inv_value[]" id="" value="{{ old('shipment_inv_value')[$i] ?? '' }}"></td>
					  <td class="char-6"><input type="text" name="delivery_gross_weight[]" id="" value="{{ old('delivery_gross_weight')[$i] ?? '' }}"></td>
					  <td class="char-6"><input type="text" name="company_code[]" id="" value="{{ old('company_code')[$i] ?? '' }}"></td>
					  <td><input type="text" name="remarks[]" id="" value="{{ old('remarks')[$i] ?? '' }}"></td>
					  
				</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Appointment data</button>
				</div>
				</div>
			  
			</form>
			
            <!-- /.card-body -->
          
       	  </div>
        </div>
  
       </div>
		  
		  
		  </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
	
	
	<script>
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