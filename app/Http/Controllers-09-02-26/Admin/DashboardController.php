<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Billdata;     // Freight uploads
use App\Models\Appointment;  // Appointments
use App\Models\Spotby;       // Spotby approvals
use App\Models\Tracking;     // Tracking statuses

class DashboardController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:admin');
	}

	  public function index()
    {
        $title = 'Dashboard';
        $pagetitle = 'Dashboard';
        $year = Carbon::now()->year;

        // === Summary Data ===
        $totalFreightUpload   = Billdata::count();
        $totalAppointments    = Appointment::count();
        $totalSpotbyApproved  = Spotby::where('approve_status', 'Approved')->count();
        $totalSpotbyPending   = Spotby::where('approve_status', 'Pending')->count();
        $totalOnTrack         = Tracking::where('shipment_status', 'On Track')->count();
        $totalInTransit       = Tracking::where('shipment_status', 'In Transit')->count();

        // === Prepare Month-wise Labels ===
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // === Billdata Monthly Count ===
        $billdata = Billdata::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        $billdataCounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $billdataCounts[] = $billdata[$i] ?? 0;
        }

        // === Appointment Monthly Count ===
        $appointments = Appointment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        $appointmentCounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $appointmentCounts[] = $appointments[$i] ?? 0;
        }

        return view('admin.index', compact(
            'totalFreightUpload',
            'totalAppointments',
            'totalSpotbyApproved',
            'totalSpotbyPending',
            'totalOnTrack',
            'totalInTransit',
            'months',
            'billdataCounts',
            'appointmentCounts',
            'year'
        ));
    }

	
}
