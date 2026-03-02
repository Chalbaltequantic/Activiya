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
      left: 100px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
.sticky-col-3 {
      position: sticky;
      left: 180px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 240px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-5 {
      position: sticky;
      left: 320px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    . {
      min-width: 100px;
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
	
  </style>
<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Unqualified Indent</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Unqualified Indent</li>
				
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
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.lopmanualupload')}}">Create</a></li>
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.loadSummary')}}">Unqualified Indent</a></li>
				<li class="nav-item"><a class="nav-link" href="{{route('admin.qualifiedloadsummary')}}">Qualified Indent</a></li>
				
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                 
                  <!-- /.tab-pane -->
                  <div class="tab-pane active" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<table id="appointdataTable" class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="">Reference<br>no</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="">Origin<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;" class="">Destination<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;">Mode</th>
								<th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
								<th style="background: #fce4d6; color: #0070c0;">ZW uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Zv uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Gross<br>utilization</th>
								<th style="background: #fce4d6; color: #0070c0;">Status</th>
								<th style="background: #c6e0b4; color: #0070c0;">Reason for<br>approval</th>
								<th style="background: #c6e0b4; color: #0070c0;">Send for <br>Approval</th>
								<th style="background: #c6e0b4; color: #0070c0;">Action</th>
													  
								</tr>
						  </thead>
						<tbody>
							@if(count($loads) > 0)
							 @foreach($loads as $row)
							  
							<tr>
								<td class="">{{ $row->reference_no }}</td>
								<td class="">{{ $row->origin_name_code }} {{ $row->origin_name }}</td>
								
								<td class="">{{ $row->destination_name_code }} {{ $row->destination_city }}</td>
								<td>{{ $row->t_mode }}</td>
									<td>{{ $row->truck->description ?? 'NA' }}</td>
									<td>{{ $row->zw_util }}%</td>
									<td>{{ $row->zv_util }}%</td>
									<td class="fw-bold">{{ $row->gross_util }}%</td>

									<td>
										@php
											if ($row->gross_util < 80) {
												$color = 'danger'; // red
											} elseif ($row->gross_util <= 90) {
												$color = 'warning'; // yellow
											} else {
												$color = 'success'; // green
											}
										@endphp
										<span class="btn btn-{{ $color }} btn-sm">&nbsp;</span>
										
									</td>

									<td>
									@if($row->approval_status=='SENT_FOR_APPROVAL')
										{{ !empty($row->approval_remark) ? $row->approval_remark : ''}}	
										@else
										
										<input type="text" class="" id="remark_{{$row->id}}" name="reason[{{ $row->reference_no }}]" required>
									@endif
									</td>

									<td class="text-center">
									@if($row->approval_status=='SENT_FOR_APPROVAL') 		
									<button class="btn btn-secondary" data-id="{{ $row->id }}">Already Sent</button>
									@else
										<button class="btn btn-warning send-approval"        data-id="{{ $row->id }}">Send for Approval</button>
									@endif
										
									</td>
									<td><a href="{{ route('admin.load.summary.items', $row->reference_no) }}"
										class="btn btn-sm btn-primary">Edit</a>
									</td>	
								</tr> 
						  @endforeach						 
						  @endif		
													  
					   </tbody>
					</table>
					</div>

				  </div>
                  <!-- /.tab-pane -->
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
////Load summary send for approval 
$('.send-approval').click(function () {
    let id = $(this).data('id');
    let remark = $('#remark_' + id).val();
	if(remark==''){
		alert('Please enter remark');
		return false;
	}
    $.post("{{ route('admin.sendApproval') }}", {
        _token: '{{ csrf_token() }}',
        id: id,
        remark: remark
    }, function () {
        alert('Sent for approval');
        location.reload();
    });
});
</script>

@endsection