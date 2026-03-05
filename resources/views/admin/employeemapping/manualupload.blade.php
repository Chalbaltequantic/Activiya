@extends('admin.admin')
@section('bodycontent')
  <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     
   
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Upload Employee Mapping</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/employeemappingdata')}}" class="btn btn-info">View Employee Mapping</a></li>
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
		<div class="col-lg-2"></div>
          <div class="col-lg-8">
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
              
            
			<form action="{{url('/admin/employeemapping/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive">
				<table class="table-bordered border-dark table-hover" id="table">
				  <thead>
				  
					<tr>
						<th style="background: #fce4d6; color: #0070c0;width:60px;" class="">Company code</th>
						<th style="background: #fce4d6; color: #0070c0;width:60px;" class="">Consignor code</th>
						<th style="background: #fce4d6; color: #0070c0;width:60px;">Consigee code</th>
						<th style="background: #fce4d6; color: #0070c0;width:60px;">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;width:60px;">Sub Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;width:60px;">Employee Code</th>
						
					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 1; $i <= 10; $i++)
				  
					<tr>
					 <td class=""><input type="text" name="company_code[]" id="company_code{{$i}}" value="" {{ $i == 1 ? 'required' : '' }}></td>
					  <td class=""><input type="text" name="consignor_code[]" id="" value=""  {{ $i == 1 ? 'required' : '' }}></td>
					  <td><input type="text" name="consignee_code[]" id="" value=""></td>
					  <td><input type="text" name="vendor_code[]" id="" value=""></td>
					  <td><input type="text" name="subvendor_code[]" id="" value=""></td>
					  <td><input type="text" name="employee_code[]" id="" value=""></td>
					</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Employee Mapping data</button>
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