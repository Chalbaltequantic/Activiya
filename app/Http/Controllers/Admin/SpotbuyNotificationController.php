<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpotbuyNotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    private function getLoggedInVendor()
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return null;
        }

        if (empty($admin->vendor_code)) {
            return null;
        }

        return DB::table('vendors')
            ->where('vendor_code', trim($admin->vendor_code))
            ->first();
    }


    /* Notification Listing
	Supplier can see ONLY his own notifications.
    
    */

   public function index(Request $request)
{
    $title     = 'Spot Buy Notifications';
    $pagetitle = 'Spot Buy Notification Listing';


    /*
    |--------------------------------------------------------------------------
    | Logged-in Supplier
    |--------------------------------------------------------------------------
    */

    $admin = Auth::guard('admin')->user();


    /*
     * No logged-in admin
     */

    if (!$admin) {

        abort(403, 'User session not found.');
    }


    /*
    |--------------------------------------------------------------------------
    | Find Vendor From Logged-in Admin Vendor Code
    |--------------------------------------------------------------------------
    |
    | admins.vendor_code
    |       ↓
    | vendors.vendor_code
    |       ↓
    | vendors.id
    |
    */

    $vendor = DB::table('vendors')
        ->where('vendor_code', trim($admin->vendor_code))
        ->first();


    /*
     * No vendor found
     */

    if (!$vendor) {

        $notifications = collect();

        return view(
            'admin.spotbuy_notifications.index',
            compact(
                'title',
                'pagetitle',
                'notifications'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Vendor Specific Notification List
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Only notification rows having:
    |
    | supplier_id = logged-in vendor's vendors.id
    |
    | will be returned.
    |
    */

    $notifications = DB::table('spotbuy_notifications')
        ->where('supplier_id', $vendor->id)
        ->orderBy('created_at', 'desc')
        ->get();


    return view(
        'admin.spotbuy_notifications.index',
        compact(
            'title',
            'pagetitle',
            'notifications'
        )
    );
}

    public function markAsRead(Request $request, $id)
    {
        try {

            /*
             * Logged-in supplier
             */

            $vendor = $this->getLoggedInVendor();


            if (!$vendor) {

                return response()->json([
                    'success' => false,
                    'message' => 'Supplier account not found.'
                ], 404);
            }

            $notification = DB::table('spotbuy_notifications as n')

                ->where(
                    'n.id',
                    $id
                )

                ->where(
                    'n.supplier_id',
                    $vendor->id
                )

                ->whereExists(function ($query) use ($vendor) {

                    $query->select(DB::raw(1))

                        ->from('spotby_vendors as sv')

                        ->whereColumn(
                            'sv.spotby_id',
                            'n.spotby_id'
                        )

                        ->where(
                            'sv.vendor_id',
                            $vendor->id
                        );
                })

                ->first();


            if (!$notification) {

                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.'
                ], 404);
            }


            /* Mark notification as read. */

            DB::table('spotbuy_notifications')
                ->where('id', $notification->id)
                ->where('supplier_id', $vendor->id)
                ->update([
                    'is_read'    => 1,
                    'read_at'    => now(),
                    'updated_at' => now(),
                ]);


            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);


        } catch (\Exception $e) {

            \Log::error(
                'Spot Buy notification mark as read failed',
                [
                    'notification_id' => $id,
                    'error'           => $e->getMessage(),
                ]
            );


            return response()->json([
                'success' => false,
                'message' => 'Unable to update notification.'
            ], 500);
        }
    }



    public function openNotification($id)
    {
        try {

            /*
             * Logged-in supplier
             */

            $vendor = $this->getLoggedInVendor();


            if (!$vendor) {

                return redirect()
                    ->route('admin.spotbuy.notifications.index')
                    ->with(
                        'error',
                        'Supplier account not found.'
                    );
            }


            /*
             * Find only this supplier's notification.
             */

            $notification = DB::table('spotbuy_notifications as n')

                ->where(
                    'n.id',
                    $id
                )

                ->where(
                    'n.supplier_id',
                    $vendor->id
                )

                ->whereExists(function ($query) use ($vendor) {

                    $query->select(DB::raw(1))

                        ->from('spotby_vendors as sv')

                        ->whereColumn(
                            'sv.spotby_id',
                            'n.spotby_id'
                        )

                        ->where(
                            'sv.vendor_id',
                            $vendor->id
                        );
                })

                ->first();


            if (!$notification) {

                return redirect()
                    ->route('admin.spotbuy.notifications.index')
                    ->with(
                        'error',
                        'Notification not found.'
                    );
            }


            /*
             * Mark notification as read.
             */

            if (!(int) $notification->is_read) {

                DB::table('spotbuy_notifications')
                    ->where('id', $notification->id)
                    ->where('supplier_id', $vendor->id)
                    ->update([
                        'is_read'    => 1,
                        'read_at'    => now(),
                        'updated_at' => now(),
                    ]);
            }


            if (!empty($notification->action_url)) {

                return redirect(
                    $notification->action_url
                );
            }

            return redirect()
                ->route(
                    'admin.spotbuy.notifications.index'
                );


        } catch (\Exception $e) {

            \Log::error(
                'Spot Buy notification open failed',
                [
                    'notification_id' => $id,
                    'error'           => $e->getMessage(),
                ]
            );


            return redirect()
                ->route('admin.spotbuy.notifications.index')
                ->with(
                    'error',
                    'Unable to open notification.'
                );
        }
    }



    public function unreadCount()
    {
        try {

            $vendor = $this->getLoggedInVendor();


            if (!$vendor) {

                return response()->json([
                    'success' => true,
                    'count'   => 0
                ]);
            }


            $count = DB::table('spotbuy_notifications as n')

                ->where(
                    'n.supplier_id',
                    $vendor->id
                )

                ->where(
                    'n.is_read',
                    0
                )

                ->whereExists(function ($query) use ($vendor) {

                    $query->select(DB::raw(1))

                        ->from('spotby_vendors as sv')

                        ->whereColumn(
                            'sv.spotby_id',
                            'n.spotby_id'
                        )

                        ->where(
                            'sv.vendor_id',
                            $vendor->id
                        );
                })

                ->count();


            return response()->json([
                'success' => true,
                'count'   => $count
            ]);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'count'   => 0
            ]);
        }
    }


    public function markAllAsRead()
    {
        try {

            $vendor = $this->getLoggedInVendor();


            if (!$vendor) {

                return redirect()
                    ->route('admin.spotbuy.notifications.index')
                    ->with(
                        'error',
                        'Supplier account not found.'
                    );
            }


  
            $notificationIds = DB::table('spotbuy_notifications as n')

                ->where(
                    'n.supplier_id',
                    $vendor->id
                )

                ->where(
                    'n.is_read',
                    0
                )

                ->whereExists(function ($query) use ($vendor) {

                    $query->select(DB::raw(1))

                        ->from('spotby_vendors as sv')

                        ->whereColumn(
                            'sv.spotby_id',
                            'n.spotby_id'
                        )

                        ->where(
                            'sv.vendor_id',
                            $vendor->id
                        );
                })

                ->pluck('n.id')
                ->toArray();

            if (!empty($notificationIds)) {

                DB::table('spotbuy_notifications')
                    ->whereIn(
                        'id',
                        $notificationIds
                    )
                    ->update([
                        'is_read'    => 1,
                        'read_at'    => now(),
                        'updated_at' => now(),
                    ]);
            }


            return redirect()
                ->route('admin.spotbuy.notifications.index')
                ->with(
                    'success',
                    'All notifications marked as read.'
                );


        } catch (\Exception $e) {

            \Log::error(
                'Spot Buy mark all notifications failed',
                [
                    'error' => $e->getMessage(),
                ]
            );


            return redirect()
                ->route('admin.spotbuy.notifications.index')
                ->with(
                    'error',
                    'Unable to update notifications.'
                );
        }
    }


    public function headerNotifications()
{
    try {

        /*
        |--------------------------------------------------------------------------
        | Logged-in Supplier
        |--------------------------------------------------------------------------
        */

        $admin = Auth::guard('admin')->user();


        if (!$admin) {

            return response()->json([
                'success'       => true,
                'unread_count'  => 0,
                'notifications' => [],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Vendor
        |--------------------------------------------------------------------------
        */

        $vendor = DB::table('vendors')
            ->where(
                'vendor_code',
                trim($admin->vendor_code)
            )
            ->first();


        /*
         * No vendor account
         */

        if (!$vendor) {

            return response()->json([
                'success'       => true,
                'unread_count'  => 0,
                'notifications' => [],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Vendor Specific Unread Count
        |--------------------------------------------------------------------------
        */

        $unreadCount = DB::table('spotbuy_notifications')
            ->where(
                'supplier_id',
                $vendor->id
            )
            ->where(
                'is_read',
                0
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Vendor Specific Latest Notifications
        |--------------------------------------------------------------------------
        */

        $notifications = DB::table('spotbuy_notifications')
            ->where(
                'supplier_id',
                $vendor->id
            )
            ->orderBy(
                'created_at',
                'desc'
            )
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Prepare Header Data
        |--------------------------------------------------------------------------
        */

        $notificationData = [];


        foreach ($notifications as $notification) {

            $created_at = '';

            if (!empty($notification->created_at)) {

                $created_at = date(
                    'd M Y h:i A',
                    strtotime($notification->created_at)
                );
            }


            $notificationData[] = [

                'id' =>
                    $notification->id,

                'title' =>
                    $notification->title ?? '',

                'message' =>
                    $notification->message ?? '',

                'round_no' =>
                    $notification->round_no ?? '',

                'is_read' =>
                    (int) ($notification->is_read ?? 0),

                'created_at' =>
                    $created_at,

                'open_url' =>
                    route(
                        'admin.spotbuy.notifications.open',
                        $notification->id
                    ),
            ];
        }


        return response()->json([

            'success' =>
                true,

            'unread_count' =>
                $unreadCount,

            'notifications' =>
                $notificationData,

        ]);


    } catch (\Exception $e) {

        \Log::error(
            'Spot Buy header notification error',
            [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]
        );


        return response()->json([
            'success'       => false,
            'unread_count'  => 0,
            'notifications' => [],
        ]);
    }
}
}