<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factory;
use GuzzleHttp\Client;

class RouteController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'source_factory_id' => 'required|exists:factories,id',
            'destination_factory_id' => 'required|exists:factories,id',
            'vehicle_type' => 'required|in:land,sea,air,rail'
        ]);

        $sourceFactory = Factory::find($request->source_factory_id);
        $destFactory = Factory::find($request->destination_factory_id);

        // OSRM API'yi kullanarak rota hesaplama
        $client = new Client();
        $response = $client->get("http://router.project-osrm.org/route/v1/driving/{$sourceFactory->longitude},{$sourceFactory->latitude};{$destFactory->longitude},{$destFactory->latitude}?overview=full&geometries=geojson");
        
        $data = json_decode($response->getBody(), true);

        if (!isset($data['routes'][0])) {
            return response()->json([
                'success' => false,
                'message' => 'Rota bulunamadı'
            ]);
        }

        $route = $data['routes'][0];
        $distance = $route['distance'] / 1000; // metre -> kilometre
        $duration = $route['duration'] / 3600; // saniye -> saat

        // Taşıma tipine göre süre ayarlaması
        switch($request->vehicle_type) {
            case 'sea':
                $duration *= 1.5;
                break;
            case 'rail':
                $duration *= 1.2;
                break;
            case 'air':
                $distance = $this->calculateAirDistance(
                    $sourceFactory->latitude,
                    $sourceFactory->longitude,
                    $destFactory->latitude,
                    $destFactory->longitude
                );
                $duration = $distance / 800; // Ortalama 800 km/saat hız
                break;
        }

        return response()->json([
            'success' => true,
            'distance' => $distance,
            'duration' => $duration,
            'geometry' => $route['geometry']
        ]);
    }

    private function calculateAirDistance($lat1, $lon1, $lat2, $lon2)
    {
        $r = 6371; // Dünya'nın yarıçapı (km)
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $d = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lon2 - $lon1)) * $r;
        
        return $d;
    }
}