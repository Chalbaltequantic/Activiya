<?php
public function assignHoToConsignee(Request $request)
{
    $createddate = now();

    DB::beginTransaction();
    try {
        $ids = $request->input('consignee_ids', []);

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No appointments selected.']);
        }

        // Step 1: Update common fields for all selected appointments
        Appointment::whereIn('id', $ids)->update([
            'assigned_to_consignee' => 1,
            'assigned_to_ho' => 0,
            'assigned_by' => Auth::id(),
            'assigned_at' => $createddate,
        ]);

        // Step 2: Update subvendor_code only where mapping exists
        DB::table('appointments as a')
            ->join('vendor_subvendor_mapping as vsm', function ($join) {
                $join->on('a.company_code', '=', 'vsm.company_code')
                     ->on('a.consignee_code', '=', 'vsm.consignee_code')
                     ->on('a.vendor_code', '=', 'vsm.vendor_code');
            })
            ->whereIn('a.id', $ids)
            ->update([
                'a.subvendor_code' => DB::raw('vsm.subvendor_code'),
            ]);

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'Appointments successfully sent to Consignee.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 'error', 'message' => 'Something went wrong: '.$e->getMessage()]);
    }
}

?>