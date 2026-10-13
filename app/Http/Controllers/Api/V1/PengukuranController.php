<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePengukuranRequest;
use App\Models\Pengukuran;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;

class PengukuranController extends Controller
{
    /**
     * Store a new measurement from a sensor device.
     *
     * Identifies the sensor by its MAC address and stores
     * the temperature and humidity reading.
     */
    public function store(StorePengukuranRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $sensor = Sensor::where('mac_address', $validated['mac_address'])->firstOrFail();

        $data = [
            'sensor_id' => $sensor->id,
            'recorded_at' => $validated['recorded_at'] ?? now(),
            'temperature_c' => $validated['temperature_c'],
            'humidity_pct' => $validated['humidity_pct'],
        ];

        foreach (['soil_moisture_pct', 'soil_temperature_c', 'soil_ec', 'soil_ph', 'nitrogen', 'phosphorus', 'potassium', 'pump_status', 'kondisi_lahan'] as $field) {
            if (isset($validated[$field])) {
                $data[$field] = $validated[$field];
            }
        }

        $pengukuran = Pengukuran::create($data);

        return response()->json([
            'message' => 'Pengukuran berhasil disimpan.',
            'data' => $pengukuran,
        ], 201);
    }
}
