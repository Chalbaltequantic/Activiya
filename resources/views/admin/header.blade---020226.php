
  <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
		<a href="/" class="navbar-brand">
        {{--<img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
		style="opacity: .8">--}}
        <span class="brand-text font-weight-light"><b>Activiya</b></span>
      </a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
            <!-- Left navbar links -->
			<div class="collapse navbar-collapse" id="navbarCollapse">

            <ul class="navbar-nav">
              @if(Auth::user() && Auth::user()->role_id ==24)
					@if(Gate::allows('admin.users.index'))
						<li class="nav-item d-none d-sm-inline-block" style="z-index:1100;"><a href="{{ route('admin.users.index') }}" class="nav-link">Users</a></li>
						@endif
					@endif
				@if(Auth::user() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 13))
					@if(Auth::user() && (Auth::user()->role_id == 1))
				<li class="nav-item d-none d-sm-inline-block">
                    <a href="/admin/dashboard" class="nav-link">Home</a>
                </li>
					@endif
					
				<li class="nav-item dropdown">
					<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Masters</a>
					<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow" style="z-index:1100;">
					    <!-- Level two dropdown-->
					@if(Gate::allows('admin.vendor'))	
					  <li class="dropdown-submenu dropdown-hover"  style="z-index:1100;">
						<a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Vendor Master</a>
						<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow" style="z-index:1100;">
						
						  <li style="z-index:1100;">
							<a tabindex="-1" href="{{ route('admin.vendor') }}" class="dropdown-item">View</a>
						  </li>
						
						  <li class="{{ request()->is('admin/vendor*') ? 'active' : '' }}"  style="z-index:1100;">
							<a tabindex="-1" href="{{ url('admin/vendor/addvendor') }}" class="dropdown-item">Add Vendor</a>
						  </li>

						</ul>
						</li>
					 @endif
					  <!-- End Level two -->
					  
					  @if(Gate::allows('admin.material'))	
					  <li class="dropdown-submenu dropdown-hover"  style="z-index:1100;">
						<a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Material Master</a>
						<ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow" style="z-index:1100;">
						
						  <li style="z-index:1100;">
							<a tabindex="-1" href="{{ route('admin.materialdatalist') }}" class="dropdown-item">View</a>
						  </li>
						
						  <li class="{{ request()->is('admin/material*') ? 'active' : '' }}"  style="z-index:1100;">
							<a tabindex="-1" href="{{ route('admin.material') }}" class="dropdown-item">Upload Material Master</a>
						  </li>

						</ul>
						</li>
					 @endif
					  
					  @if(Gate::allows('admin.ratedata'))
						<li class="{{ request()->is('admin/ratedata*') ? 'active' : '' }}" style="z-index:1100;"><a href="{{ route('admin.ratedata') }}" class="dropdown-item">Rate Master</a></li>
					 @endif
					 @if(Gate::allows('admin.truck_master.index'))
						<li class="{{ request()->is('admin/ratedata*') ? 'active' : '' }}" style="z-index:1100;"><a href="{{ route('admin.truck_master.index') }}" class="dropdown-item">Truck Master</a></li>
					@endif
					 @if(Gate::allows('admin.siteplantdata'))
						<li style="z-index:1100;"><a href="{{ route('admin.siteplantdata') }}" class="dropdown-item">Site Master</a></li>
					@endif
					@if(Gate::allows('admin.roles.index'))
						<li class=" {{ request()->is('admin/roles*') ? 'active' : '' }}" style="z-index:1100;"><a href="{{ route('admin.roles.index') }}" class="dropdown-item">Roles</a></li>
					@endif
					@if(Gate::allows('admin.users.index'))
						<li class="{{ request()->is('admin/users*') ? 'active' : '' }}" style="z-index:1100;"><a href="{{ route('admin.users.index') }}" class="dropdown-item">Users</a></li>
					@endif
					@if(Gate::allows('admin.mapping'))
						<li class="{{ request()->is('admin/mapping*') ? 'active' : '' }}"  style="z-index:1100;"><a href="{{ route('admin.mapping') }}" class="dropdown-item">Upload Vendor Mapping</a></li>
					@endif
					@if(Gate::allows('admin.mapping'))
						<li class="{{ request()->is('admin/mapping*') ? 'active' : '' }}"  style="z-index:1100;"><a href="{{ route('admin.mappingdatalist') }}" class="dropdown-item">View Vendor Mapping</a></li>
					@endif
					@if(Gate::allows('admin.mapping'))
						<li class="{{ request()->is('admin/consignee-return-duration*') ? 'active' : '' }}"  style="z-index:1100;"><a href="{{ url('admin/consignee-return-duration/data-list') }}" class="dropdown-item">Consignee Return Duration</a></li>
					@endif
					@if(Gate::allows('admin.mappingdatalist'))
						<li class="{{ request()->is('admin/consignee-return-duration*') ? 'active' : '' }}"  style="z-index:1100;"><a href="{{ route('admin.returnmanualupload') }}" class="dropdown-item">Upload Consignee Return Duration</a></li>
					@endif
						<li class="dropdown-divider"></li>
				</ul>
				</li>
				@endif
				
					
				@if(Gate::allows('admin.billdatalist') || Gate::allows('admin.billdata') || Gate::allows('admin.freightdata') || Gate::allows('admin.validatefreightdata'))
				
				
				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Freight Shipment</a>
					<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">	
					 @if(Gate::allows('admin.billdata'))
					<li class="{{ request()->is('admin/billdata*') ? 'active' : '' }}"><a href="{{ route('admin.billdata') }}" class="dropdown-item">Add New Freight Shipments</a></li>	
					@endif
					@if(Gate::allows('admin.billdatalist'))
					 <li class="{{ request()->is('admin/billdata/freight-shipment-history') ? 'active' : '' }}"><a href="{{ route('admin.billdatalist') }}" class="dropdown-item">Freight Shipment History</a></li>
					@endif	
					{{-- @if(Auth::user() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 5)) --}}
					
					@if(Gate::allows('admin.freightdata'))
					  <li class="{{ request()->is('admin/freightdata*') ? 'active' : '' }}"><a href="{{ route('admin.freightdata') }}" class="dropdown-item">Freight Worksheet</a></li>
					@endif
					{{--  @if(Auth::user() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 4)) --}}
					
					@if(Gate::allows('admin.validatefreightdata'))
					   <li class="{{ request()->is('admin/freightdata*') ? 'active' : '' }}"><a href="{{ route('admin.validatefreightdata') }}" class="dropdown-item">Validate Freight Bills</a></li>
					 @endif

					  
					</ul>
				</li>
				 @endif
				 {{-- @if(Auth::user() && (Auth::user()->role_id != 5 || Auth::user()->role_id != 4 )) --}}
				 
				 @if(Gate::allows('admin.appointmentdatalist') || Gate::allows('admin.appointment') || Gate::allows('admin.appointmentdatalist') || Gate::allows('admin.appointment_send_ho_consignee') || Gate::allows('admin.appointments.assign') || Gate::allows('admin.appointments.accept') || Gate::allows('admin.appointmentdata') || Gate::allows('admin.appointments.deliverystatus') || Gate::allows('admin.appointments.podfile'))
				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Appointment</a>
					<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">	
					@if(Gate::allows('admin.appointment'))
					 <li class=""><a href="{{ route('admin.appointment') }}" class="dropdown-item">Add/Upload Shipments</a></li>
					@endif
					 @if(Gate::allows('admin.appointmentdatalist'))
					 <li class=""><a href="{{ route('admin.appointmentdatalist') }}" class="dropdown-item">Shipments History</a></li>
					@endif
					@if(Gate::allows('admin.appointment_send_ho_consignee'))
					<li class=""><a href="{{ route('admin.appointment_send_ho_consignee') }}" class="dropdown-item">Branch/Site – Appointment Request Board</a></li>
					@endif
					@if(Gate::allows('admin.appointments.assign'))
					<li class=""><a href="{{ route('admin.appointments.assign') }}" class="dropdown-item">Central – Appointment Request Board</a></li>@endif					
					@if(Gate::allows('admin.appointments.accept'))
					<li class=""><a href="{{ route('admin.appointments.accept') }}" class="dropdown-item">Appointment Acceptance</a></li> 
					@endif
					@if(Gate::allows('admin.appointmentdata'))
					<li class=""><a href="{{ route('admin.appointmentdata') }}" class="dropdown-item">Update Truck & Driver Details</a></li>
					@endif
					
					{{-- @if(Auth::user() && (Auth::user()->role_id == 12 || Auth::user()->role_id == 14 || Auth::user()->role_id==1)) --}}
					
					@if(Gate::allows('admin.appointments.deliverystatus'))
					<li class=""><a href="{{ route('admin.appointments.deliverystatus') }}" class="dropdown-item">Delivery Stage/Status</a></li>
					@endif
					
					@if(Gate::allows('admin.appointments.podfile'))
					  <li class=""><a href="{{ route('admin.appointments.podfile') }}" class="dropdown-item">Upload POD</a></li>
					 @endif 
					</ul>
				</li>
				{{-- @if(Auth::user() && (Auth::user()->role_id != 12)) --}}
				
				@if(Gate::allows('admin.spotbylist') || Gate::allows('admin.spotby') || Gate::allows('admin.selectvendor') || Gate::allows('admin.vendor.quotes.index')|| Gate::allows('admin.buyerB1R2Quote') || Gate::allows('admin.vendor.quotes.round2') || Gate::allows('admin.buyerB1R3Quote') || Gate::allows('admin.buyerQuoteRound3Approver') )
				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">RFQ</a>
					<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
						@if(Gate::allows('admin.spotby'))					
							 <li class=""><a href="{{ route('admin.spotby') }}" class="dropdown-item">Add/Upload RFQ</a></li>					 
						@endif
						@if(Gate::allows('admin.spotbylist'))	
							<li class=""><a href="{{ route('admin.spotbylist') }}" class="dropdown-item">RFQ History</a></li>
						@endif	
						@if(Gate::allows('admin.selectvendor'))
							<li class=""><a href="{{ route('admin.selectvendor') }}" class="dropdown-item">Buyer Round 1</a></li>
						@endif
						@if(Gate::allows('admin.vendor.quotes.index'))
							<li class=""><a href="{{ route('admin.vendor.quotes.index') }}" class="dropdown-item">Supplier Round 1</a></li>
						@endif
						@if(Gate::allows('admin.buyerB1R2Quote'))
							<li class=""><a href="{{ route('admin.buyerB1R2Quote') }}" class="dropdown-item">Buyer Round 2</a></li>
						@endif	
						@if(Gate::allows('admin.vendor.quotes.round2'))
							<li class=""><a href="{{ route('admin.vendor.quotes.round2') }}" class="dropdown-item">Supplier Round 2</a></li>
						@endif	
						@if(Gate::allows('admin.buyerB1R3Quote'))
							<li class=""><a href="{{ route('admin.buyerB1R3Quote') }}" class="dropdown-item">Buyer Round 3</a></li>
						@endif	
						@if(Gate::allows('admin.buyerQuoteRound3Approver'))
							<li class=""><a href="{{ route('admin.buyerQuoteRound3Approver') }}" class="dropdown-item">Approver</a></li>
						@endif	
							
							</ul>
						</li>
						@endif
				@endif
				{{-- @if(Auth::user() && (Auth::user()->role_id != 12)) --}}
				
				@if(Gate::allows('admin.trackingdatalist') || Gate::allows('admin.trackingdata') || Gate::allows('admin.vendortrackingdataupdate') || Gate::allows('admin.update_by_vendor_consign'))
				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">In Tracking</a>
					<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
					@if(Gate::allows('admin.trackingdata'))	
					 <li class=""><a href="{{ route('admin.trackingdata') }}" class="dropdown-item">Add/Upload Tracking</a></li>					 
					@endif
					@if(Gate::allows('admin.trackingdatalist'))	
					<li class=""><a href="{{ route('admin.trackingdatalist') }}" class="dropdown-item">Tracking History</a></li>
					@endif
					@if(Gate::allows('admin.vendortrackingdataupdate'))
					<li class=""><a href="{{ route('admin.vendortrackingdataupdate') }}" class="dropdown-item">Tracking Update by Vendor</a></li>
					@endif
					@if(Gate::allows('admin.update_by_vendor_consign'))
					<li class=""><a href="{{ route('admin.update_by_vendor_consign') }}" class="dropdown-item">Tracking Update by Consignor/Consignee/Vendor</a></li>
					@endif
					</ul>
				</li>
				@endif
				@if(Auth::user() && (Auth::user()->role_id == 1)) 

				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu4" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">LOP</a>
					<ul aria-labelledby="dropdownSubMenu4" class="dropdown-menu border-0 shadow">
					
					 <li class=""><a href="{{ url('admin/loadoptimizer') }}" class="dropdown-item">Add/Upload Lop</a></li>					 
					
					
					</ul>
				</li>
				@endif
				
				@if(Auth::user() && (Auth::user()->role_id == 1)) 
				 <li class="nav-item dropdown">
					<a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Pre Appointment</a>
					<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">	
					@if(Gate::allows('admin.appointment'))
					 <li class=""><a href="{{ route('admin.preappointment') }}" class="dropdown-item">Add/Upload Shipments</a></li>
					@endif
					@if(Gate::allows('admin.appointmentdatalist'))
					 <li class=""><a href="{{ route('admin.preappointmentdatalist') }}" class="dropdown-item">Shipments History</a></li>
					@endif
					
					{{-- @if(Gate::allows('admin.appointment_send_ho_consignee')) --}}
					<li class=""><a href="{{ route('admin.pre_appointment_request_boards') }}" class="dropdown-item">Appointment Request Board</a></li>
					{{-- @endif	--}}				
					
					{{-- @if(Gate::allows('admin.appointmentdata')) --}}
					<li class=""><a href="{{ route('admin.appointmentlr.detail.data.update') }}" class="dropdown-item">Update Appointment Status</a></li>
					{{-- @endif --}}
					
					
					{{-- @if(Gate::allows('admin.appointments.deliverystatus')) --}}
					<li class=""><a href="{{ route('admin.preappointments.deliverystatus') }}" class="dropdown-item">Delivery Stage/Status</a></li>
					{{-- @endif --}}
					
				{{-- 	@if(Gate::allows('admin.appointments.podfile')) --}}
					  <li class=""><a href="{{ route('admin.appointments.podfile') }}" class="dropdown-item">Upload POD</a></li>
				{{-- 	 @endif --}}
					</ul>
				</li>
				@endif
				
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li id="lgout_li_header">          
					<a class="btn btn-warning mr-3" href="{{ route('logout') }}" onclick = "event.preventDefault(); document.getElementById('logout-form').submit();" style="color:#fff;">
                     {{ __('Logout') }}</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST"
						class="d-none">
						@csrf
					</form>
                </li>
               
                <li class="nav-item">
                    Welcome {{ auth()->user()->name }}
                   
                </li>
            </ul>
        
			</div>
		</nav>
        <!-- /.navbar -->