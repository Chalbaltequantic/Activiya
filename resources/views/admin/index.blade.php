@extends('admin.admin')
@section('bodycontent')
       
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
            <div class="card">
              <div class="card-body">
                <p class="card-text">
                 Welcome to panel!
                </p>

              </div>
            </div>

           
          </div>
         </div>
        <!-- /.row -->
		
		 <!-- Small boxes (Stat box) -->
	 @if(Auth::user() && (Auth::user()->role_id != 12))
		 
      <div class="row">
        <!-- Total Freight Upload -->
		 @if(Auth::user() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 4 ))
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
		@endif
        <!-- Total Appointments -->
		 @if(Auth::user() && (Auth::user()->role_id != 5 || Auth::user()->role_id != 4))
        <div class="col-lg-3 col-6">
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
        </div>
		@endif
        <!-- Spotby Approved -->
        <div class="col-lg-3 col-6">
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
    </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
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
        </div>
        <!-- /.row (main row) -->
      
	@endif	
	 @if(Auth::user() && (Auth::user()->role_id == 12))	
		 <div class="row">
        

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
    </div>
      
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
