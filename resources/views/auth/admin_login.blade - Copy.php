<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset=UTF-8>
  <meta name=viewport content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Admin </title>
<style>
body.loginpage {
    padding: 50% 1.25em;
}
</style>
</head>
<body style="background:url({{asset('/backend/assets/dist/img/login-register.jpg')}});background-repeat: no-repeat; background-size: auto;">
    <div class="container">
      <div class="row justify-content-center mt-5">
          <div class="col-md-4 mt-5">
            <div class="card loginpage card-outline card-primary">
			<div class="card-header text-center">
				  <h1>Admin Login</h1>
			</div>
              <div class="card-body">
                <form id="sign_in_adm" method="POST" action="{{ route('admin.login') }}">
                  {{ csrf_field() }}
              
                <div >
                  <input type="text" name="login" id="login" class="form-control" placeholder="Email or Mobile Address" value="{{ old('login') }}" required autofocus>
                </div>
                @if ($errors->has('login'))
                <span ><strong>{{ $errors->first('login') }}</strong></span>
                @endif
                <br>
                <div >
                  <input type="password" name="password" class="form-control" placeholder="password" required>
                </div>
                <br>
                <div >
                  <button type="submit" class="btn btn-primary">Admin Login</button>
                </div>
                </form>
              </div>
            </div>
          </div>
      </div>
    </div>
</body>
</html>