<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Taşıma yöntemleri
        DB::table('transportations')->insert([
            [
                'name' => 'Karayolu',
                'cost_per_km' => 5.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Demiryolu',
                'cost_per_km' => 3.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Denizyolu',
                'cost_per_km' => 2.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Havayolu',
                'cost_per_km' => 12.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Fabrikalar
        DB::table('factories')->insert([
            [
                'name' => 'İstanbul Fabrika',
                'location' => 'İstanbul, Türkiye',
                'latitude' => 41.0082,
                'longitude' => 28.9784,
                'address' => 'Maslak Mah. Büyükdere Cad. No:1',
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ankara Fabrika',
                'location' => 'Ankara, Türkiye',
                'latitude' => 39.9334,
                'longitude' => 32.8597,
                'address' => 'Çankaya Mah. Atatürk Bulvarı No:1',
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}