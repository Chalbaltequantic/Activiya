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
      left: 135px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
.sticky-col-3 {
      position: sticky;
      left: 250px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 340px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-5 {
      position: sticky;
      left: 420px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
      min-width: 100px;
    }

    @media (max-width: 768px) {
      .col-width {
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
            <h1 class="m-0">Validate Freight Bills</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Validate Freight Bills</li>
				
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
					
				@if(session('mismatches') || session('fileErrors') || session('saveErrors'))
					<div class="alert alert-warning alert-dismissible fade show " style="display: none; max-height: 300px; overflow-y: auto;">
						<strong>Errors:</strong>
							<ul>
								@foreach((array) session('mismatches') as $item)
									<li>Amount mismatch for Ref No: {{ $item['order_ref_no'] }}</li>
								@endforeach
								@foreach((array) session('fileErrors') as $fileError)
									<li>{{ $fileError }}</li>
								@endforeach
								@foreach((array) session('saveErrors') as $saveError)
									<li>{{ $saveError }}</li>
								@endforeach
							</ul>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
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
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Validate Freight Bills </a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">View Freight Bills</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						    <form method="POST" action="{{ route('admin.freight.store') }}" id="freightValidationForm">
								@csrf
								<table class="table table-bordered border-dark table-hover" id="table">
								  <thead>
									<tr>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:140px;" class="sticky-col-1">S5 consignor short<br> name & location</th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:125px;" class="sticky-col-2">D5 consignor short<br> name & location</th>
									  <th style="background: #fce4d6; color: #0070c0;z-index:999;width:90px;" class="sticky-col-3">Order Ref No.<br />( <small>Indent ID</small>)
										</th>
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px" class="sticky-col-4">Vendor name</th>	
																  
										<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-5">LR/CN No.</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">LR/CN Date</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Charged<br /><small>Truck Type</small></th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Ref 1<br />FPO NO.</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Ref 2 <br />FGRN</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Freight <br>Invoice No.</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Invoice Dt.</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Amount</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Freight Invoice</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">POD</th>
										<th style="background: #fce4d6; color: #0070c0;" class="">Approvals</th>						
																			
										<th style="background: #ddebf7; color: #0070c0;" class="">Validate<br>
										<input type="checkbox" id="selectAllValidate"></th> 
										
										<th style="background: #fce4d6; color: #0070c0;" class="">Submit<br>
										<input type="checkbox" id="selectAllSubmit"></th>
										
										<th style="background: #fce4d6; color: #0070c0;" class="">Return<br>
										<input type="checkbox" id="selectAllReturn"></th>

										<th style="background: #fce4d6; color: #0070c0;" class="">Status</th>
										<th style="background: #fce4d6; color: #0070c0;" class="col-width">Remark</th>
									  
									</tr>
								  </thead>
								  <tbody>
									 
									  @php($i=1)
									  @if(count($entries) > 0)
									  @foreach($entries as $billdata)
								  
									<tr data-id="{{ $billdata->id }}">
										<td class="sticky-col-1">{{$billdata->s5_consignor_short_name_and_location}}</td>
										<td class="sticky-col-2">{{$billdata->d5_consignor_short_name_and_location}}</td>
										<td class="sticky-col-3">{{$billdata->ref1}}</td>
										<td class="sticky-col-4">{{$billdata->vendor_name}}</td>
										
										<td class="sticky-col-5">{{$billdata->lr_no}}</td>
										<td>{{$billdata->lr_cn_date}}</td>
										<td>{{$billdata->truck_type}}</td>
										<td>{{$billdata->ref2}}</td>
										<td>{{$billdata->ref3}}</td>
									  
										<td>{{ $billdata->freight_invoice_no }}</td>
										<td>{{ $billdata->freight_invoice_date }}</td>
										<td>{{ number_format($billdata->freight_amount) }}</td>
										<td>
											@if($billdata->freight_invoice_file)
											<a href="/{{$billdata->freight_invoice_file}}" target="_blank">View Invoice File</a>
											@else 
												NA 
											@endif
											</td>
											<td>
											@if($billdata->pod_file)
											<a href="/{{$billdata->pod_file}}" target="_blank">View Pod File</a>
											@else 
												NA 
											@endif
										</td>
											<td>
											@if($billdata->approval_file)
											<a href="/{{$billdata->approval_file}}" target="_blank">View Approval File</a>
											@else 
												NA 
											@endif
											</td>
											
											<input type="hidden" class="freight-amount" value="{{ $billdata->freight_amount }}">
											 
											<td><input type="checkbox" class="validate-checkbox"></td>
											<td><input type="checkbox" class="submit-checkbox" disabled></td>
											<td><input type="checkbox" class="return-checkbox" disabled></td>
											<td>
											<input type="hidden" class="row-id" value="{{ $billdata->id }}">
											<div class="status"></div>
											</td>
											<td><input type="text" name="remark[]" class="form-control remark"></td>
											
										<input type="hidden" name="validated_ids[]" class="validated-id" value="">
										<input type="hidden" name="submitted_ids[]" class="submitted-id" value="">
										<input type="hidden" name="returned_ids[]" class="returned-id" value="">
										<input type="hidden" name="remarks[]" class="remark-input" value="">
										</tr>
									  
							  @endforeach
							  @else
								  <tr><td colspan="19">No data found</td></tr>
							  @endif
							  
						   </tbody>
						    <tr><td colspan="14"></td>
							<td colspan="3"> 
							
							<button type="button" class="btn btn-primary" id="validateBtn">Validate</button>
							<button type="submit" class="btn btn-success">Send</button>
							 
							<td colspan="2"></td>
							</tr>
						   
					  </table>
					  
					</form>
					 
					</div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                  	<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">
										 
						<table class="table table-bordered border-dark table-hover">
							<thead>
							<tr>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:140px;" class="sticky-col-1">S5 consignor short<br> name & location</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:49" class="sticky-col-2">D5 consignor short<br> name & location</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:60px;" class="sticky-col-3">Order Ref No.<br />( <small>Indent ID</small>)
								</th>
								<th style="background: #fce4d6; color: #0070c0;z-index:999; width:60px;" class="sticky-col-4">Vendor name</th>	
													  
								<th style="background: #fce4d6; color: #0070c0;z-index:999;width:50px;" class="sticky-col-5">LR/CN No.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">LR/CN Date</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Charged<br /><small>Truck Type</small></th>	
								<th style="background: #fce4d6; color: #0070c0;" class="">Ref 1<br />FPO NO.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Ref 2 <br />FGRN</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Freight <br>Invoice No.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Invoice Dt.</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Amount</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Freight Invoice</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">POD</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Approvals</th>						
								<th style="background: #ddebf7; color: #0070c0;" class="">Validate</th> 
								<th style="background: #ddebf7; color: #0070c0;" class="">Submit</th> 
								<th style="background: #fce4d6; color: #0070c0;" class="">Return</th>
								<th style="background: #fce4d6; color: #0070c0;" class="col-width">Remark</th>
								

							</tr>
						  </thead>
						<tbody>
							
						  @if(count($updatedentries) > 0)
						  @foreach($updatedentries as $updatedbilldata)
							  
						<tr>
							<td class="sticky-col-1 ">{{$updatedbilldata->s5_consignor_short_name_and_location}}</td>
							<td class="sticky-col-2 ">{{$updatedbilldata->d5_consignor_short_name_and_location}}</td>
							<td class="sticky-col-3 ">{{$updatedbilldata->ref1}}</td>
							<td class="sticky-col-4 ">{{$updatedbilldata->vendor_name}}</td>
							
							<td class="sticky-col-5 ">{{$updatedbilldata->lr_no}}</td>
							<td>{{$updatedbilldata->lr_cn_date}}</td>
							<td>{{$updatedbilldata->truck_type}}</td>
							<td>{{$updatedbilldata->ref2}}</td>
							<td>{{$updatedbilldata->ref3}}</td>
							<td>{{ $updatedbilldata->freight_invoice_no }}</td>
							<td>{{ $updatedbilldata->freight_invoice_date }}</td>
							<td>{{ number_format($updatedbilldata->freight_amount) }}</td>
							<td>
								
								<span class="uploaded-file" id="invoice-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->freight_invoice_file)
										<a href="{{ asset( $updatedbilldata->freight_invoice_file) }}" target="_blank"  class="btn btn-sm btn-primary">
											View
										</a>
										
										@else NA
									@endif
								</span>
							</td>
							<td>
								
								<span class="uploaded-file" id="pod-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->pod_file)
										<a href="{{ asset( $updatedbilldata->pod_file) }}" target="_blank" class="btn btn-sm btn-primary">
											View
										</a>
										
										@else NA
									@endif
								</span>
							</td>
							<td>
								@if ($updatedbilldata->freight_type=='ADHOC')
									
									<span class="uploaded-file" id="approval-{{ $updatedbilldata->id }}">
									@if ($updatedbilldata->approval_file)
										<a href="{{ asset($updatedbilldata->approval_file) }}" target="_blank">View</a>
										
										@else NA
											
									@endif
									</span>
									@else NA
								@endif
							</td>
							
							
							<td>{!! ($updatedbilldata->validated_status=='submitted') 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td></td>
							<td>{!! ($updatedbilldata->submit==1) 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td></td>
							<td>{!! ($updatedbilldata->f_return==1) 
									? '<span style="color: green;">&#9989;</span>' 
									: '<span style="color: red;">&#10060;</span>' 
								!!}</td>
							<td>{{$updatedbilldata->validation_remark}}</td>
								
					 
						</tr>
								  
						  @endforeach
						  @else
							  <tr><td colspan="15">No data found</td></tr>
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
<script>
    document.getElementById('selectAllValidate').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.validate-checkbox').forEach(cb => cb.checked = checked);
    });

    document.getElementById('selectAllSubmit').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.submit-checkbox').forEach(cb => {
            if (!cb.disabled) cb.checked = checked;
        });
    });

    document.getElementById('selectAllReturn').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.return-checkbox').forEach(cb => {
            if (!cb.disabled) cb.checked = checked;
        });
    });

    document.getElementById('validateBtn').addEventListener('click', function () {
        const data = [];

        document.querySelectorAll('tbody tr').forEach(row => {
            const checkbox = row.querySelector('.validate-checkbox');
            if (checkbox && checkbox.checked) {
                const id = row.querySelector('.row-id').value;
                const amount = row.querySelector('.freight-amount').value;

                data.push({ id: id, freight_amount: amount });
            }
        });

        fetch("{{ route('admin.freight.validate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rows: data })
        })
        .then(response => response.json())
        .then(result => {
            document.querySelectorAll('tbody tr').forEach(row => {
                const rowId = row.querySelector('.row-id').value;
                const submitCb = row.querySelector('.submit-checkbox');
                const returnCb = row.querySelector('.return-checkbox');
                const statusCell = row.querySelector('.status');

                const found = result.find(r => r.id == rowId);
                if (found) {
                    if (found.valid) {
                        submitCb.checked = true;
                        submitCb.disabled = false;
                        returnCb.checked = false;
                        returnCb.disabled = true;
                        statusCell.innerText = 'Valid';
                    } else {
                        returnCb.checked = true;
                        returnCb.disabled = false;
                        submitCb.checked = false;
                        submitCb.disabled = true;
                        statusCell.innerText = 'Invalid';
                    }
                }
            });
        });
    });
	
	document.getElementById('freightValidationForm').addEventListener('submit', function (e) {
    document.querySelectorAll('tbody tr').forEach(row => {
        const id = row.getAttribute('data-id');
        const validateCb = row.querySelector('.validate-checkbox');
        const submitCb = row.querySelector('.submit-checkbox');
        const returnCb = row.querySelector('.return-checkbox');
        const remarkVal = row.querySelector('input[name="remark[]"]').value;

        // Update hidden inputs
        row.querySelector('.validated-id').value = validateCb.checked ? id : '';
        row.querySelector('.submitted-id').value = (submitCb.checked && !submitCb.disabled) ? id : '';
        row.querySelector('.returned-id').value = (returnCb.checked && !returnCb.disabled) ? id : '';
        row.querySelector('.remark-input').value = remarkVal;
    });
});
</script>
@endsection