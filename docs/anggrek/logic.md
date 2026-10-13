# Logika Aplikasi Anggrek

## Ringkasan

Aplikasi **Anggrek** adalah sistem monitoring lingkungan untuk budidaya anggrek (orchid). Aplikasi ini memungkinkan pengguna mengelola sensor IoT, mendaftarkan koleksi anggrek beserta ambang batas suhu & kelembapan ideal, lalu memantau data pengukuran dari sensor untuk mendeteksi apakah kondisi lingkungan masih dalam rentang aman bagi setiap jenis anggrek.

---

## Arsitektur

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.4 |
| Frontend | Livewire 3, Volt, Flux UI Pro, Tailwind CSS 4 |
| Auth | Laravel Fortify (+ Two-Factor Authentication) |
| Testing | Pest 4, PHPUnit 12 |

Semua halaman interaktif menggunakan **Livewire full-page components** yang di-mount langsung di route (bukan controller tradisional).

---

## Alur Bisnis Utama

### 1. Autentikasi & Otorisasi

- Registrasi, login, dan reset password ditangani oleh **Laravel Fortify**.
- Two-Factor Authentication (2FA) tersedia via kolom `two_factor_secret` dan `two_factor_recovery_codes` di tabel `users`.
- Semua route manajemen data dilindungi middleware `auth` dan `verified`.
- **Otorisasi level data**: setiap Livewire component memverifikasi bahwa `user_id` record sama dengan `Auth::id()`. Jika tidak cocok, request di-abort dengan `403 Forbidden`.

### 2. Manajemen Sensor

**Namespace:** `App\Livewire\Sensor`

| Komponen | Fungsi |
|---|---|
| `Index` | Menampilkan daftar sensor milik user dengan search, filter site, filter area, dan paginasi (10/page). |
| `Create` | Form untuk menambah sensor baru. Validasi: `name` wajib, `mac_address` unik. |
| `Edit` | Form edit sensor. Cek kepemilikan di `mount()`. Validasi `mac_address` unik (kecuali record sendiri). |
| `Delete` | Modal konfirmasi hapus sensor. Cek kepemilikan di `destroy()`. Dispatch event `sensorDeleted` setelah berhasil. |

**Alur Create:**
1. User mengisi form (nama, model, serial number, MAC address, lokasi, catatan).
2. Validasi input (termasuk `mac_address` unik di tabel `sensors`).
3. `user_id` diisi otomatis dari `Auth::id()`.
4. Record `Sensor` di-create lalu redirect ke halaman index.

**Fitur Pencarian & Filter (Index):**
- Search: mencari di kolom `name`, `model`, `serial_no`, `mac_address`.
- Filter Site: filter berdasarkan `site_name`.
- Filter Area: filter berdasarkan `area_name`.
- Semua filter tersimpan di query string.

### 3. Manajemen Anggrek

**Namespace:** `App\Livewire\Anggrek`

| Komponen | Fungsi |
|---|---|
| `Index` | Daftar anggrek milik user dengan search, filter sensor, dan paginasi (10/page). Eager load relasi `sensor`. |
| `Create` | Form tambah anggrek. Dropdown sensor diambil dari sensor milik user. |
| `Edit` | Form edit anggrek. Cek kepemilikan di `mount()`. |
| `Delete` | Modal konfirmasi hapus. Dispatch event `anggrekDeleted`. |

**Validasi Khusus:**
- `temp_min_c` harus lebih kecil dari `temp_max_c` (rule `lt`).
- `temp_max_c` harus lebih besar dari `temp_min_c` (rule `gt`).
- `hum_min_pct` harus lebih kecil dari `hum_max_pct`.
- `hum_max_pct` harus lebih besar dari `hum_min_pct`.
- Suhu: 0–50°C. Kelembapan: 0–100%.
- Sensor bersifat opsional (`nullable`), satu anggrek bisa tanpa sensor.

**Pesan Error Custom (Bahasa Indonesia):**
- "Temperature minimum harus lebih kecil dari maximum."
- "Humidity minimum harus lebih kecil dari maximum."

### 4. Monitoring Pengukuran

**Namespace:** `App\Livewire\Pengukuran`

| Komponen | Fungsi |
|---|---|
| `Index` | Daftar semua pengukuran dari sensor milik user dengan filter sensor, rentang tanggal, dan paginasi (50/page). |
| `Detail` | Detail satu pengukuran beserta analisis apakah nilai masih dalam rentang ideal anggrek. |

**Logika Analisis pada Detail:**

Ini adalah **core logic** utama aplikasi. Ketika user membuka detail pengukuran:

1. Ambil data anggrek pertama (`->first()`) yang terkait dengan sensor pengukuran tersebut.
2. Jika anggrek memiliki ambang batas, bandingkan nilai pengukuran:

   | Kondisi | Deskripsi | Severity |
   |---|---|---|
   | `temperature_c < temp_min_c` | Suhu terlalu rendah | `low` |
   | `temperature_c > temp_max_c` | Suhu terlalu tinggi | `high` |
   | `humidity_pct < hum_min_pct` | Kelembapan terlalu rendah | `low` |
   | `humidity_pct > hum_max_pct` | Kelembapan terlalu tinggi | `high` |

3. Jika ada pelanggaran (`violation`), `isInRange` menjadi `false` dan daftar `violations` dikirim ke view.
4. Jika tidak ada anggrek terkait, semua pengukuran dianggap `isInRange = true`.

**Filter Pengukuran (Index):**
- Filter sensor: berdasarkan `sensor_id`.
- Filter tanggal: `dateFrom` dan `dateTo` pada kolom `recorded_at`.
- Data diurutkan dari yang terbaru (`recorded_at desc`).
- Akses dibatasi: hanya pengukuran dari sensor milik user (`whereHas('sensor', ...)` dengan cek `user_id`).

---

## Routing

Semua route didefinisikan di `routes/web.php`:

| Method | URI | Komponen | Route Name |
|---|---|---|---|
| GET | `/` | welcome view | `home` |
| GET | `/dashboard` | dashboard view | `dashboard` |
| GET | `/sensors` | `Sensor\Index` | `sensors.index` |
| GET | `/sensors/create` | `Sensor\Create` | `sensors.create` |
| GET | `/sensors/{sensor}/edit` | `Sensor\Edit` | `sensors.edit` |
| GET | `/anggrek` | `Anggrek\Index` | `anggrek.index` |
| GET | `/anggrek/create` | `Anggrek\Create` | `anggrek.create` |
| GET | `/anggrek/{anggrek}/edit` | `Anggrek\Edit` | `anggrek.edit` |
| GET | `/pengukuran` | `Pengukuran\Index` | `pengukuran.index` |
| GET | `/pengukuran/{pengukuran}` | `Pengukuran\Detail` | `pengukuran.detail` |

Semua route (kecuali `/` dan auth routes) memerlukan middleware `auth` dan `verified`.

---

## Pola Keamanan

1. **Ownership Check**: setiap operasi CRUD memvalidasi `user_id === Auth::id()`.
2. **Cascade Delete**: menghapus user akan cascade ke sensors, menghapus sensor akan cascade ke pengukuran, dan null-kan `sensor_id` di anggrek.
3. **MAC Address Unique**: mencegah duplikasi sensor di database.
4. **Validasi Server-Side**: semua input divalidasi di Livewire component menggunakan Laravel validation rules.
5. **Middleware Auth**: route dilindungi middleware `auth` dan `verified`.

---

## Seeders

Urutan eksekusi seeder:

1. `UserSeeder` → 3 user (admin, john, jane)
2. `SensorSeeder` → 5 sensor (4 milik user 1, 1 milik user 2)
3. `AnggrekSeeder` → 10 anggrek (8 milik user 1, 2 milik user 2)
4. `PengukuranSeeder` → 100 pengukuran (20 per sensor, 7 hari terakhir, data random dengan variasi)
