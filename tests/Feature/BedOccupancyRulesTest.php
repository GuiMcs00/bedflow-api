<?php

namespace Tests\Feature;

use App\Models\Bed;
use App\Models\BedOccupancy;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

final class BedOccupancyRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_cannot_be_in_more_than_one_bed_at_the_same_time(): void
    {
        $patient = Patient::factory()->create();
        $bedA = Bed::factory()->create(['code' => 'A101']);
        $bedB = Bed::factory()->create(['code' => 'A102']);

        // First active occupancy is allowed
        $first = BedOccupancy::query()->create([
            'bed_id' => $bedA->id,
            'patient_id' => $patient->id,
            'occupied_at' => now(),
        ]);

        // Second active occupancy for same patient must fail (unique partial index)
        $this->expectException(\Illuminate\Database\QueryException::class);

        BedOccupancy::query()->create([
            'bed_id' => $bedB->id,
            'patient_id' => $patient->id,
            'occupied_at' => now(),
        ]);
    }

    public function test_bed_cannot_have_more_than_one_patient_at_the_same_time(): void
    {
        $bed = Bed::factory()->create(['code' => 'B201']);
        $patientA = Patient::factory()->create();
        $patientB = Patient::factory()->create();

        // First active occupancy is allowed
        $first = BedOccupancy::query()->create([
            'bed_id' => $bed->id,
            'patient_id' => $patientA->id,
            'occupied_at' => now(),
        ]);

        // Second active occupancy for same bed must fail (unique partial index)
        $this->expectException(\Illuminate\Database\QueryException::class);

        BedOccupancy::query()->create([
            'bed_id' => $bed->id,
            'patient_id' => $patientB->id,
            'occupied_at' => now(),
        ]);
    }

    public function test_patient_can_be_assigned_again_after_release(): void
    {
        $patient = Patient::factory()->create();
        $bedA = Bed::factory()->create(['code' => 'C301']);
        $bedB = Bed::factory()->create(['code' => 'C302']);

        $occ = BedOccupancy::query()->create([
            'bed_id' => $bedA->id,
            'patient_id' => $patient->id,
            'occupied_at' => now(),
        ]);

        // Release the occupancy (makes released_at NOT NULL)
        $occ->released_at = now();
        $occ->save();

        // Now patient can occupy another bed again (should pass)
        $second = BedOccupancy::query()->create([
            'bed_id' => $bedB->id,
            'patient_id' => $patient->id,
            'occupied_at' => now(),
        ]);

        $this->assertNotNull($second->id);
        $this->assertNull($second->released_at);
    }

    public function test_bed_can_be_assigned_again_after_release(): void
    {
        $bed = Bed::factory()->create(['code' => 'D401']);
        $patientA = Patient::factory()->create();
        $patientB = Patient::factory()->create();

        $occ = BedOccupancy::query()->create([
            'bed_id' => $bed->id,
            'patient_id' => $patientA->id,
            'occupied_at' => now(),
        ]);

        $occ->released_at = now();
        $occ->save();

        $second = BedOccupancy::query()->create([
            'bed_id' => $bed->id,
            'patient_id' => $patientB->id,
            'occupied_at' => now(),
        ]);

        $this->assertNotNull($second->id);
        $this->assertNull($second->released_at);
    }
}