<?php

namespace App\Livewire\Pengukuran;

use App\Models\Pengukuran;
use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSensor = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected function rules(): array
    {
        return [
            'dateFrom' => 'nullable|date',
            'dateTo' => 'nullable|date|after_or_equal:dateFrom',
        ];
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'filterSensor' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSensor()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $pengukurans = Pengukuran::query()
            ->whereHas('sensor', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with('sensor')
            ->when($this->filterSensor, function ($query) {
                $query->where('sensor_id', $this->filterSensor);
            })
            ->when($this->dateFrom && strtotime($this->dateFrom), function ($query) {
                $query->whereDate('recorded_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo && strtotime($this->dateTo), function ($query) {
                $query->whereDate('recorded_at', '<=', $this->dateTo);
            })
            ->orderBy('recorded_at', 'desc')
            ->paginate(50);

        $sensors = Sensor::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('livewire.pengukuran.index', [
            'pengukurans' => $pengukurans,
            'sensors' => $sensors,
        ]);
    }
}
