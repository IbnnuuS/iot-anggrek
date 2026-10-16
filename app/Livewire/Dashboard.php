<?php

namespace App\Livewire;

use App\Models\Anggrek;
use App\Models\Pengukuran;
use App\Models\Sensor;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $firebaseUrl;

    public $monitoring = [];

    public $thresholds = [];

    public $control = [];

    public $savedMessage = null;

    public $controlMessage = null;

    public function mount(FirebaseService $firebase)
    {
        $this->firebaseUrl = FirebaseService::getDatabaseUrl();
        $this->monitoring = $firebase->getMonitoringData() ?? [];
        $this->thresholds = $firebase->getThresholds();
        $this->control = $firebase->getControl();
    }

    public function startMonitoring(FirebaseService $firebase)
    {
        if ($firebase->setSensorOn(true)) {
            $this->control['sensor_on'] = 1;
            $this->controlMessage = 'Monitoring diaktifkan. Alat mulai membaca & mengirim data.';
        } else {
            $this->controlMessage = 'Gagal mengaktifkan monitoring. Periksa koneksi internet.';
        }
    }

    public function stopMonitoring(FirebaseService $firebase)
    {
        if ($firebase->setSensorOn(false)) {
            $this->control['sensor_on'] = 0;
            $this->controlMessage = 'Monitoring dinonaktifkan. Alat berhenti membaca data & riwayat otomatis berhenti.';
        } else {
            $this->controlMessage = 'Gagal menonaktifkan monitoring. Periksa koneksi internet.';
        }
    }

    public function startAutoWater(FirebaseService $firebase)
    {
        if ($firebase->setAutoWater(true)) {
            $this->control['auto_water'] = 1;
            $this->controlMessage = 'Penyiraman otomatis diaktifkan. Alat menyiram sendiri saat tanah kering.';
        } else {
            $this->controlMessage = 'Gagal mengaktifkan penyiraman otomatis. Periksa koneksi internet.';
        }
    }

    public function stopAutoWater(FirebaseService $firebase)
    {
        if ($firebase->setAutoWater(false)) {
            $this->control['auto_water'] = 0;
            $this->controlMessage = 'Penyiraman otomatis dinonaktifkan. Alat tidak akan menyiram sendiri.';
        } else {
            $this->controlMessage = 'Gagal menonaktifkan penyiraman otomatis. Periksa koneksi internet.';
        }
    }

    public function saveSnapshot(FirebaseService $firebase)
    {
        $sensor = Sensor::where('user_id', Auth::id())->first() ?? Sensor::first();
        $record = $firebase->recordSnapshot($sensor ? $sensor->id : 1);

        if ($record) {
            $this->savedMessage = 'Snapshot berhasil disimpan ke riwayat ('.now()->format('H:i:s').')';
        } else {
            $this->savedMessage = 'Gagal menyimpan snapshot dari Firebase.';
        }
    }

    public function render()
    {
        $userSensorsCount = Sensor::where('user_id', Auth::id())->count();
        $userAnggrekCount = Anggrek::where('user_id', Auth::id())->count();
        $recentPengukuran = Pengukuran::whereHas('sensor', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->with('sensor')
            ->orderBy('recorded_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'sensorsCount' => $userSensorsCount,
            'anggrekCount' => $userAnggrekCount,
            'recentPengukuran' => $recentPengukuran,
        ])->layout('components.layouts.app', ['title' => __('Dashboard')]);
    }
}
