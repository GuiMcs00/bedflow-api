<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BedOccupancy extends Model
{
    use HasFactory;
    protected $fillable = [
        'bed_id',
        'patient_id',
        'occupied_at',
        'released_at',
    ];

    protected $casts = [
        'occupied_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Scope for active occupancies
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('released_at');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Marks this occupancy as released.
     */
    public function release(): void
    {
        $this->update(['released_at' => now()]);
    }
}
