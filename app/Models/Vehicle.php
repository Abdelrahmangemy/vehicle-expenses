<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = ['name', 'plate_number'];

    public function fuelEntries(): HasMany
    {
        return $this->hasMany(FuelEntry::class);
    }

    public function insurancePayments(): HasMany
    {
        return $this->hasMany(InsurancePayment::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
