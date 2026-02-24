<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset=UTF-8>
  <meta name=viewport content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   <title>Login - Activiya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .login-container {
      width: 100%;
      max-width: 420px;
      background: white;
      border-radius: 30px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .login-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 50px 30px 40px;
      text-align: center;
      position: relative;
    }
    
    .login-header::before {
      content: '';
      position: absolute;
      bottom: -1px;
      left: 0;
      right: 0;
      height: 30px;
      background: white;
      border-radius: 30px 30px 0 0;
    }
    
    .app-icon {
      width: 80px;
      height: 80px;
      background: white;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .app-icon i {
      font-size: 40px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .app-title {
      color: white;
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 5px;
    }
    
    .app-subtitle {
      color: rgba(255, 255, 255, 0.9);
      font-size: 14px;
      font-weight: 300;
    }
    
    .login-body {
      padding: 40px 30px 30px;
    }
    
    .welcome-text {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .welcome-text h2 {
      font-size: 24px;
      font-weight: 600;
      color: #333;
      margin-bottom: 5px;
    }
    
    .welcome-text p {
      font-size: 14px;
      color: #888;
    }
    
    .form-group {
      margin-bottom: 25px;
    }
    
    .input-wrapper {
      position: relative;
    }
    
    .input-wrapper i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 18px;
      transition: color 0.3s;
    }
    
    .form-control {
      height: 55px;
      padding-left: 55px;
      border: 2px solid #e8e8e8;
      border-radius: 15px;
      font-size: 15px;
      transition: all 0.3s;
    }
    
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .form-control:focus ~ i {
      color: #667eea;
    }
    
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      font-size: 14px;
    }
    
    .custom-control-label {
      color: #666;
      cursor: pointer;
    }
    
    .forgot-link {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }
    
    .forgot-link:hover {
      color: #764ba2;
    }
    
    .btn-login {
      width: 100%;
      height: 55px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 15px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    }
    
    .btn-login:active {
      transform: translateY(0);
    }
    
    .divider {
      text-align: center;
      margin: 30px 0;
      position: relative;
    }
    
    .divider::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 1px;
      background: #e8e8e8;
    }
    
    .divider span {
      background: white;
      padding: 0 15px;
      color: #999;
      font-size: 14px;
      position: relative;
    }
    
    .social-login {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .btn-social {
      height: 50px;
      border: 2px solid #e8e8e8;
      border-radius: 12px;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 500;
      color: #333;
    }
    
    .btn-social:hover {
      border-color: #667eea;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-social i {
      font-size: 20px;
    }
    
    .btn-google i {
      color: #EA4335;
    }
    
    .btn-facebook i {
      color: #1877F2;
    }
    
    .signup-link {
      text-align: center;
      font-size: 14px;
      color: #666;
    }
    
    .signup-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }
    
    .signup-link a:hover {
      color: #764ba2;
    }
    
    /* Mobile specific adjustments */
    @media (max-width: 480px) {
      body {
        padding: 0;
        background: white;
      }
      
      .login-container {
        max-width: 100%;
        border-radius: 0;
        box-shadow: none;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }
      
      .login-header {
        border-radius: 0;
        padding-top: 60px;
      }
      
      .login-body {
        flex: 1;
      }
    }
  </style>
</head>

	
<body>
  <div class="login-container">
    <div class="login-header">
       <!--<div class="app-icon">
       <i class="fas fa-rocket"></i>
      </div>-->
      <h1 class="app-title">Activiya</h1>
      <p class="app-subtitle">Your Business Solution</p>
    </div>
    
    <div class="login-body">
      <div class="welcome-text">
        <h2>Welcome Back!</h2>
        <p>Sign in to continue</p>
      </div>
      
       <form id="sign_in_adm" method="POST" action="{{ route('admin.login') }}">
       {{ csrf_field() }}
        <div class="form-group">
          <div class="input-wrapper">
		  <input type="text" name="login" id="login" class="form-control" placeholder="Email/Mobile Number" value="{{ old('login') }}" required autofocus>          
            <i class="fas fa-envelope"></i>
          </div>
		  @if ($errors->has('login'))
			<span ><strong>{{ $errors->first('login') }}</strong></span>
			@endif
        </div>
        
        <div class="form-group">
          <div class="input-wrapper">
             <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" required>
            <i class="fas fa-lock"></i>
          </div>
        </div>
        
        <!--<div class="remember-forgot">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="rememberMe">
            <label class="custom-control-label" for="rememberMe">Remember me</label>
          </div>
        </div>-->
        <button type="submit" class="btn btn-login">Sign In</button>
        
      </form>
      
    </div>
  </div>
  

</body>
</html>

