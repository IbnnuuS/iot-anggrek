<?php

namespace App\Livewire\Anggrek;

use App\Models\Anggrek;
use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $sensor_id = '';
    public $jenis_anggrek = '';
    public $nama_anggrek = '';
    public $temp_min_c = '';
    public $temp_max_c = '';
    public $hum_min_pct = '';
    public $hum_max_pct = '';

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

    public function store()
    {
        $validated = $this->validate();
        
        $validated['user_id'] = Auth::id();
        $validated['sensor_id'] = $validated['sensor_id'] ?: null;

        Anggrek::create($validated);

        session()->flash('message', 'Data anggrek berhasil ditambahkan.');
        
        return redirect()->route('anggrek.index');
    }

    public function render()
    {
        $sensors = Sensor::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('livewire.anggrek.create', [
            'sensors' => $sensors,
        ]);
    }
}
