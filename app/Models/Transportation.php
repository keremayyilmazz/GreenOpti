<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    protected $fillable = [
        'name',
        'cost_per_km'
    ];

    public function calculations()
    {
        return $this->hasMany(Calculation::class);
    }
}