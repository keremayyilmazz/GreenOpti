<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CalculationController extends Controller
{
    /**
     * Rota hesaplama işlemi
     */
    public function calculate(Request $request)
    {
        try {
            $validated = $request->validate([
                'source_factory_id' => 'required|exists:factories,id',
                'destination_factory_id' => 'required|exists:factories,id|different:source_factory_id',
                'vehicle_type' => 'required|in:land,sea,air,rail'
            ]);

            $sourceFactory = Factory::findOrFail($validated['source_factory_id']);
            $destinationFactory = Factory::findOrFail($validated['destination_factory_id']);

            // Deniz taşımacılığı kontrolü
            if ($validated['vehicle_type'] === 'sea') {
                $isSeaPossible = $this->isSeaTransportPossible(
                    $sourceFactory->latitude,
                    $sourceFactory->longitude,
                    $destinationFactory->latitude,
                    $destinationFactory->longitude
                );

                if (!$isSeaPossible) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Seçilen konumlar arasında deniz taşımacılığı mümkün değildir. Lütfen başka bir taşıma tipi seçin.',
                        'error_type' => 'sea_transport_not_possible'
                    ], 422);
                }
            }

            // Mesafe hesaplama
            $distance = $this->calculateDistance(
                $sourceFactory->latitude,
                $sourceFactory->longitude,
                $destinationFactory->latitude,
                $destinationFactory->longitude
            );

            // Taşıma tipine göre süre hesaplama
            $duration = $this->calculateDuration($distance, $validated['vehicle_type']);

            return response()->json([
                'success' => true,
                'source_factory' => $sourceFactory->name,
                'destination_factory' => $destinationFactory->name,
                'distance' => round($distance, 2),
                'duration' => round($duration, 2),
                'vehicle_type' => $validated['vehicle_type']
            ]);

        } catch (ValidationException $e) {
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
                'message' => 'Rota hesaplanırken bir hata oluştu'
            ], 500);
        }
    }

    /**
     * İki nokta arasındaki mesafeyi hesaplar (Haversine formülü)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Mesafe ve taşıma tipine göre süreyi hesaplar
     */
    private function calculateDuration($distance, $vehicleType)
    {
        // Ortalama hızlar (km/saat)
        $speeds = [
            'land' => 70,    // Kara taşımacılığı için ortalama hız
            'sea' => 30,     // Deniz taşımacılığı için ortalama hız
            'air' => 800,    // Hava taşımacılığı için ortalama hız
            'rail' => 120    // Tren taşımacılığı için ortalama hız
        ];

        // Ek süreler (saat) - yükleme, boşaltma, gümrük vb.
        $additionalTimes = [
            'land' => 2,     // Kara taşımacılığı için ek süre
            'sea' => 24,     // Deniz taşımacılığı için ek süre (liman işlemleri)
            'air' => 4,      // Hava taşımacılığı için ek süre (havalimanı işlemleri)
            'rail' => 3      // Tren taşımacılığı için ek süre (istasyon işlemleri)
        ];

        // Mesafe / Hız = Hareket Süresi
        $travelTime = $distance / $speeds[$vehicleType];

        // Toplam süre = Hareket Süresi + Ek Süreler
        return $travelTime + $additionalTimes[$vehicleType];
    }

    /**
     * Bir konumun deniz kıyısında olup olmadığını kontrol eder
     */
    private function isCoastalLocation($latitude, $longitude)
    {
        // Türkiye'nin deniz kıyısı olan bölgelerinin yaklaşık koordinatları
        $coastalAreas = [
            // Karadeniz Kıyısı
            ['min_lat' => 41.0, 'max_lat' => 42.1, 'min_lon' => 27.5, 'max_lon' => 41.5],
            
            // Marmara Kıyısı
            ['min_lat' => 40.0, 'max_lat' => 41.0, 'min_lon' => 26.0, 'max_lon' => 30.0],
            
            // Ege Kıyısı
            ['min_lat' => 37.0, 'max_lat' => 40.0, 'min_lon' => 26.0, 'max_lon' => 28.0],
            
            // Akdeniz Kıyısı
            ['min_lat' => 36.0, 'max_lat' => 37.0, 'min_lon' => 27.5, 'max_lon' => 36.2]
        ];

        foreach ($coastalAreas as $area) {
            if ($latitude >= $area['min_lat'] && $latitude <= $area['max_lat'] &&
                $longitude >= $area['min_lon'] && $longitude <= $area['max_lon']) {
                return true;
            }
        }

        return false;
    }

    /**
     * İki nokta arasında deniz taşımacılığının mümkün olup olmadığını kontrol eder
     */
    private function isSeaTransportPossible($lat1, $lon1, $lat2, $lon2)
    {
        // Her iki nokta da kıyıda olmalı
        return $this->isCoastalLocation($lat1, $lon1) && 
               $this->isCoastalLocation($lat2, $lon2);
    }
}