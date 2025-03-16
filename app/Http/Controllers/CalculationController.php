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

    private $railWaypoints = [
        // İstanbul-Ankara Hattı (Yüksek Hızlı Tren)
        'haydarpasa' => ['lat' => 40.997, 'lon' => 29.019],    // Haydarpaşa
        'gebze' => ['lat' => 40.796, 'lon' => 29.431],         // Gebze
        'izmit' => ['lat' => 40.766, 'lon' => 29.923],         // İzmit
        'arifiye' => ['lat' => 40.702, 'lon' => 30.389],       // Arifiye
        'bilecik' => ['lat' => 40.142, 'lon' => 30.024],       // Bilecik
        'bozuyuk' => ['lat' => 39.907, 'lon' => 30.051],       // Bozüyük
        'eskisehir' => ['lat' => 39.784, 'lon' => 30.520],     // Eskişehir
        'polatli' => ['lat' => 39.588, 'lon' => 32.147],       // Polatlı
        'sincan' => ['lat' => 39.974, 'lon' => 32.623],        // Sincan
        'ankara' => ['lat' => 39.943, 'lon' => 32.861],        // Ankara

        // Ankara-Sivas Hattı (Yüksek Hızlı Tren)
        'kirikkale' => ['lat' => 39.846, 'lon' => 33.515],     // Kırıkkale
        'yerkoy' => ['lat' => 39.635, 'lon' => 34.467],        // Yerköy
        'yozgat' => ['lat' => 39.824, 'lon' => 34.815],        // Yozgat
        'akdagmadeni' => ['lat' => 39.666, 'lon' => 35.885],   // Akdağmadeni
        'sivas' => ['lat' => 39.747, 'lon' => 37.015],         // Sivas

        // Ankara-Zonguldak Hattı
        'kayas' => ['lat' => 39.969, 'lon' => 32.890],         // Kayaş
        'kalecikkale' => ['lat' => 40.067, 'lon' => 33.407],   // Kalecik
        'cerkes' => ['lat' => 40.817, 'lon' => 32.893],        // Çerkeş
        'karabuk' => ['lat' => 41.200, 'lon' => 32.627],       // Karabük
        'zonguldak' => ['lat' => 41.456, 'lon' => 31.798],     // Zonguldak

        // Karabük-Sinop Hattı
        'kastamonu' => ['lat' => 41.389, 'lon' => 33.783],     // Kastamonu
        'boyabat' => ['lat' => 41.467, 'lon' => 34.767],       // Boyabat
        'sinop' => ['lat' => 42.027, 'lon' => 35.151]          // Sinop
    ];

    private $railRoutes = [
        'istanbul-ankara' => [
            'haydarpasa', 'gebze', 'izmit', 'arifiye', 'bilecik', 
            'bozuyuk', 'eskisehir', 'polatli', 'ankara'
        ],
        'ankara-karabuk' => [
            'ankara', 'kayas', 'kirikkale', 'kalecikkale', 
            'cerkes', 'karabuk'
        ],
        'karabuk-sinop' => [
            'karabuk', 'kastamonu', 'boyabat', 'sinop'
        ],
        // Alternatif rotalar
        'gebze-ankara' => [
            'gebze', 'izmit', 'arifiye', 'bilecik', 
            'bozuyuk', 'eskisehir', 'polatli', 'ankara'
        ],
        'ankara-sinop' => [
            'ankara', 'kayas', 'kirikkale', 'kalecikkale', 
            'cerkes', 'karabuk', 'kastamonu', 'boyabat', 'sinop'
        ]
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

            // Tren taşımacılığı için özel rota
            if ($vehicleType === 'rail') {
                try {
                    $geometry = $this->createRailRoute(
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
                } catch (\Exception $e) {
                    Log::error('Tren rotası hesaplama hatası:', ['error' => $e->getMessage()]);
                    return [
                        'success' => false,
                        'message' => 'Tren rotası hesaplanamadı: ' . $e->getMessage()
                    ];
                }
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

    private function createRailRoute($lat1, $lon1, $lat2, $lon2)
    {
        try {
            Log::info('Tren rotası oluşturuluyor', [
                'start' => ['lat' => $lat1, 'lon' => $lon1],
                'end' => ['lat' => $lat2, 'lon' => $lon2]
            ]);

            $coordinates = [];
            $coordinates[] = [$lon1, $lat1]; // Başlangıç noktası

            // En yakın tren istasyonlarını bul
            $startStation = $this->findNearestStation($lat1, $lon1);
            $endStation = $this->findNearestStation($lat2, $lon2);

            Log::info('En yakın istasyonlar bulundu', [
                'startStation' => $startStation,
                'endStation' => $endStation
            ]);

            // İstasyonlar arasındaki rotayı bul
            $route = $this->findRailRoute($startStation, $endStation);

            if (empty($route)) {
                throw new \Exception("İstasyonlar arasında uygun rota bulunamadı");
            }

            // Rotadaki her istasyonu koordinatlara ekle
            foreach ($route as $station) {
                $coordinates[] = [
                    $this->railWaypoints[$station]['lon'],
                    $this->railWaypoints[$station]['lat']
                ];
            }

            $coordinates[] = [$lon2, $lat2]; // Bitiş noktası

            Log::info('Tren rotası başarıyla oluşturuldu', [
                'stationCount' => count($route),
                'coordinateCount' => count($coordinates)
            ]);

            return [
                'type' => 'LineString',
                'coordinates' => $coordinates
            ];

        } catch (\Exception $e) {
            Log::error('Tren rotası oluşturma hatası:', [
                'error' => $e->getMessage(),
                'start' => ['lat' => $lat1, 'lon' => $lon1],
                'end' => ['lat' => $lat2, 'lon' => $lon2]
            ]);
            throw $e;
        }
    }

    private function findNearestStation($lat, $lon)
    {
        $nearestStation = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($this->railWaypoints as $station => $coords) {
            $distance = $this->calculateHaversineDistance(
                $lat,
                $lon,
                $coords['lat'],
                $coords['lon']
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestStation = $station;
            }
        }

        return $nearestStation;
    }

    private function findRailRoute($startStation, $endStation)
    {
        try {
            Log::info('Rota hesaplanıyor:', ['start' => $startStation, 'end' => $endStation]);

            // Önce direkt bağlantı var mı diye kontrol et
            foreach ($this->railRoutes as $routeName => $route) {
                $startIndex = array_search($startStation, $route);
                $endIndex = array_search($endStation, $route);

                if ($startIndex !== false && $endIndex !== false) {
                    Log::info('Direkt rota bulundu', ['route' => $routeName]);
                    // Her iki istasyon da aynı rotada bulundu
                    if ($startIndex > $endIndex) {
                        return array_reverse(array_slice($route, $endIndex, $startIndex - $endIndex + 1));
                    } else {
                        return array_slice($route, $startIndex, $endIndex - $startIndex + 1);
                    }
                }
            }

            Log::info('Direkt rota bulunamadı, aktarmalı rota aranıyor');

            // Direkt bağlantı yoksa, Ankara veya Eskişehir üzerinden aktarma dene
            $transferStations = ['ankara', 'eskisehir'];
            
            foreach ($transferStations as $transfer) {
                // İlk parça: Başlangıç -> Transfer
                $firstLeg = $this->findDirectRoute($startStation, $transfer);
                
                // İkinci parça: Transfer -> Hedef
                $secondLeg = $this->findDirectRoute($transfer, $endStation);

                if ($firstLeg && $secondLeg) {
                    Log::info('Aktarmalı rota bulundu', ['transfer' => $transfer]);
                    // Transfer noktasını tekrar eklememek için
                    array_shift($secondLeg);
                    return array_merge($firstLeg, $secondLeg);
                }
            }

            // Hiçbir rota bulunamadıysa
            Log::error('Rota bulunamadı', ['start' => $startStation, 'end' => $endStation]);
            throw new \Exception('Uygun tren rotası bulunamadı');

        } catch (\Exception $e) {
            Log::error('Rota hesaplama hatası:', [
                'error' => $e->getMessage(),
                'start' => $startStation,
                'end' => $endStation
            ]);
            throw $e;
        }
    }

    private function findDirectRoute($start, $end)
    {
        try {
            foreach ($this->railRoutes as $routeName => $route) {
                $startIndex = array_search($start, $route);
                $endIndex = array_search($end, $route);

                if ($startIndex !== false && $endIndex !== false) {
                    Log::info('Direkt bağlantı bulundu', [
                        'route' => $routeName,
                        'start' => $start,
                        'end' => $end
                    ]);

                    if ($startIndex > $endIndex) {
                        return array_reverse(array_slice($route, $endIndex, $startIndex - $endIndex + 1));
                    } else {
                        return array_slice($route, $startIndex, $endIndex - $startIndex + 1);
                    }
                }
            }

            Log::info('Direkt bağlantı bulunamadı', ['start' => $start, 'end' => $end]);
            return null;
        } catch (\Exception $e) {
            Log::error('Direkt rota arama hatası:', [
                'error' => $e->getMessage(),
                'start' => $start,
                'end' => $end
            ]);
            return null;
        }
    }

    private function createSeaRoute($lat1, $lon1, $lat2, $lon2)
    {
        try {
            Log::info('Deniz rotası oluşturuluyor', [
                'start' => ['lat' => $lat1, 'lon' => $lon1],
                'end' => ['lat' => $lat2, 'lon' => $lon2]
            ]);

            $coordinates = [];
            $coordinates[] = [$lon1, $lat1]; // Başlangıç noktası

            // Başlangıç ve bitiş noktalarının hangi denizde olduğunu belirle
            $startSea = $this->determineSeaRegion($lat1, $lon1);
            $endSea = $this->determineSeaRegion($lat2, $lon2);

            Log::info('Deniz bölgeleri belirlendi', [
                'startSea' => $startSea,
                'endSea' => $endSea
            ]);

            if (!$startSea || !$endSea) {
                throw new \Exception('Başlangıç veya bitiş noktası deniz bölgesi dışında');
            }

            // Deniz rotası waypoint'lerini ekle
            $routeWaypoints = $this->getSeaRouteWaypoints($startSea, $endSea);
            
            foreach ($routeWaypoints as $waypoint) {
                $coordinates[] = [
                    $this->seaWaypoints[$waypoint]['lon'],
                    $this->seaWaypoints[$waypoint]['lat']
                ];
            }

            $coordinates[] = [$lon2, $lat2]; // Bitiş noktası

            Log::info('Deniz rotası başarıyla oluşturuldu', [
                'waypointCount' => count($routeWaypoints),
                'coordinateCount' => count($coordinates)
            ]);

            return [
                'type' => 'LineString',
                'coordinates' => $coordinates
            ];

        } catch (\Exception $e) {
            Log::error('Deniz rotası oluşturma hatası:', [
                'error' => $e->getMessage(),
                'start' => ['lat' => $lat1, 'lon' => $lon1],
                'end' => ['lat' => $lat2, 'lon' => $lon2]
            ]);
            throw $e;
        }
    }

    private function determineSeaRegion($lat, $lon)
    {
        // Marmara Denizi sınırları
        if ($lat >= 40.0 && $lat <= 41.0 && $lon >= 26.5 && $lon <= 30.0) {
            return 'marmara';
        }
        
        // Karadeniz sınırları
        if ($lat >= 41.0 && $lat <= 42.5 && $lon >= 28.0 && $lon <= 41.0) {
            return 'blacksea';
        }
        
        // Ege Denizi sınırları
        if ($lat >= 36.5 && $lat <= 40.0 && $lon >= 25.5 && $lon <= 27.5) {
            return 'aegean';
        }
        
        // Akdeniz sınırları
        if ($lat >= 35.5 && $lat <= 37.0 && $lon >= 27.5 && $lon <= 36.0) {
            return 'mediterranean';
        }
        
        return null;
    }

    private function getSeaRouteWaypoints($startSea, $endSea)
    {
        // Denizler arası geçiş rotaları
        $seaRoutes = [
            'marmara-blacksea' => [
                'istanbul_entry', 'istanbul_mid', 'istanbul_exit',
                'blacksea_nw', 'blacksea_w1', 'blacksea_w2'
            ],
            'marmara-aegean' => [
                'canakkale_exit', 'canakkale_mid2', 'canakkale_mid1',
                'canakkale_entry', 'aegean_ne', 'aegean_n3'
            ],
            'blacksea-marmara' => [
                'blacksea_w2', 'blacksea_w1', 'blacksea_nw',
                'istanbul_exit', 'istanbul_mid', 'istanbul_entry'
            ],
            'aegean-marmara' => [
                'aegean_n3', 'aegean_ne', 'canakkale_entry',
                'canakkale_mid1', 'canakkale_mid2', 'canakkale_exit'
            ]
        ];

        $routeKey = $startSea . '-' . $endSea;

        if (isset($seaRoutes[$routeKey])) {
            return $seaRoutes[$routeKey];
        }

        // Aynı deniz içindeki rotalar
        if ($startSea === $endSea) {
            switch ($startSea) {
                case 'blacksea':
                    return ['blacksea_w1', 'blacksea_w2', 'blacksea_w3'];
                case 'marmara':
                    return ['marmara_sw', 'marmara_south', 'marmara_se'];
                case 'aegean':
                    return ['aegean_n1', 'aegean_n2', 'aegean_n3'];
                case 'mediterranean':
                    return ['med_w1', 'med_w2', 'med_w3'];
            }
        }

        throw new \Exception('Bu deniz bölgeleri arasında rota bulunamadı');
    }

    private function isSeaTransportPossible($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $startSea = $this->determineSeaRegion($lat1, $lon1);
            $endSea = $this->determineSeaRegion($lat2, $lon2);

            // Her iki nokta da deniz kıyısında olmalı
            if (!$startSea || !$endSea) {
                return false;
            }

            // Aynı denizde veya bağlantılı denizlerde olmalı
            $connectedSeas = [
                'marmara' => ['blacksea', 'aegean'],
                'blacksea' => ['marmara'],
                'aegean' => ['marmara', 'mediterranean'],
                'mediterranean' => ['aegean']
            ];

            return $startSea === $endSea || 
                   (isset($connectedSeas[$startSea]) && in_array($endSea, $connectedSeas[$startSea]));

        } catch (\Exception $e) {
            Log::error('Deniz taşımacılığı kontrolü hatası:', ['error' => $e->getMessage()]);
            return false;
        }
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

    private function calculateTotalDistance($coordinates)
    {
        $distance = 0;
        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $distance += $this->calculateHaversineDistance(
                $coordinates[$i][1],
                $coordinates[$i][0],
                $coordinates[$i + 1][1],
                $coordinates[$i + 1][0]
            );
        }
        return $distance;
    }
}