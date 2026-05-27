@extends('admin.admin')
@section('bodycontent')
 <link rel="stylesheet" href="{{ asset('backend/assets/manual_upload_setting.css') }}">     

<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Digi Wim Data</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Digi Wim</li>
				
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
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.digiWim')}}" data-toggle="tab">Create</a></li>
                 
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
				  
					
						<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">

							

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

											<th style="background: #fce4d6; color: #0070c0;">Inv/Challan Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Consignee Location</th>

											<th style="background: #fce4d6; color: #0070c0;">M.Code*</th>

											<th style="background: #fce4d6; color: #0070c0;" class="sticky-col-3">Material Descriptions</th>

											<th style="background: #fce4d6; color: #0070c0;">Batch No.</th>

											<th style="background: #fce4d6; color: #0070c0;">MFG Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Expiry Date</th>

											<th style="background: #fce4d6; color: #0070c0;">Qty. (Units)</th>

											<th style="background: #fce4d6; color: #0070c0;">Total Cs</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter Code*</th>

											<th style="background: #fce4d6; color: #0070c0;">Transporter Name</th>

											<th style="background: #fce4d6; color: #0070c0;">Truck No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR No</th>

											<th style="background: #fce4d6; color: #0070c0;">LR Date</th>

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

										
										
										  @php($i=1)
										  @if(count($datalist) > 0)
										  @foreach($datalist as $digiwimdata)
									  
									   <tr>
										<td class="sticky-col-1">{{$digiwimdata->indent_id}}</td>
										<td class="sticky-col-2">{{$digiwimdata->supplier_code}}</td>
										<td class="sticky-col-3" class="sticky-col-2 col-width">{{$billdata->supplier_name}}</td>
										<td>{{$digiwimdata->supplier_location }}></td>

										<td>{{$digiwimdata->po_no}}</td>

										<td>{{$digiwimdata->invoice_challan_no}}</td>

										<td>{{$digiwimdata->invoice_challan_date}}</td>

										<td>{{$digiwimdata->consignee_code}}</td>

										<td>{{$digiwimdata->consignee_name}}
										</td>

										<td>{{$digiwimdata->consignee_location}}
										</td>

										<td>{{$digiwimdata->m_code[]}}
										</td>

										<td>{{$digiwimdata->material_descriptions}}
										</td>

										<td>{{$digiwimdata->batch_no}}
										</td>

										<td>{{$digiwimdata->mfg_date}}
										</td>

										<td>{{$digiwimdata->expiry_date}}
										</td>

											<td>
												{{$digiwimdata->qty_units}}
											</td>

											<td>
												{{$digiwimdata->total_cs}}
											</td>

											<td>
												{{$digiwimdata->transporter_code}}
											</td>

											<td>
												{{$digiwimdata->transporter_name}}
											</td>

											<td>
												{{$digiwimdata->truck_no}}
											</td>

											<td>
												{{$digiwimdata->lr_no}}
											</td>

											<td>
											{{$digiwimdata->lr_date}}
											</td>

											<td>
											{{$digiwimdata->ewaybill_no}}
											</td>

											<td>
												{{$digiwimdata->truck_code}}
											</td>

											<td>
												{{$digiwimdata->vehicle_type}}
											</td>

											<td>
												{{$digiwimdata->custom}}
											</td>

											<td>
											{{$digiwimdata->custom_1}}
											</td>

											<td>
												{{$digiwimdata->custom_2}}
											</td>

											<td>
												{{$digiwimdata->custom_3}}
											</td>

											<td>
												{{$digiwimdata->custom_4}}
											</td>

										</tr>

										@endfor

									</tbody>

								</table>
							</div>

						</div>

						

					
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
@endsection