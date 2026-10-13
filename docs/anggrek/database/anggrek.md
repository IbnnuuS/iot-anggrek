# Tabel: `anggrek`

## Deskripsi

Menyimpan data koleksi tanaman anggrek beserta ambang batas suhu dan kelembapan ideal untuk setiap jenis. Data ini digunakan sebagai acuan saat menganalisis hasil pengukuran sensor — apakah kondisi lingkungan masih dalam rentang yang aman.

## Migration

`2026_01_12_000002_create_anggrek_table.php`

## Model

`App\Models\Anggrek`

**Catatan:** Model menggunakan `protected $table = 'anggrek'` karena nama tabel tidak mengikuti konvensi plural Laravel.

## Kolom

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (unsigned) | No | auto | Primary key |
| `user_id` | bigint (unsigned) | No | — | FK ke `users.id` (cascade on delete) |
| `sensor_id` | bigint (unsigned) | Yes | null | FK ke `sensors.id` (null on delete) |
| `jenis_anggrek` | varchar(100) | No | — | Jenis/spesies anggrek (wajib) |
| `nama_anggrek` | varchar(100) | Yes | null | Nama individu anggrek |
| `temp_min_c` | decimal(5,2) | Yes | null | Suhu minimum ideal (°C) |
| `temp_max_c` | decimal(5,2) | Yes | null | Suhu maksimum ideal (°C) |
| `hum_min_pct` | decimal(5,2) | Yes | null | Kelembapan minimum ideal (%) |
| `hum_max_pct` | decimal(5,2) | Yes | null | Kelembapan maksimum ideal (%) |
| `created_at` | timestamp | Yes | null | Waktu dibuat |
| `updated_at` | timestamp | Yes | null | Waktu terakhir diubah |

## Index

| Nama | Kolom | Tipe |
|---|---|---|
| PRIMARY | `id` | Primary |
| `anggrek_user_id_index` | `user_id` | Index |
| `anggrek_sensor_id_index` | `sensor_id` | Index |

## Relasi

| Relasi | Tipe | Target | FK |
|---|---|---|---|
| `user()` | BelongsTo | `User` | `anggrek.user_id` |
| `sensor()` | BelongsTo | `Sensor` | `anggrek.sensor_id` |

## Casts

```php
'temp_min_c'  => 'decimal:2',
'temp_max_c'  => 'decimal:2',
'hum_min_pct' => 'decimal:2',
'hum_max_pct' => 'decimal:2',
```

## Validasi (Livewire)

```php
'sensor_id'     => 'nullable|exists:sensors,id',
'jenis_anggrek' => 'required|string|max:100',
'nama_anggrek'  => 'nullable|string|max:100',
'temp_min_c'    => 'nullable|numeric|min:0|max:50|lt:temp_max_c',
'temp_max_c'    => 'nullable|numeric|min:0|max:50|gt:temp_min_c',
'hum_min_pct'   => 'nullable|numeric|min:0|max:100|lt:hum_max_pct',
'hum_max_pct'   => 'nullable|numeric|min:0|max:100|gt:hum_min_pct',
```

**Constraint Logis:**
- `temp_min_c` harus < `temp_max_c`
- `hum_min_pct` harus < `hum_max_pct`
- Suhu dalam rentang 0–50°C
- Kelembapan dalam rentang 0–100%

## Seeder Data

| jenis_anggrek | nama_anggrek | temp_min | temp_max | hum_min | hum_max | sensor_id | user_id |
|---|---|---|---|---|---|---|---|
| Phalaenopsis | Anggrek Bulan Putih | 18.00 | 28.00 | 60.00 | 80.00 | 1 | 1 |
| Dendrobium | Dendrobium Ungu | 20.00 | 30.00 | 50.00 | 70.00 | 1 | 1 |
| Cattleya | Cattleya Pink | 21.00 | 29.00 | 55.00 | 75.00 | 2 | 1 |
| Vanda | Vanda Blue | 22.00 | 32.00 | 60.00 | 85.00 | 2 | 1 |
| Oncidium | Oncidium Kuning | 19.00 | 27.00 | 55.00 | 70.00 | 3 | 1 |
| Paphiopedilum | Paphiopedilum Hijau | 18.00 | 26.00 | 65.00 | 80.00 | 3 | 1 |
| Cymbidium | Cymbidium Merah | 16.00 | 24.00 | 50.00 | 65.00 | 4 | 1 |
| Miltonia | Miltonia Orange | 20.00 | 28.00 | 60.00 | 75.00 | 4 | 1 |
| Brassia | Brassia Spider | 19.00 | 29.00 | 55.00 | 70.00 | 5 | 2 |
| Epidendrum | Epidendrum Mini | 18.00 | 30.00 | 50.00 | 75.00 | null | 2 |
