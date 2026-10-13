# Tabel: `pengukuran`

## Deskripsi

Menyimpan data pengukuran suhu dan kelembapan yang dicatat oleh sensor. Tabel ini **tidak menggunakan timestamps** (`created_at`/`updated_at`) — waktu pencatatan disimpan di kolom `recorded_at`. Data pengukuran digunakan sebagai bahan analisis apakah kondisi lingkungan masih dalam rentang ideal untuk jenis anggrek yang terkait dengan sensor tersebut.

## Migration

`2026_01_12_000003_create_pengukuran_table.php`

## Model

`App\Models\Pengukuran`

**Catatan:**
- Model menggunakan `protected $table = 'pengukuran'` (nama tabel non-plural).
- `public $timestamps = false` — tidak ada kolom `created_at`/`updated_at`.

## Kolom

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (unsigned) | No | auto | Primary key |
| `sensor_id` | bigint (unsigned) | No | — | FK ke `sensors.id` (cascade on delete) |
| `recorded_at` | datetime | No | — | Waktu pencatatan pengukuran |
| `temperature_c` | decimal(5,2) | No | — | Suhu dalam derajat Celsius |
| `humidity_pct` | decimal(5,2) | No | — | Kelembapan dalam persen |

## Index

| Nama | Kolom | Tipe |
|---|---|---|
| PRIMARY | `id` | Primary |
| `pengukuran_sensor_id_recorded_at_index` | `sensor_id`, `recorded_at` | Composite Index |

**Composite index** pada `(sensor_id, recorded_at)` mengoptimalkan query filter berdasarkan sensor dan rentang waktu.

## Relasi

| Relasi | Tipe | Target | FK |
|---|---|---|---|
| `sensor()` | BelongsTo | `Sensor` | `pengukuran.sensor_id` |

## Casts

```php
'recorded_at'   => 'datetime',
'temperature_c' => 'decimal:2',
'humidity_pct'  => 'decimal:2',
```

## Logika Analisis

Pada halaman detail pengukuran (`Livewire\Pengukuran\Detail`), data pengukuran dianalisis terhadap ambang batas anggrek yang terkait:

```
Jika sensor memiliki anggrek terkait:
  - temperature_c < temp_min_c → "Suhu terlalu rendah"
  - temperature_c > temp_max_c → "Suhu terlalu tinggi"
  - humidity_pct  < hum_min_pct → "Kelembapan terlalu rendah"
  - humidity_pct  > hum_max_pct → "Kelembapan terlalu tinggi"
```

## Seeder Data

Seeder menghasilkan **100 record** (20 per sensor × 5 sensor):

| Parameter | Nilai |
|---|---|
| Rentang waktu | 7 hari terakhir (random) |
| Base temperature | 25.0°C ± 7°C + noise |
| Base humidity | 70.0% ± 20% + noise |
| Clamp temperature | 10°C – 40°C |
| Clamp humidity | 30% – 100% |

Data sengaja memiliki variasi dan outlier untuk menghasilkan beberapa pelanggaran ambang batas saat dianalisis.
