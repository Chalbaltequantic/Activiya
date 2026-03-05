@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     
    
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Upload Material Master data by copy & paste</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{ route('admin.materialdatalist') }}" class="btn btn-info">View Material Master data</a></li>
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
              
            
			<form action="{{url('/admin/material/save_manual_upload')}}" method="post" name="addfrm">
              @csrf
			  
			  
			  <div class="table-responsive">
				<table class="table-bordered border-dark table-hover" id="table">
				  <thead>
				
					<tr>
						<th style="background: #fce4d6; color: #0070c0;" class="">Material code</th>
						<th style="background: #fce4d6; color: #0070c0;" class="">Material description</th>
						<th style="background: #fce4d6; color: #0070c0;">UOM</th>
						<th style="background: #fce4d6; color: #0070c0;">Division</th>
						<th style="background: #fce4d6; color: #0070c0;">Piece<br> per box</th>
						<th style="background: #fce4d6; color: #0070c0;">Length(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Width(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Height(cm)</th>
						<th style="background: #fce4d6; color: #0070c0;">Net weight(kg)</th>
						<th style="background: #fce4d6; color: #0070c0;">Gross(kg)</th>
						<th style="background: #fce4d6; color: #0070c0;">Volume(cft)</th>
						<th style="background: #fce4d6; color: #0070c0;">Category</th>
						<th style="background: #fce4d6; color: #0070c0;">Pallets</th>
						<th style="background: #fce4d6; color: #0070c0;">Brand</th>
						<th style="background: #fce4d6; color: #0070c0;">Sub brand</th>
						<th style="background: #fce4d6; color: #0070c0;">Thickness</th>
						<th style="background: #fce4d6; color: #0070c0;">Load<br>sequence</th>
						<th style="background: #fce4d6; color: #0070c0;">Associated</th>
						<th style="background: #fce4d6; color: #0070c0;">Parent</th>
						<th style="background: #fce4d6; color: #0070c0;">Child</th>
						
					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 1; $i <= 10; $i++)
				  
					<tr>
					 <td class="char-10"><input type="text" name="material_code[]" id="material_code{{$i}}" value="{{ old('material_code')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
						  <td class=""><input type="text" name="material_description[]" id="material_description{{$i}}" value="{{ old('material_description')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }}></td>
						  <td class="char-6"><input type="text" name="uom[]" id="uom{{$i}}" value="{{ old('uom')[$i] ?? '' }}"></td>
						  <td class="char-6"><input type="text" name="division[]" id="division{{$i}}" value="{{ old('division')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
						  <td class="char-3"><input type="text" name="piece_per_box[]" id="" value="{{ old('piece_per_box')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="length_cm[]" id="" value="{{ old('length_cm')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="width_cm[]" id="" value="{{ old('width_cm')[$i] ?? '' }}"></td>
						
						  
						  <td class="char-4"><input type="text" name="height_cm[]" id="" value="{{ old('height_cm')[$i] ?? '' }}"></td>
						  <td class="char-6"><input type="text" name="net_weight_kg[]" id="" value="{{ old('net_weight_kg')[$i] ?? '' }}"></td>
						  <td class="char-6"><input type="text" name="gross_weight_kg[]" id="" value="{{ old('gross_weight_kg')[$i] ?? '' }}"></td>
						
						  <td class="char-10"><input type="text" name="volume_cft[]" id="" value="{{ old('volume_cft')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="category[]" id="" value="{{ old('category')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="pallets[]" id="" value="{{ old('pallets')[$i] ?? '' }}"></td>
						  <td><input type="text" name="brand[]" id="" value="{{ old('brand')[$i] ?? '' }}"></td>
						  <td><input type="text" name="sub_brand[]" id="" value="{{ old('sub_brand')[$i] ?? '' }}"></td>
						  <td class="char-6"><input type="text" name="thickness[]" id="" value="{{ old('thickness')[$i] ?? '' }}"></td>
						  <td class="char-10"><input type="text" name="load_sequence[]" id="" value="{{ old('load_sequence')[$i] ?? '' }}"></td>
						  <td><input type="text" name="associated[]" id="" value="{{ old('associated')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="parent[]" id="" value="{{ old('parent')[$i] ?? '' }}"></td>
						  <td class="char-4"><input type="text" name="child[]" id="" value="{{ old('child')[$i] ?? '' }}"></td>
						  
						</tr>  
					@endfor	
				  </tbody>
				</table>
			  </div>
				<div class="row text-right">
				<div class="col-md-6">
				<button type="submit" class="btn btn-primary text-right" name="submit">Upload Material Master data</button>
				</div>
				</div>
			  
			</form>
			
            <!-- /.card-body -->
          
       	  </div>
        </div>
	
       </div>
		  
		  <div class="col-lg-2"></div>
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