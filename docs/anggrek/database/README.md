# Dokumentasi Database

## Ringkasan

Aplikasi Anggrek menggunakan 7 tabel utama (+ tabel support). Semua migrasi menggunakan Laravel migration dan dijalankan secara berurutan.

## Daftar Tabel

| Tabel | Deskripsi | Migration |
|---|---|---|
| `users` | Data pengguna aplikasi | `0001_01_01_000000_create_users_table.php` |
| `password_reset_tokens` | Token reset password | `0001_01_01_000000_create_users_table.php` |
| `sessions` | Session management | `0001_01_01_000000_create_users_table.php` |
| `cache` | Cache storage | `0001_01_01_000001_create_cache_table.php` |
| `jobs` / `job_batches` / `failed_jobs` | Queue system | `0001_01_01_000002_create_jobs_table.php` |
| `sensors` | Perangkat sensor IoT | `2026_01_12_000001_create_sensors_table.php` |
| `anggrek` | Data koleksi anggrek & ambang batas | `2026_01_12_000002_create_anggrek_table.php` |
| `pengukuran` | Data pengukuran suhu & kelembapan | `2026_01_12_000003_create_pengukuran_table.php` |

## Entity Relationship Diagram (ERD)

```
┌──────────┐       ┌──────────┐       ┌──────────────┐
│  users   │       │ sensors  │       │  pengukuran  │
├──────────┤       ├──────────┤       ├──────────────┤
│ id (PK)  │──┐    │ id (PK)  │──┐    │ id (PK)      │
│ name     │  │    │ user_id  │  │    │ sensor_id    │
│ email    │  ├───>│ name     │  ├───>│ recorded_at  │
│ password │  │    │ model    │  │    │ temperature_c│
│ ...      │  │    │ ...      │  │    │ humidity_pct │
└──────────┘  │    └──────────┘  │    └──────────────┘
              │                  │
              │    ┌──────────┐  │
              │    │ anggrek  │  │
              │    ├──────────┤  │
              │    │ id (PK)  │  │
              ├───>│ user_id  │  │
              │    │ sensor_id│<─┘
              │    │ jenis    │
              │    │ ...      │
              │    └──────────┘
```

### Relasi

| Relasi | Tipe | Keterangan |
|---|---|---|
| `users` → `sensors` | One-to-Many | Satu user memiliki banyak sensor |
| `users` → `anggrek` | One-to-Many | Satu user memiliki banyak anggrek |
| `sensors` → `pengukuran` | One-to-Many | Satu sensor memiliki banyak pengukuran |
| `sensors` → `anggrek` | One-to-Many | Satu sensor bisa terkait banyak anggrek |

### Foreign Key Behavior

| FK | On Delete |
|---|---|
| `sensors.user_id` → `users.id` | CASCADE (hapus user → hapus sensors) |
| `anggrek.user_id` → `users.id` | CASCADE (hapus user → hapus anggrek) |
| `anggrek.sensor_id` → `sensors.id` | SET NULL (hapus sensor → sensor_id jadi null) |
| `pengukuran.sensor_id` → `sensors.id` | CASCADE (hapus sensor → hapus pengukuran) |

## Dokumentasi Per Tabel

Lihat file terpisah untuk detail setiap tabel:

- [users.md](users.md) — Tabel pengguna
- [sensors.md](sensors.md) — Tabel sensor
- [anggrek.md](anggrek.md) — Tabel anggrek
- [pengukuran.md](pengukuran.md) — Tabel pengukuran
