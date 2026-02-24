 <aside class="main-sidebar sidebar-dark-primary elevation-4">
     <!-- Brand Logo -->
     <a href="/admin" class="brand-link">
         <!--<img src="{{ asset('backend/assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">-->
         <span class="brand-text font-weight-light">Consignment</span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user panel (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">

             <div class="info">
                 <a href="#" class="d-block">Admin</a>
             </div>
         </div>
         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">

                 <li class="nav-item menu-open">
                     <a href="#" class="nav-link active">
                         <i class="nav-icon fas fa-tachometer-alt"></i>
                         <p>
                             Dashboard
                             <i class="right fas fa-angle-left"></i>
                         </p>
                     </a>
                     
                 
                 @if (Auth::user()->hasPermission('admin.users.index'))
                 <li class="nav-item">
                     <a href="{{ route('admin.users.index') }}"
                         class="nav-link  {{ request()->is('admin/users*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-table"></i>
                         <p>
                            Admin Users
                         </p>
                     </a>
                 </li>
                 @endif
                 @if (Auth::user()->hasPermission('admin.roles.index'))
                     <li class="nav-item">
                         <a href="{{ route('admin.roles.index') }}"
                             class="nav-link  {{ request()->is('admin/roles*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Roles
                             </p>
                         </a>
                     </li>
                 @endif
                 <li class="nav-item">
                         <a href="{{ route('admin.billdata') }}"
                             class="nav-link  {{ request()->is('admin/billdata*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Bill Data Upload
                             </p>
                         </a>
                  </li>
				  
				   <li class="nav-item">
                         <a href="{{ route('admin.freightdata') }}"
                             class="nav-link  {{ request()->is('admin/freightdata*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Freight Info Data Update
                             </p>
                         </a>
                  </li>
				  
				  <li class="nav-item">
                         <a href="{{ route('admin.validatefreightdata') }}"
                             class="nav-link  {{ request()->is('admin/freightinfo*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Validate Freight Data Info
                             </p>
                         </a>
                  </li>
				  
				   <li class="nav-item">
                         <a href="{{ route('admin.siteplantdata') }}"
                             class="nav-link  {{ request()->is('admin/siteplant*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Site Plant Data Upload
                             </p>
                         </a>
                  </li>
				   <li class="nav-item">
                         <a href="{{ route('admin.ratedata') }}"
                             class="nav-link  {{ request()->is('admin/ratedata*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                 Rate Master Data
                             </p>
                         </a>
                  </li>
				  
				   <li class="nav-item {{ request()->is('admin/vendor/*') ? 'menu-open' : '' }}">
                     <a href="#" class="nav-link {{ request()->is('admin/vendor/*') ? 'active' : '' }}">
                         <i class="nav-icon fas fa-table"></i>
                         <p>Vendor Management <i class="fas fa-angle-left right"></i>
                         </p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="{{ route('admin.vendor') }}"
                                 class="nav-link {{ request()->is('admin/vendor*') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Vendor List</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="{{ url('admin/vendor/addvendor') }}"
                                 class="nav-link {{ request()->is('admin/vendor/addvendor*') ? 'active' : '' }}">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Add Vendor</p>
                             </a>
                         </li>
                     </ul>
                 </li>	
					<li class="nav-item">
                         <a href="{{ route('admin.truck_master.index') }}"
                             class="nav-link  {{ request()->is('admin/truck_master*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                Truck master
                             </p>
                         </a>
                  </li>
				  
				  <li class="nav-item">
                         <a href="{{ route('admin.appointment') }}"
                             class="nav-link  {{ request()->is('admin/appointment*') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-table"></i>
                             <p>
                                Appointment
                             </p>
                         </a>
                  </li>
			</ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->
 </aside>
