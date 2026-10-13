<?php

namespace App\Livewire\Anggrek;

use App\Models\Anggrek;
use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Anggrek $anggrek;
    
    public $sensor_id;
    public $jenis_anggrek;
    public $nama_anggrek;
    public $temp_min_c;
    public $temp_max_c;
    public $hum_min_pct;
    public $hum_max_pct;

    public function mount(Anggrek $anggrek)
    {
        // Ensure user can only edit their own orchids
        if ($anggrek->user_id !== Auth::id()) {
            abort(403);
        }

        $this->anggrek = $anggrek;
        $this->sensor_id = $anggrek->sensor_id;
        $this->jenis_anggrek = $anggrek->jenis_anggrek;
        $this->nama_anggrek = $anggrek->nama_anggrek;
        $this->temp_min_c = $anggrek->temp_min_c;
        $this->temp_max_c = $anggrek->temp_max_c;
        $this->hum_min_pct = $anggrek->hum_min_pct;
        $this->hum_max_pct = $anggrek->hum_max_pct;
    }

    protected function rules()
    {
        return [
            'sensor_id' => ['nullable', Rule::exists('sensors', 'id')->where('user_id', Auth::id())],
            'jenis_anggrek' => 'required|string|max:100',
            'nama_anggrek' => 'nullable|string|max:100',
            'temp_min_c' => 'nullable|numeric|min:0|max:50|lt:temp_max_c',
            'temp_max_c' => 'nullable|numeric|min:0|max:50|gt:temp_min_c',
            'hum_min_pct' => 'nullable|numeric|min:0|max:100|lt:hum_max_pct',
            'hum_max_pct' => 'nullable|numeric|min:0|max:100|gt:hum_min_pct',
        ];
    }

    protected $messages = [
        'temp_min_c.lt' => 'Temperature minimum harus lebih kecil dari maximum.',
        'temp_max_c.gt' => 'Temperature maximum harus lebih besar dari minimum.',
        'hum_min_pct.lt' => 'Humidity minimum harus lebih kecil dari maximum.',
        'hum_max_pct.gt' => 'Humidity maximum harus lebih besar dari minimum.',
    ];

    public function update()
    {
        $validated = $this->validate();

        $validated['sensor_id'] = $validated['sensor_id'] ?: null;

        $this->anggrek->update($validated);

        session()->flash('message', 'Data anggrek berhasil diupdate.');
        
        return redirect()->route('anggrek.index');
    }

    public function render()
    {
        $sensors = Sensor::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('livewire.anggrek.edit', [
            'sensors' => $sensors,
        ]);
    }
}
