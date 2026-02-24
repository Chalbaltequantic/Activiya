@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Site setting</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <!--<li class="breadcrumb-item"><a href="{{url('admin/sitesetting/addsitesetting')}}" class="btn btn-info">Add site setting</a></li>-->
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
				<div class="alert alert-success alert-dismissible fade show ">
			<strong>{{session('success')}}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			</div>
			@endif	
              <div class="card-body p-0">
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th style="width: 1%">
                          #
                      </th>
					  <th style="width: 15%">
                          Phone
                      </th> 
                      <th style="width: 15%">
                          Email
                      </th>  
						
					  <th style="width: 8%">
                          Status
                      </th>
                      <th style="width: 15%">Action
                      </th>
                  </tr>
              </thead>
              <tbody>
			  @php($i=1)
			    @if(count($sitesettings) > 0)
			  @foreach($sitesettings as $sitesetting)
			  
                  <tr>
                      <td>
					  {{$i++}}
                      </td>
					  <td> {{$sitesetting->phone}} </td>
                      <td> {{$sitesetting->email}} </td>
                      
					   <td>@if($sitesetting->status==1)Active @else Inactive @endif</td>
                      <td class="project-actions">
                          <a class="btn btn-info btn-sm" href="{{url('admin/sitesetting/editsitesetting/'.$sitesetting->id)}}">
                              <i class="fas fa-pencil-alt">
                              </i>
                              Edit
                          </a>
                          
                      </td>
                  </tr>
				  @endforeach
				  @else
					  <tr><td colspan="11">Data Not found</td></tr>
				  @endif
				  
               </tbody>
          </table>
        </div>
       
            </div>

           
          </div>
		 </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection