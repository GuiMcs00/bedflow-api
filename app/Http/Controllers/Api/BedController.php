<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OccupyBedRequest;
use App\Http\Resources\BedListResource;
use App\Http\Resources\BedResource;
use App\Models\Bed;
use App\Models\BedOccupancy;
use App\Models\Patient;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class BedController extends Controller
{
    public function index(): JsonResponse
    {
        $beds = Bed::query()
            ->with('activeOccupancy')
            ->orderBy('code')
            ->get();

        return response()->json([
            'data' => BedListResource::collection($beds),
        ]);
    }

    public function show(Bed $bed): BedResource
    {
        $bed->load('activeOccupancy.patient');

        return new BedResource($bed);
    }

    public function occupy(OccupyBedRequest $request, Bed $bed): JsonResponse
    {
        try {
            DB::transaction(function () use ($request, $bed) {
                $patient = Patient::query()->firstOrCreate(
                    ['cpf' => $request->input('cpf')],
                    ['name' => $request->input('name')]
                );

                if (!$patient->name && $request->filled('name')) {
                    $patient->update(['name' => $request->input('name')]);
                }

                BedOccupancy::query()->create([
                    'bed_id' => $bed->id,
                    'patient_id' => $patient->id,
                    'occupied_at' => now(),
                ]);
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                return response()->json([
                    'message' => 'Bed is already occupied or patient is already assigned to another bed.',
                ], 409);
            }
            throw $e;
        }

        $bed->load('activeOccupancy.patient');

        return response()->json([
            'message' => 'Bed occupied successfully.',
            'data' => new BedResource($bed),
        ], 201);
    }

    public function release(Bed $bed): JsonResponse
    {
        $active = $bed->activeOccupancy()->first();

        if (!$active) {
            return response()->json(['message' => 'Bed is already available.'], 409);
        }

        $active->release();

        $bed->load('activeOccupancy.patient');

        return response()->json([
            'message' => 'Bed released successfully.',
            'data' => new BedResource($bed),
        ]);
    }
}