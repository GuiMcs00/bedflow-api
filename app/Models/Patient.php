<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;
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
