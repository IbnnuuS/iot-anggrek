<?php

namespace App\Livewire\Sensor;

use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public Sensor $sensor;
    
    public $name;
    public $model;
    public $serial_no;
    public $mac_address;
    public $site_name;
    public $area_name;
    public $zone_label;
    public $rack_label;
    public $notes;

    public function mount(Sensor $sensor)
    {
        // Ensure user can only edit their own sensors
        if ($sensor->user_id !== Auth::id()) {
            abort(403);
        }

        $this->sensor = $sensor;
        $this->name = $sensor->name;
        $this->model = $sensor->model;
        $this->serial_no = $sensor->serial_no;
        $this->mac_address = $sensor->mac_address;
        $this->site_name = $sensor->site_name;
        $this->area_name = $sensor->area_name;
        $this->zone_label = $sensor->zone_label;
        $this->rack_label = $sensor->rack_label;
        $this->notes = $sensor->notes;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'model' => 'nullable|string|max:50',
            'serial_no' => 'nullable|string|max:80',
            'mac_address' => 'nullable|string|max:32|unique:sensors,mac_address,' . $this->sensor->id,
            'site_name' => 'nullable|string|max:100',
            'area_name' => 'nullable|string|max:100',
            'zone_label' => 'nullable|string|max:60',
            'rack_label' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $this->sensor->update($validated);

        session()->flash('message', 'Sensor berhasil diupdate.');
        
        return redirect()->route('sensors.index');
    }

    public function render()
    {
        return view('livewire.sensor.edit');
    }
}
