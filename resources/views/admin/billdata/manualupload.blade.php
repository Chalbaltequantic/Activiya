@extends('admin.admin')
@section('bodycontent')
 <style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}


.table-container {
    max-height: 400px;
    overflow-y: auto;
}


#table {
    table-layout: auto;       
    width: max-content;     
    border-collapse: collapse;
}

/* Sticky header */
#table th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f8f9fa;
    white-space: nowrap;
    padding: 4px 10px;
    text-align: left;
}


#table th,
#table td {
    white-space: nowrap;
    vertical-align: middle;
}


#table td {
    padding: 2px 4px;
}


#table td input:focus {
    background-color: #fffbe6;
}

#table td {
    padding: 0 !important;   /* remove td padding */
}

#table td input {
    display: block;          /* important */
    width: 100%;
    padding: 0 2px;          /* minimal safe padding */
    margin: 0;
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
    background: transparent !important;

    box-sizing: border-box;  /* IMPORTANT CHANGE */
}

/* Character-based column width controlled by TD */
#table td.char-3  { width: 3ch; }
#table td.char-4  { width: 4ch; }
#table td.char-6  { width: 6ch; }
#table td.char-10 { width: 11ch; }

</style>     
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manual Shipment Upload</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/billdata')}}" class="btn btn-info">View Shipments</a></li>
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
            
			<form action="{{url('/admin/billdata/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;">Consignor name</th>
						<th style="background: #fce4d6; color: #0070c0;">Consignor code</th>
						<th style="background: #fce4d6; color: #0070c0;">Consignor location</th>
						{{--	<th style="background: #fce4d6; color: #0070c0;">S5 consignor <br>short name & location</th>--}}

						<th style="background: #fce4d6; color: #0070c0;">Consignee Name</th>
						<th style="background: #fce4d6; color: #0070c0;">Consignee Code</th>
						<th style="background: #fce4d6; color: #0070c0;">Consignee Location</th>
						{{--<th style="background: #fce4d6; color: #0070c0;">D5 consignor short <br>name & location</th>--}}
						<th style="background: #fce4d6; color: #0070c0;">Ref1 </th>
						
						<th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
						<th style="background: #fce4d6; color: #0070c0;">T code</th> 
						<th style="background: #fce4d6; color: #0070c0;">Truck type</th> 
						<th style="background: #fce4d6; color: #0070c0;">LR No.</th>
						<th style="background: #fce4d6; color: #0070c0;">LR CN Date</th>
						<th style="background: #fce4d6; color: #0070c0;">A amount </th>
						<th style="background: #fce4d6; color: #0070c0;">Ref2 </th>
						<th style="background: #fce4d6; color: #0070c0;">Ref3 </th>
						<th style="background: #fce4d6; color: #0070c0;">Freight type</th>
						<th style="background: #fce4d6; color: #0070c0;">Ap status</th>
						<th style="background: #fce4d6; color: #0070c0;">Mode </th>
						<th style="background: #fce4d6; color: #0070c0;">cases </th>
						<th style="background: #fce4d6; color: #0070c0;">Driver number</th>
						<th style="background: #fce4d6; color: #0070c0;">Truck no</th>

					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 0; $i <= 19; $i++)
				  
					<tr>
					 <td class="">
					 <input type="text" name="consignor_name[]" id="consignor_name{{$i}}" value="{{ old('consignor_name')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
						  <td class="char-6"><input type="text" name="consignor_code[]" id="" value="{{ old('consignor_code')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
						  <td><input type="text" name="consignor_location[]" id="" value="{{ old('consignor_location')[$i] ?? '' }}"></td>
							  {{-- <td><input type="text" name="s5_consignor_short_name_and_location[]" id="" value="{{ old('s5_consignor_short_name_and_location')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>--}}
						  <td><input type="text" name="consignee_name[]" id="" value="{{ old('consignee_name')[$i] ?? '' }}"></td>
						  <td class="char-6"><input type="text" name="consignee_code[]" id="" value="{{ old('consignee_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="consignee_location[]" id="" value="{{ old('consignee_location')[$i] ?? '' }}"></td>
							  {{--  <td><input type="text" name="d5_consignor_short_name_and_location[]" id="" value="{{ old('d5_consignor_short_name_and_location')[$i] ?? '' }}"></td>--}}
						  <td class="char-10"><input type="text" name="ref1[]" id="" value="{{ old('ref1')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="vendor_code[]" id="" value="{{ old('vendor_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="vendor_name[]" id="" value="{{ old('vendor_name')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="t_code[]" id="" value="{{ old('t_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="truck_type[]" id="" value="{{ old('truck_type')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="lr_no[]" id="" value="{{ old('lr_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="lr_cn_date[]" id="" value="{{ old('lr_cn_date')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="a_amount[]" id="" value="{{ old('a_amount')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="ref2[]" id="" value="{{ old('ref2')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="ref3[]" id="" value="{{ old('ref3')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="freight_type[]" id="" value="{{ old('freight_type')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="ap_status[]" id="" value="{{ old('ap_status')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="custom[]" id="" value="{{ old('custom')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="custom1[]" id="" value="{{ old('custom1')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="custom2[]" id="" value="{{ old('custom2')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="custom3[]" id="" value="{{ old('custom3')[$i] ?? '' }}"></td>
						</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Bill data</button>
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