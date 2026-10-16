<?php

use App\Livewire\Dashboard;
use App\Models\Pengukuran;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function fakeKontrolFirebase(array $control = ['sensor_on' => 1, 'auto_water' => 0, 'pump_seq' => 0, 'pump_duration' => 5]): void
{
    Http::fake([
        '*/control/sensor_on.json' => Http::response(1, 200),
        '*/control/auto_water.json' => Http::response(1, 200),
        '*/control.json' => Http::response($control, 200),
        '*/monitoring.json' => Http::response([
            'suhu_udara' => 28.5,
            'lembap_udara' => 70.2,
            'lux' => 15000,
            'lembap_tanah' => 55.0,
            'suhu_tanah' => 26.0,
            'ec' => 300,
            'ph' => 6.2,
            'n' => 120,
            'p' => 60,
            'k' => 150,
            'status_pompa' => 'MATI',
            'kondisi_lahan' => 'Normal',
        ], 200),
        '*/thresholds.json' => Http::response([], 200),
    ]);
}

it('menampilkan tombol kontrol perangkat di dashboard', function () {
    fakeKontrolFirebase();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Mulai Monitoring')
        ->assertSee('Hentikan')
        ->assertSee('Penyiraman Otomatis')
        ->assertSee('Nyalakan Otomatis')
        ->assertSee('Matikan Otomatis');
});

it('tombol stop mengirim perintah sensor_on=0 ke Firebase', function () {
    fakeKontrolFirebase();
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->call('stopMonitoring')
        ->assertSet('control.sensor_on', 0);

    Http::assertSent(fn ($request) => $request->method() === 'PUT'
        && str_contains($request->url(), '/control/sensor_on.json')
        && trim($request->body()) === '0');
});

it('tombol mulai mengirim perintah sensor_on=1 ke Firebase', function () {
    fakeKontrolFirebase(['sensor_on' => 0, 'pump_seq' => 0, 'pump_duration' => 5]);
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->call('startMonitoring')
        ->assertSet('control.sensor_on', 1);

    Http::assertSent(fn ($request) => $request->method() === 'PUT'
        && str_contains($request->url(), '/control/sensor_on.json')
        && trim($request->body()) === '1');
});

it('tombol nyalakan otomatis mengirim auto_water=1 ke Firebase', function () {
    fakeKontrolFirebase();
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->call('startAutoWater')
        ->assertSet('control.auto_water', 1);

    Http::assertSent(fn ($request) => $request->method() === 'PUT'
        && str_contains($request->url(), '/control/auto_water.json')
        && trim($request->body()) === '1');
});

it('tombol matikan otomatis mengirim auto_water=0 ke Firebase', function () {
    fakeKontrolFirebase(['sensor_on' => 1, 'auto_water' => 1, 'pump_seq' => 0, 'pump_duration' => 5]);
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->call('stopAutoWater')
        ->assertSet('control.auto_water', 0);

    Http::assertSent(fn ($request) => $request->method() === 'PUT'
        && str_contains($request->url(), '/control/auto_water.json')
        && trim($request->body()) === '0');
});

it('firebase:sync tidak menyimpan riwayat saat monitoring nonaktif', function () {
    fakeKontrolFirebase(['sensor_on' => 0, 'pump_seq' => 0, 'pump_duration' => 5]);

    $this->artisan('firebase:sync')->assertExitCode(0);

    expect(Pengukuran::count())->toBe(0);
});

it('firebase:sync menyimpan riwayat saat monitoring aktif', function () {
    fakeKontrolFirebase();

    $user = User::factory()->create();
    Sensor::create(['user_id' => $user->id, 'name' => 'Sensor Uji']);

    $this->artisan('firebase:sync')->assertExitCode(0);

    expect(Pengukuran::count())->toBe(1);
});
