<?php

namespace App\Services;

use App\Models\LoadSendHistory;
use App\Models\LoadSummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorLoadAutoRejectedMail;

namespace App\Services;

use App\Models\LoadSummary;
use App\Models\ManualLoadSummary;
use Illuminate\Support\Collection;

class IndentSummaryService
{
    public static function getMergedSummaries(): Collection
    {
        $auto = LoadSummary::query()
            ->select([
                'id',
                'reference_no',
                'origin_name_code',
                'destination_name_code',
                't_mode',
                'truck_name',
                'zw_util',
                'zv_util',
                'gross_util',
                'vendor_name',
                'vendor_rank',
                'created_at',
            ])
            ->get()
            ->map(function ($row) {
                $row->source_type = 'AUTO';
                return $row;
            });

        $manual = ManualLoadSummary::query()
            ->select([
                'id',
                'reference_no',
                'origin_name_code',
                'destination_name_code',
                't_mode',
                'truck_name',
                DB::raw('NULL as zw_util'),
                DB::raw('NULL as zv_util'),
                DB::raw('NULL as gross_util'),
                DB::raw('NULL as vendor_name'),
                DB::raw('NULL as vendor_rank'),
                'created_at',
            ])
            ->get()
            ->map(function ($row) {
                $row->source_type = 'MANUAL';
                return $row;
            });

        return $auto
            ->merge($manual)
            ->sortByDesc('created_at')
            ->values();
    }
}