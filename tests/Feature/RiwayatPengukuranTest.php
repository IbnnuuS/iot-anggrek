<?php

use App\Livewire\Pengukuran\Index;
use App\Models\Pengukuran;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('menampilkan riwayat monitoring lalu menghapusnya lewat tombol hapus', function () {
    $user = User::factory()->create();
    $sensor = Sensor::create(['user_id' => $user->id, 'name' => 'Sensor Uji']);
    $row = Pengukuran::create([
        'sensor_id' => $sensor->id,
        'recorded_at' => now(),
        'temperature_c' => 33.0,
        'humidity_pct' => 65.8,
        'lux' => 1200,
        'nitrogen' => 39,
        'phosphorus' => 60,
        'potassium' => 139,
        'pump_status' => 'MATI',
        'kondisi_lahan' => 'Normal',
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->assertSee('Data Riwayat', 'Pencatatan otomatis aktif')
        ->assertSee('1,200 lx')
        ->call('confirmDelete', $row->id)
        ->assertSet('showDeleteModal', true)
        ->call('destroy')
        ->assertSet('showDeleteModal', false);

    expect(Pengukuran::find($row->id))->toBeNull();
});

it('menampilkan menu Riwayat Monitoring di sidebar', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('riwayat.index'))
        ->assertOk()
        ->assertSee('Riwayat Monitoring');
});

it('semua user melihat semua riwayat, termasuk milik user lain', function () {
    $owner = User::factory()->create();
    $sensorA = Sensor::create(['user_id' => $owner->id, 'name' => 'Sensor Greenhouse A']);
    Pengukuran::create([
        'sensor_id' => $sensorA->id,
        'recorded_at' => now(),
        'temperature_c' => 31.0,
        'humidity_pct' => 70.0,
        'lux' => 5,
        'kondisi_lahan' => 'Normal',
    ]);

    $lain = User::factory()->create();
    $sensorB = Sensor::create(['user_id' => $lain->id, 'name' => 'Sensor Nursery']);
    Pengukuran::create([
        'sensor_id' => $sensorB->id,
        'recorded_at' => now(),
        'temperature_c' => 30.0,
        'humidity_pct' => 72.0,
        'lux' => 6,
        'kondisi_lahan' => 'Normal',
    ]);

    $this->actingAs($lain);

    Livewire::test(Index::class)
        ->assertSee('31.0 °C')
        ->assertSee('30.0 °C')
        ->assertSee('Data Riwayat');
});

it('menghapus semua riwayat pada satu tanggal lewat tombol Hapus Semua', function () {
    $user = User::factory()->create();
    $sensor = Sensor::create(['user_id' => $user->id, 'name' => 'Sensor Uji']);

    foreach ([now(), now()->subDay()] as $waktu) {
        Pengukuran::create([
            'sensor_id' => $sensor->id,
            'recorded_at' => $waktu,
            'temperature_c' => 33.0,
            'humidity_pct' => 65.8,
            'kondisi_lahan' => 'Normal',
        ]);
    }

    $tanggal = now()->toDateString();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->assertSee('Hapus Tanggal Ini')
        ->call('confirmBulkDelete')
        ->assertSet('bulkDate', $tanggal)
        ->assertSet('bulkCount', 1)
        ->call('destroyByDate')
        ->assertSet('showBulkModal', false);

    expect(Pengukuran::whereDate('recorded_at', $tanggal)->count())->toBe(0);
    expect(Pengukuran::count())->toBe(1);
});
