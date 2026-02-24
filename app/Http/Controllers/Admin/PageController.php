<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Page;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:admin');
	}

	public function Viewcontent(Request $request)
	{
		Gate::authorize('admin.page.index');
		$pagename = $request->pagename;
		$displayname = Ucfirst($pagename);
		$contents = Page::where('pagename', '=', $pagename)->get();

		return view('admin.pages', compact('contents', 'pagename', 'displayname'));
	}

	public function Add(Request $request)
	{
		Gate::authorize('admin.page.index');
		$pagename = $request->pagename;
		$displayname = Ucfirst($pagename);
		return view('admin.addpages', compact('pagename', 'displayname'));
	}

	public function Addpage(Request $request)
	{
		Gate::authorize('admin.page.index');
		$validatedData = $request->validate(
			[
				'title' => 'required',
				'status' => 'required',

			],
			[
				'title.required' => 'Please enter title',
				'status.required' => 'Please Select status',

			]
		);
		$userid = auth()->user()->id;

		//echo $user_id; exit;
		$page_image = $request->file('image');
		$img_path_name = '';
		if (!empty($page_image)) {
			$name_generated = time();
			$extension = strtolower($page_image->getClientOriginalExtension());
			$image_name = $name_generated . "." . $extension;
			$upload_location = 'uploads/pages/';
			$img_path_name = $upload_location . $image_name;
			$page_image->move($upload_location, $image_name);
		}
		Page::insert([
			'pagename' => $request->page_name,
			'section' => $request->section,
			'title' => $request->title,
			'subtitle' => $request->subtitle,
			'description' => $request->description,
			'image' => $img_path_name,
			'button_header' => $request->button_header,
			'button_label' => $request->button_label,
			'button_url' => $request->button_url,
			'button_header1' => $request->button_header1,
			'button_label1' => $request->button_label1,
			'button_url1' => $request->button_url1,
			'user_id' => $userid,
			'created_at' => Carbon::now(),
			'status' => $request->status,
		]);
		return Redirect('admin/page/' . $request->page_name)->with('success', 'Page section detail added successfully!');
	}

	public function EditPage(Request $request)
	{
		Gate::authorize('admin.page.index');
		$id = $request->id;
		$pagename = $request->pagename;
		$displayname = Ucfirst($pagename);

		$pagedata = Page::find($id);
		return view('admin.editpages', compact('pagedata', 'pagename', 'displayname'));
	}

	public function UpdatePage(Request $request)
	{
		Gate::authorize('admin.page.index');
		$id = $request->id;
		$pagename = $request->pagename;
		
		$meta_title = !empty($request->meta_title)?$request->meta_title:'';
		$meta_keywords = !empty($request->meta_keywords)?$request->meta_keywords:'';
		$meta_description = !empty($request->meta_description)?$request->meta_description:'';
		$displayname = Ucfirst($pagename);
		$validatedData = $request->validate(
			[
				'title' => 'required',
				'status' => 'required',
			],
			[
				'title.required' => 'Please enter title',
				'status.required' => 'Please Select status',
			]
		);

		$page_image = $request->file('image');
		$userid = auth()->user()->id;
		if ($page_image) {
			$name_generated = time();
			$extension = strtolower($page_image->getClientOriginalExtension());
			$image_name = $name_generated . "." . $extension;
			$upload_location = 'uploads/pages/';
			$img_path_name = $upload_location . $image_name;
			$page_image->move($upload_location, $image_name);

			$old_image = $request->old_image;
			unlink($old_image);

			Page::find($id)->update([
				'title' => $request->title,
				'subtitle' => $request->subtitle,
				'description' => $request->description,
				'image' => $img_path_name,
				'button_header' => $request->button_header,
				'button_label' => $request->button_label,
				'button_url' => $request->button_url,
				'button_header1' => $request->button_header1,
				'button_label1' => $request->button_label1,
				'button_url1' => $request->button_url1,
				'meta_title' => $request->meta_title,
				'meta_keywords' => $request->meta_keywords,
				'meta_description' => $request->meta_description,
				'user_id' => $userid,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('admin/page/' . $pagename)->with('success', 'Page section detail updated successfully!');
		} else {
			Page::find($id)->update([
				'title' => $request->title,
				'subtitle' => $request->subtitle,
				'description' => $request->description,
				'button_header' => $request->button_header,
				'button_label' => $request->button_label,
				'button_url' => $request->button_url,
				'button_header1' => $request->button_header1,
				'button_label1' => $request->button_label1,
				'button_url1' => $request->button_url1,
				'meta_title' => $meta_title,
				'meta_keywords' => $meta_keywords,
				'meta_description' => $meta_description,
				'user_id' => $userid,
				'updated_at' => Carbon::now(),
				'status' => $request->status,
			]);
			return Redirect('admin/page/' . $pagename)->with('success', 'Page Section updated successfully!');
		}
	}

	public function DeletePage(Request $request)
	{
		Gate::authorize('admin.page.index');
		$id = $request->id;
		$pagename = $request->pagename;
		$pagedata = Page::find($id);
		$pageimage = $pagedata->image;
		unlink($pageimage);
		Page::find($id)->delete();
		return Redirect('admin/page/' . $pagename)->with('success', 'Page Section detail deleted successfully!');
	}
}
