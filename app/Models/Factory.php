<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'user_id'  // Bu satırı ekleyin
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}