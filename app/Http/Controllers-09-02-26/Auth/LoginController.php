<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin_login', ['url' => route('admin.login'), 'title'=>'Admin']);
    }

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'login'   => 'required',
            'password' => 'required|min:6'
        ]);
		
	 	$loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
 
    // Attempt to log in using the admin guard
    if (\Auth::guard('admin')->attempt([
        $loginField => $request->input('login'),
        'password' => $request->input('password')
    ], $request->get('remember'))) {
        return redirect()->intended('/admin/dashboard');
    }
		
		
/* commented for adding mobile login with email id
        if (\Auth::guard('admin')->attempt($request->only(['email','password']), $request->get('remember'))){
            return redirect()->intended('/admin/dashboard');
        }
*/
        //return back()->withInput($request->only('email', 'remember'));
		   return back()->withErrors([
        'login' => $loginField.' or password does not match.',
    ])->onlyInput('login');
    }
	
	/*public function logout( Request $request )
	{
		if(\Auth::guard('admin')->check()) // this means that the admin was logged in.
		{
			\Auth::guard('admin')->logout();
			return redirect()->route('admin.login');
		}
	
		$this->guard('web')->logout();
		$request->session()->invalidate();
	
		return redirect('/');
	}*/
	
	public function logout(Request $request)
	{
		if (\Auth::guard('admin')->check()) {
			\Auth::guard('admin')->logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();
			return redirect('/');
		}

		$this->guard('web')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect('/');
	}
}