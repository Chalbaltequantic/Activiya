@extends('admin.admin')
@section('title', $title)
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
      left: 51px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-3 {
      position: sticky;
      left: 133px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }
 .sticky-col-4 {
      position: sticky;
      left: 180px; /* Adjust based on col-1 width */
      background: #fff;
      z-index: 99;
    }

    /* Column widths */
    .col-width {
     /* min-width: 160px;*/
    }

    @media (max-width: 768px) {
      .col-width {
        min-width: 90px;
      }

      .sticky-col-2 {
        left: 80px;
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
    min-width: 120px;
    padding: 8px;
    border: 1px solid #ccc;
    background: #fff;
    text-align: left;
}

#table th {
    position: sticky;
    top: 0;
    z-index: 2;
}
	
  </style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h4 class="mb-0">{{ $pagetitle }}</h4><small class="text-muted">Add one or more physical-count activities, then end the IRA.</small></div>
        <div>
            <a href="{{ route('admin.digiwim-inventory-ira.history') }}" class="btn btn-outline-primary">IRA History</a>
            <a href="{{ route('admin.digiwim-inventory-ira.inventory-book') }}" class="btn btn-outline-success">Book Vs IRA</a>
        </div>
    </div>
    <div id="pageAlert" class="alert d-none"></div>
    <div class="card mb-3"><div class="card-body">
        <form method="GET" action="{{ route('admin.digiwim-inventory-ira.index') }}">
            <div class="row">
                <div class="col-md-3 mb-2"><label>Plant</label><select name="plant_code" class="form-control"><option value="">All Plants</option>@foreach($plants as $plant)<option value="{{ $plant }}" @selected(request('plant_code') == $plant)>{{ $plant }}</option>@endforeach</select></div>
                <div class="col-md-3 mb-2"><label>Location</label><select name="plant_location" class="form-control"><option value="">All Locations</option>@foreach($locations as $location)<option value="{{ $location }}" @selected(request('plant_location') == $location)>{{ $location }}</option>@endforeach</select></div>
                <div class="col-md-4 mb-2"><label>Search</label><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Material, description, brand or batch"></div>
                <div class="col-md-2 mb-2"><label class="d-block">&nbsp;</label><button class="btn btn-primary">Search</button> <a href="{{ route('admin.digiwim-inventory-ira.index') }}" class="btn btn-secondary">Reset</a></div>
            </div>
        </form>
    </div></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-bordered table-striped table-sm mb-0" id="iraTable">
            <thead class="thead-light">
				<tr>
					<th class="sticky-col-1" style="background: #fce4d6; color: #0070c0;z-index:999;">Sl.</th>
					<th class="sticky-col-2" style="background: #fce4d6; color: #0070c0;z-index:999;">Material Code</th>
					
					<th class="sticky-col-3" style="background: #fce4d6; color: #0070c0;z-index:999;">Description</th>
					<th style="background: #fce4d6; color: #0070c0;">Division</th>
					<th style="background: #fce4d6; color: #0070c0;">Brand</th>
					<th style="background: #fce4d6; color: #0070c0;">Sub Brand</th>
					<th style="background: #fce4d6; color: #0070c0;">UOM</th>
					<th style="background: #fce4d6; color: #0070c0;">Piece/Box</th>
					<th style="background: #fce4d6; color: #0070c0;">MRP</th>
					<th style="background: #fce4d6; color: #0070c0;">Plant</th>
					<th style="background: #fce4d6; color: #0070c0;">Location</th>
					<th style="background: #fce4d6; color: #0070c0;">Batch</th>
					<th style="background: #fce4d6; color: #0070c0;">Book Qty</th>
					<th style="background: #fce4d6; color: #0070c0;">Qty Unit</th>
					<th style="background: #fce4d6; color: #0070c0;">Qty Case</th>
					<th style="background: #fce4d6; color: #0070c0;">BIN No.</th>
					<th style="background: #fce4d6; color: #0070c0;">Remarks</th>
					<th style="background: #fce4d6; color: #0070c0;">Summary</th>
					<th>Action</th>
				</tr>
				</thead>
            <tbody>
            @forelse($datalist as $index => $item)
                <tr data-inventory-key="{{ $item->inventory_key }}">
                    <td class="sticky-col-1">{{ $datalist->firstItem() + $index }}</td>
					<td class="sticky-col-2">{{ $item->material_code ?: '-' }}</td>
					<td class="sticky-col-3">{{ $item->material_description ?: '-' }}</td>
					<td>{{ $item->division ?: '-' }}</td>
					<td>{{ $item->brand ?: '-' }}</td>
					<td>{{ $item->sub_brand ?: '-' }}</td>
					<td>{{ $item->uom ?: '-' }}</td>
					<td>{{ $item->piece_per_box ?? '-' }}</td>
					<td>{{ $item->mrp !== null ? number_format((float)$item->mrp,2) : '-' }}</td>
					<td>{{ $item->storage_plant_code ?: '-' }}</td>
					<td>{{ $item->storage_plant_location ?: '-' }}</td>
					<td>{{ $item->batch_no ?: '-' }}</td>
					<td class="text-right font-weight-bold">{{ number_format((float)$item->available_qty,3) }}</td>
                    <td><input type="number" step="0.01" min="0.01" class="qty-unit"></td>
                    <td><input type="number" step="0.01" min="0.01" class="qty-case"></td>
                    <td><input type="text" maxlength="10" class="bin-no"></td>
                    <td><input type="text" maxlength="100" class="remarks"></td>
                    <td>Activities: <strong class="activity-count">{{ (int)$item->activity_count }}</strong><br>Unit: <strong class="total-unit">{{ number_format((float)$item->total_qty_unit,3) }}</strong><br>Case: <strong class="total-case">{{ number_format((float)$item->total_qty_case,3) }}</strong></td>
                    <td class="text-nowrap"><button type="button" class="btn btn-success btn-sm add-btn">Add</button> <button type="button" class="btn btn-danger btn-sm end-btn">End</button> <a target="_blank" href="{{ route('admin.digiwim-inventory-ira.report',$item->inventory_key) }}" class="btn btn-info btn-sm">Report</a></td>
                </tr>
            @empty<tr><td colspan="19" class="text-center py-4">No pending inventory is available for IRA.</td></tr>@endforelse
            </tbody>
        </table>
    </div></div>@if($datalist->hasPages())<div class="card-footer">{{ $datalist->links() }}</div>@endif</div>
</div>
@endsection
@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    /*  Global Variables  */

    const csrfToken = @json(csrf_token());

    const addActivityUrl = @json(route('admin.digiwim-inventory-ira.add-activity'));

    const endActivityUrl = @json(route('admin.digiwim-inventory-ira.end-activity'));

    const alertBox = document.getElementById('pageAlert');


    /*  Show Success / Error  */

    function showAlert(message, type = 'success')
    {
        alertBox.className = 'alert alert-' + type;

        alertBox.innerHTML = message;

        alertBox.classList.remove('d-none');

        setTimeout(function () {

            alertBox.classList.add('d-none');

        }, 6000);
    }


    /*  Read Error Message Returned by Laravel */

    function getErrorMessage(response)
    {
        if (response.message) {
            return response.message;
        }

        if (response.errors) {

            let firstError = Object.values(response.errors)[0];

            if (Array.isArray(firstError)) {
                return firstError[0];
            }

            return firstError;
        }

        return 'Something went wrong.';
    }


    /*  Enable / Disable Entire Row While Saving  */

    function setRowBusy(row, isBusy)
    {
        row.querySelectorAll('button,input').forEach(function (element) {

            element.disabled = isBusy;

        });
    }


    /*  ADD IRA ACTIVITY */

    document.querySelectorAll('.add-btn').forEach(function (button) {

        button.addEventListener('click', async function () {

            let currentRow = button.closest('tr');

            setRowBusy(currentRow, true);

            try {

                let response = await fetch(addActivityUrl, {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': csrfToken

                    },

                    body: JSON.stringify({

                        inventory_key: currentRow.dataset.inventoryKey,

                        qty_unit: currentRow.querySelector('.qty-unit').value,

                        qty_case: currentRow.querySelector('.qty-case').value,

                        bin_no: currentRow.querySelector('.bin-no').value,

                        remarks: currentRow.querySelector('.remarks').value

                    })

                });


                let result = await response.json();


                if (!response.ok) {
                    throw new Error(getErrorMessage(result));
                }


                /*  Update Summary  */

                currentRow.querySelector('.activity-count').textContent =
                    result.activity_count;

                currentRow.querySelector('.total-unit').textContent =
                    result.total_qty_unit;

                currentRow.querySelector('.total-case').textContent =
                    result.total_qty_case;


                /*  Clear Entry Fields */

                currentRow.querySelector('.qty-unit').value = '';

                currentRow.querySelector('.qty-case').value = '';

                currentRow.querySelector('.bin-no').value = '';

                currentRow.querySelector('.remarks').value = '';


                showAlert(result.message);

            }
            catch (error) {

                showAlert(error.message, 'danger');

            }
            finally {

                setRowBusy(currentRow, false);

            }

        });

    });



    /* END IRA  */

    document.querySelectorAll('.end-btn').forEach(function (button) {

        button.addEventListener('click', async function () {

            let currentRow = button.closest('tr');


            /* Validate Activity Count  */

            let totalActivities = parseInt(

                currentRow.querySelector('.activity-count').textContent || 0

            );


            if (totalActivities < 1) {

                showAlert(
                    'Add at least one activity before ending IRA.',
                    'warning'
                );

                return;
            }


            /* Confirmation */

            let confirmation = confirm(

                'End this IRA and move it to History?'

            );

            if (!confirmation) {
                return;
            }


            setRowBusy(currentRow, true);


            try {

                let response = await fetch(endActivityUrl, {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': csrfToken

                    },

                    body: JSON.stringify({

                        inventory_key: currentRow.dataset.inventoryKey

                    })

                });


                let result = await response.json();


                if (!response.ok) {

                    throw new Error(getErrorMessage(result));

                }


                /* Remove Completed */

                currentRow.remove();


                showAlert(result.message);

            }
            catch (error) {

                setRowBusy(currentRow, false);

                showAlert(error.message, 'danger');

            }

        });

    });

});

</script>
@endpush
