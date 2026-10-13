<?php

namespace App\Livewire\Anggrek;

use App\Models\Anggrek;
use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSensor = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSensor' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSensor()
    {
        $this->resetPage();
    }

    public function render()
    {
        $anggreks = Anggrek::query()
            ->where('user_id', Auth::id())
            ->with('sensor')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('jenis_anggrek', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_anggrek', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterSensor, function ($query) {
                $query->where('sensor_id', $this->filterSensor);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $sensors = Sensor::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('livewire.anggrek.index', [
            'anggreks' => $anggreks,
            'sensors' => $sensors,
        ]);
    }
}
