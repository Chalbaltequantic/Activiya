@extends('admin.admin')
@section('bodycontent')
<style>

.dashboard-link {
    display: block;
    padding: 18px;
    border-radius: 12px;
    background: #f8f9fa;
    transition: 0.3s;
    text-decoration: none;
    color: #333;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.dashboard-link:hover {
    background: #ffffff;
    transform: translateY(-4px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.dashboard-icon {
    font-size: 30px;
    margin-bottom: 10px;
}

.dashboard-link p {
    margin: 0;
    font-weight: 600;
    font-size: 14px;
}

</style>
    
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
         <!-- <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <!--<div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
            </ol>
          </div>--><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
		  
			<div class="card card-primary card-outline">

				<div class="card-header p-0 border-bottom-0">
					<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">

						<li class="nav-item">
							<a class="nav-link active" id="masters-tab" data-toggle="pill"
							   href="#masters" role="tab">Masters</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="freight-tab" data-toggle="pill"
							   href="#freight" role="tab">Freight Shipment</a>
						</li>
		
						<li class="nav-item">
							<a class="nav-link" id="appointment-tab" data-toggle="pill"
							   href="#appointment" role="tab">Appointment</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="quotation-tab" data-toggle="pill"
							   href="#quotation" role="tab">Quotation</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="tracking-tab" data-toggle="pill"
							   href="#tracking" role="tab">In Tracking</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="lot-tab" data-toggle="pill"
							   href="#lot" role="tab">LOT</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="preapmnt-tab" data-toggle="pill"
							   href="#preapmnt" role="tab">Pre Appointment</a>
						</li>

					</ul>
				</div>

				<div class="card-body">
					<div class="tab-content" id="dashboardTabContent">

						{{-- Masters --}}
						<div class="tab-pane fade show active" id="masters" role="tabpanel">
							<div class="row text-center">

								@if(Gate::allows('admin.vendor'))	
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.vendor') }}" class="dashboard-link">
										<i class="fas fa-building dashboard-icon text-primary"></i>
										<p>Vendor Master</p>
									</a>
								</div>
								@endif

								@if(Gate::allows('admin.material'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.materialdatalist') }}" class="dashboard-link">
										<i class="fas fa-box dashboard-icon text-success"></i>
										<p>Material Master</p>
									</a>
								</div>
								@endif
								 @if(Gate::allows('admin.ratedata'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.ratedata') }}" class="dashboard-link">
										<i class="fas fa-rupee-sign dashboard-icon text-warning"></i>
										<p>Rate Master</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.truck_master.index'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.truck_master.index') }}" class="dashboard-link">
										<i class="fas fa-truck dashboard-icon text-info"></i>
										<p>Truck Master</p>
									</a>
								</div>
								@endif
								 @if(Gate::allows('admin.siteplantdata'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.siteplantdata') }}" class="dashboard-link">
										<i class="fas fa-map dashboard-icon text-danger"></i>
										<p>Site Master</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.roles.index'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.roles.index') }}" class="dashboard-link">
										<i class="fas fa-user-shield dashboard-icon text-secondary"></i>
										<p>Roles</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.users.index'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.users.index') }}" class="dashboard-link">
										<i class="fas fa-users dashboard-icon text-dark"></i>
										<p>Users</p>
									</a>
								</div>
								@endif	
								@if(Gate::allows('admin.mapping'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.mapping') }}" class="dashboard-link">
										<i class="fas fa-upload dashboard-icon text-primary"></i>
										<p>Upload Vendor Mapping</p>
									</a>
								</div>
							
								
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.mappingdatalist') }}" class="dashboard-link">
										<i class="fas fa-eye dashboard-icon text-success"></i>
										<p>View Vendor Mapping</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.mapping'))
								
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.employeemapping') }}" class="dashboard-link">
										<i class="fas fa-file-upload dashboard-icon text-warning"></i>
										<p>Upload Employee Mapping</p>
									</a>
								</div>

								
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.employeemappingdatalist') }}" class="dashboard-link">
										<i class="fas fa-id-card dashboard-icon text-info"></i>
										<p>View Employee Mapping</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.consignee-return-duration.data-list'))
								<div class="col-md-2 col-6 mb-4">
									<a href="url('admin/consignee-return-duration/data-list') }}" class="dashboard-link">
										<i class="fas fa-clock dashboard-icon text-danger"></i>
										<p>Consignee Return Duration</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.returnmanualupload'))
								<div class="col-md-2 col-6 mb-4">
									<a href="{{ route('admin.returnmanualupload') }}" class="dashboard-link">
										<i class="fas fa-file-import dashboard-icon text-secondary"></i>
										<p>Upload Consignee Return</p>
									</a>
								</div>
								@endif
							</div>
              
						</div>

						@if(Gate::allows('admin.billdatalist') || Gate::allows('admin.billdata') || Gate::allows('admin.freightdata') || Gate::allows('admin.validatefreightdata'))
						<div class="tab-pane fade" id="freight" role="tabpanel">
								<div class="row text-center">

									@if(Gate::allows('admin.billdata'))
									<div class="col-md-3 col-6 mb-4">
										<a href="{{ route('admin.billdata') }}" class="dashboard-link">
											<i class="fas fa-plus-circle dashboard-icon text-primary"></i>
											<p>Add New Freight</p>
										</a>
									</div>
									@endif
									@if(Gate::allows('admin.billdatalist'))
									<div class="col-md-3 col-6 mb-4">
										<a href="{{ route('admin.billdatalist') }}" class="dashboard-link">
											<i class="fas fa-history dashboard-icon text-success"></i>
											<p>Freight History</p>
										</a>
									</div>
									@endif	
									@if(Gate::allows('admin.freightdata'))
									<div class="col-md-3 col-6 mb-4">
										<a href="{{ route('admin.freightdata') }}" class="dashboard-link">
											<i class="fas fa-clipboard-list dashboard-icon text-warning"></i>
											<p>Freight Worksheet</p>
										</a>
									</div>
									@endif	
									@if(Gate::allows('admin.validatefreightdata'))
									<div class="col-md-3 col-6 mb-4">
										<a href="{{ route('admin.validatefreightdata') }}" class="dashboard-link">
											<i class="fas fa-check-circle dashboard-icon text-danger"></i>
											<p>Validate Freight Bills</p>
										</a>
									</div>
									@endif
								</div>
			
						</div>
						@endif
						@if(Gate::allows('admin.appointmentdatalist') || Gate::allows('admin.appointment') || Gate::allows('admin.appointmentdatalist') || Gate::allows('admin.appointment_send_ho_consignee') || Gate::allows('admin.appointments.assign') || Gate::allows('admin.appointments.accept') || Gate::allows('admin.appointmentdata') || Gate::allows('admin.appointments.deliverystatus') || Gate::allows('admin.appointments.podfile'))
						<div class="tab-pane fade" id="appointment" role="tabpanel">
							<div class="row text-center">
								@if(Gate::allows('admin.appointment'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointment') }}" class="dashboard-link">
										<i class="fas fa-upload dashboard-icon text-primary"></i>
										<p>Add / Upload Shipments</p>
									</a>
								</div>
								@endif
								 @if(Gate::allows('admin.appointmentdatalist'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointmentdatalist') }}" class="dashboard-link">
										<i class="fas fa-history dashboard-icon text-success"></i>
										<p>Shipments History</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.appointment_send_ho_consignee'))	
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointment_send_ho_consignee') }}" class="dashboard-link">
										<i class="fas fa-building dashboard-icon text-warning"></i>
										<p>Branch/Site Appointment Request Board</p>
									</a>
								</div>
								@endif	
								@if(Gate::allows('admin.appointments.assign'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointments.assign') }}" class="dashboard-link">
										<i class="fas fa-network-wired dashboard-icon text-info"></i>
										<p>Central Appointment Request Board</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.appointments.accept'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointments.accept') }}" class="dashboard-link">
										<i class="fas fa-check dashboard-icon text-danger"></i>
										<p>Appointment Acceptance</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.appointmentdata'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointmentdata') }}" class="dashboard-link">
										<i class="fas fa-id-card dashboard-icon text-secondary"></i>
										<p>Update Truck & Driver Detail</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.appointments.deliverystatus'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointments.deliverystatus') }}" class="dashboard-link">
										<i class="fas fa-truck-loading dashboard-icon text-dark"></i>
										<p>Delivery Stage / Status</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.appointments.podfile'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointments.podfile') }}" class="dashboard-link">
										<i class="fas fa-file-upload dashboard-icon text-primary"></i>
										<p>Upload POD</p>
									</a>
								</div>
								@endif 
							</div>
				
						</div>
						@endif
						@if(Gate::allows('admin.spotbylist') || Gate::allows('admin.spotby') || Gate::allows('admin.selectvendor') || Gate::allows('admin.vendor.quotes.index')|| Gate::allows('admin.buyerB1R2Quote') || Gate::allows('admin.vendor.quotes.round2') || Gate::allows('admin.buyerB1R3Quote') || Gate::allows('admin.buyerQuoteRound3Approver') )
						<div class="tab-pane fade" id="quotation" role="tabpanel">
							<div class="row text-center">
							@if(Gate::allows('admin.spotby'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.spotby') }}" class="dashboard-link">
									<i class="fas fa-file-upload dashboard-icon text-primary"></i>
									<p>Add / Upload RFQ</p>
								</a>
							</div>
							@endif
							@if(Gate::allows('admin.spotbylist'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.spotbylist') }}" class="dashboard-link">
									<i class="fas fa-history dashboard-icon text-success"></i>
									<p>RFQ History</p>
								</a>
							</div>
							@endif
							@if(Gate::allows('admin.selectvendor'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.selectvendor') }}" class="dashboard-link">
									<i class="fas fa-user-tie dashboard-icon text-warning"></i>
									<p>Buyer Round 1</p>
								</a>
							</div>
							@endif
							@if(Gate::allows('admin.vendor.quotes.index'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.vendor.quotes.index') }}" class="dashboard-link">
									<i class="fas fa-truck dashboard-icon text-info"></i>
									<p>Supplier Round 1</p>
								</a>
							</div>
							@endif
							@if(Gate::allows('admin.buyerB1R2Quote'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.buyerB1R2Quote') }}" class="dashboard-link">
									<i class="fas fa-user-tie dashboard-icon text-danger"></i>
									<p>Buyer Round 2</p>
								</a>
							</div>
							@endif
							@if(Gate::allows('admin.vendor.quotes.round2'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.vendor.quotes.round2') }}" class="dashboard-link">
									<i class="fas fa-truck dashboard-icon text-secondary"></i>
									<p>Supplier Round 2</p>
								</a>
							</div>
							@endif	
						@if(Gate::allows('admin.buyerB1R3Quote'))

							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.buyerB1R3Quote') }}" class="dashboard-link">
									<i class="fas fa-user-tie dashboard-icon text-primary"></i>
									<p>Buyer Round 3</p>
								</a>
							</div>
							@endif	
						@if(Gate::allows('admin.buyerQuoteRound3Approver'))
							<div class="col-md-3 col-6 mb-4">
								<a href="{{ route('admin.buyerQuoteRound3Approver') }}" class="dashboard-link">
									<i class="fas fa-user-check dashboard-icon text-success"></i>
									<p>Approver</p>
								</a>
							</div>
						@endif		
						</div>
						</div>
						@endif
						@if(Gate::allows('admin.trackingdatalist') || Gate::allows('admin.trackingdata') || Gate::allows('admin.vendortrackingdataupdate') || Gate::allows('admin.update_by_vendor_consign'))
						<div class="tab-pane fade" id="tracking" role="tabpanel">
							<div class="row text-center">
								@if(Gate::allows('admin.trackingdata'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.trackingdata') }}" class="dashboard-link">
										<i class="fas fa-upload dashboard-icon text-primary"></i>
										<p>Add / Upload Tracking</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.trackingdatalist'))

								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.trackingdatalist') }}" class="dashboard-link">
										<i class="fas fa-history dashboard-icon text-success"></i>
										<p>Tracking History</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.vendortrackingdataupdate'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.vendortrackingdataupdate') }}" class="dashboard-link">
										<i class="fas fa-truck dashboard-icon text-warning"></i>
										<p>Tracking Update by Vendor</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.update_by_vendor_consign'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.update_by_vendor_consign') }}" class="dashboard-link">
										<i class="fas fa-users dashboard-icon text-info"></i>
										<p>Tracking Update by Consignor/Consignee/Vendor</p>
									</a>
								</div>
								@endif
							</div>
				
						</div>
						@endif
						
						@if(Gate::allows('admin.lot') || Gate::allows('admin.allocation') || Gate::allows('admin.autoindentallocation') || Gate::allows('admin.approve_llocation') || Gate::allows('admin.V_Indent') || Gate::allows('admin.approve_indent') || Gate::allows('admin.V_Placement_Status') || Gate::allows('admin.Track_Placement_Status') || Gate::allows('admin.Manual_Indent'))	

						<div class="tab-pane fade" id="lot" role="tabpanel">
							<div class="row text-center">
								@if(Gate::allows('admin.allocation'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.lop') }}" class="dashboard-link">
										<i class="fas fa-random dashboard-icon text-primary"></i>
										<p>Allocation</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.autoindentallocation'))

								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.loadsummary_auto_allocation') }}" class="dashboard-link">
										<i class="fas fa-cogs dashboard-icon text-success"></i>
										<p>Auto Indent Allocation</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.approve_llocation'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{route('admin.loadSummaryApproval')}}" class="dashboard-link">
										<i class="fas fa-check-circle dashboard-icon text-warning"></i>
										<p>Approve Allocation</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.V_Indent'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.vendor.loads') }}" class="dashboard-link">
										<i class="fas fa-file-alt dashboard-icon text-info"></i>
										<p>V_Indent</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.approve_indent'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.loadsummary.allocation.send') }}" class="dashboard-link">
										<i class="fas fa-user-check dashboard-icon text-danger"></i>
										<p>Approve Indent</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.V_Placement_Status'))

								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.update.placement.status') }}" class="dashboard-link">
										<i class="fas fa-truck-moving dashboard-icon text-secondary"></i>
										<p>V_Placement Status</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.Track_Placement_Status'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.track.placement.status') }}" class="dashboard-link">
										<i class="fas fa-map-marked-alt dashboard-icon text-primary"></i>
										<p>Track Placement Status</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.Manual_Indent'))

								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.manualloadSummarydatalist') }}" class="dashboard-link">
										<i class="fas fa-edit dashboard-icon text-success"></i>
										<p>Manual Indent</p>
									</a>
								</div>
								@endif	
							</div>
				
						</div>
						@endif
						
						<div class="tab-pane fade" id="preapmnt" role="tabpanel">
							<div class="row text-center">
								@if(Gate::allows('admin.preappointment.adduploadd.shipments'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.preappointment') }}" class="dashboard-link">
										<i class="fas fa-upload dashboard-icon text-primary"></i>
										<p>Add / Upload Shipments</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.pre.appointment.shipment.history'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.preappointmentdatalist') }}" class="dashboard-link">
										<i class="fas fa-history dashboard-icon text-success"></i>
										<p>Shipments History</p>
									</a>
								</div>
								@endif
								@if(Gate::allows('admin.pre.appointment.request.board'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.pre_appointment_request_boards') }}" class="dashboard-link">
										<i class="fas fa-building dashboard-icon text-warning"></i>
										<p>Appointment Request Board</p>
									</a>
								</div>
								@endif
								{{--	@if(Gate::allows('admin.update.preappointment.status'))--}}
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.appointmentlr.detail.data.update') }}" class="dashboard-link">
										<i class="fas fa-network-wired dashboard-icon text-info"></i>
										<p>Update Appointment Status</p>
									</a>
								</div>
								{{-- @endif --}}
					
						@if(Gate::allows('aadmin.preappointment.delivery.stage.status'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.preappointments.deliverystatus') }}" class="dashboard-link">
										<i class="fas fa-check dashboard-icon text-danger"></i>
										<p>Delivery Stage/Status</p>
									</a>
								</div>
								@endif
					
								@if(Gate::allows('admin.pre.appointment.upload.pod'))
								<div class="col-md-3 col-6 mb-4">
									<a href="{{ route('admin.preappointments.podfile') }}" class="dashboard-link">
										<i class="fas fa-id-card dashboard-icon text-secondary"></i>
										<p>Upload POD</p>
									</a>
								</div>
								@endif
							</div>
						</div>
					</div>
				</div>

			</div>
		  
		 </div>
         </div>
        <!-- /.row -->
		
		 <!-- Small boxes (Stat box) -->
	 @if(Auth::user() && (Auth::user()->role_id == 1))
		 
    {{--   <div class="row">
        <!-- Total Freight Upload -->
		 @if(Auth::user() && (Auth::user()->role_id == 1))
		 <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalFreightUpload ?? 0 }}</h3>
                    <p>Total Freight Upload</p>
                </div>
                <div class="icon"><i class="fas fa-file-upload"></i></div>
                <a href="{{ route('admin.billdatalist') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
		@endif  --}}
        <!-- Total Appointments -->
		 @if(Auth::user() && (Auth::user()->role_id ==1))
		 {{-- <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalAppointments ?? 0 }}</h3>
                    <p>Total Appointments</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <a href="#" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
		 </div> --}}
		@endif
        <!-- Spotby Approved -->
		{{--   <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalSpotbyApproved ?? 0 }}</h3>
                    <p>Spotby Approved</p>
                </div>
                <div class="icon"><i class="fas fa-thumbs-up"></i></div>
                <a href="{{ route('admin.spotbylist') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Spotby Pending -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalSpotbyPending ?? 0 }}</h3>
                    <p>Spotby Pending</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="#" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- On Track -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalOnTrack ?? 0 }}</h3>
                    <p>On Track</p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
                <a href="{{ route('admin.trackingdatalist') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- In Transit -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $totalInTransit ?? 0 }}</h3>
                    <p>In Transit</p>
                </div>
                <div class="icon"><i class="fas fa-shipping-fast"></i></div>
                <a href="{{ route('admin.trackingdatalist') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
		</div>--}}
        <!-- /.row -->
        <!-- Main row -->
		{{-- <div class="row">
          <!-- Left col -->
          <section class="col-lg-6 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                 <h3 class="card-title">Freight Data - Monthly Summary ({{ $year }})</h3>

              
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                
                  <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
				    <canvas id="billdataChart"  height="300" style="height: 300px;"></canvas>
                        
                  </div>  
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->

		</section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-6 connectedSortable">
             <div class="card">
              <div class="card-header">
                <h3 class="card-title">Appointments - Monthly Summary ({{ $year }})</h3>

              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                  <!-- Morris chart - Sales -->
                 
                  <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
				   <canvas id="appointmentChart" height="300" style="height: 300px;"></canvas>
                        
                  </div>  
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->

          </section>
          <!-- right col -->
        </div> --}}
        <!-- /.row (main row) -->
      
	@endif	
	 @if(Auth::user() && (Auth::user()->role_id == 1))	
	 {{-- <div class="row">
        

        <!-- On Track -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><a href="{{ route('admin.appointments.deliverystatus') }}" style="color:#fff;">Delivery <br />Stage/Status</a></h3>
                    <p></p>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
                <a href="{{ route('admin.appointments.deliverystatus') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- In Transit -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><a href="{{ route('admin.appointments.podfile') }}" style="color:#fff;">Upload POD <br />Files</a></h3>
                    <p></p>
                </div>
                <div class="icon"><i class="fa fa-file-photo-o"></i></div>
                <a href="{{ route('admin.appointments.podfile') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div> --}}
      
	 @endif 
	 
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
	@endsection
	@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Get data from Laravel controller
    const billdataLabels = {!! json_encode($months ?? []) !!};
    const billdataCounts = {!! json_encode($billdataCounts ?? []) !!};
    const appointmentLabels = {!! json_encode($months ?? []) !!};
    const appointmentCounts = {!! json_encode($appointmentCounts ?? []) !!};

    // Billdata Chart
    const billCtx = document.getElementById('billdataChart');
    if (billCtx) {
        new Chart(billCtx, {
            type: 'bar',
            data: {
                labels: billdataLabels,
                datasets: [{
                    label: 'Total Freight Uploads',
                    data: billdataCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Appointment Chart
    const appCtx = document.getElementById('appointmentChart');
   
        new Chart(appCtx, {
            type: 'bar',
            data: {
                labels:appointmentLabels,
                datasets: [{
                    label: 'Total Appointments',
                    data: appointmentCounts,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
   
});
</script>
@endpush
