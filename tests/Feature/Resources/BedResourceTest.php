<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\BedListResource;
use App\Http\Resources\BedResource;
use App\Models\Bed;
use App\Models\BedOccupancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

final class BedResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_bed_list_resource_returns_available_when_no_active_occupancy(): void
    {
        $bed = Bed::factory()->create();

        $bed->load('activeOccupancy');

        $payload = (new BedListResource($bed))->resolve();

        $this->assertSame($bed->id, $payload['id']);
        $this->assertSame($bed->code, $payload['code']);
        $this->assertSame('available', $payload['status']);
    }

    public function test_bed_resource_returns_occupied_and_patient_when_active_occupancy_loaded(): void
    {
        $occ = BedOccupancy::factory()->create(['released_at' => null]);

        $bed = $occ->bed->load('activeOccupancy.patient');

        $payload = (new BedResource($bed))->resolve();

        $this->assertSame('occupied', $payload['status']);
        $this->assertNotNull($payload['patient']);
        $this->assertSame($occ->patient->cpf, $payload['patient']['cpf']);
    }
}