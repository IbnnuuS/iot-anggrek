<?php

namespace App\Livewire\Pengukuran;

use App\Models\Pengukuran;
use App\Models\Sensor;
use App\Services\FirebaseService;
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

    public $savedMessage = null;

    // Hapus riwayat
    public $showDeleteModal = false;

    public $deletingId = null;

    // Hapus semua riwayat per tanggal
    public $bulkDate = null;

    public $showBulkModal = false;

    public $bulkCount = 0;

    /** Simpan snapshot terbaru dari alat ke riwayat (tambah riwayat manual). */
    public function captureNow(FirebaseService $firebase): void
    {
        $sensor = Sensor::where('user_id', Auth::id())->first() ?? Sensor::first();
        $record = $firebase->recordSnapshot($sensor ? $sensor->id : 1);

        $this->savedMessage = $record
            ? 'Riwayat baru tersimpan ('.now()->format('H:i:s').')'
            : 'Gagal mengambil data terbaru dari alat.';

        if ($record) {
            $this->resetPage();
        }
    }

    public function resetFilter(): void
    {
        $this->reset(['search', 'filterSensor', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function destroy(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $pengukuran = Pengukuran::find($this->deletingId);

        if ($pengukuran) {
            $pengukuran->delete();
            $this->dispatch('notify', message: 'Riwayat monitoring berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /** Buka modal konfirmasi hapus semua data pada tanggal terpilih. */
    public function confirmBulkDelete(): void
    {
        if (! $this->bulkDate) {
            $this->bulkDate = today()->toDateString();
        }

        $this->validate(['bulkDate' => 'required|date']);

        $this->bulkCount = Pengukuran::whereDate('recorded_at', $this->bulkDate)->count();
        $this->showBulkModal = true;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkModal = false;
    }

    /** Hapus semua riwayat pada tanggal terpilih. */
    public function destroyByDate(): void
    {
        $deleted = Pengukuran::whereDate('recorded_at', $this->bulkDate)->delete();

        $this->showBulkModal = false;
        $this->bulkCount = 0;
        $this->dispatch('notify', message: $deleted.' riwayat tanggal '.$this->bulkDate.' berhasil dihapus.');
        $this->resetPage();
    }

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
        $base = Pengukuran::query();

        $pengukurans = (clone $base)
            ->with('sensor')
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('kondisi_lahan', 'like', $term)
                        ->orWhereHas('sensor', fn ($s) => $s->where('name', 'like', $term));
                });
            })
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
            ->paginate(20);

        $sensors = Sensor::orderBy('name')->get();

        return view('livewire.pengukuran.index', [
            'pengukurans' => $pengukurans,
            'sensors' => $sensors,
            'totalRiwayat' => (clone $base)->count(),
            'riwayatHariIni' => (clone $base)->whereDate('recorded_at', today())->count(),
            'terakhirRekam' => (clone $base)->orderBy('recorded_at', 'desc')->first(),
        ])->layout('components.layouts.app', ['title' => __('Riwayat Monitoring')]);
    }
}
