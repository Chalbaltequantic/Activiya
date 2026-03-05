@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Create Indent</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.lopmanualupload')}}" data-toggle="tab">Create</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummary')}}">Unqualified Indent</a></li>
				 <li class="nav-item"><a class="nav-link" href="{{route('admin.qualifiedloadsummary')}}">Qualified Indent</a></li>	
					 {{-- <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummaryApproval')}}">Approve Indent</a></li> --}}
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
                  <form action="{{url('/admin/loadoptimizer/save_manual_upload')}}" method="post" name="addfrm" id="postform">
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						
						  @csrf
						   <div class="excel-wrapper">
						  <table class="table table-bordered excel-table" id="table">
							  <thead>
								<tr>
									
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin<br>Code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin city</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination<br>code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination city</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">SKU code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">SKU description</th>
									<th style="background: #fce4d6; color: #0070c0;">Priority</th>
									<th style="background: #fce4d6; color: #0070c0;">SKU class</th>
									<th style="background: #fce4d6; color: #0070c0;">RDD(YYYY-mm-dd)</th>
									<th style="background: #fce4d6; color: #0070c0;">T mode</th>
									
									<th style="background: #fce4d6; color: #0070c0;">QTY</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total<br>weight</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total<br>Volume</th>
											  
								</tr>
							  </thead>
							  <tbody>
							  @for ($i = 1; $i <= 20; $i++)
							  
								<tr>
								 <td class="char-4"><input type="text" name="origin_name_code[]" id="origin_name_code{{$i}}" value="{{ old('origin_name_code')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="origin">	
								 
								 </td>
								 <td class="">
								  <input type="text" name="origin_name[]" id="origin_name{{$i}}" value="{{ old('origin_name')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="origin_city">
								 </td>
								 <td class="char-4"><input type="text" name="destination_name_code[]" id="destination_name_code{{$i}}" value="{{ old('destination_name_code')[$i] ?? '' }}"  {{ $i == 1 ? 'required' : '' }} class="destination">
								 </td>
								  <td class=""><input type="text" name="destination_name[]" id="destination_name{{$i}}" value="{{ old('destination_name')[$i] ?? '' }}"  {{ $i == 1 ? 'required' : '' }} class="destination_city">
								 </td>
								<td class="char-10"><input type="text" name="sku_code[]" id="sku_code{{$i}}" value="{{ old('sku_code')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="sku"></td>
								
								<td><input type="text" name="sku_description[]" id="sku_description{{$i}}" value="{{ old('sku_description')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="sku_desc"></td>
									  
								<td class="char-3"><input type="text" name="priority[]" id="" value="{{ old('priority')[$i] ?? '' }}" class="priority"></td>
									  
								<td class="char-3"><input type="text" name="sku_class[]" id="" value="{{ old('sku_class')[$i] ?? '' }}" class="sku_class"></td>
								
								<td><input type="text" name="required_delivery_date[]" id="" value="{{ old('required_delivery_date')[$i] ?? '' }}" class="required_delivery_date"></td>	
								
								<td class="char-6"><input type="text" name="t_mode[]" id="" value="{{ old('t_mode')[$i] ?? '' }}" class="t_mode"></td>		
								
								<td class="char-10"><input type="text" name="qty[]" id="" value="{{ old('qty')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="qty"></td>
									  
								<td class="char-10"><input type="text" name="z_total_weight[]" id="" value="{{ old('z_total_weight')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="weight"></td>
									  
								<td class="char-10"><input type="text" name="z_total_volume[]" id="" value="{{ old('z_total_volume')[$i] ?? '' }}" {{ $i == 1 ? 'required' : '' }} class="volume"></td>
									
								</tr>  
								@endfor	
							  </tbody>
							</table>
						</div>				
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

        pendingRows = 0;
        showProcessingAlert();

        $('#table tbody tr').each(function () {

            let row = $(this);

            if (
                row.find('.origin').val() &&
                row.find('.destination').val() &&
                row.find('.sku').val() &&
                row.find('.qty').val()
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

    $.post("{{ route('admin.loadoptimizer.fetchRow') }}", {
        _token: "{{ csrf_token() }}",
        origin_code: row.find('.origin').val(),
        destination_code: row.find('.destination').val(),
        sku_code: row.find('.sku').val(),
        qty: row.find('.qty').val()
    }, function (res) {

        if (res.error) {
            Swal.fire('Error', res.error, 'error');
            pendingRows--;
            return;
        }

        row.find('.sku_desc').val(res.sku_description);
        row.find('.weight').val(res.total_weight);
        row.find('.volume').val(res.total_volume);
        row.find('.origin_city').val(res.origin_city);
        row.find('.destination_city').val(res.destination_city);

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

</script>

@endsection