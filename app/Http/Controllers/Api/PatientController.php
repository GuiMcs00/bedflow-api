<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BedResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

final class PatientController extends Controller
{
    public function bedByCpf(string $cpf): JsonResponse
    {
        $cpfDigits = preg_replace('/\D+/', '', $cpf);

        $patient = Patient::query()
            ->where('cpf', $cpfDigits)
            ->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient not found.'], 404);
        }

        $patient->load('activeOccupancy.bed', 'activeOccupancy.patient');

        if (!$patient->activeOccupancy) {
            return response()->json([
                'message' => 'Patient is not assigned to any bed.',
                'data' => null,
            ]);
        }

        $bed = $patient->activeOccupancy->bed->load('activeOccupancy.patient');

        return response()->json([
            'data' => new BedResource($bed),
        ]);
    }
}