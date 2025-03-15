<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_factory_id',
        'destination_factory_id',
        'weight',
        'distance',
        'amount'
    ];

    protected $casts = [
        'weight' => 'float',
        'distance' => 'float',
        'amount' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceFactory()
    {
        return $this->belongsTo(Factory::class, 'source_factory_id');
    }

    public function destinationFactory()
    {
        return $this->belongsTo(Factory::class, 'destination_factory_id');
    }
}