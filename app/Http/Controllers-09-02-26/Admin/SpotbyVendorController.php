<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Models\Admin;
use App\Models\SpotbyVendor;
use App\Models\Vendor;
use App\Models\Spotby;

use Auth;


class SpotbyVendorController extends Controller
{
   	public function __construct()
    {
        $this->middleware('auth:admin');     
    }
	public function bulkStore(Request $request)
	{
		$vendorsData = $request->input('vendors', []);

		$userId = Auth::user()->id; 
		
		foreach ($vendorsData as $spotbyId => $vendorIds) {
			$spotby = Spotby::find($spotbyId);
			if ($spotby) {
				$pivotData = [];
				foreach ($vendorIds as $vendorId) {
					$pivotData[$vendorId] = ['created_by' => $userId];
				}
				$spotby->vendors()->sync($pivotData);
			}
		}

		return response()->json([
			'success' => true,
			'message' => 'Vendors updated successfully!'
		]);
	}
}