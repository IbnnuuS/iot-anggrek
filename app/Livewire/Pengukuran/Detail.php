<?php

namespace App\Livewire\Pengukuran;

use App\Models\Pengukuran;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Detail extends Component
{
    public Pengukuran $pengukuran;

    public function mount(Pengukuran $pengukuran)
    {
        $pengukuran->loadMissing('sensor.anggrek');

        // Ensure user can only view measurements from their own sensors
        if ($pengukuran->sensor->user_id !== Auth::id()) {
            abort(403);
        }

        $this->pengukuran = $pengukuran;
    }

    public function render()
    {
        $anggrek = $this->pengukuran->sensor->anggrek->first();

        $isInRange = true;
        $violations = [];

        if ($anggrek) {
            // Check temperature range
            if ($anggrek->temp_min_c && $this->pengukuran->temperature_c < $anggrek->temp_min_c) {
                $isInRange = false;
                $violations[] = [
                    'type' => 'temperature',
                    'severity' => 'low',
                    'message' => 'Temperature terlalu rendah',
                ];
            }

            if ($anggrek->temp_max_c && $this->pengukuran->temperature_c > $anggrek->temp_max_c) {
                $isInRange = false;
                $violations[] = [
                    'type' => 'temperature',
                    'severity' => 'high',
                    'message' => 'Temperature terlalu tinggi',
                ];
            }

            // Check humidity range
            if ($anggrek->hum_min_pct && $this->pengukuran->humidity_pct < $anggrek->hum_min_pct) {
                $isInRange = false;
                $violations[] = [
                    'type' => 'humidity',
                    'severity' => 'low',
                    'message' => 'Humidity terlalu rendah',
                ];
            }

            if ($anggrek->hum_max_pct && $this->pengukuran->humidity_pct > $anggrek->hum_max_pct) {
                $isInRange = false;
                $violations[] = [
                    'type' => 'humidity',
                    'severity' => 'high',
                    'message' => 'Humidity terlalu tinggi',
                ];
            }
        }

        return view('livewire.pengukuran.detail', [
            'anggrek' => $anggrek,
            'isInRange' => $isInRange,
            'violations' => $violations,
        ]);
    }
}
