<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bed_occupancies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bed_id')->constrained('beds')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();

            $table->timestampTz('occupied_at')->useCurrent();
            $table->timestampTz('released_at')->nullable();

            $table->timestamps();

            $table->index(['bed_id', 'released_at']);
            $table->index(['patient_id', 'released_at']);
        });

        // Rules : uniqueness only for active occupancy
        // A bed can only have 1 active patient
        DB::statement("CREATE UNIQUE INDEX bed_occupancies_one_active_per_bed
                       ON bed_occupancies (bed_id)
                       WHERE released_at IS NULL");

        // A patient can only be in 1 active bed
        DB::statement("CREATE UNIQUE INDEX bed_occupancies_one_active_per_patient
                       ON bed_occupancies (patient_id)
                       WHERE released_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS bed_occupancies_one_active_per_bed");
        DB::statement("DROP INDEX IF EXISTS bed_occupancies_one_active_per_patient");

        Schema::dropIfExists('beds_occupancies');
    }
};
