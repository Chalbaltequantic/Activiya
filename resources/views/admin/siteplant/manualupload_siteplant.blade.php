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
      z-index: 99;
    }

    .sticky-col-2 {
      position: sticky;
      left: 160px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
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
            <h1 class="m-0">Upload Site plant data by copy & paste</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/siteplant')}}" class="btn btn-info">View Site Plant data</a></li>
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
              
            
			<form action="{{url('/admin/siteplant/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>					
						<th style="background: #fce4d6; color: #0070c0;" class="">PLANT Site Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Plant/Site Location Name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Status</th>

						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Plant/Site Name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Street/House number</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">STREET1</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">STREET2</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">CITY</th>
						
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">POST CODE</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">STATE</th>
						<th style="background: #ddebf7; color: #0070c0;" class="col-width">STATE DESC</th> 
						<th style="background: #ddebf7; color: #0070c0;" class="col-width">PAN NO</th> 
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Food License No.</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Food License Expiry</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">GSTIN Number</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Executive Name</th>
						<th style="background: #fce4d6; color: #0070c0;" class="col-width">Site Executive Contact No.</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Executive Mail ID</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Name</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Contact No.</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Incharge Mail ID</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Name</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Contact No.</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Site Manager Mail ID</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Region</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Company Code</th>
						<th style="background: #c6e0b4; color: #0070c0;" class="col-width">Company Type</th>

					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 1; $i <= 10; $i++)
				  
					<tr>
					 <td class=""><input type="text" name="plant_site_code[]" id="plant_site_code{{$i}}" value="{{ old('plant_site_code')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }}></td>
					 
					 <td class=""><input type="text" name="plant_site_location_name[]" id="plant_site_location_name{{$i}}" value="{{ old('plant_site_location_name')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }}></td>
						 
						  <td><input type="text" name="site_code[]" id="" value="{{ old('site_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_status[]" id="" value="{{old('site_status')[$i] ?? ''}}" {{ $i == 1 ? 'required' : '' }}></td>
						  <td><input type="text" name="plant_site_name[]" id="" value="{{ old('plant_site_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="street_house_number[]" id="" value="{{ old('street_house_number')[$i] ?? '' }}"></td>
						  <td><input type="text" name="street1[]" id="" value="{{ old('street1')[$i] ?? '' }}"></td>
						  <td><input type="text" name="street2[]" id="" value="{{ old('street2')[$i] ?? '' }}"></td>
						  <td><input type="text" name="city[]" id="" value="{{ old('city')[$i] ?? '' }}"></td>
						  <td><input type="text" name="post_code[]" id="" value="{{ old('post_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="state_code[]" id="" value="{{ old('state_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="state_name[]" id="" value="{{ old('state_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="pan_no[]" id="" value="{{ old('pan_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="food_license_no[]" id="" value="{{ old('food_license_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="food_license_expiry[]" id="" value="{{ old('food_license_expiry')[$i] ?? '' }}"></td>
						  <td><input type="text" name="gstin_number[]" id="" value="{{ old('gstin_number')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_executive_name[]" id="" value="{{ old('site_executive_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_executive_contact_no[]" id="" value="{{ old('site_executive_contact_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_executive_mail_id[]" id="" value="{{ old('site_executive_mail_id')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_incharge_name[]" id="" value="{{ old('site_incharge_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_incharge_contact_no[]" id="" value="{{ old('site_incharge_contact_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_incharge_mail_id[]" id="" value="{{ old('site_incharge_mail_id')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_manager_name[]" id="" value="{{ old('site_manager_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_manager_contact_no[]" id="" value="{{ old('site_manager_contact_no')[$i] ?? '' }}"></td>
						  <td><input type="text" name="site_manager_mail_id[]" id="" value="{{ old('site_manager_mail_id')[$i] ?? '' }}"></td>
						  <td><input type="text" name="region[]" id="" value="{{ old('region')[$i] ?? '' }}"></td>
						  <td><input type="text" name="company_code[]" id="" value="{{ old('company_code')[$i] ?? '' }}"></td>
						  <td><input type="text" name="company_type[]" id="" value="{{ old('company_type')[$i] ?? '' }}"></td>
						</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Site Plant data</button>
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