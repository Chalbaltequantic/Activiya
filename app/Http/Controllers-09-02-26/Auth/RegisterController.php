<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Sitesetting;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:admin');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'numeric', 'digits:10'],
        ]);
        $old_user = User::where('email', $data['email'])->where('password', null)->first();
        if ($old_user) {
        } else {
            return Validator::make($data, [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $old_user = User::where('email', $data['email'])->where('password', null)->first();
        if ($old_user) {
            $old_user->update([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'status' => 1,
                'phone' => $data['phone'],
            ]);
            return $old_user;
        } else {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 1,
                'phone' => $data['phone'],
            ]);
        }
    }
    public function showRegisterForm()

    {
        $sitesetting = Sitesetting::select('phone','email','status')->latest()->first();
        $section1 = Page::where('pagename', 'common')->where('section', 'section1')->first();
        return view('auth.register', compact('section1','sitesetting')); 
		// 'auth.register' should match the name of your registration view file
    }

    public function showAdminRegisterForm()
    {


        return view('auth.register', ['route' => route('admin.register-view'), 'title' => 'Admin', 'section1']);
    }

    protected function createAdmin(Request $request)
    {
        $this->validator($request->all())->validate();
        $admin = Admin::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'role_id' => $request['role'],
            'status' => $request['status'],
            'password' => Hash::make($request['password']),
        ]);
        return redirect()->intended('admin');
    }
}
