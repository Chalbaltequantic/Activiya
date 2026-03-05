@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manual Upload Rate master</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/ratedata')}}" class="btn btn-info">View Rate data</a></li>
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
              
            
			<form action="{{url('/admin/ratedata/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;" class="">Consignor name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Consignor code</th>
						<th style="background: #fce4d6; color: #0070c0;" >Consignor location</th>
						<th style="background: #fce4d6; color: #0070c0;" >S5 consignor <br>short name & location</th>

						<th style="background: #fce4d6; color: #0070c0;" >Consignee Name</th>
						<th style="background: #fce4d6; color: #0070c0;" >Consignee Code</th>
						<th style="background: #fce4d6; color: #0070c0;" >Consignee Location</th>
						<th style="background: #fce4d6; color: #0070c0;" >D5 consignor short <br>name & location</th>
						<th style="background: #fce4d6; color: #0070c0;" >Mode</th>
						<th style="background: #fce4d6; color: #0070c0;" >Logic</th>
						
						<th style="background: #fce4d6; color: #0070c0;" >Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;" >Vendor Name</th>
						<th style="background: #fce4d6; color: #0070c0;" >T code</th> 
						<th style="background: #fce4d6; color: #0070c0;" >Truck type</th>
						<th style="background: #fce4d6; color: #0070c0;" >A amount </th>
						
						<th style="background: #fce4d6; color: #0070c0;" >Validity start</th>
						<th style="background: #fce4d6; color: #0070c0;" >Validity end</th>
						<th style="background: #fce4d6; color: #0070c0;" >TAT</th>
						<th style="background: #fce4d6; color: #0070c0;" >Rank</th>
						<th style="background: #fce4d6; color: #0070c0;" >Distance</th>
						
						<th style="background: #fce4d6; color: #0070c0;" >Custom 1</th>
						<th style="background: #fce4d6; color: #0070c0;" >Custom 2</th>
						<th style="background: #fce4d6; color: #0070c0;" >Custom 3</th>
						<th style="background: #fce4d6; color: #0070c0;" >Custom 4</th>
						<th style="background: #fce4d6; color: #0070c0;" >Custom 5</th>
						
					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 1; $i <= 10; $i++)
				  
					<tr>
					 <td class=""><input type="text" name="consignor_name[]" id="consignor_name{{$i}}" value="" {{ $i == 1 ? 'required' : '' }}></td>
						  <td class="char-6"><input type="text" name="consignor_code[]" id="" value=""  {{ $i == 1 ? 'required' : '' }}></td>
						  <td><input type="text" name="consignor_location[]" id="" value=""></td>
						  <td><input type="text" name="s5_consignor_short_name_and_location[]" id="" value="" {{ $i == 1 ? 'required' : '' }}></td>
						  <td><input type="text" name="consignee_name[]" id="" value=""></td>
						  <td class="char-6"><input type="text" name="consignee_code[]" id="" value=""></td>
						  <td><input type="text" name="consignee_location[]" id="" value=""></td>
						  <td><input type="text" name="d5_consignor_short_name_and_location[]" id="" value=""></td>
						  <td class="char-4"><input type="text" name="mode[]" id="" value=""></td>
						  <td class="char-10"><input type="text" name="logic[]" id="" value=""></td>
						  <td class="char-10"><input type="text" name="vendor_code[]" id="" value=""></td>
						  <td><input type="text" name="vendor_name[]" id="" value=""></td>
						  <td class="char-4"><input type="text" name="t_code[]" id="" value=""></td>
						  <td><input type="text" name="truck_type[]" id="" value=""></td>
						  <td class="char-6"><input type="text" name="a_amount[]" id="" value=""></td>
						  <td><input type="text" name="validity_start[]" id="" value=""></td>
						  <td><input type="text" name="validity_end[]" id="" value=""></td>
						  
						  <td class="char-3"><input type="text" name="tat[]" id="" value=""></td>
						  <td class="char-3"><input type="text" name="rank[]" id="" value=""></td>
						  <td class="char-6"><input type="text" name="distance[]" id="" value=""></td>
						  <td class="char-4"><input type="text" name="custom1[]" id="" value=""></td>
						  <td  class="char-4"><input type="text" name="custom2[]" id="" value=""></td>
						  <td class="char-4"><input type="text" name="custom3[]" id="" value=""></td>
						  <td><input type="text" name="custom4[]" id="" value=""></td>
						  <td><input type="text" name="custom5[]" id="" value=""></td>
						</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Rate data</button>
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