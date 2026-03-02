<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>Admin :: Activiya App</title>
	<link rel="icon" href="{{asset('favicon.png')}}" type="image/png" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/assets/dist/css/adminlte.min.css') }}">
    <!-- jQuery -->
    <script src="{{ asset('backend/assets/plugins/jquery/jquery.min.js') }}"></script>
	
    @stack('css')
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
/*input, textarea {
  field-sizing: content; /* Makes the field resize to fit its content */
  min-width: 50px;      /* Prevents it from becoming too small */
  max-width: 200px;      /* Prevents it from becoming too wide */
  /* Add other styling like font, padding, border etc., to ensure proper sizing */
  font: inherit;
  padding: 1px 2px;
  font-size: 14px;
}

textarea {
  min-height: 1.5lh;     /* Sets a minimum height in line units */
  resize: none;          /* Optional: removes the manual resize handle */
}*/
.dataTables_filter {
    float: left !important;
}
.navbar-white {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: #ffffff;
}
.navbar-light .navbar-nav .nav-link {
    color: #ffffff;
}
.navbar-light .navbar-brand {
    color: rgba(255, 255, 255, 1);
}

 th {
        vertical-align: top !important;
    }

.table-container {
    max-height: 400px; 
    overflow-y: auto;
    border: 1px solid #ccc;
}
#billDataTable{
    max-height: 400px;   
    overflow-y: auto;
    border: 1px solid #ccc;
}
</style>
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
<script src="{{ asset('backend/assets/dist/js/pages/dashboard.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/bootstrap/js/bootstrap-select.min.js') }}"></script>
@stack('js')
   
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
$(document).ready(function() {
	$('#billDataTable').DataTable({
		responsive: true,
		ordering: false,
		 paging: false,
		dom: 'Bfrtip',
		buttons: [
			'csv', 'excel'
		]
	});
	$('#billDataTable1').DataTable({
		responsive: true,
		ordering: false,
		 paging: false,
		dom: 'Bfrtip',
		buttons: [
			'csv', 'excel'
		]
	});
	
});
const isMobileg = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

    // Hide camera buttons if not mobile
    if (isMobileg) { 
        const lgout_li_header = document.getElementById('lgout_li_header');
        
        lgout_li_header.style.display = 'none';
		document.getElementById('fspan').style.display='none';
		document.getElementById('fspan_mob_logout').style.display='block';
		
		//remove stickie from th, td on mobile
		/*document.querySelectorAll("th, td").forEach(el => {
                el.classList.remove("sticky-col-2");
                el.classList.remove("sticky-col-3");
                el.classList.remove("sticky-col-4");
                el.style.zIndex = null;
            });*/
			
			document.querySelectorAll("th.sticky-col-2, td.sticky-col-2,                  th.sticky-col-3,td.sticky-col-3,th.sticky-col-4, td.sticky-col-4, th.sticky-col-5, td.sticky-col-5")
        .forEach(el => {
            el.style.zIndex = null;
            el.classList.remove("sticky-col-2", "sticky-col-3", "sticky-col-4", "sticky-col-5");
        });
			
		 // Hide TH + TD with mobile-hide
        document.querySelectorAll("th.mobile-hide, td.mobile-hide, li.mobile-hide")
            .forEach(el => el.style.setProperty("display", "none", "important"));

        // Remove OPTION completely (hiding does not work)
        document.querySelectorAll("option.mobile-hide")
            .forEach(opt => opt.remove());
		
    }
	
</script>
</body>

</html>
