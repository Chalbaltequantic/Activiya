<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoadBoxCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoadBoxCountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'load_summary_id' => 'required|integer',
            'source_type' => 'required|string',
            'reference_no' => 'nullable|string',
            'placement_status' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        try {
            $folder = public_path('uploads/load_box_counts');

            if (!file_exists($folder)) {
                mkdir($folder, 0775, true);
            }

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();

            $image->move($folder, $filename);

            $imageFullPath = public_path('uploads/load_box_counts/' . $filename);

            $response = Http::attach(
                'image',
                file_get_contents($imageFullPath),
                $filename
            )->post('http://127.0.0.1:5000/count-boxes');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Python box counting service failed.'
                ], 500);
            }

            $result = $response->json();

            $record = LoadBoxCount::create([
                'load_summary_id' => $request->load_summary_id,
                'source_type' => $request->source_type,
                'reference_no' => $request->reference_no,
                'placement_status' => $request->placement_status,
                'image_path' => 'uploads/load_box_counts/' . $filename,
                'box_count' => $result['count'] ?? 0,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo captured and box count saved.',
                'count' => $record->box_count,
                'image_url' => asset($record->image_path),
                'record_id' => $record->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function list($load_id, $source_type)
    {
        $records = LoadBoxCount::where('load_summary_id', $load_id)
            ->where('source_type', $source_type)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'records' => $records
        ]);
    }

    public function destroy($id)
    {
        $record = LoadBoxCount::findOrFail($id);

        if ($record->image_path && file_exists(public_path($record->image_path))) {
            unlink(public_path($record->image_path));
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }
}