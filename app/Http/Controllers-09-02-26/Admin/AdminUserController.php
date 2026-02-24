<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Auth;

class AdminUserController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:admin');
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Gate::authorize('admin.users.index');
		$roleid = Auth::user()->role_id;
		if($roleid==24)
		{
			$users = Admin::where('role_id', 12)->get();
		}
		else
		{
			$users = Admin::get();
		}
        return view('admin.user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      //  Gate::authorize('admin.users.index');
	  $roleid = Auth::user()->role_id;
		if($roleid==24)
		{
			$role = Role::where('id', 12)->get();
		}
		else
		{
			 $role = Role::all();
		}
       
		$vendors = Vendor::all();
        return view('admin.user.from', ['roles' => $role, 'vendors'=>$vendors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

 
       // Gate::authorize('admin.users.index');
        $this->validate($request, [
            'name' => 'required |max:190|string',
            'email' => 'required|email|max:190|unique:admins',
            'role' => 'required',
            'password' => 'required|confirmed|string|min:8',
            // 'avatar'=>'required|image'

        ]);

        $user = Admin::create([
            'role_id' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'vendor_code' => $request->vendor_code,
            'password' => Hash::make($request->password),
            'status' => $request->filled('status'),
        ]);
        

        return redirect()->route('admin.users.index')->with('success', 'User Successfully Added.');

        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $user)
    {
        //Gate::authorize('admin.users.index');

        return view('admin.user.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $user)
    {
      $roleid = Auth::user()->role_id;
		if($roleid==24)
		{
			$roles = Role::where('id', 12)->get();
		}
		else
		{
			 $roles = Role::all();
		}
       
        $vendors = Vendor::all();
        return view('admin.user.from', ['roles' =>  $roles, 'user' => $user, 'vendors'=>$vendors]);
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $user)
    {
       // Gate::authorize('admin.users.index');

        $this->validate($request, [
            'name' => 'required |max:190|string',
            'email' => 'required|email|max:190|unique:admins,email,' . $user->id,
            'role' => 'required',
            'password' => 'nullable|confirmed|string|min:8',
            'avatar' => 'nullable|image',

        ]);



        $user->update([
            'role_id' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'vendor_code' => $request->vendor_code,
            'password' => isset($request->password) ? Hash::make($request->password) : $user->password,
            'status' => $request->filled('status'),
        ]);

        // upload images
        // if ($request->hasFile('avatar')) {
        //     $user->addMedia($request->avatar)->toMediaCollection('avatar');
        // }


        // return redirect()->route('admin.users.index');
        return back()->with('success', 'User Updated Added.');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $user)
    {
        //Gate::authorize('admin.users.index');

        $user->delete();

        return back()->with('success', 'User Successfully Deleted');;

        //
    }
}
