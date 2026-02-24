@extends('admin.admin')
@section('bodycontent')
 <style>
    .table-responsive-fixed {
      overflow-x: auto;
      position: relative;
    }

    table {
      min-width: max-content;
      font-size: 12px;
    }

    .consign-data-table th, .consign-data-table td {
      white-space: nowrap;
      vertical-align: middle;
    }

    .consign-data-table thead th {
      position: sticky;
      top: 0;
      background: #f8f9fa;
    }

    .consign-data-table .table th, .consign-data-table .table td {
      padding: 5px 10px;
    }

    /* Sticky columns */
    .sticky-col-1 {
      position: sticky;
      left: 0;
      background: #fff;
      z-index: 9999;
    }

    .sticky-col-2 {
      position: sticky;
      left: 160px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 9999;
    }

    /* Column widths */
    .col-width {
      min-width: 160px;
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
      }
    }
	
/*css   */


.table-container {
    max-height: 400px;   /* Set your desired table height */
    overflow-y: auto;
    border: 1px solid #ccc;
}

#input-table {
    border-collapse: collapse;
    width: 100%;
    min-width: 1200px; /* Optional: ensures columns don't shrink too much */
}

#input-table th,
#input-table td {
    min-width: 120px;
    padding: 8px;
    border: 1px solid #ccc;
    background: #fff;
    text-align: left;
}

#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}	

</style>      
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Upload Vendor data by copy & paste</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/billdata')}}" class="btn btn-info">View Bill data</a></li>
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
              <div class="card-body p-0">			
              
            
			<form action="{{url('/admin/billdata/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;" class="">Consignor name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Consignor code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignor location</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">S5 consignor short name & location</th>

						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Consignee Location</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">D5 consignor short name & location</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref1</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Vendor Name</th>
						<th style="background: #ddebf7; color: #0070c0;" class="col-width">T code</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="col-width">Truck type</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">LR No.</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">LR CN Date</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">A amount </th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref3</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Freight type</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Ap status</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Custom</th>

					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 1; $i <= 10; $i++)
				  
					<tr>
					 <td class=""><input type="text" name="consignor_name[]" id="consignor_name{{$i}}" value="" {{ $i == 1 ? 'required' : '' }}></td>
						  <td class=""><input type="text" name="consignor_code[]" id="" value=""  {{ $i == 1 ? 'required' : '' }}></td>
						  <td><input type="text" name="consignor_location[]" id="" value=""></td>
						  <td><input type="text" name="s5_consignor_short_name_and_location[]" id="" value="" {{ $i == 1 ? 'required' : '' }}></td>
						  <td><input type="text" name="consignee_name[]" id="" value=""></td>
						  <td><input type="text" name="consignee_code[]" id="" value=""></td>
						  <td><input type="text" name="consignee_location[]" id="" value=""></td>
						  <td><input type="text" name="d5_consignor_short_name_and_location[]" id="" value=""></td>
						  <td><input type="text" name="ref1[]" id="" value=""></td>
						  <td><input type="text" name="vendor_code[]" id="" value=""></td>
						  <td><input type="text" name="vendor_name[]" id="" value=""></td>
						  <td><input type="text" name="t_code[]" id="" value=""></td>
						  <td><input type="text" name="truck_type[]" id="" value=""></td>
						  <td><input type="text" name="lr_no[]" id="" value=""></td>
						  <td><input type="text" name="lr_cn_date[]" id="" value=""></td>
						  <td><input type="text" name="a_amount[]" id="" value=""></td>
						  <td><input type="text" name="ref2[]" id="" value=""></td>
						  <td><input type="text" name="ref3[]" id="" value=""></td>
						  <td><input type="text" name="freight_type[]" id="" value=""></td>
						  <td><input type="text" name="ap_status[]" id="" value=""></td>
						  <td><input type="text" name="custom[]" id="" value=""></td>
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