<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalculationController extends Controller
{
    public function index()
    {
        try {
            $factories = Factory::all();
            Log::info('Listelenen fabrikalar:', $factories->toArray());
            
            return view('calculations.index', [
                'factories' => $factories
            ]);
        } catch (\Exception $e) {
            Log::error('Fabrika listeleme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->with('error', 'Fabrikalar listelenirken bir hata oluştu');
        }
    }

    public function calculate(Request $request)
    {
        try {
            Log::info('Gelen hesaplama verisi:', $request->all());

            // Validate the request
            $validated = $request->validate([
                'source_factory_id' => 'required|exists:factories,id',
                'destination_factory_id' => 'required|exists:factories,id|different:source_factory_id',
                'vehicle_type' => 'required|in:truck,van'
            ]);

            // Fabrikaları veritabanından çek
            $sourceFactory = Factory::findOrFail($validated['source_factory_id']);
            $destinationFactory = Factory::findOrFail($validated['destination_factory_id']);

            // İki nokta arasındaki mesafeyi hesapla
            $distance = $this->calculateDistance(
                $sourceFactory->latitude,
                $sourceFactory->longitude,
                $destinationFactory->latitude,
                $destinationFactory->longitude
            );

            // Araç tipine göre hız belirle (km/saat)
            $speed = $validated['vehicle_type'] === 'truck' ? 70 : 90;

            // Süreyi hesapla (saat)
            $duration = $distance / $speed;

            return response()->json([
                'success' => true,
                'distance' => round($distance, 2),
                'duration' => round($duration, 2),
                'source_factory' => $sourceFactory->name,
                'destination_factory' => $destinationFactory->name,
                'vehicle_type' => $validated['vehicle_type']
            ]);

        } catch (ValidationException $e) {
            Log::warning('Rota hesaplama validasyon hatası:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Rota hesaplama hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hesaplama sırasında bir hata oluştu'
            ], 500);
        }
    }

    /**
     * İki nokta arasındaki mesafeyi Haversine formülü ile hesaplar (km cinsinden)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}