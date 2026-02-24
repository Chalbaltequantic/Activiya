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


    @media (max-width: 768px) {
      . {
        min-width: 90px;
      }

     
    }
	
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
    min-width: 50px;
    padding: 2px;
    border: 0.5px solid #ccc;
    background: #fff;
    text-align: left;
}

#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}
table {
  table-layout: auto !important;
  width: 100%;
}	
.table td,
.table th {
  white-space: normal !important;
  word-break: break-word;
  vertical-align: middle;
}
	
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create Manual Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Create Manual Indent</li>
				
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
					<div class="alert alert-success alert-dismissible fade show">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif			       
            </div>
          </div>
		</div>
        <!-- /.row -->
		 <div class="row">          	  
			<div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.manualloadsummary')}}" data-toggle="tab">Create</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.manualloadSummarydatalist')}}">Manual Indent</a></li>
					
					 
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  <form action="{{url('/admin/manual-load-summary/save')}}" method="post" name="addfrm" id="postform">
						  @csrf
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						
						  <table class="table-bordered border-dark table-hover" id="table">
							  <thead>
							  <tr><td></td><td><b>Mode</b></td><td>
							  <select id="t_mode" name="t_mode" class="form-control t_mode">
									<option value="FTL">FTL</option>
									<option value="PTL">PTL</option>
									<option value="Train">Train</option>
									<option value="Air">Air</option>
								</select></td>
								<td colspan="7"></td>
							</tr>
								<tr>
									
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin<br>Code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin city</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination<br>code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination city</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Truck code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-truck">Truck name</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-qty">QTY</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-weight">Approx wt(Kg)</th>
									
									<th style="background: #fce4d6; color: #0070c0;" class="col-cft">Approx CFT</th>
									<th style="background: #fce4d6; color: #0070c0;" class="col-cases">No of <br />Cases / bags</th>
									<th style="background: #fce4d6; color: #0070c0;">Required<br>pickup date</th>
									<th style="background: #fce4d6; color: #0070c0;">Indent<br>Instructions</th>
									<th style="background: #fce4d6; color: #0070c0;">Remarks</th>
											  
								</tr>
							  </thead>
							  <tbody>
							  @for ($i = 1; $i <= 20; $i++)
							  
								<tr>
								 <td class=""><input type="text" name="origin_name_code[]" id="origin_name_code{{$i}}" value="{{ old('origin_name_code')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="origin">	
								 
								 </td>
								 <td class="">
								  <input type="text" name="origin_name[]" id="origin_name{{$i}}" value="{{ old('origin_name')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="origin_city">
								 </td>
								 <td class=""><input type="text" name="destination_name_code[]" id="destination_name_code{{$i}}" value="{{ old('destination_name_code')[$i] ?? '' }}"  {{ $i == 1 ? 'required' : '' }} class="destination">
								 </td>
								  <td class=""><input type="text" name="destination_name[]" id="destination_name{{$i}}" value="{{ old('destination_name')[$i] ?? '' }}"  {{ $i == 1 ? 'required' : '' }} class="destination_city">
								 </td>
								<td class=""><input type="text" name="truck_code[]" id="truck_code{{$i}}" value="{{ old('truck_code')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="truck_code"></td>
								
								<td class="col-truck"><input type="text" name="truck_name[]" id="truck_name{{$i}}" value="{{ old('truck_name')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="truck_name"></td>
									  
								<td class="col-qty"><input type="text" name="qty[]" id="" value="{{ old('qty')[$i] ?? '' }}" class="qty"></td>
									  
								<td class="col-weight"><input type="text" name="weight[]" id="" value="{{ old('weight')[$i] ?? '' }}" class="weight"></td>
								
								<td class="col-cft"><input type="text" name="cft[]" id="" value="{{ old('cft')[$i] ?? '' }}" class="cft"></td>	
								
								<td class="col-cases"><input type="text" name="no_of_cases[]" id="" value="{{ old('no_of_cases')[$i] ?? '' }}" class="no_of_cases"></td>	
								
								<td><input type="text" name="required_pickup_date[]" id="" value="{{ old('required_pickup_date')[$i] ?? '' }}" class="required_pickup_date"></td>	
								
								<td><input type="text" name="indent_instructions[]" id="" value="{{ old('indent_instructions')[$i] ?? '' }}" class="indent_instructions"></td>		
								
								<td><input type="text" name="remarks[]" id="" value="{{ old('remarks')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="remarks"></td>
									
								</tr>  
								@endfor	
							  </tbody>
							</table>
						</div>
						<div class="row text-right">
							<div class="col-md-10">
							<button type="submit" class="btn btn-primary text-right" name="submit">Submit</button>
							</div>
							</div>
						</form>
                  </div>
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
      
  </div><!-- /.container-fluid -->
</div>
</div>
<!-- /.content -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<script>
let pendingRows = 0;
let swalOpened = false;

function showProcessingAlert() {
    if (swalOpened) return;

    swalOpened = true;
    Swal.fire({
        title: 'Processing rows…',
        text: 'Please wait until all data is populated.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function closeProcessingAlert() {
    if (swalOpened) {
        Swal.close();
        swalOpened = false;
    }
}


$('#table').on('paste', 'input', function () {
setTimeout(function () {
console.log("asdssa");
        pendingRows = 0;
        showProcessingAlert();

        $('#table tbody tr').each(function () {

            let row = $(this);

            if (
                row.find('.origin').val() &&
                row.find('.destination').val() 
            ) {
                pendingRows++;
                fetchRowData(row);
            }
        });

        if (pendingRows === 0) {
            closeProcessingAlert();
        }

    }, 100);
});

function fetchRowData(row) {

    $.post("{{ route('admin.manualloadsummary.fetchRow') }}", {
        _token: "{{ csrf_token() }}",
        origin_code: row.find('.origin').val(),
        destination_code: row.find('.destination').val(),
        t_mode: $('#t_mode').val()
    }, function (res) {

        if (res.error) {
            Swal.fire('Error', res.error, 'error');
            pendingRows--;
            return;
        }

       if($('#t_mode').val()=='FTL')
	   {
        row.find('.truck_code').val(res.truck_code);
        row.find('.truck_name').val(res.truck_type);
        row.find('.origin_city').val(res.origin_city);
        row.find('.destination_city').val(res.destination_city);
	   }
	   else{
		row.find('.origin_city').val(res.origin_city);
        row.find('.destination_city').val(res.destination_city);
	   }

        pendingRows--;

        if (pendingRows === 0) {
            closeProcessingAlert();
        }

    }).fail(function () {

        pendingRows--;

        if (pendingRows === 0) {
            closeProcessingAlert();
        }
    });
}



$(document).ready(function () {

    function toggleColumns(mode) {

        if (mode === 'FTL') {

            //$('.col-truck, .col-qty').show();
            //$('.col-weight, .col-cft, .col-cases').hide();
			 $('.col-truck, .col-qty').css('display', 'table-cell');
            $('.col-weight, .col-cft, .col-cases').css('display', 'none');
			$('.col-weight input, .col-cft input, .col-cases input').val('');

        } else {

            //$('.col-truck, .col-qty').hide();
            //$('.col-weight, .col-cft, .col-cases').show();
			$('.col-truck, .col-qty').css('display', 'none');

            $('.col-weight, .col-cft, .col-cases').css('display', 'table-cell');
			
			$('.col-truck input, .col-qty input').val('');

        }
    }

    // On page load
    toggleColumns($('#t_mode').val());

    // On mode change
    $('#t_mode').on('change', function () {
        toggleColumns($(this).val());
    });
});

</script>

@endsection