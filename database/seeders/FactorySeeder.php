<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactorySeeder extends Seeder
{
    public function run()
    {
        // Fabrikaları ekle
        $factories = [
            ['name' => 'İstanbul Fabrika', 'location' => 'İstanbul'],
            ['name' => 'Ankara Fabrika', 'location' => 'Ankara'],
            ['name' => 'İzmir Fabrika', 'location' => 'İzmir'],
            // Excel'deki diğer fabrikaları da ekleyebilirsiniz
        ];

        foreach ($factories as $factory) {
            DB::table('factories')->insert($factory);
        }

        // Taşıma tiplerini ekle
        $transportations = [
            ['type' => 'kara', 'base_cost' => 100, 'cost_per_km' => 2, 'cost_per_ton' => 5],
            ['type' => 'deniz', 'base_cost' => 200, 'cost_per_km' => 1, 'cost_per_ton' => 3],
            ['type' => 'hava', 'base_cost' => 500, 'cost_per_km' => 5, 'cost_per_ton' => 10],
            ['type' => 'tren', 'base_cost' => 150, 'cost_per_km' => 1.5, 'cost_per_ton' => 4],
        ];

        foreach ($transportations as $transportation) {
            DB::table('transportations')->insert($transportation);
        }
    }
}