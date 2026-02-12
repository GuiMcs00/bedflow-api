<?php

namespace Database\Seeders;

use App\Models\Bed;
use Illuminate\Database\Seeder;

final class BedSeeder extends Seeder
{
    /**
     * Seeds a default set of beds for local development/testing.
     */
    public function run(): void
    {
        // A101..A120
        for ($i = 101; $i <= 120; $i++) {
            Bed::query()->firstOrCreate(['code' => 'A' . $i]);
        }
    }
}