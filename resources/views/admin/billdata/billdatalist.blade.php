@extends('admin.admin')

@section('bodycontent')
@push('style')
<style>
.table-responsive-fixed {
    overflow-x: auto;
    position: relative;
}
table {
    min-width: max-content;
    font-size: 12px;
}
.consign-data-table th,
.consign-data-table td {
    white-space: nowrap;
    vertical-align: middle;
}
.consign-data-table thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
}

.consign-data-table .table th,
.consign-data-table .table td {
    padding: 5px 10px;
}
.sticky-col-1 {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 99;
}
.sticky-col-2 {
    position: sticky;
    left: 132px;
    background: #fff;
    z-index: 99;
}
.sticky-col-3 {
    position: sticky;
    left: 242px;
    background: #fff;
    z-index: 99;
}

.sticky-col-4 {
    position: sticky;
    left: 332px;
    background: #fff;
    z-index: 99;
}
.table-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ccc;
}

#input-table {
    border-collapse: collapse;
    width: 100%;
    min-width: 1200px;
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
.bill-search-box {
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.bill-search-box label {
    font-size: 12px;
    margin-bottom: 4px;
    font-weight: 600;
}

.bill-search-box .form-control {
    height: 36px;
    font-size: 13px;
}

.bill-search-box .btn {
    height: 36px;
}

.bill-sort {
    color: #0070c0 !important;
    text-decoration: none !important;
    cursor: pointer;
}

.bill-sort:hover {
    color: #004b87 !important;
    text-decoration: none !important;
}

.sort-icon {
    font-size: 10px;
    margin-left: 4px;
    color: #666;
}
.bulk-delete-footer-wrap {
    overflow-x: auto;
    background: #fff;
    border-top: 1px solid #dee2e6;
}

.bulk-delete-footer-inner {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 12px 15px;
    background: #fff;
}

.bulk-delete-footer-inner .btn {
    min-width: 180px;
}
.pagination-wrap {
    padding: 12px 15px;
    background: #fff;
    border-top: 1px solid #dee2e6;
}

.pagination-info {
    font-size: 12px;
    color: #666;
    padding-top: 8px;
}
#billDataAjaxArea {
    position: relative;
}

.ajax-loading {
    opacity: 0.45;
    pointer-events: none;
}

.ajax-loader-box {
    display: none;
    position: absolute;
    left: 50%;
    top: 100px;
    transform: translateX(-50%);
    z-index: 9999;

    background: #fff;
    border: 1px solid #ddd;
    padding: 10px 18px;
    border-radius: 4px;

    box-shadow: 0 2px 6px rgba(0,0,0,.15);

    font-size: 13px;
}
@media (max-width: 768px) {

    .col-width {
        min-width: 90px;
    }

    .sticky-col-2 {
        left: 80px;
    }

}

</style>
@endpush
<!-- Content Header -->
<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="m-0">
                    Freight Shipment History
                </h1>

            </div>


            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">

                        <a href="/admin/dashboard">
                            Dashboard
                        </a>

                    </li>


                    <li class="breadcrumb-item active">
                        Freight Shipment History
                    </li>

                </ol>

            </div>

        </div>

    </div>

</div>
<!-- /.content-header -->



<!-- Main Content -->
<div class="content">

    <div class="container-fluid">


        <!-- Success / Error Message -->
        <div class="row">

            <div class="col-lg-12">


                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show">

                        <strong>
                            {{ session('success') }}
                        </strong>


                        <button
                            type="button"
                            class="close"
                            data-dismiss="alert"
                            aria-label="Close">

                            <span aria-hidden="true">
                                &times;
                            </span>

                        </button>

                    </div>

                @endif



                @if(session('error'))

                    <div class="alert alert-warning alert-dismissible fade show">

                        <strong>
                            {{ session('error') }}
                        </strong>


                        <button
                            type="button"
                            class="close"
                            data-dismiss="alert"
                            aria-label="Close">

                            <span aria-hidden="true">
                                &times;
                            </span>

                        </button>

                    </div>

                @endif


            </div>

        </div>
		
        <div class="row">

            <div class="col-lg-12">

                <div class="card">

                    <div class="bill-search-box">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="billSearch">Search</label>
                                    <input type="text" id="billSearch" class="form-control" value="{{ request('search')}}" placeholder="Search Vendor, LR No, Plant, Ref No., Invoice No. etc.">

                                </div>

                            </div>



                            <!-- Rows Per Page -->
                            <div class="col-md-2">

                                <div class="form-group mb-0">

                                    <label for="perPage">
                                        Rows Per Page
                                    </label>


                                    <select id="perPage" class="form-control">
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25
                                        </option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
										<option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>&nbsp;</label>
                                    <button type="button" id="searchButton" class="btn btn-primary btn-block"><i class="fas fa-search"></i>
                                        Search
                                    </button>
                                </div>
                            </div>
                            <!-- Reset Button -->
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>&nbsp</label>
                                    <div class="row">
										<div class="col-md-6 pr-1">
											<button type="button" id="resetButton" class="btn btn-secondary btn-block"><i class="fas fa-sync"></i>
												Reset
											</button>
										</div>

										<div class="col-md-6 pl-1">

											<button
												type="button"
												id="downloadXlsButton"
												class="btn btn-success btn-block">

												<i class="fas fa-file-excel"></i>

												XLS

											</button>

										</div>

									</div>

                                </div>

                            </div>


                        </div>


                    </div>



                    <!--
                    |--------------------------------------------------------------------------
                    | AJAX AREA
                    |--------------------------------------------------------------------------
                    |
                    | Only this part will reload during:
                    |
                    | Search
                    | Sorting
                    | Pagination
                    | Rows per page
                    |
                    -->

                    <div id="billDataAjaxArea">


                        <!-- AJAX Loader -->
                        <div
                            id="ajaxLoader"
                            class="ajax-loader-box">

                            <i class="fas fa-spinner fa-spin"></i>

                            Loading...

                        </div>


                        @include(
                            'admin.billdata.billdatalist_ajax',
                            [
                                'billdatalist' => $billdatalist,
                                'user_role' => $user_role,
                                'sortBy' => $sortBy,
                                'sortDirection' => $sortDirection
                            ]
                        )


                    </div>
                    <!-- /AJAX AREA -->


                </div>

            </div>

        </div>


    </div>

</div>
<!-- /.content -->



<script>

$(document).ready(function()
{


    /*
    |--------------------------------------------------------------------------
    | Current Sort Information
    |--------------------------------------------------------------------------
    |
    | These two variables remember which column
    | is currently sorted.
    |
    */

    let currentSortBy =
        "{{ $sortBy }}";


    let currentSortDirection =
        "{{ $sortDirection }}";



    /*
    |--------------------------------------------------------------------------
    | AJAX URL
    |--------------------------------------------------------------------------
    |
    | Same URL is used for:
    |
    | normal page
    | search
    | sorting
    | pagination
    |
    */

    let billDataUrl =
        "{{ url('admin/billdata/freight-shipment-history') }}";



    /*
    |--------------------------------------------------------------------------
    | Main AJAX Function
    |--------------------------------------------------------------------------
    |
    | All AJAX operations use this function.
    |
    */

    function loadBillData(page)
    {


        /*
         * Default page is page 1.
         */

        if(!page)
        {
            page = 1;
        }



        /*
         * Get search value.
         */

        let search =
            $.trim(
                $('#billSearch').val()
            );



        /*
         * Get selected rows per page.
         */

        let perPage =
            $('#perPage').val();



        /*
         * Show loader.
         */

        $('#ajaxLoader').show();


        $('#billDataAjaxArea')
            .addClass(
                'ajax-loading'
            );



        /*
         * Send AJAX request.
         */

        $.ajax({


            url:
                billDataUrl,


            type:
                "GET",


            dataType:
                "html",


            data:
            {

                page:
                    page,

                search:
                    search,

                per_page:
                    perPage,

                sort_by:
                    currentSortBy,

                sort_direction:
                    currentSortDirection

            },



            /*
            |--------------------------------------------------------------------------
            | AJAX Success
            |--------------------------------------------------------------------------
            */

            success:
                function(response)
                {


                    /*
                     * Replace AJAX area.
                     */

                    $('#billDataAjaxArea')
                        .html(
                            response
                        );


                    /*
                     * Remove loading style.
                     */

                    $('#billDataAjaxArea')
                        .removeClass(
                            'ajax-loading'
                        );


                },



            /*
            |--------------------------------------------------------------------------
            | AJAX Error
            |--------------------------------------------------------------------------
            */

            error:
                function(xhr)
                {


                    $('#billDataAjaxArea')
                        .removeClass(
                            'ajax-loading'
                        );


                    $('#ajaxLoader').hide();



                    /*
                     * Show real error in browser console.
                     *
                     * Press F12 -> Console
                     */

                    console.log(
                        xhr.responseText
                    );



                    /*
                     * User friendly error.
                     */

                    alert(
                        'Unable to load Bill Data. Error Code: '
                        + xhr.status
                    );


                }


        });


    }



    /*
    |--------------------------------------------------------------------------
    | Search Button
    |--------------------------------------------------------------------------
    */

    $('#searchButton').click(function()
    {

        loadBillData(1);

    });



    /*
    |--------------------------------------------------------------------------
    | Press Enter inside Search Box
    |--------------------------------------------------------------------------
    */

    $('#billSearch').on(
        'keypress',
        function(e)
        {


            if(e.which == 13)
            {

                e.preventDefault();


                loadBillData(1);

            }


        }
    );



    /*
    |--------------------------------------------------------------------------
    | Change Rows Per Page
    |--------------------------------------------------------------------------
    */

    $('#perPage').change(function()
    {

        loadBillData(1);

    });



    /*
    |--------------------------------------------------------------------------
    | Reset Search
    |--------------------------------------------------------------------------
    */

    $('#resetButton').click(function()
    {


        /*
         * Clear search.
         */

        $('#billSearch')
            .val('');



        /*
         * Reset page size.
         */

        $('#perPage')
            .val('25');



        /*
         * Default sorting.
         */

        currentSortBy =
            'created_at';


        currentSortDirection =
            'desc';



        /*
         * Load first page.
         */

        loadBillData(1);


    });



    /*
    |--------------------------------------------------------------------------
    | Column Sorting
    |--------------------------------------------------------------------------
    |
    | Because table is loaded through AJAX,
    | we use $(document).on().
    |
    */

    $(document).on(
        'click',
        '.bill-sort',
        function(e)
        {


            e.preventDefault();



            /*
             * Get clicked database column.
             */

            let column =
                $(this).data(
                    'column'
                );



            /*
             * Same column clicked again.
             *
             * ASC becomes DESC.
             * DESC becomes ASC.
             */

            if(
                currentSortBy == column
            )
            {


                if(
                    currentSortDirection == 'asc'
                )
                {

                    currentSortDirection =
                        'desc';

                }
                else
                {

                    currentSortDirection =
                        'asc';

                }


            }
            else
            {


                /*
                 * New column.
                 *
                 * Start with ASC.
                 */

                currentSortBy =
                    column;


                currentSortDirection =
                    'asc';


            }



            /*
             * After sorting start from page 1.
             */

            loadBillData(1);


        }
    );



    /*
    |--------------------------------------------------------------------------
    | AJAX Pagination
    |--------------------------------------------------------------------------
    |
    | Laravel pagination link contains:
    |
    | ?page=2
    |
    */

    $(document).on(
        'click',
        '#billDataAjaxArea .pagination a',
        function(e)
        {


            e.preventDefault();



            let url =
                $(this).attr(
                    'href'
                );



            if(!url)
            {
                return;
            }



            /*
             * Get page number from URL.
             */

            let page =
                getUrlParameter(
                    url,
                    'page'
                );



            /*
             * Load requested page.
             */

            loadBillData(page);


        }
    );



    /*
    |--------------------------------------------------------------------------
    | Select All Checkbox
    |--------------------------------------------------------------------------
    |
    | Delegated event is required because
    | table HTML changes after AJAX.
    |
    */

    $(document).on(
        'change',
        '#selectAll',
        function()
        {


            let checked =
                $(this).is(
                    ':checked'
                );



            $('.row-checkbox')
                .prop(
                    'checked',
                    checked
                );


        }
    );



    /*
    |--------------------------------------------------------------------------
    | Get Query String Value
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | ?page=3
    |
    | returns 3
    |
    */

    function getUrlParameter(
        url,
        name
    )
    {


        let result =
            new RegExp(
                '[?&]'
                +
                name
                +
                '=([^&#]*)'
            )
            .exec(
                url
            );



        if(
            result
            &&
            result[1]
        )
        {

            return decodeURIComponent(
                result[1]
            );

        }



        return 1;


    }

	$('#downloadXlsButton').click(function()
	{

		/*
		 * Current search value.
		 */

		let search =
			$.trim(
				$('#billSearch').val()
			);


		/*
		 * Export URL.
		 */

		let exportUrl =
			"{{ url('admin/billdata/freight-shipment-history/export') }}";


		/*
		 * Add current search and sorting.
		 */

		exportUrl +=
			'?search='
			+
			encodeURIComponent(search)
			+
			'&sort_by='
			+
			encodeURIComponent(currentSortBy)
			+
			'&sort_direction='
			+
			encodeURIComponent(currentSortDirection);


		/*
		 * Start download.
		 */

		window.location.href =
			exportUrl;

	});
});



/*
|--------------------------------------------------------------------------
| Existing Bulk Delete Confirmation
|--------------------------------------------------------------------------
*/

function confirmBulkDelete()
{


    let selected =
        $('.row-checkbox:checked')
        .length;



    if(
        selected == 0
    )
    {


        alert(
            'Please select at least one record to delete.'
        );


        return false;


    }



    return confirm(
        'Are you sure you want to delete selected records?'
    );


}

</script>


@endsection