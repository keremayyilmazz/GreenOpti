<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Fabrikaları ekle
        $factories = [
            ['name' => 'İstanbul Fabrika', 'location' => 'İstanbul'],
            ['name' => 'Ankara Fabrika', 'location' => 'Ankara'],
            ['name' => 'İzmir Fabrika', 'location' => 'İzmir'],
            ['name' => 'Adana Fabrika', 'location' => 'Adana'],
            ['name' => 'Adıyaman Fabrika', 'location' => 'Adıyaman'],
            ['name' => 'Afyonkarahisar Fabrika', 'location' => 'Afyonkarahisar'],
            ['name' => 'Ağrı Fabrika', 'location' => 'Ağrı'],
            ['name' => 'Amasya Fabrika', 'location' => 'Amasya'],
            ['name' => 'Antalya Fabrika', 'location' => 'Antalya'],
            ['name' => 'Artvin Fabrika', 'location' => 'Artvin'],
            ['name' => 'Aydın Fabrika', 'location' => 'Aydın'],
            ['name' => 'Balıkesir Fabrika', 'location' => 'Balıkesir'],
            ['name' => 'Bilecik Fabrika', 'location' => 'Bilecik'],
            ['name' => 'Bingöl Fabrika', 'location' => 'Bingöl'],
            ['name' => 'Bitlis Fabrika', 'location' => 'Bitlis'],
            ['name' => 'Bolu Fabrika', 'location' => 'Bolu'],
            ['name' => 'Burdur Fabrika', 'location' => 'Burdur'],
            ['name' => 'Bursa Fabrika', 'location' => 'Bursa'],
            ['name' => 'Çanakkale Fabrika', 'location' => 'Çanakkale'],
            ['name' => 'Çankırı Fabrika', 'location' => 'Çankırı'],
            ['name' => 'Çorum Fabrika', 'location' => 'Çorum'],
            ['name' => 'Denizli Fabrika', 'location' => 'Denizli'],
            ['name' => 'Diyarbakır Fabrika', 'location' => 'Diyarbakır'],
            ['name' => 'Edirne Fabrika', 'location' => 'Edirne'],
            ['name' => 'Elazığ Fabrika', 'location' => 'Elazığ'],
            ['name' => 'Erzincan Fabrika', 'location' => 'Erzincan'],
            ['name' => 'Erzurum Fabrika', 'location' => 'Erzurum'],
            ['name' => 'Eskişehir Fabrika', 'location' => 'Eskişehir'],
            ['name' => 'Gaziantep Fabrika', 'location' => 'Gaziantep'],
            ['name' => 'Giresun Fabrika', 'location' => 'Giresun'],
            ['name' => 'Gümüşhane Fabrika', 'location' => 'Gümüşhane'],
            ['name' => 'Hakkari Fabrika', 'location' => 'Hakkari'],
            ['name' => 'Hatay Fabrika', 'location' => 'Hatay'],
            ['name' => 'Isparta Fabrika', 'location' => 'Isparta'],
            ['name' => 'Mersin Fabrika', 'location' => 'Mersin'],
            ['name' => 'Kars Fabrika', 'location' => 'Kars'],
            ['name' => 'Kastamonu Fabrika', 'location' => 'Kastamonu'],
            ['name' => 'Kayseri Fabrika', 'location' => 'Kayseri'],
            ['name' => 'Kırklareli Fabrika', 'location' => 'Kırklareli'],
            ['name' => 'Kırşehir Fabrika', 'location' => 'Kırşehir'],
            ['name' => 'Kocaeli Fabrika', 'location' => 'Kocaeli'],
            ['name' => 'Konya Fabrika', 'location' => 'Konya'],
            ['name' => 'Kütahya Fabrika', 'location' => 'Kütahya'],
            ['name' => 'Malatya Fabrika', 'location' => 'Malatya'],
            ['name' => 'Manisa Fabrika', 'location' => 'Manisa'],
            ['name' => 'Kahramanmaraş Fabrika', 'location' => 'Kahramanmaraş'],
            ['name' => 'Mardin Fabrika', 'location' => 'Mardin'],
            ['name' => 'Muğla Fabrika', 'location' => 'Muğla'],
            ['name' => 'Muş Fabrika', 'location' => 'Muş'],
            ['name' => 'Nevşehir Fabrika', 'location' => 'Nevşehir'],
            ['name' => 'Niğde Fabrika', 'location' => 'Niğde'],
            ['name' => 'Ordu Fabrika', 'location' => 'Ordu'],
            ['name' => 'Rize Fabrika', 'location' => 'Rize'],
            ['name' => 'Sakarya Fabrika', 'location' => 'Sakarya'],
            ['name' => 'Samsun Fabrika', 'location' => 'Samsun'],
            ['name' => 'Siirt Fabrika', 'location' => 'Siirt'],
            ['name' => 'Sinop Fabrika', 'location' => 'Sinop'],
            ['name' => 'Sivas Fabrika', 'location' => 'Sivas'],
            ['name' => 'Tekirdağ Fabrika', 'location' => 'Tekirdağ'],
            ['name' => 'Tokat Fabrika', 'location' => 'Tokat'],
            ['name' => 'Trabzon Fabrika', 'location' => 'Trabzon'],
            ['name' => 'Tunceli Fabrika', 'location' => 'Tunceli'],
            ['name' => 'Şanlıurfa Fabrika', 'location' => 'Şanlıurfa'],
            ['name' => 'Uşak Fabrika', 'location' => 'Uşak'],
            ['name' => 'Van Fabrika', 'location' => 'Van'],
            ['name' => 'Yozgat Fabrika', 'location' => 'Yozgat'],
            ['name' => 'Zonguldak Fabrika', 'location' => 'Zonguldak'],
            ['name' => 'Aksaray Fabrika', 'location' => 'Aksaray'],
            ['name' => 'Bayburt Fabrika', 'location' => 'Bayburt'],
            ['name' => 'Karaman Fabrika', 'location' => 'Karaman'],
            ['name' => 'Kırıkkale Fabrika', 'location' => 'Kırıkkale'],
            ['name' => 'Batman Fabrika', 'location' => 'Batman'],
            ['name' => 'Şırnak Fabrika', 'location' => 'Şırnak'],
            ['name' => 'Bartın Fabrika', 'location' => 'Bartın'],
            ['name' => 'Ardahan Fabrika', 'location' => 'Ardahan'],
            ['name' => 'Iğdır Fabrika', 'location' => 'Iğdır'],
            ['name' => 'Yalova Fabrika', 'location' => 'Yalova'],
            ['name' => 'Karabük Fabrika', 'location' => 'Karabük'],
            ['name' => 'Kilis Fabrika', 'location' => 'Kilis'],
            ['name' => 'Osmaniye Fabrika', 'location' => 'Osmaniye'],
            ['name' => 'Düzce Fabrika', 'location' => 'Düzce']
        ];

        foreach ($factories as $factory) {
            DB::table('factories')->insert($factory);
        }

        // Taşıma tiplerini ekle
        $transportations = [
            [
                'type' => 'kara',
                'base_cost' => 1000,
                'cost_per_km' => 3.5,
                'cost_per_ton' => 150
            ],
            [
                'type' => 'deniz',
                'base_cost' => 2500,
                'cost_per_km' => 2.0,
                'cost_per_ton' => 100
            ],
            [
                'type' => 'hava',
                'base_cost' => 5000,
                'cost_per_km' => 8.0,
                'cost_per_ton' => 500
            ],
            [
                'type' => 'tren',
                'base_cost' => 1500,
                'cost_per_km' => 2.5,
                'cost_per_ton' => 120
            ]
        ];

        foreach ($transportations as $transportation) {
            DB::table('transportations')->insert($transportation);
        }

        // Mesafeleri ve ulaşım tiplerini oluştur
        $this->generateDistancesAndTransportations();
    }

    private function generateDistancesAndTransportations()
    {
        $factories = DB::table('factories')->get();
        
        foreach ($factories as $from) {
            foreach ($factories as $to) {
                if ($from->id != $to->id) {
                    // Mesafeyi hesapla ve logla
                    $distance = $this->calculateRealDistance($from->location, $to->location);
                    Log::info("Distance calculated for {$from->location} to {$to->location}: {$distance}km");
                    
                    // Mesafeyi ekle
                    DB::table('distances')->insert([
                        'from_factory_id' => $from->id,
                        'to_factory_id' => $to->id,
                        'distance' => $distance
                    ]);

                    // Ulaşım tiplerini belirle
                    $this->setAvailableTransportations($from->id, $to->id, $from->location, $to->location);
                }
            }
        }
    }

    private function calculateRealDistance($from, $to)
    {
        // Debug için
        Log::info("Mesafe hesaplanıyor: {$from} -> {$to}");

        // Şehir isimlerini normalize et
        $from = $this->normalizeCity($from);
        $to = $this->normalizeCity($to);

        $distances = [
            'Adana-Mersin' => 69,
            'Istanbul-Ankara' => 453,
            'Istanbul-Izmir' => 565,
            'Istanbul-Antalya' => 724,
            'Istanbul-Bursa' => 243,
            'Istanbul-Trabzon' => 1074,
            'Istanbul-Samsun' => 684,
            'Istanbul-Erzurum' => 1218,
            'Istanbul-Gaziantep' => 1137,
            'Istanbul-Adana' => 939,
            'Istanbul-Konya' => 662,
            'Istanbul-Mersin' => 960,
            'Istanbul-Diyarbakir' => 1367,
            'Istanbul-Kayseri' => 770,
            
            'Ankara-Izmir' => 580,
            'Ankara-Konya' => 258,
            'Ankara-Samsun' => 418,
            'Ankara-Erzurum' => 872,
            'Ankara-Antalya' => 485,
            'Ankara-Gaziantep' => 687,
            'Ankara-Adana' => 490,
            'Ankara-Diyarbakir' => 917,
            'Ankara-Trabzon' => 736,
            'Ankara-Mersin' => 485,
            'Ankara-Kayseri' => 320
            // ... diğer mesafeler buraya eklenecek
        ];

        $key = "$from-$to";
        $reverseKey = "$to-$from";

        Log::info("Aranan mesafe anahtarları:", ['key' => $key, 'reverseKey' => $reverseKey]);

        if (isset($distances[$key])) {
            Log::info("Mesafe bulundu ($key): {$distances[$key]} km");
            return $distances[$key];
        } elseif (isset($distances[$reverseKey])) {
            Log::info("Mesafe bulundu ($reverseKey): {$distances[$reverseKey]} km");
            return $distances[$reverseKey];
        } else {
            $randomDistance = rand(50, 1500);
            Log::info("Mesafe bulunamadı, random üretildi: {$randomDistance} km");
            return $randomDistance;
        }
    }

    private function normalizeCity($city)
    {
        $replacements = [
            'İ' => 'I',
            'Ğ' => 'G',
            'Ü' => 'U',
            'Ş' => 'S',
            'Ö' => 'O',
            'Ç' => 'C',
            'ı' => 'i',
            'ğ' => 'g',
            'ü' => 'u',
            'ş' => 's',
            'ö' => 'o',
            'ç' => 'c'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $city);
    }

    private function setAvailableTransportations($fromId, $toId, $fromLocation, $toLocation)
    {
        // Deniz kenarında olan iller
        $coastalCities = ['İstanbul', 'İzmir', 'Antalya', 'Mersin', 'Samsun', 'Trabzon', 
            'Zonguldak', 'Ordu', 'Giresun', 'Rize', 'Artvin', 'Sinop', 'Bartın', 
            'Kastamonu', 'Düzce', 'Sakarya', 'Kocaeli', 'Yalova', 'Bursa', 'Balıkesir', 
            'Çanakkale', 'Aydın', 'Muğla', 'Adana', 'Hatay'];

        // Havalimanı olan iller
        $airportCities = ['İstanbul', 'Ankara', 'İzmir', 'Antalya', 'Adana', 'Trabzon', 
            'Erzurum', 'Gaziantep', 'Diyarbakır', 'Van', 'Kayseri', 'Samsun', 'Malatya',
            'Denizli', 'Bursa', 'Eskişehir', 'Ordu-Giresun', 'Hatay', 'Kars', 'Muş',
            'Şanlıurfa', 'Batman', 'Elazığ', 'Erzincan', 'Kahramanmaraş', 'Mardin', 'Zonguldak'];

        // Tren yolu olan iller
        $railwayCities = ['İstanbul', 'Ankara', 'İzmir', 'Konya', 'Eskişehir', 'Kayseri',
            'Sivas', 'Erzurum', 'Adana', 'Mersin', 'Balıkesir', 'Kütahya', 'Afyonkarahisar',
            'Bilecik', 'Kocaeli', 'Sakarya', 'Zonguldak', 'Karabük', 'Çankırı', 'Kırıkkale',
            'Yozgat', 'Kırşehir', 'Aksaray', 'Niğde', 'Karaman'];

        // Karayolu her zaman mevcut
        DB::table('available_transportations')->insert([
            'from_factory_id' => $fromId,
            'to_factory_id' => $toId,
            'transportation_type' => 'kara',
            'is_available' => true
        ]);

        // Denizyolu kontrolü
        $hasSeaRoute = in_array($fromLocation, $coastalCities) && in_array($toLocation, $coastalCities);
        DB::table('available_transportations')->insert([
            'from_factory_id' => $fromId,
            'to_factory_id' => $toId,
            'transportation_type' => 'deniz',
            'is_available' => $hasSeaRoute
        ]);

        // Havayolu kontrolü
        $hasAirRoute = in_array($fromLocation, $airportCities) && in_array($toLocation, $airportCities);
        DB::table('available_transportations')->insert([
            'from_factory_id' => $fromId,
            'to_factory_id' => $toId,
            'transportation_type' => 'hava',
            'is_available' => $hasAirRoute
        ]);

        // Tren yolu kontrolü
        $hasRailRoute = in_array($fromLocation, $railwayCities) && in_array($toLocation, $railwayCities);
        DB::table('available_transportations')->insert([
            'from_factory_id' => $fromId,
            'to_factory_id' => $toId,
            'transportation_type' => 'tren',
            'is_available' => $hasRailRoute
        ]);
    }
}