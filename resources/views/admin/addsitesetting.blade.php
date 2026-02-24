@extends('admin.admin')
@section('bodycontent')
       
		<!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Site Setting</h1>
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
               <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Add Site setting</h3>

            </div>
            <div class="card-body">
			<form action="{{route('store.sitesetting')}}" method="post" name="addfrm" enctype="multipart/form-data">
              @csrf
			 
			  
			  <div class="form-group">
                <label for="inputName">Phone*</label>
                <input type="text" id="inputphone" name="phone" class="form-control" placeholder="Enter Phone" value="{{old('phone')}}">
				
				@error('phone')
				<span class="text-danger">{{$message}}</span>
				@enderror
			</div>
			
			<div class="form-group">
                <label for="inputemail">Email</label>
                <input type="text" id="inputemail" name="email" class="form-control" placeholder="Enter Email" value="{{old('email')}}">
				
				@error('email')
				<span class="text-danger">{{$message}}</span>
				@enderror
			</div>
			
              
				<div class="form-group">
                <label for="inputfb">Facebook Url</label>
                <input type="text" id="inputfb" name="facebook_url" value="{{old('facebook_url')}}" class="form-control">
				
				@error('facebook_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
			  
			  <div class="form-group">
                <label for="inputtwitter">Twitter Url</label>
                <input type="text" id="inputtwitter" name="twitter_url" value="{{old('twitter_url')}}" class="form-control">
				
				@error('twitter_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
			  <div class="form-group">
                <label for="inputlinkedin">Linkedin Url</label>
                <input type="text" id="inputlinkedin" name="linkedin_url" value="{{old('linkedin_url')}}" class="form-control">
				
				@error('linkedin_url')
				<span class="text-danger">{{$message}}</span>
				@enderror
              </div>   
              
              <div class="form-group">
                <label for="inputStatus">Status</label>
                <select id="inputStatus" name="status" class="form-control custom-select">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>               
                </select>
              </div>
              <div class="form-group">
               <button type="submit" name="submit" class="btm btn-primary">Add Content</button>
              </div>
            </div>
			</form>
            <!-- /.card-body -->
          </div>
       
       
            </div>

           
          </div>
         </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection