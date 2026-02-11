<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BedOccupancy>
 */
class BedOccupancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bed_id' => \App\Models\Bed::factory(),
            'patient_id' => \App\Models\Patient::factory(),
            'occupied_at' => now(),
            'released_at' => null,
        ];
    }
}
