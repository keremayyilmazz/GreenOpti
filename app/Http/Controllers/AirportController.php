<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AirportController extends Controller
{
    public function getAirports()
    {
        // JSON dosyasından havalimanı verilerini oku
        $airports = json_decode(Storage::disk('public')->get('airports.json'), true);
        
        return response()->json([
            'success' => true,
            'data' => $airports
        ]);
    }
}