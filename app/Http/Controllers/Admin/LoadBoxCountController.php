<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoadBoxCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LoadBoxCountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'load_summary_id' => 'required|integer',
            'source_type' => 'required|string|max:20',
            'reference_no' => 'nullable|string|max:100',
            'placement_status' => 'required|string|max:50',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:20480',
        ]);

        try {
            $folder = public_path('uploads/load_box_counts');

            if (!file_exists($folder)) {
                mkdir($folder, 0775, true);
            }

            $image = $request->file('image');

            $filename = time() . '_' . uniqid() . '.jpg';
            $imageFullPath = $folder . '/' . $filename;

            /**
             * Compress mobile image before save and before sending to Python.
             */
            $manager = new ImageManager(new Driver());
            $compressedImage = $manager->read($image->getPathname());
            $compressedImage->scaleDown(width: 1600);
            $compressedImage->toJpeg(80)->save($imageFullPath);

            $imageInfo = getimagesize($imageFullPath);
            $imageWidth = $imageInfo[0] ?? null;
            $imageHeight = $imageInfo[1] ?? null;
            $imageSizeKb = round(filesize($imageFullPath) / 1024);

            $start = microtime(true);

            $response = Http::timeout(180)
                ->attach(
                    'image',
                    file_get_contents($imageFullPath),
                    $filename
                )
                ->post(env('BOX_COUNTER_API_URL', 'http://127.0.0.1:5000/count-boxes'));

            $processingTimeMs = round((microtime(true) - $start) * 1000);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Python box counting service failed.'
                ], 500);
            }

            $result = $response->json();

            if (empty($result['success'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'AI count failed.'
                ], 500);
            }

            $count = (int)($result['count'] ?? 0);

            $confidenceScore = null;
            if (!empty($result['detections']) && is_array($result['detections'])) {
                $totalConfidence = 0;
                $confidenceCount = 0;

                foreach ($result['detections'] as $detection) {
                    if (isset($detection['confidence'])) {
                        $totalConfidence += (float)$detection['confidence'];
                        $confidenceCount++;
                    }
                }

                if ($confidenceCount > 0) {
                    $confidenceScore = round($totalConfidence / $confidenceCount, 3);
                }
            }

            $record = LoadBoxCount::create([
                'load_summary_id' => $request->load_summary_id,
                'source_type' => $request->source_type,
                'reference_no' => $request->reference_no,
                'placement_status' => $request->placement_status,
                'image_path' => 'uploads/load_box_counts/' . $filename,
                'image_width' => $imageWidth,
                'image_height' => $imageHeight,
                'image_size_kb' => $imageSizeKb,
                'box_count' => $count,
                'ai_box_count' => $count,
                'manual_box_count' => null,
                'confidence_score' => $confidenceScore,
                'processing_time_ms' => $processingTimeMs,
                'image_status' => 'completed',
                'review_status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo captured, compressed and box count saved.',
                'count' => $record->box_count,
                'manual_box_count' => $record->manual_box_count,
                'confidence_score' => $record->confidence_score,
                'record_id' => $record->id,
                'image_url' => asset($record->image_path),
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

    public function updateCount(Request $request, $id)
    {
        $request->validate([
            'manual_box_count' => 'required|integer|min:0',
        ]);

        $record = LoadBoxCount::findOrFail($id);

        $record->update([
            'manual_box_count' => $request->manual_box_count,
            'review_status' => 'corrected',
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manual count updated successfully.',
            'manual_box_count' => $record->manual_box_count,
        ]);
    }

    public function updateRemark(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
        ]);

        $record = LoadBoxCount::findOrFail($id);

        $record->update([
            'remarks' => $request->remarks,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Remark updated successfully.',
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