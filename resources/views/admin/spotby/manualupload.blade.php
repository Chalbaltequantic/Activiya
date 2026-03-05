@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     
      
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Upload Spot By data by copy & paste</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/billdata/history')}}" class="btn btn-info">View Spot By data</a></li>
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
            
			<form action="{{url('/admin/spotby/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
				<table class="table table-bordered border-dark table-hover" id="table">
				  <thead>
					<tr>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">From</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">To</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Vehicle type</th>
						

						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Valid from</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Valid upto</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">No of vehicles</th>
						
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Goods qty</th>
						
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Loading charges</th>
						<th style="background: #fce4d6; color: #0070c0;width: 40px" class="col-width">Unloading charges</th> 
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">Special instruction</th> 
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">RFQ start date time</th>
						<th style="background: #fce4d6; color: #0070c0;width: 140px" class="col-width">RFQ end date time</th>
						
					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 0; $i <= 19; $i++)
				
					<tr>
					 <td class=""><input type="text" name="from[]" id="from{{$i}}" value="{{ old('from')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
					<td class=""><input type="text" name="to[]" id="to{{$i}}" value="{{ old('to')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
					  <td><input type="text" name="vehicle_type[]" id="" value="{{ old('vehicle_type')[$i] ?? '' }}"></td>
						  
					  <td><input type="text" name="valid_from[]" id="valid_from{{$i}}" value="{{ old('valid_from')[$i] ?? '' }}"></td>
					  <td><input type="text" name="valid_upto[]" id="valid_upto{{$i}}" value="{{ old('valid_upto')[$i] ?? '' }}"></td>
					  <td class="char-4"><input type="text" name="no_of_vehicles[]" id="no_of_vehicles{{$i}}" value="{{ old('no_of_vehicles')[$i] ?? '' }}"></td>
						
					  <td class="char-4"><input type="text" name="goods_qty[]" id="goods_qty{{$i}}" value="{{ old('goods_qty')[$i] ?? '' }}"></td>
					  <td class="char-6"><input type="text" name="uom[]" id="uom{{$i}}" value="{{ old('uom')[$i] ?? '' }}"></td>
					  <td class="char-10"><input type="text" name="loading_charges[]" id="loading_charges{{$i}}" value="{{ old('loading_charges')[$i] ?? '' }}"></td>
					  <td><input type="text" name="unloading_charges[]" id="unloading_charges{{$i}}" value="{{ old('unloading_charges')[$i] ?? '' }}"></td>
					  <td><input type="text" name="special_instruction[]" id="special_instruction{{$i}}" value="{{ old('special_instruction')[$i] ?? '' }}"></td>
					  <td><input type="text" name="rfq_start_date_time[]" id="rfq_start_date_time{{$i}}" value="{{ old('rfq_start_date_time')[$i] ?? '' }}"></td>
					  <td><input type="text" name="rfq_end_date_time[]" id="rfq_end_date_time{{$i}}" value="{{ old('rfq_end_date_time')[$i] ?? '' }}"></td>
					</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Spot By data</button>
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