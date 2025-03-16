<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CalculationController extends Controller
{
    private $seaWaypoints = [
        // Marmara Denizi detaylı waypoints
        'canakkale_entry' => ['lat' => 40.02, 'lon' => 26.20],    // Çanakkale Boğazı giriş
        'canakkale_mid1' => ['lat' => 40.15, 'lon' => 26.35],     // Çanakkale Boğazı orta 1
        'canakkale_mid2' => ['lat' => 40.25, 'lon' => 26.45],     // Çanakkale Boğazı orta 2
        'canakkale_exit' => ['lat' => 40.38, 'lon' => 26.70],     // Çanakkale Boğazı çıkış
        'marmara_sw' => ['lat' => 40.40, 'lon' => 27.10],         // Marmara güneybatı
        'marmara_south' => ['lat' => 40.42, 'lon' => 27.50],      // Marmara güney
        'marmara_se' => ['lat' => 40.50, 'lon' => 28.00],         // Marmara güneydoğu
        'marmara_ne' => ['lat' => 40.85, 'lon' => 28.50],         // Marmara kuzeydoğu
        'istanbul_entry' => ['lat' => 40.95, 'lon' => 28.70],     // İstanbul Boğazı giriş
        'istanbul_mid' => ['lat' => 41.10, 'lon' => 29.00],       // İstanbul Boğazı orta
        'istanbul_exit' => ['lat' => 41.28, 'lon' => 29.15],      // İstanbul Boğazı çıkış

        // Karadeniz detaylı waypoints
        'blacksea_nw' => ['lat' => 41.35, 'lon' => 29.50],        // Karadeniz kuzeybatı
        'blacksea_w1' => ['lat' => 41.45, 'lon' => 30.50],        // Karadeniz batı 1
        'blacksea_w2' => ['lat' => 41.65, 'lon' => 31.50],        // Karadeniz batı 2
        'blacksea_w3' => ['lat' => 41.85, 'lon' => 32.50],        // Karadeniz batı 3
        'sinop_w' => ['lat' => 42.00, 'lon' => 33.50],            // Sinop batı
        'sinop_point' => ['lat' => 42.10, 'lon' => 35.15],        // Sinop merkez
        'sinop_e' => ['lat' => 41.90, 'lon' => 36.50],            // Sinop doğu
        'blacksea_e1' => ['lat' => 41.70, 'lon' => 37.50],        // Karadeniz doğu 1
        'blacksea_e2' => ['lat' => 41.50, 'lon' => 38.50],        // Karadeniz doğu 2
        'blacksea_e3' => ['lat' => 41.30, 'lon' => 39.50],        // Karadeniz doğu 3

        // Akdeniz detaylı waypoints
        'med_se' => ['lat' => 36.40, 'lon' => 35.90],             // Akdeniz güneydoğu
        'med_e1' => ['lat' => 36.30, 'lon' => 35.40],             // Akdeniz doğu 1
        'med_e2' => ['lat' => 36.20, 'lon' => 34.80],             // Akdeniz doğu 2
        'med_c1' => ['lat' => 36.15, 'lon' => 34.20],             // Akdeniz merkez 1
        'med_c2' => ['lat' => 36.10, 'lon' => 33.60],             // Akdeniz merkez 2
        'med_w1' => ['lat' => 36.15, 'lon' => 33.00],             // Akdeniz batı 1
        'med_w2' => ['lat' => 36.20, 'lon' => 32.40],             // Akdeniz batı 2
        'med_w3' => ['lat' => 36.30, 'lon' => 31.80],             // Akdeniz batı 3
        'med_nw' => ['lat' => 36.40, 'lon' => 31.20],             // Akdeniz kuzeybatı

        // Ege Denizi detaylı waypoints
        'aegean_se' => ['lat' => 36.60, 'lon' => 28.20],          // Ege güneydoğu
        'aegean_s1' => ['lat' => 36.80, 'lon' => 27.60],          // Ege güney 1
        'aegean_s2' => ['lat' => 37.00, 'lon' => 27.20],          // Ege güney 2
        'aegean_c1' => ['lat' => 37.40, 'lon' => 27.00],          // Ege merkez 1
        'aegean_c2' => ['lat' => 37.80, 'lon' => 26.80],          // Ege merkez 2
        'aegean_n1' => ['lat' => 38.20, 'lon' => 26.60],          // Ege kuzey 1
        'aegean_n2' => ['lat' => 38.60, 'lon' => 26.50],          // Ege kuzey 2
        'aegean_n3' => ['lat' => 39.00, 'lon' => 26.40],          // Ege kuzey 3
        'aegean_ne' => ['lat' => 39.50, 'lon' => 26.30]           // Ege kuzeydoğu
    ];

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

            // Rota detaylarını al
            $routeDetails = $this->getRouteDetails($sourceFactory, $destinationFactory, $validated['vehicle_type']);

            if (!$routeDetails['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $routeDetails['message']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'source_factory' => $sourceFactory->name,
                'destination_factory' => $destinationFactory->name,
                'distance' => round($routeDetails['distance'], 2),
                'duration' => round($routeDetails['duration'], 2),
                'vehicle_type' => $validated['vehicle_type'],
                'geometry' => $routeDetails['geometry'] ?? null
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

    private function getRouteDetails($sourceFactory, $destinationFactory, $vehicleType)
    {
        try {
            // Deniz taşımacılığı için özel rota
            if ($vehicleType === 'sea') {
                $geometry = $this->createSeaRoute(
                    $sourceFactory->latitude,
                    $sourceFactory->longitude,
                    $destinationFactory->latitude,
                    $destinationFactory->longitude
                );

                $distance = $this->calculateTotalDistance($geometry['coordinates']);

                return [
                    'success' => true,
                    'distance' => $distance,
                    'duration' => $this->calculateDuration($distance, $vehicleType),
                    'geometry' => $geometry
                ];
            }

            // Hava yolu için kuşbakışı mesafe
            if ($vehicleType === 'air') {
                $distance = $this->calculateHaversineDistance(
                    $sourceFactory->latitude,
                    $sourceFactory->longitude,
                    $destinationFactory->latitude,
                    $destinationFactory->longitude
                );

                return [
                    'success' => true,
                    'distance' => $distance,
                    'duration' => $this->calculateDuration($distance, $vehicleType),
                    'geometry' => $this->createAirRouteGeometry($sourceFactory, $destinationFactory)
                ];
            }

            // OSRM API endpoint
            $baseUrl = "https://router.project-osrm.org/route/v1";
            $profile = $this->getVehicleProfile($vehicleType);

            $url = "{$baseUrl}/{$profile}/{$sourceFactory->longitude},{$sourceFactory->latitude};{$destinationFactory->longitude},{$destinationFactory->latitude}";
            $url .= "?overview=full&geometries=geojson&steps=true";

            $response = Http::get($url);
            $data = $response->json();

            if ($response->successful() && isset($data['routes'][0])) {
                return [
                    'success' => true,
                    'distance' => $data['routes'][0]['distance'] / 1000,
                    'duration' => $this->calculateDuration($data['routes'][0]['distance'] / 1000, $vehicleType),
                    'geometry' => $data['routes'][0]['geometry']
                ];
            }

            return [
                'success' => false,
                'message' => 'Rota bulunamadı'
            ];

        } catch (\Exception $e) {
            Log::error('Rota detayları alınamadı', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Rota hesaplanırken bir hata oluştu'
            ];
        }
    }

    private function getVehicleProfile($vehicleType)
    {
        return match($vehicleType) {
            'land' => 'driving',    // Karayolu için
            'rail' => 'driving',    // Şimdilik driving, daha sonra tren yolları için özelleştireceğiz
            'sea' => 'driving',     // Deniz rotası için özel çözüm gerekecek
            'air' => 'driving',     // Hava yolu için kuşbakışı hesaplama yapacağız
            default => 'driving'
        };
    }

    private function createSeaRoute($lat1, $lon1, $lat2, $lon2)
    {
        $coordinates = [];
        $coordinates[] = [$lon1, $lat1]; // Başlangıç noktası

        // Başlangıç ve bitiş noktalarının hangi denizde olduğunu belirle
        $startSea = $this->determineSeaRegion($lat1, $lon1);
        $endSea = $this->determineSeaRegion($lat2, $lon2);

        if ($startSea === $endSea) {
            // Aynı denizdeyse direkt bağla
            $coordinates[] = [$lon2, $lat2];
        } else {
            // Farklı denizlerdeyse uygun rotayı belirle
            $route = $this->findSeaRoute($startSea, $endSea);
            
            foreach ($route as $point) {
                $coordinates[] = [$this->seaWaypoints[$point]['lon'], $this->seaWaypoints[$point]['lat']];
            }
            
            $coordinates[] = [$lon2, $lat2]; // Bitiş noktası
        }

        return [
            'type' => 'LineString',
            'coordinates' => $coordinates
        ];
    }

    private function determineSeaRegion($lat, $lon)
    {
        // Karadeniz
        if ($lat >= 41.0 && $lon >= 28.0) {
            return 'blacksea';
        }
        // Marmara
        if ($lat >= 40.0 && $lat <= 41.0 && $lon >= 26.0 && $lon <= 30.0) {
            return 'marmara';
        }
        // Ege
        if ($lat >= 37.0 && $lat <= 40.0 && $lon <= 27.0) {
            return 'aegean';
        }
        // Akdeniz
        if ($lat <= 37.0) {
            return 'mediterranean';
        }
        
        return null;
    }

    private function findSeaRoute($startSea, $endSea)
    {
        $routes = [
            // Akdeniz'den Karadeniz'e
            'mediterranean-blacksea' => [
                'med_se', 'med_e1', 'med_e2', 'med_c1', 'med_c2', 'med_w1', 'med_w2', 'med_w3',
                'aegean_se', 'aegean_s1', 'aegean_s2', 'aegean_c1', 'aegean_c2', 
                'aegean_n1', 'aegean_n2', 'aegean_n3', 'aegean_ne',
                'canakkale_entry', 'canakkale_mid1', 'canakkale_mid2', 'canakkale_exit',
                'marmara_sw', 'marmara_south', 'marmara_se', 'marmara_ne',
                'istanbul_entry', 'istanbul_mid', 'istanbul_exit',
                'blacksea_nw', 'blacksea_w1', 'blacksea_w2', 'blacksea_w3',
                'sinop_w', 'sinop_point'
            ],
            
            // Akdeniz'den Ege'ye
            'mediterranean-aegean' => [
                'med_se', 'med_e1', 'med_e2', 'med_c1', 'med_c2',
                'aegean_se', 'aegean_s1', 'aegean_s2', 'aegean_c1'
            ],
            
            // Ege'den Karadeniz'e
            'aegean-blacksea' => [
                'aegean_n1', 'aegean_n2', 'aegean_n3', 'aegean_ne',
                'canakkale_entry', 'canakkale_mid1', 'canakkale_mid2', 'canakkale_exit',
                'marmara_sw', 'marmara_south', 'marmara_se', 'marmara_ne',
                'istanbul_entry', 'istanbul_mid', 'istanbul_exit',
                'blacksea_nw', 'blacksea_w1', 'blacksea_w2', 'blacksea_w3',
                'sinop_w', 'sinop_point'
            ],
            
            // Akdeniz'den Marmara'ya
            'mediterranean-marmara' => [
                'med_se', 'med_e1', 'med_e2', 'med_c1', 'med_c2',
                'aegean_se', 'aegean_s1', 'aegean_s2', 'aegean_c1', 'aegean_c2',
                'aegean_n1', 'aegean_n2', 'aegean_n3', 'aegean_ne',
                'canakkale_entry', 'canakkale_mid1', 'canakkale_mid2', 'canakkale_exit',
                'marmara_sw', 'marmara_south'
            ],
            
            // Ege'den Marmara'ya
            'aegean-marmara' => [
                'aegean_n1', 'aegean_n2', 'aegean_n3', 'aegean_ne',
                'canakkale_entry', 'canakkale_mid1', 'canakkale_mid2', 'canakkale_exit',
                'marmara_sw', 'marmara_south'
            ],
            
            // Marmara'dan Karadeniz'e
            'marmara-blacksea' => [
                'marmara_ne', 'istanbul_entry', 'istanbul_mid', 'istanbul_exit',
                'blacksea_nw', 'blacksea_w1', 'blacksea_w2', 'blacksea_w3',
                'sinop_w', 'sinop_point'
            ]
        ];

        $routeKey = $startSea . '-' . $endSea;
        if (isset($routes[$routeKey])) {
            return $routes[$routeKey];
        }

        // Ters rota var mı diye kontrol et
        $reverseKey = $endSea . '-' . $startSea;
        if (isset($routes[$reverseKey])) {
            return array_reverse($routes[$reverseKey]);
        }

        return [];
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        // Dünya'nın yarıçapı (km)
        $r = 6371;

        return $r * $c;
    }

    private function calculateTotalDistance($coordinates)
    {
        $total = 0;
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $total += $this->calculateHaversineDistance(
                $coordinates[$i][1],    // lat1
                $coordinates[$i][0],    // lon1
                $coordinates[$i+1][1],  // lat2
                $coordinates[$i+1][0]   // lon2
            );
        }
        return $total;
    }

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

    private function isSeaTransportPossible($lat1, $lon1, $lat2, $lon2)
    {
        return $this->isCoastalLocation($lat1, $lon1) && 
               $this->isCoastalLocation($lat2, $lon2);
    }

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

    private function createAirRouteGeometry($sourceFactory, $destinationFactory)
    {
        return [
            'type' => 'LineString',
            'coordinates' => [
                [$sourceFactory->longitude, $sourceFactory->latitude],
                [$destinationFactory->longitude, $destinationFactory->latitude]
            ]
        ];
    }
}