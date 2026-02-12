<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferBedRequest;
use App\Http\Resources\BedResource;
use App\Models\Bed;
use App\Models\BedOccupancy;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class BedTransferController extends Controller
{
    public function transfer(TransferBedRequest $request): JsonResponse
    {
        $from = Bed::findOrFail($request->integer('from_bed_id'));
        $to = Bed::findOrFail($request->integer('to_bed_id'));

        $fromActive = $from->activeOccupancy()->first();
        if (!$fromActive) {
            return response()->json(['message' => 'Source bed is available (no patient to transfer).'], 409);
        }

        try {
            DB::transaction(function () use ($fromActive, $to) {
                $fromActive->release();

                BedOccupancy::query()->create([
                    'bed_id' => $to->id,
                    'patient_id' => $fromActive->patient_id,
                    'occupied_at' => now(),
                ]);
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                return response()->json([
                    'message' => 'Target bed is already occupied or patient is already assigned.',
                ], 409);
            }
            throw $e;
        }

        $to->load('activeOccupancy.patient');

        return response()->json([
            'message' => 'Patient transferred successfully.',
            'data' => new BedResource($to),
        ]);
    }
}