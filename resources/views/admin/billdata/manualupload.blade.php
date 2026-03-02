@extends('admin.admin')
@section('bodycontent')
 <style>
    .table-responsive-fixed {
      overflow-x: auto;
      position: relative;
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
      padding: 1px 0px;
    }

 .table-container {
    max-height: 400px;   
    overflow-y: auto;
  
}


#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}	
/*
#table {
    table-layout: auto !important;
    width: auto !important;
}

*/


/* Input should define column width */
#table td input {
    width: auto !important;
    min-width: 40px;
    display: inline-block;
    border: none;
}
#table td input {
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
    padding: 2px 4px;
}

#table td input:focus {
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
}
#table td input:focus {
    background-color: #fffbe6;
}

#table td input {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    padding: 2px 4px;
    width: auto;
}

/* Character based widths */
#table td input.char-3  { width: 3ch !important; }
#table td input.char-4  { width: 4ch !important; }
#table td input.char-6  { width: 6ch !important; }
#table td input.char-10 { width: 10ch !important; }

/* Prevent column expansion */
#table th,
#table td {
    white-space: nowrap !important;
    width: 1% !important;
}

/* Prevent header from collapsing */
#table th {
    min-width: max-content;
    padding: 4px 10px;
    white-space: nowrap;
}

/* If you want header in single line but not crushed */
#table th {
    text-align: left;
}


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
						<th style="background: #fce4d6; color: #0070c0;">Ref1</th>
						
						<th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
						<th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
						<th style="background: #fce4d6; color: #0070c0;">T code</th> 
						<th style="background: #fce4d6; color: #0070c0;">Truck type</th> 
						<th style="background: #fce4d6; color: #0070c0;">LR No.</th>
						<th style="background: #fce4d6; color: #0070c0;">LR CN Date</th>
						<th style="background: #fce4d6; color: #0070c0;">A amount </th>
						<th style="background: #fce4d6; color: #0070c0;">Ref2</th>
						<th style="background: #fce4d6; color: #0070c0;">Ref3</th>
						<th style="background: #fce4d6; color: #0070c0;">Freight type</th>
						<th style="background: #fce4d6; color: #0070c0;">Ap status</th>
						<th style="background: #fce4d6; color: #0070c0;">Mode</th>
						<th style="background: #fce4d6; color: #0070c0;">cases</th>
						<th style="background: #fce4d6; color: #0070c0;">Driver number</th>
						<th style="background: #fce4d6; color: #0070c0;">Truck no</th>

					  
					</tr>
				  </thead>
				  <tbody>
				  @for ($i = 0; $i <= 19; $i++)
				  
					<tr>
					 <td class="">
					 <input type="text" name="consignor_name[]" id="consignor_name{{$i}}" value="{{ old('consignor_name')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>
						  <td class=""><input type="text" name="consignor_code[]" id="" value="{{ old('consignor_code')[$i] ?? '' }}"  {{ $i == 0 ? 'required' : '' }} class="char-10"></td>
						  <td><input type="text" name="consignor_location[]" id="" value="{{ old('consignor_location')[$i] ?? '' }}"></td>
							  {{-- <td><input type="text" name="s5_consignor_short_name_and_location[]" id="" value="{{ old('s5_consignor_short_name_and_location')[$i] ?? '' }}" {{ $i == 0 ? 'required' : '' }}></td>--}}
						  <td><input type="text" name="consignee_name[]" id="" value="{{ old('consignee_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="consignee_code[]" id="" value="{{ old('consignee_code')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="consignee_location[]" id="" value="{{ old('consignee_location')[$i] ?? '' }}"></td>
							  {{--  <td><input type="text" name="d5_consignor_short_name_and_location[]" id="" value="{{ old('d5_consignor_short_name_and_location')[$i] ?? '' }}"></td>--}}
						  <td><input type="text" name="ref1[]" id="" value="{{ old('ref1')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="vendor_code[]" id="" value="{{ old('vendor_code')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="vendor_name[]" id="" value="{{ old('vendor_name')[$i] ?? '' }}"></td>
						  <td><input type="text" name="t_code[]" id="" value="{{ old('t_code')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="truck_type[]" id="" value="{{ old('truck_type')[$i] ?? '' }}" class="char-6"></td>
						  <td><input type="text" name="lr_no[]" id="" value="{{ old('lr_no')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="lr_cn_date[]" id="" value="{{ old('lr_cn_date')[$i] ?? '' }}"></td>
						  <td><input type="text" name="a_amount[]" id="" value="{{ old('a_amount')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="ref2[]" id="" value="{{ old('ref2')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="ref3[]" id="" value="{{ old('ref3')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="freight_type[]" id="" value="{{ old('freight_type')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="ap_status[]" id="" value="{{ old('ap_status')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="custom[]" id="" value="{{ old('custom')[$i] ?? '' }}" class="char-4"></td>
						  <td><input type="text" name="custom1[]" id="" value="{{ old('custom1')[$i] ?? '' }}" class="char-4"></td>
						  <td><input type="text" name="custom2[]" id="" value="{{ old('custom2')[$i] ?? '' }}" class="char-10"></td>
						  <td><input type="text" name="custom3[]" id="" value="{{ old('custom3')[$i] ?? '' }}" class="char-10"></td>
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

document.querySelectorAll('#table input').forEach(function(input) {
    input.addEventListener('input', function() {
        this.style.width = ((this.value.length + 1) * 8) + 'px';
    });
});

/*resize column on drag*/
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("table");
    const ths = table.querySelectorAll("th");

    ths.forEach((th, colIndex) => {

        // Set initial width to current computed width
        th.style.width = th.offsetWidth + "px";

        const handle = document.createElement("div");
        handle.classList.add("resize-handle");
        th.appendChild(handle);

        let startX, startWidth;

        handle.addEventListener("mousedown", function (e) {
            e.preventDefault();
            startX = e.pageX;
            startWidth = th.offsetWidth;

            document.addEventListener("mousemove", onMouseMove);
            document.addEventListener("mouseup", onMouseUp);
        });

        function onMouseMove(e) {
            const newWidth = startWidth + (e.pageX - startX);
            if (newWidth < 40) return; // minimum width

            th.style.width = newWidth + "px";

            table.querySelectorAll("tr").forEach(row => {
                if (row.children[colIndex]) {
                    row.children[colIndex].style.width = newWidth + "px";
                }
            });
        }

        function onMouseUp() {
            document.removeEventListener("mousemove", onMouseMove);
            document.removeEventListener("mouseup", onMouseUp);
        }
    });
});
</script>
@endsection