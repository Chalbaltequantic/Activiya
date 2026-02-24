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

/*Specific Widths based on typical data lengths */
input[name*="sku"], input[name*="code"] {
    width: 15ch; /* Fits approx 15 characters */
}

input[name*="qty"], input[name*="priority"] {
    width: 6ch;  /* Fits approx 6 characters */
    text-align: center;
}
input[name*="qty"], input[name*="priority"] {
    width: 6ch;  /* Fits approx 6 characters */
    text-align: center;
}
input[name*="z_weight"], input[name*="z_volume"], input[name*="totalwt"], input[name*="totalvol"], input[name*="totalzwutil"],input[name*="totalzvutil"], input[name*="totalgross"] {
    width: 9ch;  /* Fits approx 6 characters */
    text-align: center;
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
    min-width: 20px;
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
            <h1 class="m-0">Load Optimizer</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">Load Optimiser</li>
				
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
                  <li class="nav-item"><a class="nav-link" href="{{route('admin.lopmanualupload')}}" data-toggle="tab">Create</a></li>
                  <li class="nav-item"><a class="nav-link active" href="{{route('admin.loadSummary')}}">Indent</a></li>
				 <li class="nav-item"><a class="nav-link" href="{{route('admin.qualifiedloadsummary')}}">Qualified Indent</a></li>	
				 <li class="nav-item"><a class="nav-link" href="{{route('admin.loadSummaryApproval')}}">Approve / Reject Summary</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="activity">
                  <div id="ajaxSuccess"
					 class="alert alert-success d-none">
				</div>
				
					<div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table table-container">
						<form action="{{url('/admin/load-summary/items/update')}}" method="post" name="addfrm" id="postform">
						  @csrf
						  
						  <input type="hidden" name="reference_no" value="{{ $referenceNo }}">
						  <input type="hidden" id="truck_id" value="{{ $summary->truck_id }}">
						  <table class="table-bordered border-dark table-hover" id="skuTable" width="100%">
							  <thead>
								<tr>
									<th style="background: #fce4d6; color: #0070c0;" class="">Reference No.</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Origin <br>Name & Code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">Destination <br>Name & code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">SKU code</th>
									<th style="background: #fce4d6; color: #0070c0;" class="">SKU description</th>
									<th style="background: #fce4d6; color: #0070c0;">Priority</th>
									<th style="background: #fce4d6; color: #0070c0;">SKU class</th>
									<th style="background: #fce4d6; color: #0070c0;">T mode</th>
									<th style="background: #fce4d6; color: #0070c0;">QTY</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total <br>weight</th>
									<th style="background: #fce4d6; color: #0070c0;">Z Total <br>Volume</th>
									<th style="background: #fce4d6; color: #0070c0;">Action</th>
								
											  
								</tr>
							  </thead>
							  <tbody>
								@if(count($items) > 0)
								@foreach($items as $i => $row)
								<tr class="sku-row">

									<input type="hidden"
										   name="items[{{ $i }}][id]"
										   value="{{ $row->id }}">

									<input type="hidden"
										   class="load_optimizer_id"
										   name="items[{{ $i }}][load_optimizer_id]"
										   value="{{ $row->id }}">
										   

									<td>{{ $referenceNo }}</td>

									<td>{{ $row->origin_name_code }} - {{ $row->origin_name }}</td>

									<td>{{ $row->destination_name_code }} - {{ $row->destination_city }}</td>

									<td>
										<input name="items[{{ $i }}][sku_code]"
											   class="sku_code"
											   value="{{ $row->sku_code }}">
											   
											     <!-- UNIT VALUES (hidden, per row) -->
										<input name="items[{{ $i }}][unit_weight]" type="hidden" class="unit_weight" value="{{ round(($row->z_total_weight / $row->qty),2) ?? 0 }}">
										<input name="items[{{ $i }}][unit_volume]" type="hidden" class="unit_volume" value="{{ round(($row->z_total_volume/$row->qty),2) ?? 0 }}">
									</td>

									<td>
										<input name="items[{{ $i }}][sku_description]"
											   class="sku_desc"
											   value="{{ $row->sku_description }}"
											   readonly>
									</td>

									<td>
										<input name="items[{{ $i }}][priority]"
											   class="priority"
											   value="{{ $row->priority }}">
									</td>

									<td>{{ $row->sku_class }}</td>

									<td>{{ $row->t_mode }}</td>

									<td>
										<input name="items[{{ $i }}][qty]"
											   class="qty"
											   value="{{ $row->qty }}">
									</td>

									<td>
										<input readonly
											   name="items[{{ $i }}][z_weight]"
											   class="z_weight"
											   value="{{ $row->z_total_weight }}">
									</td>

									<td>
										<input readonly
											   name="items[{{ $i }}][z_volume]"
											   class="z_volume"
											   value="{{ $row->z_total_volume }}">
									</td>
									<td>
										<button type="button"
											class="btn btn-danger btn-sm deleteSku"
											data-id="{{ $row->id }}">
											Delete
										</button>
									</td>
								</tr>
								@endforeach
								@endif
								</tbody>
								<tfoot>
								<tr style="background:#f5f5f5;font-weight:bold">
									<td colspan="9" class="text-right">TOTAL</td>
									<td><input id="totalWeight" name="totalwt" readonly></td>
									<td><input id="totalVolume" name="totalvol" readonly></td>
									<td></td>
								</tr>
								<tr style="background:#e8f4ff;font-weight:bold">
									<td colspan="9" class="text-right">UTILIZATION</td>
									<td>
										<input id="zwUtil" name="totalzwutil" readonly placeholder="Z WT %">
									</td>
									<td>
										<input id="zvUtil" name="totalzvutil" readonly placeholder="Z VOL %">
									</td>
									<td></td>
								</tr>
								<tr style="background:#d1ecf1;font-weight:bold">
									<td colspan="9" class="text-right">GROSS UTIL %</td>
									<td colspan="2">
										<input id="grossUtil" name="totalgross" readonly>
									</td>
									<td></td>
								</tr>
								</tfoot>
							</table>
						
							<div class="row text-right mt-5">
							<div class="col-md-7">
							<button type="button" id="addSku" class="btn btn-primary">Add more SKU</button>
							</div>
							<div class="col-md-3">
							<button id="btnUpdateSummary" type="button" class="btn btn-success">Update</button> 
							</div>
							</div>
						</form>
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

<script>
function recalcTotals() {
    let totalWeight = 0;
    let totalVolume = 0;

    $('.z_weight').each(function () {
        totalWeight += parseFloat($(this).val()) || 0;
    });

    $('.z_volume').each(function () {
        totalVolume += parseFloat($(this).val()) || 0;
    });

    $('#totalWeight').val(totalWeight.toFixed(2));
    $('#totalVolume').val(totalVolume.toFixed(2));

    $.post('/admin/load-summary/calc-util', {
        _token: '{{ csrf_token() }}',
        total_weight: totalWeight,
        total_volume: totalVolume,
        truck_id: '{{ $summary->truck_id ?? '' }}'
    }, function (res) {
        $('#zwUtil').val(res.zw_util + '%');
        $('#zvUtil').val(res.zv_util + '%');
        $('#grossUtil').val(res.gross_util + '%');
    });
}

let rowIndex = {{ count($items) }};

/* SKU lookup */
$(document).on('blur', '.sku_code', function () {
    let row = $(this).closest('tr');
    let code = $(this).val();

    if (!code) return;

    $.get('/admin/loadoptimizer/sku/' + code, function (res) {
        row.find('.sku_desc').val(res.description);
        row.find('.unit_weight').val(res.weight);
		row.find('.unit_volume').val(res.volume)
    });
});

/* Qty → weight & volume */
/* QTY BLUR */
$(document).on('blur', '.qty', function () {
    let row = $(this).closest('tr');
    let qty = parseFloat($(this).val()) || 0;

    let unitWeight = parseFloat(row.find('.unit_weight').val()) || 0;
	let unitVolume = parseFloat(row.find('.unit_volume').val()) || 0;

    row.find('.z_weight').val((unitWeight * qty).toFixed(2));
    row.find('.z_volume').val((unitVolume * qty).toFixed(2));

    recalcTotals();
});

/* ADD MORE SKU (CLONE) */
$('#addSku').on('click', function () {

    let baseRow = $('#skuTable tbody tr.sku-row:first');

    if (!baseRow.length) {
        alert('No row available to clone');
        return;
    }

    for (let i = 0; i < 5; i++) {

        let clone = baseRow.clone();

        clone.find('input').each(function () {
            let name = $(this).attr('name');
            if (!name) return;

            name = name.replace(/\[\d+]/, `[${rowIndex}]`);
            $(this).attr('name', name);

            $(this).val('');
        });

        /* New SKU → no optimizer id */
        clone.find('.load_optimizer_id').val('');

       // clone.removeData('weight');
       // clone.removeData('volume');

        $('#skuTable tbody').append(clone);
        rowIndex++;
    }
});

//edit sku item to qualify
$(document).ready(function () {

    $('#btnUpdateSummary').on('click', function () {

        let referenceNo = "{{ $referenceNo }}"; // blade variable
        let itemsArray = [];

        //Loop each SKU row
        $('.sku-row').each(function () {

            let skuCode = $(this).find('.sku_code').val();
            let qty     = $(this).find('.qty').val();

            if (!skuCode || !qty) return;
			 itemsArray.push({
                sku_code: skuCode,
                sku_description: $(this).find('.sku_desc').val(),
                qty: qty,
                unit_weight: $(this).find('.unit_weight').val(),
                unit_volume: $(this).find('.unit_volume').val(),
				load_optimizer_id: $(this).find('.load_optimizer_id').val(),
				priority: $(this).find('.priority').val(),
				z_weight: $(this).find('.z_weight').val(),
                z_volume: $(this).find('.z_volume').val(),
            });
        });

        if (itemsArray.length === 0) {
            alert('Please add at least one SKU');
            return;
        }

        $.ajax({
            url: '/admin/load-optimizer/' + referenceNo + '/update-skus',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                truck_id: $('#truck_id').val(),
                items: itemsArray
            },
            success: function (res) {
					if (res.status === 'success') {

						// show success message
						$('#ajaxSuccess')
							.removeClass('d-none')
							.html(res.message);

						// optional reload
						// location.reload();
					} else {
						alert(res.message || 'Update failed');
					}
				},
				error: function (xhr) {
					alert('Something went wrong. Please try again.');
				}
        });

    });

});

//delete item
$('.deleteSku').on('click', function () {
    if (!confirm('Delete this SKU?')) return;

    let id = $(this).data('id');

    $.ajax({
        url: '/admin/load-optimizer/item/' + id + '/delete',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {
            alert(res.message);
           // location.reload();
        }
    });
});

</script>

@endsection