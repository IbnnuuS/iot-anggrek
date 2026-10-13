<?php

namespace Database\Seeders;

use App\Models\Anggrek;
use Illuminate\Database\Seeder;

class AnggrekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anggrekData = [
            [
                'user_id' => 1,
                'sensor_id' => 1,
                'jenis_anggrek' => 'Phalaenopsis',
                'nama_anggrek' => 'Anggrek Bulan Putih',
                'temp_min_c' => 18.00,
                'temp_max_c' => 28.00,
                'hum_min_pct' => 60.00,
                'hum_max_pct' => 80.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 1,
                'jenis_anggrek' => 'Dendrobium',
                'nama_anggrek' => 'Dendrobium Ungu',
                'temp_min_c' => 20.00,
                'temp_max_c' => 30.00,
                'hum_min_pct' => 50.00,
                'hum_max_pct' => 70.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 2,
                'jenis_anggrek' => 'Cattleya',
                'nama_anggrek' => 'Cattleya Pink',
                'temp_min_c' => 21.00,
                'temp_max_c' => 29.00,
                'hum_min_pct' => 55.00,
                'hum_max_pct' => 75.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 2,
                'jenis_anggrek' => 'Vanda',
                'nama_anggrek' => 'Vanda Blue',
                'temp_min_c' => 22.00,
                'temp_max_c' => 32.00,
                'hum_min_pct' => 60.00,
                'hum_max_pct' => 85.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 3,
                'jenis_anggrek' => 'Oncidium',
                'nama_anggrek' => 'Oncidium Kuning',
                'temp_min_c' => 19.00,
                'temp_max_c' => 27.00,
                'hum_min_pct' => 55.00,
                'hum_max_pct' => 70.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 3,
                'jenis_anggrek' => 'Paphiopedilum',
                'nama_anggrek' => 'Paphiopedilum Hijau',
                'temp_min_c' => 18.00,
                'temp_max_c' => 26.00,
                'hum_min_pct' => 65.00,
                'hum_max_pct' => 80.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 4,
                'jenis_anggrek' => 'Cymbidium',
                'nama_anggrek' => 'Cymbidium Merah',
                'temp_min_c' => 16.00,
                'temp_max_c' => 24.00,
                'hum_min_pct' => 50.00,
                'hum_max_pct' => 65.00,
            ],
            [
                'user_id' => 1,
                'sensor_id' => 4,
                'jenis_anggrek' => 'Miltonia',
                'nama_anggrek' => 'Miltonia Orange',
                'temp_min_c' => 20.00,
                'temp_max_c' => 28.00,
                'hum_min_pct' => 60.00,
                'hum_max_pct' => 75.00,
            ],
            [
                'user_id' => 2,
                'sensor_id' => 5,
                'jenis_anggrek' => 'Brassia',
                'nama_anggrek' => 'Brassia Spider',
                'temp_min_c' => 19.00,
                'temp_max_c' => 29.00,
                'hum_min_pct' => 55.00,
                'hum_max_pct' => 70.00,
            ],
            [
                'user_id' => 2,
                'sensor_id' => null,
                'jenis_anggrek' => 'Epidendrum',
                'nama_anggrek' => 'Epidendrum Mini',
                'temp_min_c' => 18.00,
                'temp_max_c' => 30.00,
                'hum_min_pct' => 50.00,
                'hum_max_pct' => 75.00,
            ],
        ];

        foreach ($anggrekData as $data) {
            Anggrek::create($data);
        }
    }
}
