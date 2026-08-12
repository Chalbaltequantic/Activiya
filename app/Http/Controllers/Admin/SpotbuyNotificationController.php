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


    public function index(Request $request)
    {
        $title     = 'Spot Buy Notifications';
        $pagetitle = 'Spot Buy Notification Listing';

        $admin = Auth::user();

        $vendor = DB::table('vendors')
            ->where('vendor_code', $admin->vendor_code)
            ->first();

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


        /* Fetch notifications belonging only to this supplier. */

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

            $admin = Auth::user();


            $vendor = DB::table('vendors')
                ->where('vendor_code', $admin->vendor_code)
                ->first();


            if (!$vendor) {

                return response()->json([
                    'success' => false,
                    'message' => 'Supplier account not found.'
                ], 404);
            }


            $notification = DB::table('spotbuy_notifications')
                ->where('id', $id)
                ->where('supplier_id', $vendor->id)
                ->first();


            if (!$notification) {

                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.'
                ], 404);
            }


            /*
             * Mark notification as read.
             */

            DB::table('spotbuy_notifications')
                ->where('id', $id)
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


    /* Supplier clicks notification.
	1. Verify notification belongs to supplier
    2. Mark notification read
    3. Redirect to action_url stored with notification
    
    */

    public function openNotification($id)
    {
        try {

            $admin = Auth::user();


            /* Get logged-in supplier/vendor. */

            $vendor = DB::table('vendors')
                ->where('vendor_code', $admin->vendor_code)
                ->first();


            if (!$vendor) {

                return redirect()
                    ->route('admin.spotbuy.notifications.index')
                    ->with(
                        'error',
                        'Supplier account not found.'
                    );
            }


            /*
             * Get supplier notification.
             */

            $notification = DB::table('spotbuy_notifications')
                ->where('id', $id)
                ->where('supplier_id', $vendor->id)
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
             * Mark as read.
             */

            if (!$notification->is_read) {

                DB::table('spotbuy_notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'is_read'    => 1,
                        'read_at'    => now(),
                        'updated_at' => now(),
                    ]);
            }


            /* Redirect supplier to the URL stored
              inside the notification.
             */

            if (!empty($notification->action_url)) {

                return redirect($notification->action_url);
            }


            /*If notification does not contain an action URL,
              return to notification listing.
             */

            return redirect()
                ->route('admin.spotbuy.notifications.index');

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

            $admin = Auth::user();


           $vendor = DB::table('vendors')
                ->where('vendor_code', $admin->vendor_code)
                ->first();


            if (!$vendor) {

                return response()->json([
                    'success' => true,
                    'count'   => 0
                ]);
            }


            $count = DB::table('spotbuy_notifications')
                ->where('supplier_id', $vendor->id)
                ->where('is_read', 0)
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


    /* Mark All Notifications As Read*/

    public function markAllAsRead()
    {
        try {

            $admin = Auth::user();

            $vendor = DB::table('vendors')
                ->where('vendor_code', $admin->vendor_code)
                ->first();


            if (!$vendor) {

                return redirect()
                    ->route('admin.spotbuy.notifications.index')
                    ->with(
                        'error',
                        'Supplier account not found.'
                    );
            }


            DB::table('spotbuy_notifications')
                ->where('supplier_id', $vendor->id)
                ->where('is_read', 0)
                ->update([
                    'is_read'    => 1,
                    'read_at'    => now(),
                    'updated_at' => now(),
                ]);


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
	
	/* Header Notifications Returns latest notifications for the AdminLTE header bell.*/

	public function headerNotifications()
	{
		try {

			$admin = Auth::user();

			if (!$admin) {

				return response()->json([
					'success'       => true,
					'unread_count'  => 0,
					'notifications' => [],
				]);
			}

			$vendor = DB::table('vendors')
				->where('vendor_code', $admin->vendor_code);
			
			if (!$vendor) {

				return response()->json([
					'success'       => true,
					'unread_count'  => 0,
					'notifications' => [],
				]);
			}

			$unreadCount = DB::table('spotbuy_notifications')
				->where('supplier_id', $vendor->id)
				->where('is_read', 0)
				->count();

			$notifications = DB::table('spotbuy_notifications')
				->where('supplier_id', $vendor->id)
				->orderBy('created_at', 'desc')
				->limit(5)
				->get();

			$notificationData = [];


			foreach ($notifications as $notification) {

				$created_at = !empty($notification->created_at)	? date('d M Y h:i A',			strtotime($notification->created_at)): '';
				
				$notificationData[] = [

					'id' => $notification->id,

					'title' => $notification->title ?? '',
					'message' => $notification->message ?? '',
					'round_no' => $notification->round_no ?? '',
					'is_read' => (int) ($notification->is_read ?? 0),
					'created_at' => $created_at,

					'open_url' => route(
						'admin.spotbuy.notifications.open',
						$notification->id
					),
				];
			}


			/*
			 * Final response
			 */
			return response()->json([

				'success' => true,

				'unread_count' => $unreadCount,

				'notifications' => $notificationData,

			]);


		} catch (\Exception $e) {

			/*
			 * Log actual error for debugging
			 */
			\Log::error(
				'Spot Buy header notification error',
				[
					'error' => $e->getMessage(),
					'line'  => $e->getLine(),
					'file'  => $e->getFile(),
				]
			);


			/*
			 * Even when something goes wrong,
			 * return a valid response so the header
			 * does not remain stuck at "Loading..."
			 */
			return response()->json([

				'success'       => false,

				'unread_count'  => 0,

				'notifications' => [],

				'message' =>
					'Unable to load notifications.',

			]);
		}
	}
}