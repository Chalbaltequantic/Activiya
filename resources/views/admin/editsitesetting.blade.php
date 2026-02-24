@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Site setting section</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{url('admin/sitesetting/home')}}" class="btn btn-info">View site setting</a></li>
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
               <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edit site setting section</h3>

            </div>
            <div class="card-body">
			<form action="{{url('admin/sitesetting/sitesettingupdate/'.$sitesetting->id)}}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			  <div class="form-group">
                <label for="inputphone">Phone*</label>
                <input type="text" id="inputphone" name="phone" value="{{$sitesetting->phone}}" class="form-control">
				@error('phone')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>
			  <div class="form-group">
                <label for="inputemail">Email*</label>
                <input type="text" id="inputemail" name="email" value="{{$sitesetting->email}}" class="form-control">
				
				@error('email')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
				<!--
				<div class="form-group">
                <label for="inputfb">Facebook Url</label>
                <input type="text" id="inputfb" name="facebook_url" value="{{$sitesetting->facebook_url}}" class="form-control">
				
				@error('facebook_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
			  
			  <div class="form-group">
                <label for="inputtwitter">Twitter Url</label>
                <input type="text" id="inputtwitter" name="twitter_url" value="{{$sitesetting->twitter_url}}" class="form-control">
				
				@error('twitter_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
			  <div class="form-group">
                <label for="inputlinkedin">Linkedin Url</label>
                <input type="text" id="inputlinkedin" name="linkedin_url" value="{{$sitesetting->linkedin_url}}" class="form-control">
				
				@error('linkedin_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   -->
              
              <div class="form-group">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                    <option value="1" @if($sitesetting->status==1) selected @endif>Active</option>       
                    <option value="0" @if($sitesetting->status==0) selected @endif>Inactive</option>       
                </select>
              </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Update</button>
              </div>
            </div>
			</form>
            <!-- /.card-body -->
          </div>
       	  </div>
        </div>
  
       </div>
		  
		  
		  </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection