<?php

namespace App\Livewire\Sensor;

use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $model = '';
    public $serial_no = '';
    public $mac_address = '';
    public $site_name = '';
    public $area_name = '';
    public $zone_label = '';
    public $rack_label = '';
    public $notes = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'model' => 'nullable|string|max:50',
            'serial_no' => 'nullable|string|max:80',
            'mac_address' => 'nullable|string|max:32|unique:sensors,mac_address',
            'site_name' => 'nullable|string|max:100',
            'area_name' => 'nullable|string|max:100',
            'zone_label' => 'nullable|string|max:60',
            'rack_label' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function store()
    {
        $validated = $this->validate();
        
        $validated['user_id'] = Auth::id();

        Sensor::create($validated);

        session()->flash('message', 'Sensor berhasil ditambahkan.');
        
        return redirect()->route('sensors.index');
    }

    public function render()
    {
        return view('livewire.sensor.create');
    }
}
