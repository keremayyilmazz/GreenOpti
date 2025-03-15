<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalculationController extends Controller
{
    public function index()
    {
        try {
            // Kullanıcının fabrikalarını al
            $factories = Factory::where('user_id', auth()->id())
                              ->orderBy('name')
                              ->get();

            // View'a fabrikaları gönder
            return view('calculations', compact('factories'));
            
        } catch (\Exception $e) {
            Log::error('Calculation index error: ' . $e->getMessage());
            return back()->with('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Calculation request received', $request->all());

            $validated = $request->validate([
                'source_factory_id' => 'required|exists:factories,id',
                'destination_factory_id' => 'required|exists:factories,id|different:source_factory_id',
                'weight' => 'required|numeric|min:0.01',
            ]);

            // Fabrikaları al
            $sourceFactory = Factory::findOrFail($validated['source_factory_id']);
            $destFactory = Factory::findOrFail($validated['destination_factory_id']);

            // Kullanıcı yetkisi kontrolü
            if ($sourceFactory->user_id !== auth()->id() || $destFactory->user_id !== auth()->id()) {
                throw new \Exception('Yetkisiz erişim');
            }

            // Mesafeyi hesapla
            $distance = $this->calculateDistance(
                $sourceFactory->latitude,
                $sourceFactory->longitude,
                $destFactory->latitude,
                $destFactory->longitude
            );

            // Maliyeti hesapla
            $amount = $this->calculateAmount($distance, $validated['weight']);

            // Hesaplamayı kaydet
            $calculation = Calculation::create([
                'user_id' => auth()->id(),
                'source_factory_id' => $validated['source_factory_id'],
                'destination_factory_id' => $validated['destination_factory_id'],
                'weight' => $validated['weight'],
                'distance' => $distance,
                'amount' => $amount
            ]);

            return response()->json([
                'success' => true,
                'id' => $calculation->id,
                'distance' => $distance,
                'amount' => $amount,
                'message' => 'Hesaplama başarıyla kaydedildi.'
            ]);

        } catch (\Exception $e) {
            Log::error('Calculation store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Hesaplama yapılırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function list()
    {
        try {
            $calculations = Calculation::with(['sourceFactory', 'destinationFactory'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($calc) {
                    return [
                        'id' => $calc->id,
                        'source_factory_name' => $calc->sourceFactory->name,
                        'destination_factory_name' => $calc->destinationFactory->name,
                        'weight' => $calc->weight,
                        'distance' => $calc->distance,
                        'amount' => $calc->amount,
                        'created_at' => $calc->created_at
                    ];
                });

            return response()->json($calculations);
            
        } catch (\Exception $e) {
            Log::error('Calculation list error: ' . $e->getMessage());
            return response()->json(['error' => 'Hesaplama listesi alınamadı'], 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        try {
            // Dünya'nın yarıçapı (km)
            $r = 6371;
            
            // String değerleri float'a çevir
            $lat1 = floatval($lat1);
            $lon1 = floatval($lon1);
            $lat2 = floatval($lat2);
            $lon2 = floatval($lon2);

            // Radyana çevir
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            
            // Haversine formülü
            $a = sin($dLat/2) * sin($dLat/2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($dLon/2) * sin($dLon/2);
                 
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $r * $c;
            
            // 2 ondalık basamağa yuvarla
            return round($distance, 2);

        } catch (\Exception $e) {
            Log::error('Distance calculation error: ' . $e->getMessage());
            throw new \Exception('Mesafe hesaplanamadı');
        }
    }

    private function calculateAmount($distance, $weight)
    {
        try {
            // Baz fiyat (TL/km/ton)
            $baseRate = 2.5;

            // Mesafe bazlı çarpanlar
            if ($distance <= 50) {
                $distanceMultiplier = 1.2; // 50km'ye kadar %20 artış
            } elseif ($distance <= 200) {
                $distanceMultiplier = 1.0; // 51-200km arası normal fiyat
            } elseif ($distance <= 500) {
                $distanceMultiplier = 0.9; // 201-500km arası %10 indirim
            } else {
                $distanceMultiplier = 0.8; // 500km üzeri %20 indirim
            }

            // Tonaj bazlı çarpanlar
            if ($weight <= 5) {
                $weightMultiplier = 1.3; // 5 tona kadar %30 artış
            } elseif ($weight <= 10) {
                $weightMultiplier = 1.2; // 6-10 ton arası %20 artış
            } elseif ($weight <= 20) {
                $weightMultiplier = 1.1; // 11-20 ton arası %10 artış
            } else {
                $weightMultiplier = 1.0; // 20 ton üzeri normal fiyat
            }

            // Toplam maliyet hesaplama
            $amount = $distance * $weight * $baseRate * $distanceMultiplier * $weightMultiplier;

            // 2 ondalık basamağa yuvarla
            return round($amount, 2);

        } catch (\Exception $e) {
            Log::error('Amount calculation error: ' . $e->getMessage());
            throw new \Exception('Maliyet hesaplanamadı');
        }
    }
}