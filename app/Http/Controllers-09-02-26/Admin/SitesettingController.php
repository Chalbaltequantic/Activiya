<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sitesetting;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class SitesettingController extends Controller
{
	//

	public function __construct()
	{
		$this->middleware('auth:admin');
	}

	public function Index()
	{
		Gate::authorize('admin.page.index');
		$sitesettings = Sitesetting::latest()->paginate(1);
		return view('admin.sitesetting', compact('sitesettings'));
	}

	public function AddSitesetting()
	{
		Gate::authorize('admin.page.index');
		return view('admin.addsitesetting');
	}

	public function InsertSitesetting(Request $request)
	{
		Gate::authorize('admin.page.index');
		$validatedData = $request->validate(
			[
				'phone' => 'required',
				'email' => 'required',
				'status' => 'required',

			],
			[
				'phone.required' => 'Please enter phone',
				'email.required' => 'Please enter email',
				'status.required' => 'Please select status',
			]
		);

		$userid = auth()->user()->id;

		Sitesetting::insert([
			'phone' => $request->phone,
			'email' => $request->email,
			'facebook_link' => $request->facebook_url,
			'twitter_link' => $request->twitter_url,
			'linkedin_link' => $request->linkedin_url,
			'user_id' => $userid,
			'status' => $request->status,
			'created_at' => Carbon::now(),
		]);
		return Redirect('/admin/sitesetting/home')->with('success', 'Site setting detail added successfully!');
	}

	public function EditSitesetting($id)
	{
		Gate::authorize('admin.page.index');
		$sitesetting = Sitesetting::find($id);
		$userid = auth()->user()->id; //get loggedin user id		
		return view('admin.editsitesetting', compact('sitesetting'));
	}

	public function UpdateSitesetting(Request $request, $id)
	{
		Gate::authorize('admin.page.index');

		$validatedData = $request->validate(
			[
				'phone' => 'required',
				'email' => 'required',
				'status' => 'required',
			],
			[
				'phone.required' => 'Please enter phone',
				'email.required' => 'Please enter email',
				'status.required' => 'Please select status',
			]
		);


		$userid = auth()->user()->id; //get loggedin user id

		Sitesetting::find($id)->update([
			'phone' => $request->phone,
			'email' => $request->email,
			'facebook_link' => $request->facebook_url,
			'twitter_link' => $request->twitter_url,
			'linkedin_link' => $request->linkedin_url,
			'user_id' => $userid,
			'updated_at' => Carbon::now(),
			'status' => $request->status,
		]);
		return Redirect('/admin/sitesetting/home')->with('success', 'Site setting detail updated successfully!');
	}
}
