<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Admin :: Activiya App</title>
	<link rel="icon" href="{{asset('favicon.png')}}" type="image/png" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/assets/dist/css/adminlte.min.css') }}">
    <!-- jQuery -->
    <script src="{{ asset('backend/assets/plugins/jquery/jquery.min.js') }}"></script>
	 <link rel="stylesheet" href="{{ asset('backend/assets/custom.css') }}">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
 @stack('style')
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

      @include('admin.header')
     
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('bodycontent')
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">

            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer navbar-white">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
			
            </div>
            <!-- Default to the left -->
            <span id="fspan"><strong>Copyright &copy; 2025 <a href="/" style="color:#ffffff;">Activiya.com</a></strong></span>
			
			<span id="fspan_mob_logout" style="display:none;"><a class="btn btn-warning mr-3" href="{{ route('logout') }}" onclick = "event.preventDefault(); document.getElementById('logout-form').submit();" style="color:#fff;">
                     {{ __('Logout') }}</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST"
						class="d-none">
						@csrf
					</form></span>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->


<!-- AdminLTE App -->
<script src="{{ asset('backend/assets/dist/js/adminlte.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('backend/assets/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/bootstrap/js/bootstrap-select.min.js') }}"></script>
@stack('js')
   
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
	
   var $table = $('#billDataTable');

	if ($table.hasClass('enable-responsive')) {

		// Responsive table
		$table.DataTable({
			ordering: false,
			paging: false,
			dom: 'Bfrtip',

			buttons: [
				'csv',
				'excel'
			],

			autoWidth: false,

			responsive: {
				details: {
					type: 'column',
					target: 0
				}
			}
		});

	} else {

		$table.DataTable({
			ordering: false,
			paging: false,
			dom: 'Bfrtip',

			buttons: [
				'csv',
				'excel'
			],

			autoWidth: false

		});
	}
	
	$('#billDataTable1').DataTable({
		responsive: {
			details:{
				type:'column',
				target:'tr'
			}
		},
		ordering: false,
		 paging: false,
		dom: 'Bfrtip',
		buttons: [
			'csv', 'excel'
		]
	});
	
	
});	

function isMobileDevice() {
    return (
        /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) ||
        (window.matchMedia && window.matchMedia("(hover: none) and (pointer: coarse)").matches)
    );
}

function applyMobileLayout() {
    if (!isMobileDevice()) {
        return;
    }

    const lgoutLiHeader = document.getElementById('lgout_li_header');
    if (lgoutLiHeader) {
        lgoutLiHeader.style.display = 'none';
    }

    const fspan = document.getElementById('fspan');
    if (fspan) {
        fspan.style.display = 'none';
    }

    const fspanMobLogout = document.getElementById('fspan_mob_logout');
    if (fspanMobLogout) {
        fspanMobLogout.style.display = 'block';
    }

    document.querySelectorAll(
        "th.sticky-col-2, td.sticky-col-2, th.sticky-col-3, td.sticky-col-3, th.sticky-col-4, td.sticky-col-4, th.sticky-col-5, td.sticky-col-5"
    ).forEach(function (el) {
        el.style.zIndex = '';
        el.style.left = '';
        el.style.position = '';
        el.classList.remove("sticky-col-2", "sticky-col-3", "sticky-col-4", "sticky-col-5");
    });

    document.querySelectorAll("th.mobile-hide, td.mobile-hide, li.mobile-hide, .mobile-hide")
        .forEach(function (el) {
            el.style.setProperty("display", "none", "important");
        });

    document.querySelectorAll("option.mobile-hide")
        .forEach(function (opt) {
            opt.remove();
        });
}

document.addEventListener("DOMContentLoaded", function () {
    applyMobileLayout();
});

window.addEventListener("resize", function () {
    applyMobileLayout();
});

window.addEventListener("orientationchange", function () {
    setTimeout(applyMobileLayout, 300);
});
</script>
@if(Auth::check() && !empty(Auth::user()->vendor_code))
<script>

$(document).ready(function () {

    function loadSpotbuyNotifications()
    {
        $.ajax({

            url: "{{ route('admin.spotbuy.notifications.header') }}",
            type: "GET",
            dataType: "json",
            success: function (response)
            {
                console.log(
                    'Spot Buy Notification Response:',
                    response
                );

                let unreadCount = 0;
                let notifications = [];


                /* Read values only when response exists */
                if (response) {

                    unreadCount = parseInt(
                        response.unread_count || 0
                    );

                    notifications =
                        response.notifications || [];
                }

                $('#spotbuyNotificationHeaderCount').text(unreadCount);

                if (unreadCount > 0) {

                    $('#spotbuyNotificationCount')
                        .text(unreadCount)
                        .show();

                } else {

                    $('#spotbuyNotificationCount')
                        .text('0')
                        .hide();
                }

                if (
                    !Array.isArray(notifications) ||
                    notifications.length === 0
                ) {

                    $('#spotbuyNotificationList').html(`
                        <div class="dropdown-item text-center text-muted" style="                padding:20px 10px;white-space:normal;">
                            <i class="far fa-bell-slash" style="display:block;font-size:24px;margin-bottom:8px; color:#adb5bd; "></i>
                            No Spot Buy notifications
                        </div>
                    `);
                    return;                
				}

                let notificationHtml = '';
                $.each(
                    notifications,
                    function (index, notification)
                    {

                        /* Background for unread notification*/
                        let unreadStyle = '';

                        if (
                            parseInt(notification.is_read || 0) === 0
                        ) {
                            unreadStyle =
                                'background:#f4f8fc;';
                        }

                        /* Round badge*/
                        let roundText = '';

                        if (notification.round_no) {

                            roundText =
                                'Round ' +
                                escapeHtml(
                                    notification.round_no
                                );
                        }


                        notificationHtml += `

                            <a href="${notification.open_url}" class="dropdown-item" style="white-space:normal;padding:12px 15px;${unreadStyle}">

                                <div class="media">
                                    <div class="mr-3" style="width:38px;height:38px;border-radius:50%;background:#007bff; color:white; display:flex; align-items:center; justify-content:center;"><i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title" style=" font-size:14px; font-weight:600; margin-bottom:4px;">

                                            ${escapeHtml(
                                                notification.title
                                            )}
                                            ${
                                                parseInt(notification.is_read || 0) === 0 ?
                                                `
                                                <span class="float-right" style="color:#dc3545; font-size:7px;">
                                                    <i class="fas fa-circle"></i>
                                                </span>
                                                `
                                                :
                                                ''
                                            }

                                        </h3>
                                        ${
                                            roundText
                                            ?
                                            `
                                            <div style="margin-bottom:4px;">
                                                <span class="badge badge-info">${roundText}</span>
                                            </div>
                                            ` : ''
                                        }


                                        <p class="text-sm mb-1" style="color:#6c757d;">

                                            ${escapeHtml(
                                                notification.message
                                            )}

                                        </p>


                                        <p class="text-sm text-muted mb-0">

                                            <i class="far fa-clock mr-1"></i>

                                            ${escapeHtml(
                                                notification.created_at
                                            )}

                                        </p>


                                    </div>

                                </div>

                            </a>

                            <div class="dropdown-divider"></div>

                        `;
                    }
                );

                $('#spotbuyNotificationList')
                    .html(notificationHtml);
            },


            error: function (xhr)
            {
                console.log(
                    'Spot Buy Notification AJAX Error:',
                    xhr.responseText
                );

                $('#spotbuyNotificationHeaderCount')
                    .text('0');


                $('#spotbuyNotificationCount')
                    .hide();

                $('#spotbuyNotificationList').html(`

                    <div class="dropdown-item text-center text-muted" style="padding:20px 10px; white-space:normal;">

                        <i class="fas fa-exclamation-circle" style="
                                display:block;
                                margin-bottom:7px;
                                color:#dc3545;
                            "
                        ></i>

                        Unable to load notifications

                    </div>

                `);
            }

        });
    }


    function escapeHtml(value)
    {
        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }


        return $('<div>')
            .text(value)
            .html();
    }



    loadSpotbuyNotifications();

    setInterval(
        function ()
        {
            loadSpotbuyNotifications();
        },
        60000
    );

});

</script>
@endif
</body>

</html>
