# Tabel: `users`

## Deskripsi

Menyimpan data pengguna aplikasi. Mendukung autentikasi standar Laravel dan Two-Factor Authentication via Fortify.

## Migration

- `0001_01_01_000000_create_users_table.php`
- `2025_09_02_075243_add_two_factor_columns_to_users_table.php`

## Model

`App\Models\User`

## Kolom

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint (unsigned) | No | auto | Primary key |
| `name` | varchar(255) | No | — | Nama lengkap user |
| `email` | varchar(255) | No | — | Email (unique) |
| `email_verified_at` | timestamp | Yes | null | Waktu verifikasi email |
| `password` | varchar(255) | No | — | Password (hashed) |
| `two_factor_secret` | text | Yes | null | Secret key 2FA (encrypted) |
| `two_factor_recovery_codes` | text | Yes | null | Recovery codes 2FA (encrypted) |
| `two_factor_confirmed_at` | timestamp | Yes | null | Waktu konfirmasi 2FA |
| `remember_token` | varchar(100) | Yes | null | Token "remember me" |
| `created_at` | timestamp | Yes | null | Waktu dibuat |
| `updated_at` | timestamp | Yes | null | Waktu terakhir diubah |

## Index

| Nama | Kolom | Tipe |
|---|---|---|
| PRIMARY | `id` | Primary |
| `users_email_unique` | `email` | Unique |

## Relasi

| Relasi | Tipe | Target | FK |
|---|---|---|---|
| `sensors()` | HasMany | `Sensor` | `sensors.user_id` |
| `anggrek()` | HasMany | `Anggrek` | `anggrek.user_id` |

## Casts

```php
'email_verified_at' => 'datetime',
'password' => 'hashed',
```

## Seeder Data

| name | email | password |
|---|---|---|
| Admin Anggrek | admin@anggrek.test | password |
| John Doe | john@anggrek.test | password |
| Jane Smith | jane@anggrek.test | password |
