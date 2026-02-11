<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Bed extends Model
{
    protected $fillable = ['code'];

    /**
     * Occupancy history for this bed.
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
