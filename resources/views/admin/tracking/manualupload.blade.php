@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manual Upload Tracking data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/trackingdata/trackingdata-history')}}" class="btn btn-info">View Tracking data</a></li>
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
            
			<form action="{{url('/admin/trackingdata/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-1">Indent No</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:95px;" class="sticky-col-2">Customer<br />PO No.</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;width:90px;" class="sticky-col-3">Origin</th>
						<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="sticky-col-4">Destination</th> 
						<th style="background: #fce4d6; color: #0070c0;">Vendor name</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;">Type of<br> vehicle</th>
						<th style="background: #fce4d6; color: #0070c0;">LR No</th>
						<th style="background: #fce4d6; color: #0070c0;">Cases</th>
						<th style="background: #fce4d6; color: #0070c0;">Truck<br>No</th>
						<th style="background: #fce4d6; color: #0070c0;">Driver<br>Mobile No</th>
						<th style="background: #fce4d6; color: #0070c0;">Dispatch Date</th>
						<th style="background: #fce4d6; color: #0070c0;">Dispatch Time</th>
						<th style="background: #fce4d6; color: #0070c0;">Lead Time</th>
						<th style="background: #fce4d6; color: #0070c0;">Distance<br>in km</th>
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 0; $i <= 9; $i++)
				  
					<tr>
					 <td class="char-10">
					 <input type="text" name="indent_no[]" id="indent_no{{$i}}" value="{{ old('indent_no')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
					
					 <td class="char-10"><input type="text" name="customer_po_no[]" id="" value="{{ old('customer_po_no')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
					<td><input type="text" name="origin[]" id="" value="{{ old('origin')[$i] ?? '' }}"></td>
					
					<td><input type="text" name="destination[]" id="" value="{{ old('destination')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>

					<td><input type="text" name="vendor_name[]" id="" value="{{ old('vendor_name')[$i] ?? '' }}"></td>
					<td class="char-10"><input type="text" name="vendor_code[]" id="" value="{{ old('vendor_code')[$i] ?? '' }}"></td>
					
					<td><input type="text" name="vehicle_type[]" id="" value="{{ old('vehicle_type')[$i] ?? '' }}"></td>
					
					<td class="char-10"><input type="text" name="lr_no[]" id="" value="{{ old('lr_no')[$i] ?? '' }}"></td>
					
					<td class="char-14"><input type="text" name="cases[]" id="" value="{{ old('cases')[$i] ?? '' }}"></td>
					
					<td><input type="text" name="truck_no[]" id="" value="{{ old('truck_no')[$i] ?? '' }}"></td>
					  <td><input type="text" name="driver_number[]" id="" value="{{ old('driver_number')[$i] ?? '' }}"></td>
					  <td><input type="text" name="dispatch_date[]" id="" value="{{ old('dispatch_date')[$i] ?? '' }}"></td>
					  <td><input type="text" name="dispatch_time[]" id="" value="{{ old('dispatch_time')[$i] ?? '' }}"></td>
					  <td class="char-4"><input type="text" name="lead_time[]" id="" value="{{ old('lead_time')[$i] ?? '' }}"></td>
					  <td class="char-6"><input type="text" name="distance[]" id="" value="{{ old('distance')[$i] ?? '' }}"></td>
					  
					</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Tracking data</button>
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