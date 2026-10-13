<?php

namespace Database\Seeders;

use App\Models\Pengukuran;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengukuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensorIds = [1, 2, 3, 4, 5];
        $measurementsPerSensor = 20; 
        $daysBack = 7;

        foreach ($sensorIds as $sensorId) {
            for ($i = 0; $i < $measurementsPerSensor; $i++) {
                
                $hoursAgo = rand(0, $daysBack * 24);
                $recordedAt = Carbon::now()->subHours($hoursAgo);

                
                $baseTemp = 25.0;
                $tempVariation = rand(-7, 7); 
                $temperature = round($baseTemp + $tempVariation + (rand(-100, 100) / 100), 2);

                
                $baseHumidity = 70.0;
                $humVariation = rand(-20, 20); 
                $humidity = round($baseHumidity + $humVariation + (rand(-100, 100) / 100), 2);

                
                $temperature = max(10, min(40, $temperature)); 
                $humidity = max(30, min(100, $humidity));

                Pengukuran::create([
                    'sensor_id' => $sensorId,
                    'recorded_at' => $recordedAt,
                    'temperature_c' => $temperature,
                    'humidity_pct' => $humidity,
                ]);
            }
        }
    }
}
