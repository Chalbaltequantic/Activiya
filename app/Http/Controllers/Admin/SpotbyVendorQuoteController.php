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
use App\Models\SpotbyVendorQuote;
use App\Models\Spotby;

use Auth;

class SpotbyVendorQuoteController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:admin');     
    }
    
    // Vendor - store first or second round quote
    public function store(Request $request, $spotbyId)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'transit_time' => 'required|string|max:50',
        ]);

        $vendorId = Auth::user()->id; 

        $round = SpotbyVendorQuote::where('spotby_id', $spotbyId)
            ->where('vendor_id', $vendorId)
            ->count() >= 1 ? 2 : 1;

        SpotbyVendorQuote::create([
            'spotby_id' => $spotbyId,
            'vendor_id' => $vendorId,
            'round' => $round,
            'price' => $request->price,
            'transit_time' => $request->transit_time,
        ]);

        return redirect()->back()->with('success', 'Quote submitted successfully!');
    }
	
	// Store multiple vendor quotes at once
    public function storeAll(Request $request)
    {
        $vendorId = Auth::user()->id;  // vendor logged in

        $data = $request->input('quotes', []);

        if (empty($data)) {
            return response()->json(['status' => 'error', 'message' => 'No quotes received']);
        }

        foreach ($data as $item) {
            if (!isset($item['spotby_id'], $item['price'], $item['transit_time'])) {
                continue;
            }

            // check if vendor already quoted -> set round accordingly
            $round = SpotbyVendorQuote::where('spotby_id', $item['spotby_id'])
                ->where('vendor_id', $vendorId)
                ->count() >= 1 ? 2 : 1;

            SpotbyVendorQuote::updateOrCreate(
                [
                    'spotby_id' => $item['spotby_id'],
                    'vendor_id' => $vendorId,
                    'round'     => $round,
                ],
                [
                    'price'        => $item['price'],
                    'transit_time' => $item['transit_time'],
                ]
            );
        }

        return response()->json(['status' => 'success', 'message' => 'All quotes saved successfully!']);
    }

    // Client - view all vendor quotes for a spotby
    public function clientView($spotbyId)
    {
        $spotby = Spotby::with(['quotes.vendor'])->findOrFail($spotbyId);
        return view('client.quotes.index', compact('spotby'));
    }

    // Client - update revised price for a vendor
    public function clientRevise(Request $request, $quoteId)
    {
        $request->validate(['client_revised_price' => 'required|numeric|min:0']);
        $quote = SpotbyVendorQuote::findOrFail($quoteId);
        $quote->update(['client_revised_price' => $request->client_revised_price]);

        return redirect()->back()->with('success', 'Revised price updated!');
    }

    // Client - freeze L1
    public function freezeVendor($spotbyId, $vendorId)
    {
        // just mark in Spotby table OR create a "selected_vendor_id" column
		$userId = Auth::user()->id; 
        $spotby = Spotby::findOrFail($spotbyId);
        $spotby->selected_vendor_id = $vendorId;
        $spotby->save();

        return redirect()->back()->with('success', 'Vendor frozen as L1!');
    }
}