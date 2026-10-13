<?php

namespace App\Livewire;

use App\Services\FirebaseService;
use Livewire\Component;

class ThresholdSetting extends Component
{
    public $air_temp_min = 21.0;
    public $air_temp_max = 30.0;
    public $air_hum_min  = 60.0;
    public $air_hum_max  = 80.0;
    public $lux_min      = 10000.0;
    public $lux_max      = 20000.0;
    public $n_min        = 100;
    public $n_max        = 200;
    public $p_min        = 50;
    public $p_max        = 100;
    public $k_min        = 100;
    public $k_max        = 200;

    public $savedMessage = null;
    public $errorMessage = null;

    protected function rules()
    {
        return [
            'air_temp_min' => 'required|numeric',
            'air_temp_max' => 'required|numeric|gte:air_temp_min',
            'air_hum_min'  => 'required|numeric|min:0|max:100',
            'air_hum_max'  => 'required|numeric|min:0|max:100|gte:air_hum_min',
            'lux_min'      => 'required|numeric|min:0',
            'lux_max'      => 'required|numeric|min:0|gte:lux_min',
            'n_min'        => 'required|integer|min:0',
            'n_max'        => 'required|integer|min:0|gte:n_min',
            'p_min'        => 'required|integer|min:0',
            'p_max'        => 'required|integer|min:0|gte:p_min',
            'k_min'        => 'required|integer|min:0',
            'k_max'        => 'required|integer|min:0|gte:k_min',
        ];
    }

    public function mount(FirebaseService $firebase)
    {
        $data = $firebase->getThresholds();
        $this->fill([
            'air_temp_min' => (float) ($data['air_temp_min'] ?? 21.0),
            'air_temp_max' => (float) ($data['air_temp_max'] ?? 30.0),
            'air_hum_min'  => (float) ($data['air_hum_min'] ?? 60.0),
            'air_hum_max'  => (float) ($data['air_hum_max'] ?? 80.0),
            'lux_min'      => (float) ($data['lux_min'] ?? 10000.0),
            'lux_max'      => (float) ($data['lux_max'] ?? 20000.0),
            'n_min'        => (int) ($data['n_min'] ?? 100),
            'n_max'        => (int) ($data['n_max'] ?? 200),
            'p_min'        => (int) ($data['p_min'] ?? 50),
            'p_max'        => (int) ($data['p_max'] ?? 100),
            'k_min'        => (int) ($data['k_min'] ?? 100),
            'k_max'        => (int) ($data['k_max'] ?? 200),
        ]);
    }

    public function save(FirebaseService $firebase)
    {
        $this->reset(['savedMessage', 'errorMessage']);
        $this->validate();

        $payload = [
            'air_temp_min' => (float) $this->air_temp_min,
            'air_temp_max' => (float) $this->air_temp_max,
            'air_hum_min'  => (float) $this->air_hum_min,
            'air_hum_max'  => (float) $this->air_hum_max,
            'lux_min'      => (float) $this->lux_min,
            'lux_max'      => (float) $this->lux_max,
            'n_min'        => (int) $this->n_min,
            'n_max'        => (int) $this->n_max,
            'p_min'        => (int) $this->p_min,
            'p_max'        => (int) $this->p_max,
            'k_min'        => (int) $this->k_min,
            'k_max'        => (int) $this->k_max,
        ];

        if ($firebase->updateThresholds($payload)) {
            $this->savedMessage = 'Threshold berhasil disimpan ke Firebase dan tersinkron ke alat.';
        } else {
            $this->errorMessage = 'Gagal menyimpan threshold ke Firebase. Periksa koneksi internet.';
        }
    }

    public function resetDefaults(FirebaseService $firebase)
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

        $this->fill($defaults);
        $firebase->updateThresholds($defaults);
        $this->savedMessage = 'Threshold berhasil dikembalikan ke nilai default.';
    }

    public function render()
    {
        return view('livewire.threshold-setting')
            ->layout('components.layouts.app', ['title' => __('Pengaturan Threshold')]);
    }
}
