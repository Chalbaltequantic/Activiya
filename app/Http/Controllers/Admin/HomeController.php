<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Slider;
//use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{

	
    public function __construct()
    {
        $this->middleware('auth:admin');
     
    }

    public function HomeSlider()
	{    Gate::authorize('admin.page.index');
	
		$sliders = Slider::latest()->get();
		return view('admin.slider', compact('sliders'));
	}
	
	public function AddSlider(Request $request)
	{    Gate::authorize('admin.page.index');
		$validatedData = $request->validate([
			'title' => 'required',
			'status' => 'required',
			'image' => 'required|mimes:jpg,jpeg,png',	
		],
		[
			'title.required' => 'Please enter title',
			'status.required' => 'Please Select status',
			'image.required' =>'Please select an image',
		]);
		$user_id = Auth::id();

		//echo $user_id; exit;
		$slider_image = $request->file('image');
		$name_generated = time();
		$extension = strtolower($slider_image->getClientOriginalExtension());
		$image_name = $name_generated.".".$extension;
		$upload_location = 'uploads/slider/';
		$img_path_name = $upload_location.$image_name;
		$slider_image->move($upload_location,$image_name);
		Slider::insert([
			'title'=>$request->title,
			'url'=>$request->url,
			'description'=>$request->description,
			'image'=>$img_path_name,
			'user_id'=>1,
			'created_at'=>Carbon::now(),
			'status'=>$request->status,
		]);
		 return Redirect()->back()->with('success', 'Slider detail added successfully!');
	}
	
	public function EditSlider($id)
	{   Gate::authorize('admin.page.index');
		$sliderdata = Slider::find($id);
		return view('admin.editslider', compact('sliderdata'));
	}
	
	public function UpdateSlider(Request $request, $id)
	{   Gate::authorize('admin.page.index');
		$validatedData = $request->validate([
			'title' => 'required',			
			'status' => 'required',			
		],
		[
			'title.required' => 'Please enter title',			
			'status.required' => 'Please Select status',			
		]);
		
		$slider_image = $request->file('image');
		$user_id = Auth::id();
		if($slider_image)
		{
			$name_generated = time();
			$extension = strtolower($slider_image->getClientOriginalExtension());
			$image_name = $name_generated.".".$extension;
			$upload_location = 'uploads/slider/';
			$img_path_name = $upload_location.$image_name;
			$slider_image->move($upload_location,$image_name);
			
			$old_image = $request->old_image;
			unlink($old_image);
			
			Slider::find($id)->update([
				'title'=>$request->title,
				'url'=>$request->url,
				'description'=>$request->description,
				'image'=>$img_path_name,
				'user_id'=>1,
				'created_at'=>Carbon::now(),
				'status'=>$request->status,
			]);
			 return Redirect('admin/home/slider')->with('success', 'Slider detail updated successfully!');
		}
		else
		{
			Slider::find($id)->update([
				'title'=>$request->title,
				'url'=>$request->url,
				'description'=>$request->description,
				'user_id'=>1,
				'created_at'=>Carbon::now(),
				'status'=>$request->status,
			]);
			 return Redirect()->back()->with('success', 'Slider detail updated successfully!');
		}			
	}
	
	public function DeleteSlider($id)
	{    Gate::authorize('admin.page.index');
		$sliderdata = Slider::find($id);
		$sliderimage = $sliderdata->image;
		unlink($sliderimage);
		Slider::find($id)->delete();
		return Redirect()->back()->with('success', 'Slider detail deleted successfully!');
	}
}

