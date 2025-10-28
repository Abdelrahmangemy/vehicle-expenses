<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePayment extends Model
{
    protected $fillable = ['vehicle_id', 'amount', 'contract_date'];
    protected $dates = ['contract_date'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}

