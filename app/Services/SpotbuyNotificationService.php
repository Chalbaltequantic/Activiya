<?php

namespace App\Services;

use App\Models\SpotbuyNotification;
use Illuminate\Support\Facades\Auth;

class SpotbuyNotificationService
{
    /* Notify One Supplier  */

    public static function create(
        int $supplierId,
        ?int $spotbyId,
        int $roundNo,
        string $title,
        string $message,
        string $actionUrl
    ): void
    {
        $buyerId = Auth::guard('admin')->id();


        SpotbuyNotification::create([

            'buyer_id' => $buyerId,

            'supplier_id' => $supplierId,

            'spotby_id' => $spotbyId,

            'round_no' => $roundNo,

            'notification_type' => 'quotation',

            'title' => $title,

            'message' => $message,

            'action_url' => $actionUrl,

            'is_read' => 0,

            'read_at' => null,
        ]);
    }


    /* Notify Multiple Suppliers */

    public static function createForSuppliers(
        array $supplierIds,
        ?int $spotbyId,
        int $roundNo,
        string $title,
        string $message,
        string $actionUrl
    ): void
    {
        /* Remove duplicate supplier IDs. */

        $supplierIds = array_unique(
            array_filter($supplierIds)
        );


        foreach ($supplierIds as $supplierId) {

            self::create(

                (int) $supplierId,

                $spotbyId,

                $roundNo,

                $title,

                $message,

                $actionUrl
            );
        }
    }
}