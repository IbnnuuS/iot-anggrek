# Tabel: `sensors`

## Deskripsi

Menyimpan data perangkat sensor IoT yang digunakan untuk mengukur suhu dan kelembapan di lingkungan budidaya anggrek. Setiap sensor dimiliki oleh satu user dan bisa terhubung ke beberapa anggrek serta pengukuran.

## Migration

`2026_01_12_000001_create_sensors_table.php`

## Model

`App\Models\Sensor`

## Kolom

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (unsigned) | No | auto | Primary key |
| `user_id` | bigint (unsigned) | No | — | FK ke `users.id` (cascade on delete) |
| `name` | varchar(100) | No | — | Nama sensor (wajib) |
| `model` | varchar(50) | Yes | null | Model/tipe perangkat (DHT22, BME280, dll) |
| `serial_no` | varchar(80) | Yes | null | Nomor seri perangkat |
| `mac_address` | varchar(32) | Yes | null | MAC address perangkat (unique) |
| `site_name` | varchar(100) | Yes | null | Nama lokasi utama (greenhouse, lab, dll) |
| `area_name` | varchar(100) | Yes | null | Nama area dalam lokasi |
| `zone_label` | varchar(60) | Yes | null | Label zona |
| `rack_label` | varchar(60) | Yes | null | Label rak |
| `notes` | varchar(255) | Yes | null | Catatan tambahan |
| `created_at` | timestamp | Yes | null | Waktu dibuat |
| `updated_at` | timestamp | Yes | null | Waktu terakhir diubah |

## Index

| Nama | Kolom | Tipe |
|---|---|---|
| PRIMARY | `id` | Primary |
| `sensors_mac_address_unique` | `mac_address` | Unique |
| `sensors_user_id_foreign` | `user_id` | Foreign Key |

## Relasi

| Relasi | Tipe | Target | FK |
|---|---|---|---|
| `user()` | BelongsTo | `User` | `sensors.user_id` |
| `anggrek()` | HasMany | `Anggrek` | `anggrek.sensor_id` |
| `pengukuran()` | HasMany | `Pengukuran` | `pengukuran.sensor_id` |

## Validasi (Livewire)

```php
'name'        => 'required|string|max:100',
'model'       => 'nullable|string|max:50',
'serial_no'   => 'nullable|string|max:80',
'mac_address' => 'nullable|string|max:32|unique:sensors,mac_address',
'site_name'   => 'nullable|string|max:100',
'area_name'   => 'nullable|string|max:100',
'zone_label'  => 'nullable|string|max:60',
'rack_label'  => 'nullable|string|max:60',
'notes'       => 'nullable|string|max:255',
```

## Seeder Data

| name | model | site_name | area_name | user_id |
|---|---|---|---|---|
| Sensor Greenhouse A | DHT22 | Greenhouse Utama | Area A | 1 |
| Sensor Greenhouse B | DHT22 | Greenhouse Utama | Area B | 1 |
| Sensor Nursery | BME280 | Nursery Room | Area C | 1 |
| Sensor Storage | DHT11 | Storage Room | Area D | 1 |
| Sensor Lab | SHT31 | Laboratory | Research Lab | 2 |
