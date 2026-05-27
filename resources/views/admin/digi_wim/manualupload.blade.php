@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Create Digi Wim</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Create Digi Wim</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.digiWim')}}" data-toggle="tab">Create</a></li>
                 	
				
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
						<form action="{{ url('/admin/digiwim/save_manual_upload') }}" method="post" name="addfrm" id="postform">
    
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							@csrf

							<div class="excel-wrapper">
								<table class="table table-bordered excel-table" id="table">

									<thead>
										<tr>

											<th style="background: #fce4d6; color: #0070c0;">Indent ID</th>

											<th style="background: #fce4d6; color: #0070c0;">Supplier Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Supplier Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Supplier Location</th>

											<th style="background: #fce4d6; color: #0070c0;">P.O.No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Invoice/Challan No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Inv/Challan Date<br>(YYYY-mm-dd)</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Location</th>

											<th style="background: #fce4d6; color: #0070c0;">M.Code*</th>

											<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">Material Descriptions</th>

											<th style="background: #fce4d6; color: #0070c0;">Batch No.</th>

											<th style="background: #fce4d6; color: #0070c0;">MFG Date<br>(YYYY-mm-dd)</</th>

											<th style="background: #fce4d6; color: #0070c0;">Expiry Date<br>(YYYY-mm-dd)</</th>

											<th style="background: #fce4d6; color: #0070c0;">Qty. (Units)</th>

											<th style="background: #fce4d6; color: #0070c0;">Total Cs</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Truck No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR Date<br>(YYYY-mm-dd)</</th>

											<th style="background: #fce4d6; color: #0070c0;">Ewaybill No.</th>

											<th style="background: #fce4d6; color: #0070c0;">Truck Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Vehicle Type</th>

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

											<td>
												<input type="text" name="indent_id[]" id="indent_id{{ $i }}"
													value="{{ old('indent_id')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }}>
											</td>

											<td>
												<input type="text" name="supplier_code[]" id="supplier_code{{ $i }}"
													value="{{ old('supplier_code')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }} class="sup_code">
											</td>

											<td>
												<input type="text" name="supplier_name[]" id="supplier_name{{ $i }}"
													value="{{ old('supplier_name')[$i] ?? '' }}" class="sup_name">
											</td>

											<td>
												<input type="text" name="supplier_location[]" id="supplier_location{{ $i }}"
													value="{{ old('supplier_location')[$i] ?? '' }}" class="sup_location">
											</td>

											<td>
												<input type="text" name="po_no[]" id="po_no{{ $i }}"
													value="{{ old('po_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="invoice_challan_no[]" id="invoice_challan_no{{ $i }}"
													value="{{ old('invoice_challan_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="invoice_challan_date[]" id="invoice_challan_date{{ $i }}"
													value="{{ old('invoice_challan_date')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="consignee_code[]" id="consignee_code{{ $i }}"
													value="{{ old('consignee_code')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }} class="cosign_code">
											</td>

											<td>
												<input type="text" name="consignee_name[]" id="consignee_name{{ $i }}"
													value="{{ old('consignee_name')[$i] ?? '' }}" class="consign_name">
											</td>

											<td>
												<input type="text" name="consignee_location[]" id="consignee_location{{ $i }}"
													value="{{ old('consignee_location')[$i] ?? '' }}" class="consign_location">
											</td>

											<td>
												<input type="text" name="m_code[]" id="m_code{{ $i }}"
													value="{{ old('m_code')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }} class="m_code">
											</td>

											<td>
												<input type="text" name="material_descriptions[]" id="material_descriptions{{ $i }}"
													value="{{ old('material_descriptions')[$i] ?? '' }}" class="m_desc">
											</td>

											<td>
												<input type="text" name="batch_no[]" id="batch_no{{ $i }}"
													value="{{ old('batch_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="mfg_date[]" id="mfg_date{{ $i }}"
													value="{{ old('mfg_date')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="expiry_date[]" id="expiry_date{{ $i }}"
													value="{{ old('expiry_date')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="qty_units[]" id="qty_units{{ $i }}"
													value="{{ old('qty_units')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="total_cs[]" id="total_cs{{ $i }}"
													value="{{ old('total_cs')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="transporter_code[]" id="transporter_code{{ $i }}"
													value="{{ old('transporter_code')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }} class="transporter_code">
											</td>

											<td>
												<input type="text" name="transporter_name[]" id="transporter_name{{ $i }}"
													value="{{ old('transporter_name')[$i] ?? '' }}" class="transporter_name">
											</td>

											<td>
												<input type="text" name="truck_no[]" id="truck_no{{ $i }}"
													value="{{ old('truck_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="lr_no[]" id="lr_no{{ $i }}"
													value="{{ old('lr_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="lr_date[]" id="lr_date{{ $i }}"
													value="{{ old('lr_date')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="ewaybill_no[]" id="ewaybill_no{{ $i }}"
													value="{{ old('ewaybill_no')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="truck_code[]" id="truck_code{{ $i }}"
													value="{{ old('truck_code')[$i] ?? '' }}"
													{{ $i == 1 ? 'required' : '' }} class="truck_code">
											</td>

											<td>
												<input type="text" name="vehicle_type[]" id="vehicle_type{{ $i }}"
													value="{{ old('vehicle_type')[$i] ?? '' }}" class="vehicle_type">
											</td>

											<td>
												<input type="text" name="custom[]" id="custom{{ $i }}"
													value="{{ old('custom')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="custom_1[]" id="custom_1{{ $i }}"
													value="{{ old('custom_1')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="custom_2[]" id="custom_2{{ $i }}"
													value="{{ old('custom_2')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="custom_3[]" id="custom_3{{ $i }}"
													value="{{ old('custom_3')[$i] ?? '' }}">
											</td>

											<td>
												<input type="text" name="custom_4[]" id="custom_4{{ $i }}"
													value="{{ old('custom_4')[$i] ?? '' }}">
											</td>

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

    const fetchRowUrl = "{{ route('admin.digiwim.fetchRow') }}";

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
                    row.find('.sup_code').val() &&
                    row.find('.cosign_code').val() &&
                    row.find('.m_code').val() &&
                    row.find('.transporter_code').val() &&
                    row.find('.truck_code').val()
                ) {
                    rowsToFetch.push(row);
                }
            });

            if (rowsToFetch.length === 0) {
                return;
            }

            Swal.fire({
                title: 'Processing rows...',
                text: 'Please wait while data is fetched.',
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

    function fetchRowData(row, callback) {

        console.log('Calling fetchRowData');

        $.ajax({
            url: fetchRowUrl,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                supplier_code: row.find('.sup_code').val(),
                consignee_code: row.find('.cosign_code').val(),
                material_code: row.find('.m_code').val(),
                transporter_code: row.find('.transporter_code').val(),
                truck_code: row.find('.truck_code').val()
            },
            success: function (res) {

                console.log('Response:', res);

                if (res.error) {
                    Swal.fire('Error', res.error, 'error');
                    callback();
                    return;
                }

                row.find('.sup_name').val(res.supplier_name ?? '');
                row.find('.sup_location').val(res.supplier_location ?? '');
                row.find('.consign_name').val(res.consignee_name ?? '');
                row.find('.consign_location').val(res.consignee_location ?? '');
                row.find('.m_desc').val(res.material_description ?? '');
                row.find('.transporter_name').val(res.transporter_name ?? '');
                row.find('.vehicle_type').val(res.vehicle_type ?? '');

                callback();
            },
            error: function (xhr) {

                console.log('AJAX Error:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    html: xhr.responseText
                });

                callback();
            }
        });
    }

});
</script>
@endpush
@endsection