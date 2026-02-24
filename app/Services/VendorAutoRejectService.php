<?php

namespace App\Services;

use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorLoadAutoRejectedMail;

class VendorAutoRejectService
{
    public static function run()
    {
        $expired = LoadSendHistory::where('status', 'pending')
            ->where('sent_at', '<=', now()->subHours(2))
            ->get();

        foreach ($expired as $row) {

            DB::transaction(function () use ($row) {

                // Update history
                $row->update([
                    'status'           => 'rejected',
                    'rejection_reason' => 'Auto rejected – no response within 2 hours',
                    'responded_at'     => now(),
                ]);

                // Release load
                $load = LoadSummary::find($row->load_summary_id);
                if ($load) {
                    $load->update([
                        'vendor_code'        => null,
                        'vendor_name'        => null,
                        'vendor_code_source' => null,
                        'vendor_code_updated_at' => null,
                    ]);
                }

                // Email vendor
               /* if ($row->vendor && $row->vendor->email) {
                    Mail::to($row->vendor->email)
                        ->send(new VendorLoadAutoRejectedMail($row));
                }*/
            });
        }
    }
}