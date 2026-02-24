<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Helpers\Helper;


class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        // Gate::authorize('admin.roles.index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Gate::authorize('admin.roles.index');
        $roles = Role::all();
        return view('admin.Role.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Gate::authorize('admin.roles.index');
        $modules = Module::all();
        return view('admin.Role.form', ['modules' => $modules]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // Gate::authorize('admin.roles.index');
        $this->validate($request, [
            'name' => 'required|unique:roles',
            'permissions' => 'required|array',
            'permissions.*' => 'integer'
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ])->permissions()->sync($request->input('permissions'), []);

        // Flash success message to session
        session()->flash('success', 'Role created successfully');

        return redirect(route('admin.roles.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    // public function show(Role $role)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //Gate::authorize('admin.roles.index');
        $modules = Module::all();
        return view('admin.Role.form', ['role' => $role, 'modules' => $modules]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        //Gate::authorize('admin.roles.index');
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        $role->permissions()->sync($request->input('permissions'));

        // Flash success message to session
        session()->flash('success', 'Role updated successfully');

        return redirect(route('admin.roles.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //Gate::authorize('admin.roles.index');
        $role->delete();
        // Flash success message to session
        session()->flash('success', 'Role Successfully Deleted');


        return back();
    }
}
