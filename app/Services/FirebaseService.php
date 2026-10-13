<?php

namespace App\Services;

use App\Models\Pengukuran;
use App\Models\Sensor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Get the configured Firebase database URL.
     */
    public static function getDatabaseUrl(): string
    {
        return rtrim(config('services.firebase.database_url', 'https://iot-anggrek-default-rtdb.asia-southeast1.firebasedatabase.app'), '/');
    }

    /**
     * Fetch current monitoring telemetry from Firebase Realtime Database.
     */
    public function getMonitoringData(): ?array
    {
        try {
            $url = self::getDatabaseUrl() . '/monitoring.json';
            $response = Http::withoutVerifying()->timeout(5)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Firebase fetch failed', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Firebase connection error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch threshold configuration from Firebase Realtime Database.
     */
    public function getThresholds(): array
    {
        $defaults = [
            'air_temp_min' => 21.0,
            'air_temp_max' => 30.0,
            'air_hum_min'  => 60.0,
            'air_hum_max'  => 80.0,
            'lux_min'      => 10000.0,
            'lux_max'      => 20000.0,
            'n_min'        => 100,
            'n_max'        => 200,
            'p_min'        => 50,
            'p_max'        => 100,
            'k_min'        => 100,
            'k_max'        => 200,
        ];

        try {
            $url = self::getDatabaseUrl() . '/thresholds.json';
            $response = Http::withoutVerifying()->timeout(5)->get($url);

            if ($response->successful() && is_array($response->json())) {
                return array_merge($defaults, $response->json());
            }
        } catch (\Throwable $e) {
            Log::error('Firebase thresholds fetch error: ' . $e->getMessage());
        }

        return $defaults;
    }

    /**
     * Update threshold configuration in Firebase Realtime Database.
     */
    public function updateThresholds(array $data): bool
    {
        try {
            $url = self::getDatabaseUrl() . '/thresholds.json';
            $response = Http::withoutVerifying()->timeout(5)->put($url, $data);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Firebase thresholds update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save current Firebase snapshot into the pengukuran database table.
     */
    public function recordSnapshot(?int $sensorId = null): ?Pengukuran
    {
        $data = $this->getMonitoringData();
        if (!$data || !isset($data['suhu_udara'])) {
            return null;
        }

        if (!$sensorId) {
            $sensor = Sensor::first();
            $sensorId = $sensor ? $sensor->id : 1;
        }

        return Pengukuran::create([
            'sensor_id' => $sensorId,
            'recorded_at' => now(),
            'temperature_c' => $data['suhu_udara'] ?? 0,
            'humidity_pct' => $data['lembap_udara'] ?? 0,
            'soil_moisture_pct' => $data['lembap_tanah'] ?? null,
            'soil_temperature_c' => $data['suhu_tanah'] ?? null,
            'soil_ec' => $data['ec'] ?? null,
            'soil_ph' => $data['ph'] ?? null,
            'nitrogen' => $data['n'] ?? null,
            'phosphorus' => $data['p'] ?? null,
            'potassium' => $data['k'] ?? null,
            'pump_status' => $data['status_pompa'] ?? null,
            'kondisi_lahan' => $data['kondisi_lahan'] ?? null,
        ]);
    }
}
