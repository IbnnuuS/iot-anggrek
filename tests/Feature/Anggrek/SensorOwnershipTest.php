<?php

use App\Models\Anggrek;
use App\Models\Sensor;
use App\Models\User;
use Livewire\Livewire;

test('user cannot create anggrek with another users sensor', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $otherSensor = Sensor::create([
        'user_id' => $owner->id,
        'name' => 'Owner Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:01',
    ]);

    Livewire::actingAs($attacker)
        ->test(\App\Livewire\Anggrek\Create::class)
        ->set('sensor_id', $otherSensor->id)
        ->set('jenis_anggrek', 'Phalaenopsis')
        ->set('temp_min_c', 18)
        ->set('temp_max_c', 30)
        ->set('hum_min_pct', 50)
        ->set('hum_max_pct', 80)
        ->call('store')
        ->assertHasErrors(['sensor_id']);

    expect(Anggrek::where('sensor_id', $otherSensor->id)->where('user_id', $attacker->id)->exists())->toBeFalse();
});

test('user can create anggrek with their own sensor', function () {
    $user = User::factory()->create();

    $sensor = Sensor::create([
        'user_id' => $user->id,
        'name' => 'My Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:02',
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Anggrek\Create::class)
        ->set('sensor_id', $sensor->id)
        ->set('jenis_anggrek', 'Dendrobium')
        ->set('temp_min_c', 18)
        ->set('temp_max_c', 30)
        ->set('hum_min_pct', 50)
        ->set('hum_max_pct', 80)
        ->call('store')
        ->assertHasNoErrors();

    expect(Anggrek::where('sensor_id', $sensor->id)->where('user_id', $user->id)->exists())->toBeTrue();
});

test('user can create anggrek without a sensor', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\Anggrek\Create::class)
        ->set('sensor_id', '')
        ->set('jenis_anggrek', 'Vanda')
        ->set('temp_min_c', 18)
        ->set('temp_max_c', 30)
        ->set('hum_min_pct', 50)
        ->set('hum_max_pct', 80)
        ->call('store')
        ->assertHasNoErrors();

    expect(Anggrek::where('jenis_anggrek', 'Vanda')->where('user_id', $user->id)->exists())->toBeTrue();
});

test('user cannot edit anggrek to use another users sensor', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $otherSensor = Sensor::create([
        'user_id' => $owner->id,
        'name' => 'Owner Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:03',
    ]);

    $anggrek = Anggrek::create([
        'user_id' => $attacker->id,
        'jenis_anggrek' => 'Cattleya',
        'temp_min_c' => 18,
        'temp_max_c' => 30,
        'hum_min_pct' => 50,
        'hum_max_pct' => 80,
    ]);

    Livewire::actingAs($attacker)
        ->test(\App\Livewire\Anggrek\Edit::class, ['anggrek' => $anggrek])
        ->set('sensor_id', $otherSensor->id)
        ->call('update')
        ->assertHasErrors(['sensor_id']);

    expect($anggrek->fresh()->sensor_id)->toBeNull();
});

test('user can edit anggrek to use their own sensor', function () {
    $user = User::factory()->create();

    $sensor = Sensor::create([
        'user_id' => $user->id,
        'name' => 'My Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:04',
    ]);

    $anggrek = Anggrek::create([
        'user_id' => $user->id,
        'jenis_anggrek' => 'Oncidium',
        'temp_min_c' => 18,
        'temp_max_c' => 30,
        'hum_min_pct' => 50,
        'hum_max_pct' => 80,
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Anggrek\Edit::class, ['anggrek' => $anggrek])
        ->set('sensor_id', $sensor->id)
        ->call('update')
        ->assertHasNoErrors();

    expect($anggrek->fresh()->sensor_id)->toBe($sensor->id);
});
