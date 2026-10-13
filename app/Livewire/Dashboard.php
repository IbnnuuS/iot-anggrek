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
    public $savedMessage = null;

    public function mount(FirebaseService $firebase)
    {
        $this->firebaseUrl = FirebaseService::getDatabaseUrl();
        $this->monitoring = $firebase->getMonitoringData() ?? [];
        $this->thresholds = $firebase->getThresholds();
    }

    public function saveSnapshot(FirebaseService $firebase)
    {
        $sensor = Sensor::where('user_id', Auth::id())->first() ?? Sensor::first();
        $record = $firebase->recordSnapshot($sensor ? $sensor->id : 1);

        if ($record) {
            $this->savedMessage = 'Snapshot berhasil disimpan ke riwayat (' . now()->format('H:i:s') . ')';
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
