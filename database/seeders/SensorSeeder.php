<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensors = [
            [
                'user_id' => 1,
                'name' => 'Sensor Greenhouse A',
                'model' => 'DHT22',
                'serial_no' => 'DHT22-001-2024',
                'mac_address' => 'A1:B2:C3:D4:E5:F1',
                'site_name' => 'Greenhouse Utama',
                'area_name' => 'Area A',
                'zone_label' => 'Zone 1',
                'rack_label' => 'Rack A1',
                'notes' => 'Sensor utama untuk monitoring greenhouse A',
            ],
            [
                'user_id' => 1,
                'name' => 'Sensor Greenhouse B',
                'model' => 'DHT22',
                'serial_no' => 'DHT22-002-2024',
                'mac_address' => 'A1:B2:C3:D4:E5:F2',
                'site_name' => 'Greenhouse Utama',
                'area_name' => 'Area B',
                'zone_label' => 'Zone 1',
                'rack_label' => 'Rack B1',
                'notes' => 'Sensor untuk monitoring greenhouse B',
            ],
            [
                'user_id' => 1,
                'name' => 'Sensor Nursery',
                'model' => 'BME280',
                'serial_no' => 'BME280-001-2024',
                'mac_address' => 'A1:B2:C3:D4:E5:F3',
                'site_name' => 'Nursery Room',
                'area_name' => 'Area C',
                'zone_label' => 'Zone 2',
                'rack_label' => 'Rack C1',
                'notes' => 'Sensor khusus untuk area pembibitan',
            ],
            [
                'user_id' => 1,
                'name' => 'Sensor Storage',
                'model' => 'DHT11',
                'serial_no' => 'DHT11-001-2024',
                'mac_address' => 'A1:B2:C3:D4:E5:F4',
                'site_name' => 'Storage Room',
                'area_name' => 'Area D',
                'zone_label' => 'Zone 3',
                'rack_label' => 'Rack D1',
                'notes' => 'Sensor untuk ruang penyimpanan',
            ],
            [
                'user_id' => 2,
                'name' => 'Sensor Lab',
                'model' => 'SHT31',
                'serial_no' => 'SHT31-001-2024',
                'mac_address' => 'A1:B2:C3:D4:E5:F5',
                'site_name' => 'Laboratory',
                'area_name' => 'Research Lab',
                'zone_label' => 'Zone 4',
                'rack_label' => 'Rack L1',
                'notes' => 'Sensor untuk laboratorium penelitian',
            ],
        ];

        foreach ($sensors as $sensorData) {
            Sensor::create($sensorData);
        }
    }
}
