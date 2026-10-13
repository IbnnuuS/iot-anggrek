<?php

use App\Models\Pengukuran;
use App\Models\Sensor;
use App\Models\User;

test('sensor can store a measurement via API', function () {
    $user = User::factory()->create();
    $sensor = Sensor::create([
        'user_id' => $user->id,
        'name' => 'Test Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:FF',
    ]);

    $response = $this->postJson('/api/v1/pengukuran', [
        'mac_address' => 'AA:BB:CC:DD:EE:FF',
        'temperature_c' => 25.50,
        'humidity_pct' => 65.00,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.sensor_id', $sensor->id)
        ->assertJsonPath('data.temperature_c', '25.50')
        ->assertJsonPath('data.humidity_pct', '65.00');

    $this->assertDatabaseHas('pengukuran', [
        'sensor_id' => $sensor->id,
        'temperature_c' => 25.50,
        'humidity_pct' => 65.00,
    ]);
});

test('sensor can store a measurement with custom recorded_at', function () {
    $user = User::factory()->create();
    Sensor::create([
        'user_id' => $user->id,
        'name' => 'Test Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:01',
    ]);

    $response = $this->postJson('/api/v1/pengukuran', [
        'mac_address' => 'AA:BB:CC:DD:EE:01',
        'temperature_c' => 22.00,
        'humidity_pct' => 70.00,
        'recorded_at' => '2026-03-25 10:30:00',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('pengukuran', [
        'recorded_at' => '2026-03-25 10:30:00',
    ]);
});

test('store measurement fails with unknown mac_address', function () {
    $response = $this->postJson('/api/v1/pengukuran', [
        'mac_address' => 'XX:XX:XX:XX:XX:XX',
        'temperature_c' => 25.00,
        'humidity_pct' => 65.00,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['mac_address']);
});

test('store measurement fails without required fields', function () {
    $response = $this->postJson('/api/v1/pengukuran', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['mac_address', 'temperature_c', 'humidity_pct']);
});

test('store measurement fails with out-of-range temperature', function () {
    $user = User::factory()->create();
    Sensor::create([
        'user_id' => $user->id,
        'name' => 'Test Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:02',
    ]);

    $response = $this->postJson('/api/v1/pengukuran', [
        'mac_address' => 'AA:BB:CC:DD:EE:02',
        'temperature_c' => 100.00,
        'humidity_pct' => 65.00,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['temperature_c']);
});

test('store measurement fails with out-of-range humidity', function () {
    $user = User::factory()->create();
    Sensor::create([
        'user_id' => $user->id,
        'name' => 'Test Sensor',
        'mac_address' => 'AA:BB:CC:DD:EE:03',
    ]);

    $response = $this->postJson('/api/v1/pengukuran', [
        'mac_address' => 'AA:BB:CC:DD:EE:03',
        'temperature_c' => 25.00,
        'humidity_pct' => 150.00,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['humidity_pct']);
});
