<?php

namespace App\Livewire\Sensor;

use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSite = '';
    public $filterArea = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSite' => ['except' => ''],
        'filterArea' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSite()
    {
        $this->resetPage();
    }

    public function updatingFilterArea()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sensors = Sensor::query()
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('model', 'like', '%' . $this->search . '%')
                        ->orWhere('serial_no', 'like', '%' . $this->search . '%')
                        ->orWhere('mac_address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterSite, function ($query) {
                $query->where('site_name', $this->filterSite);
            })
            ->when($this->filterArea, function ($query) {
                $query->where('area_name', $this->filterArea);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $sites = Sensor::where('user_id', Auth::id())
            ->whereNotNull('site_name')
            ->distinct()
            ->pluck('site_name');

        $areas = Sensor::where('user_id', Auth::id())
            ->whereNotNull('area_name')
            ->distinct()
            ->pluck('area_name');

        return view('livewire.sensor.index', [
            'sensors' => $sensors,
            'sites' => $sites,
            'areas' => $areas,
        ]);
    }
}
