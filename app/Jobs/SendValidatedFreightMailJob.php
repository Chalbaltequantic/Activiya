<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Billdata;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendValidatedFreightMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entryId;

    /**
     * Create a new job instance.
     */
    public function __construct($entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $entry = Billdata::find($this->entryId);

        if (!$entry) {
            return;
        }

        $vendor_code = $entry->vendor_code;
        $vendor_name = $entry->vendor_name;
        $source_name = $entry->s5_consignor_short_name_and_location;
        $destination_name = $entry->d5_consignor_short_name_and_location;
        $truck_type = $entry->truck_type;
        $ref1 = $entry->ref1;
        $ref2 = $entry->ref2;
        $ref3 = $entry->ref3;
        $lr_no = $entry->lr_no;
        $amount = $entry->a_amount;
        $freight_info_updated_at = $entry->freight_info_updated_at;
        $freight_invoice_no = $entry->freight_invoice_no;

        /* ATTACHMENT FILES   */

        $files = [];

        if (!empty($entry->freight_invoice_file)) {

            $file = public_path($entry->freight_invoice_file);

            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        if (!empty($entry->pod_file)) {

            $file = public_path($entry->pod_file);

            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        if (!empty($entry->approval_file)) {

            $file = public_path($entry->approval_file);

            if (file_exists($file)) {
                $files[] = $file;
            }
        }

      
        $subject = "Vendor name : $vendor_name & freight invoice no : $freight_invoice_no";



        $body = '
        <div class="table-responsive-fixed border rounded shadow-sm bg-white consign-data-table">

            <table class="table table-bordered border-dark table-hover">

                <thead>

                    <tr>
                        <th style="background: #fce4d6; color: #0070c0;">Vendor Code</th>
                        <th style="background: #fce4d6; color: #0070c0;">Vendor Name</th>
                        <th style="background: #fce4d6; color: #0070c0;">Source Name</th>
                        <th style="background: #fce4d6; color: #0070c0;">Destination Name</th>
                        <th style="background: #fce4d6; color: #0070c0;">Truck Type</th>
                        <th style="background: #fce4d6; color: #0070c0;">Ref1(IndentId)</th>
                        <th style="background: #fce4d6; color: #0070c0;">Freight PO</th>
                        <th style="background: #fce4d6; color: #0070c0;">Freight GRN</th>
                        <th style="background: #fce4d6; color: #0070c0;">LR/CN No.</th>
                        <th style="background: #fce4d6; color: #0070c0;">Invoice No.</th>
                        <th style="background: #fce4d6; color: #0070c0;">Amount</th>
                        <th style="background: #fce4d6; color: #0070c0;">Invoice Receive Date</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>'.$vendor_code.'</td>
                        <td>'.$vendor_name.'</td>
                        <td>'.$source_name.'</td>
                        <td>'.$destination_name.'</td>
                        <td>'.$truck_type.'</td>
                        <td>'.$ref1.'</td>
                        <td>'.$ref2.'</td>
                        <td>'.$ref3.'</td>
                        <td>'.$lr_no.'</td>
                        <td>'.$freight_invoice_no.'</td>
                        <td>'.$amount.'</td>
                        <td>'.$freight_info_updated_at.'</td>
                    </tr>

                </tbody>

            </table>

        </div>';

        /* SEND TO ADMINS  */

        $admins = Admin::whereIn('role_id', [4, 6])
            ->where('status', '1')
            ->whereNotNull('email')
            ->get();

        foreach ($admins as $admin) {

            $data = [
                'name' => $admin->name,
                'body' => $body,
            ];

            Mail::send('mail.freight_info_mail', $data, function ($message) use ($admin, $subject, $files) {

                $message->to($admin->email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), 'Activiya.com');

                foreach ($files as $file) {

                    if (file_exists($file)) {
                        $message->attach($file);
                    }
                }
            });
        }


        $data = [
            'name' => 'Roshan Jha',
            'body' => $body,
        ];

        Mail::send('mail.freight_info_mail', $data, function ($message) use ($subject, $files) {

            $message->to('jhachalbal@gmail.com')
                ->subject($subject)
                ->from(config('mail.from.address'), 'Activiya.com');

            foreach ($files as $file) {

                if (file_exists($file)) {
                    $message->attach($file);
                }
            }
        });*/
    }
}