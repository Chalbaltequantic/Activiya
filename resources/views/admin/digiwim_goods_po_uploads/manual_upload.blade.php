@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create Digi Wim M Good Po Upload</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Create Digi Wim M Good Po Upload</li>
				
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
				@if(session('errorRows'))
					<div class="alert alert-warning">
						<b>Skipped Rows:</b>
						<ul>
							@foreach(session('errorRows') as $err)
								<li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>
							@endforeach
						</ul>
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.digiwim-goods-po.index')}}" data-toggle="tab">Create</a></li>
                 	
				
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
						<form action="{{ route('admin.digiwim-goods-po.save') }}" method="post" name="addfrm" id="postform">
    
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							@csrf

							<div class="excel-wrapper">
								<table class="table table-bordered excel-table" id="table">
									<thead>
										<tr>
										<th style="background: #fce4d6; color: #0070c0;">Buyer Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Buyer Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Buyer Location</th>

										<th style="background: #fce4d6; color: #0070c0;">Bill To Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Bill To Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Bill To Location</th>
										<th style="background: #fce4d6; color: #0070c0;">Ship To Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Ship To Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Ship To Location</th>
										<th style="background: #fce4d6; color: #0070c0;">Supplier Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Supplier Name</th>
										<th style="background: #fce4d6; color: #0070c0;">Supplier Location</th>
										<th style="background: #fce4d6; color: #0070c0;">P.O.No.</th>
										<th style="background: #fce4d6; color: #0070c0;">Date</th>
										<th style="background: #fce4d6; color: #0070c0;">Material Code</th>
										<th style="background: #fce4d6; color: #0070c0;">Material Description</th>
										<th style="background: #fce4d6; color: #0070c0;">Qty.(Units)</th>
										<th style="background: #fce4d6; color: #0070c0;">Total Cs</th>
										<th style="background: #fce4d6; color: #0070c0;">Rate Per Unit</th>
										<th style="background: #fce4d6; color: #0070c0;">Tax</th>
										<th style="background: #fce4d6; color: #0070c0;">Conversion</th>
										<th style="background: #fce4d6; color: #0070c0;">Discount</th>
										<th style="background: #fce4d6; color: #0070c0;">Inco Terms</th>
										<th style="background: #fce4d6; color: #0070c0;">Freight</th>
										<th style="background: #fce4d6; color: #0070c0;">Custom</th>
										<th style="background: #fce4d6; color: #0070c0;">Custom 1</th>
										<th style="background: #fce4d6; color: #0070c0;">Custom 2</th>
										<th style="background: #fce4d6; color: #0070c0;">Custom 3</th>
										<th style="background: #fce4d6; color: #0070c0;">Custom 4</th>

										</tr>

										</thead>
										<tbody>

										@for ($i = 1; $i <= 20; $i++)

										<tr>
										<td><input type="text" name="buyer_code[]" class="buyer_code char-10"></td>
										<td><input type="text" name="buyer_name[]" class="buyer_name" readonly></td>
										<td><input type="text" name="buyer_location[]" class="buyer_location" readonly></td>
										<td><input type="text" name="bill_to_code[]" class="bill_to_code char-10"></td>
										<td><input type="text" name="bill_to_name[]" class="bill_to_name " readonly></td>
										<td<input type="text" name="bill_to_location[]" class="bill_to_location" readonly></td>
										<td><input type="text" name="ship_to_code[]" class="ship_to_code char-10"></td>
										<td><input type="text" name="ship_to_name[]" class="ship_to_name" readonly></td>
										<td><input type="text" name="ship_to_location[]" class="ship_to_location" readonly></td>
										<td><input type="text" name="supplier_code[]" class="supplier_code char-10"></td>
										<td><input type="text" name="supplier_name[]" class="supplier_name" readonly></td>
										<td><input type="text" name="supplier_location[]" class="supplier_location" readonly></td>
										<td><input type="text" name="po_no[]" class="char-10"></td>
										<td><input type="text" name="po_date[]"></td>
										<td><input type="text" name="material_code[]" class="material_code char-10"></td>
										<td><input type="text" name="material_description[]" class="material_description" readonly></td>
										<td><input type="number" step="0.001" name="qty_units[]" class="char-6"></td>
										<td><input type="number" step="0.001" name="total_cs[]" class="char-6"></td>
										<td><input type="number" step="0.01" name="rate_per_unit[]" class="char-6"></td>
										<td><input type="number" step="0.01" name="tax[]" class="char-6"></td>
										<td><input type="number" step="0.001" name="conversion[]" class="char-6"></td>
										<td><input type="number" step="0.01" name="discount[]" class="char-6"></td>
										<td><input type="text" name="inco_terms[]"></td>
										<td><input type="number" step="0.01" name="freight[]" class="char-10"></td>
										<td><input type="text" name="custom[]"></td>
										<td><input type="text" name="custom_1[]"></td>
										<td><input type="text" name="custom_2[]"></td>
										<td><input type="text" name="custom_3[]"></td>
										<td><input type="text" name="custom_4[]"></td>

										</tr>


										@endfor

									</tbody>

								</table>
							</div>

						</div>

						<div class="row text-right mt-3">
							<div class="col-md-10">
								<button type="submit" class="btn btn-primary text-right" name="submit">
									Submit
								</button>
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
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

    const fetchRowUrl = "{{ route('admin.digiwim-goods-po.fetch-row') }}";

     $('#table').on('paste', 'input', function (e) {

        e.preventDefault();

        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text');

        const rows = pastedData.split(/\r\n|\n|\r/).filter(row => row.length > 0);

        const startInput = this;
        const table = document.getElementById('table');

        const startCell = startInput.closest('td');
        const startRow = startCell.parentElement;

        const rowIndex = Array.from(table.rows).indexOf(startRow);
        const colIndex = Array.from(startRow.cells).indexOf(startCell);

        rows.forEach((rowData, i) => {

            const cols = rowData.split('\t');

            const tr = table.rows[rowIndex + i];

            if (!tr) return;

            cols.forEach((col, j) => {

                const td = tr.cells[colIndex + j];

                if (!td) return;

                const input = td.querySelector('input');

                if (input) {

                    input.value = col.trim();

                }

            });

        });

        setTimeout(function () {

            let rowsToFetch = [];

            $('#table tbody tr').each(function () {

                let row = $(this);

                if (

                    row.find('.buyer_code').val() &&
                    row.find('.bill_to_code').val() &&
                    row.find('.ship_to_code').val() &&
                    row.find('.supplier_code').val() &&
                    row.find('.material_code').val()

                ) {

                    rowsToFetch.push(row);

                }

            });

            if (rowsToFetch.length === 0) {

                return;

            }

            Swal.fire({

                title: 'Processing Goods PO...',

                text: 'Fetching master data...',

                allowOutsideClick: false,

                allowEscapeKey: false,

                didOpen: () => {

                    Swal.showLoading();

                }

            });

            let completed = 0;

            rowsToFetch.forEach(function (row) {

                fetchRowData(row, function () {

                    completed++;

                    if (completed >= rowsToFetch.length) {

                        Swal.close();

                    }

                });

            });

        }, 200);

    });


    /* Manual Entry */

    $('#table').on(
        'blur',
        '.buyer_code,.bill_to_code,.ship_to_code,.supplier_code,.material_code',
        function () {

            let row = $(this).closest('tr');

            if (

                row.find('.buyer_code').val() &&
                row.find('.bill_to_code').val() &&
                row.find('.ship_to_code').val() &&
                row.find('.supplier_code').val() &&
                row.find('.material_code').val()

            ) {

                fetchRowData(row);

            }

        }
    );


    /* Fetch Master Data */

    function fetchRowData(row, callback = function(){}) {

        $.ajax({

            url: fetchRowUrl,

            type: "POST",

            data: {

                _token: "{{ csrf_token() }}",

                buyer_code: row.find('.buyer_code').val(),

                bill_to_code: row.find('.bill_to_code').val(),

                ship_to_code: row.find('.ship_to_code').val(),

                supplier_code: row.find('.supplier_code').val(),

                material_code: row.find('.material_code').val()

            },

            success: function (res) {

                console.log(res);

                if (!res.success) {

                    let msg='';

                    $.each(res.errors,function(k,v){

                        msg+=v+"<br>";

                    });

                    Swal.fire({

                        icon:'error',

                        title:'Validation Error',

                        html:msg

                    });

                    callback();

                    return;

                }


                /* Buyer */

                row.find('.buyer_name').val(res.buyer_name ?? '');

                row.find('.buyer_location').val(res.buyer_location ?? '');


                /* Bill To */

                row.find('.bill_to_name').val(res.bill_to_name ?? '');

                row.find('.bill_to_location').val(res.bill_to_location ?? '');


                /* Ship To */

                row.find('.ship_to_name').val(res.ship_to_name ?? '');

                row.find('.ship_to_location').val(res.ship_to_location ?? '');


                /* Supplier */

                row.find('.supplier_name').val(res.supplier_name ?? '');

                row.find('.supplier_location').val(res.supplier_location ?? '');


                /* Material */

                row.find('.material_description').val(res.material_description ?? '');

                callback();

            },

            error:function(xhr){

                console.log(xhr.responseText);

                Swal.fire({

                    icon:'error',

                    title:'Server Error',

                    html:xhr.responseText

                });

                callback();

            }

        });

    }

});

</script>
@endpush
@endsection