<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    protected $fillable = ['cpf', 'name'];

    /**
     * Occupancy history for this patient.
     */
    public function occupancies(): HasMany
    {
        return $this->hasMany(BedOccupancy::class);
    }

    /**
     * Current active occupancy
     */
    public function activeOccupancy(): HasOne
    {
        return $this->hasOne(BedOccupancy::class)->active();
    }
}
