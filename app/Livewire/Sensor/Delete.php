<?php

namespace App\Livewire\Sensor;

use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Delete extends Component
{
    public $sensorId;
    public $showModal = false;

    protected $listeners = ['deleteSensor' => 'delete'];

    public function delete($id)
    {
        $this->sensorId = $id;
        $this->showModal = true;
    }

    public function destroy()
    {
        $sensor = Sensor::findOrFail($this->sensorId);

        // Ensure user can only delete their own sensors
        if ($sensor->user_id !== Auth::id()) {
            abort(403);
        }

        $sensor->delete();

        session()->flash('message', 'Sensor berhasil dihapus.');
        
        $this->showModal = false;
        $this->dispatch('sensorDeleted');
    }

    public function cancel()
    {
        $this->showModal = false;
        $this->sensorId = null;
    }

    public function render()
    {
        return view('livewire.sensor.delete');
    }
}
