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
            <h1 class="m-0">V_Placement</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
             <li class="breadcrumb-item active">V_Placement</li>
				
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
                 
				  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.vendor.loads') }}">V_Placement</a></li>
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
								<th style="background: #fce4d6; color: #0070c0;" class="">Reference<br>no</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Origin<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;" class="">Destination<br>name code</th>
								<th style="background: #fce4d6; color: #0070c0;">Mode</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Truck Type</th>
								{{-- <th style="background: #fce4d6; color: #0070c0;">ZW uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Zv uti %</th>
								<th style="background: #fce4d6; color: #0070c0;">Gross<br>utilization</th>--}}
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Total<br>Wt</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Total<br>Vol</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Vendor<br>name</th>
								<th style="background: #fce4d6; color: #0070c0;" class="mobile-hide">Sent<br>Date</th>
								<th style="background: #fce4d6; color: #0070c0;">Last<br>Status</th>
								<th style="background: #c6e0b4; color: #0070c0;">Placement<br>Status</th>
								<th style="background: #c6e0b4; color: #0070c0;">Camera</th>
								<th style="background: #c6e0b4; color: #0070c0;">Box Count</th>
								<th style="background: #c6e0b4; color: #0070c0;">Remarks</th>
								
								
								
								<th style="background: #c6e0b4; color: #0070c0;">Action</th>
								
													  
								</tr>
						  </thead>
						<tbody>
							@if(count($loads) > 0)
							 @foreach($loads as $row)							  
							<tr>
								<td class="">{{ $row->reference_no }}</td>
								<td class="mobile-hide">{{ $row->origin_name_code }} {{ $row->origin_name }}</td>								
								<td class="">{{ $row->destination_name_code }} {{ $row->destination_city }}</td>
								<td>{{ $row->t_mode }}</td>
								<td class="mobile-hide">{{ $row->truck->description ?? $row->truck_name ?? $row->truck_code ?? 'NA' }}</td>
									{{-- <td>{{ $row->zw_util }}%</td>
								<td>{{ $row->zv_util }}%</td>
									<td>{{ $row->gross_util }}%</td>--}}
								
								<td class="mobile-hide">{{ $row->total_weight }}</td>
								<td class="mobile-hide">{{ $row->total_volume }}</td>
								<td class="mobile-hide">{{ $row->vendor_name }}</td>
								<td class="mobile-hide">{{ $row->sent_at  }}</td>
								<td>
									@if($row->latestPlacement)
										<span class="badge bg-info">
											{{ ucfirst(str_replace('_',' ', $row->latestPlacement->placement_status)) }}
										</span>
									@else
										<span class="badge bg-secondary">--</span>
									@endif
								</td>
								<td>
									<select class="placement-status"
										data-load="{{ $row->id }}"  data-last-status="{{ $row->latestPlacement->placement_status ?? '' }}" {{ ($row->latestPlacement->placement_status ?? '') === 'Dispatch' ? 'disabled' : '' }}>
									<option value="">Select</option>
									<option value="Reported">Reported</option>
									<option value="Loading_Start">Loading Start</option>
									<option value="Loading_End">Loading End</option>
									<option value="Dispatch" {{ ($row->latestPlacement->placement_status ?? '') == 'Dispatch' ? 'selected' : '' }}>Dispatch</option>
									<option value="Others">Others</option>
								</select>
								</td>
								@php
									$boxKey = $row->id . '_' . $row->source_type;
									$boxData = $boxCounts[$boxKey] ?? null;
								@endphp

								<td>
									<button type="button"
											class="btn btn-info btn-sm camera-btn d-none"
											data-load-id="{{ $row->id }}"
											data-reference-no="{{ $row->reference_no }}"
											data-source-type="{{ $row->source_type }}">
										<i class="fa fa-camera"></i>
									</button>

									<input type="file"
										   class="camera-input d-none"
										   accept="image/*"
										   capture="environment">
								</td>

								<td>
									<span class="box-count-result">
										@if($boxData)
											Images: {{ $boxData->total_images }} |
											Boxes: {{ $boxData->total_boxes }}
										@else
											--
										@endif
									</span>

									<br>

									<button type="button"
											class="btn btn-xs btn-secondary view-box-images"
											data-load-id="{{ $row->id }}"
											data-source-type="{{ $row->source_type }}">
										View
									</button>
								</td>
								<td>
									<input type="text" class="lr-no d-none" placeholder="Enter LR No">
									<input type="text" class="remarks" placeholder="Enter remark">
								</td>
								
								<td><button class="btn btn-success btn-sm submit-placement"    data-load-id="{{ $row->id }}" data-reference_no="{{ $row->reference_no }}" data-source_type="{{$row->source_type}}">Submit</button>
								
								
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

<div class="modal fade" id="boxImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Box Count Images</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="boxImageList" class="row"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

$(document).on('change', '.placement-status', function () {

    let row = $(this).closest('tr');
    let status = $(this).val();
    let lrInput = row.find('.lr-no');
    if (status === 'Dispatch') {
        lrInput.removeClass('d-none');
    } else {
        lrInput.addClass('d-none').val('');
    }
});

$(document).ready(function () {

    $('.placement-status').each(function () {

        let lastStatus = $(this).data('last-status');
        if (!lastStatus) return;

        let disable = true;

        $(this).find('option').each(function () {
            if (disable) {
                $(this).prop('disabled', true);
            }
            if ($(this).val() === lastStatus) {
                disable = false;
            }
			if ($(this).val() === 'Dispatch') {
				$(this).find('option').not(':selected').prop('disabled', true);
			} else {
				$(this).find('option').prop('disabled', false);
			}
        });
    });

});
</script>
<script>
$(document).on('click', '.submit-placement', function () {

    let row = $(this).closest('tr');

    let loadId   = $(this).data('load-id');
    let reference_no   = $(this).data('reference_no');
    let source_type   = $(this).data('source_type');	
	
    let status   = row.find('.placement-status').val();
    let lrNo     = row.find('.lr-no').val();
    let remarks = row.find('.remarks').val();

    /* Validation */
    if (!status) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Please select placement status'
        });
        return;
    }

    if (status === 'dispatch' && (!lrNo || lrNo.trim() === '')) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'LR Number is mandatory when status is Dispatch'
        });
        return;
    }

    /* AJAX Submit */
    $.post("{{ route('admin.vendor.placement.status') }}", {
        _token: "{{ csrf_token() }}",
        load_id: loadId,
        placement_status: status,
        lr_no: lrNo,
        remarks: remarks,
        reference_no: reference_no,
        source_type: source_type,
    })
    .done(function (res) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: res.message || 'Placement status updated successfully'
        }).then(() => {
            location.reload();
        });
    })
    .fail(function (xhr) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseJSON?.message || 'Something went wrong'
        });
    });
});


$(document).on('change', '.placement-status', function () {
    let row = $(this).closest('tr');
    let status = $(this).val();

    if (status === 'Loading_End') {
        row.find('.camera-btn').removeClass('d-none');
    } else {
        row.find('.camera-btn').addClass('d-none');
    }
});

$(document).on('click', '.camera-btn', function () {
    let row = $(this).closest('tr');
    let input = row.find('.camera-input');

    input.data('load-id', $(this).data('load-id'));
    input.data('reference-no', $(this).data('reference-no'));
    input.data('source-type', $(this).data('source-type'));
    input.data('placement-status', row.find('.placement-status').val());

    input.click();
});

$(document).on('change', '.camera-input', function () {
    let file = this.files[0];

    if (!file) {
        return;
    }

    let input = $(this);
    let row = input.closest('tr');

    let loadId = input.data('load-id');
    let referenceNo = input.data('reference-no');
    let sourceType = input.data('source-type');
    let placementStatus = input.data('placement-status');

    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('load_summary_id', loadId);
    formData.append('reference_no', referenceNo);
    formData.append('source_type', sourceType);
    formData.append('placement_status', placementStatus);
    formData.append('image', file);

    row.find('.box-count-result').html('Counting...');

    $.ajax({
        url: "{{ route('admin.load.boxcount.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (res) {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Box Count: ' + res.count
                });

                row.find('.box-count-result').html('Latest Boxes: ' + res.count);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message
                });

                row.find('.box-count-result').html('--');
            }
        },

        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Image upload/count failed.'
            });

            row.find('.box-count-result').html('--');
        }
    });
});

$(document).on('click', '.view-box-images', function () {
    let loadId = $(this).data('load-id');
    let sourceType = $(this).data('source-type');

    $('#boxImageList').html('Loading...');

    $.get("{{ url('/admin/load-box-count/list') }}/" + loadId + "/" + sourceType, function (res) {
        if (res.success) {
            let html = '';

            if (res.records.length === 0) {
                html = '<div class="col-md-12">No images found.</div>';
            }

            res.records.forEach(function (item) {
                html += `
                    <div class="col-md-4 mb-3" id="box-record-${item.id}">
                        <div class="card">
                            <img src="/${item.image_path}" class="card-img-top" style="height:160px; object-fit:cover;">
                            <div class="card-body p-2">
                                <p class="mb-1"><b>Count:</b> ${item.box_count}</p>
                                <p class="mb-1"><b>Status:</b> ${item.placement_status}</p>

                                <button type="button"
                                        class="btn btn-danger btn-xs delete-box-image"
                                        data-id="${item.id}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#boxImageList').html(html);
            $('#boxImageModal').modal('show');
        }
    });
});

$(document).on('click', '.delete-box-image', function () {
    let id = $(this).data('id');

    if (!confirm('Delete this image?')) {
        return;
    }

    $.ajax({
        url: "{{ url('/admin/load-box-count/delete') }}/" + id,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            _method: "DELETE"
        },
        success: function (res) {
            if (res.success) {
                $('#box-record-' + id).remove();

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: res.message
                });
            }
        }
    });
});

</script>

@endsection