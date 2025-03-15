<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    protected $fillable = ['from_factory_id', 'to_factory_id', 'distance'];
}