# IoT Anggrek - Sistem Monitoring Lingkungan Budidaya Anggrek

Sistem monitoring telemetri lingkungan berbasis IoT (Internet of Things) yang dirancang khusus untuk budidaya tanaman anggrek. Sistem ini mengintegrasikan mikrokontroler ESP32, sensor mikroklimat (suhu, kelembapan, parameter tanah NPK/pH/EC), Firebase Realtime Database, dan web dashboard berbasis Laravel 12 untuk pengawasan kondisi lingkungan secara real-time serta deteksi anomali ambang batas toleransi anggrek.

---

## 1. Arsitektur dan Tumpukan Teknologi

### Backend
- Framework: Laravel 12.x
- Runtime: PHP 8.4
- Autentikasi: Laravel Fortify (dengan dukungan Two-Factor Authentication / 2FA)
- CLI & Background Task: Laravel Artisan Command (`firebase:sync`)

### Frontend
- Komponen Reaktif: Livewire 3 (Full-page components & Volt)
- Desain Sistem: Flux UI Pro
- CSS Framework: Tailwind CSS 4
- Bundler: Vite

### IoT & Telemetri
- Mikrokontroler: ESP32 (NodeMCU / DOIT ESP32 DevKit)
- Sensor Udara: DHT22 (Suhu dan Kelembapan Relatif)
- Sensor Tanah & Nutrisi: Sensor NPK, Soil Moisture, pH, EC (mendukung integrasi data Firebase)
- Integrasi Cloud: Firebase Realtime Database (REST API via HTTP Client Laravel)
- Protokol Komunikasi: HTTP/REST (JSON payload via WiFi)

### Kualitas Kode dan Pengujian
- Test Runner: Pest 4 & PHPUnit 12
- Linter & Code Style: Laravel Pint

---

## 2. Struktur Direktori Utama

```
anggrek/
|-- app/
|   |-- Actions/Fortify/          # Logika registrasi, reset password, dan aturan validasi user
|   |-- Console/Commands/         # Artisan command: FirebaseSyncCommand
|   |-- Http/Controllers/Api/V1/  # Controller API ingestion telemetri (PengukuranController)
|   |-- Http/Requests/Api/V1/     # Form request validasi payload telemetri IoT
|   |-- Livewire/                 # Komponen Livewire dashboard, sensor, anggrek, pengukuran
|   |-- Models/                   # Model Eloquent: User, Sensor, Anggrek, Pengukuran
|   `-- Services/                 # FirebaseService untuk interaksi Realtime Database
|-- arduino/
|   `-- esp32_anggrek_sensor.ino  # Firmware ESP32 pengirim data sensor DHT22
|-- config/                       # Konfigurasi aplikasi, database, dan services (Firebase)
|-- database/
|   |-- migrations/               # Skema migrasi tabel users, sensors, anggrek, pengukuran
|   `-- seeders/                  # Seeder data pengujian
|-- docs/                         # Dokumentasi internal database, logic, dan arduino
|-- resources/                    # Blade views, Volt components, Tailwind CSS
|-- routes/
|   |-- api.php                   # Endpoint REST API ingestion telemetri IoT
|   |-- console.php               # Konfigurasi task scheduler
|   |-- settings.php              # Route profil, password, dan 2FA
|   `-- web.php                   # Route web aplikasi & Livewire components
`-- tests/                        # Pest unit dan feature tests
```

---

## 3. Skema Basis Data dan Relasi

Aplikasi menggunakan arsitektur relasional dengan isolasi kepemilikan multi-user.

### Relasi Antar Entitas
- `User` memiliki banyak `Sensor` (1 to N, cascade on delete).
- `User` memiliki banyak `Anggrek` (1 to N, cascade on delete).
- `Sensor` dimiliki oleh 1 `User`.
- `Sensor` dapat diasosiasikan dengan banyak `Anggrek` (1 to N, nullable, null on delete).
- `Sensor` memiliki banyak `Pengukuran` (1 to N, cascade on delete).
- `Pengukuran` terikat ke 1 `Sensor`.

### Detail Struktur Tabel

#### Tabel: `users`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary key (auto increment) |
| `name` | VARCHAR(255) | Nama lengkap pengguna |
| `email` | VARCHAR(255) | Alamat email unik |
| `password` | VARCHAR(255) | Hash password |
| `two_factor_secret` | TEXT | Kunci enkripsi 2FA (nullable) |
| `two_factor_recovery_codes` | TEXT | Kode pemulihan 2FA (nullable) |
| `created_at` / `updated_at` | TIMESTAMP | Timestamp standar Laravel |

#### Tabel: `sensors`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary key |
| `user_id` | BIGINT UNSIGNED | Foreign key ke `users.id` (cascade on delete) |
| `name` | VARCHAR(100) | Label identitas sensor |
| `model` | VARCHAR(50) | Tipe perangkat/hardware (nullable) |
| `serial_no` | VARCHAR(80) | Nomor seri perangkat (nullable) |
| `mac_address` | VARCHAR(32) | Identitas hardware unik perangkat ESP32 |
| `site_name` | VARCHAR(100) | Nama lokasi/greenhouse (nullable) |
| `area_name` | VARCHAR(100) | Area spesifik dalam greenhouse (nullable) |
| `zone_label` | VARCHAR(60) | Zona penempatan (nullable) |
| `rack_label` | VARCHAR(60) | Label rak penempatan (nullable) |
| `notes` | VARCHAR(255) | Catatan operasional (nullable) |
| `created_at` / `updated_at` | TIMESTAMP | Timestamp standar Laravel |

#### Tabel: `anggrek`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary key |
| `user_id` | BIGINT UNSIGNED | Foreign key ke `users.id` (cascade on delete) |
| `sensor_id` | BIGINT UNSIGNED | Foreign key ke `sensors.id` (nullable, set null on delete) |
| `jenis_anggrek` | VARCHAR(100) | Taksonomi/spesies anggrek (contoh: Phalaenopsis, Dendrobium) |
| `nama_anggrek` | VARCHAR(100) | Nama identifikasi tanaman (nullable) |
| `temp_min_c` | DECIMAL(5,2) | Ambang batas suhu minimum ideal dalam Celsius |
| `temp_max_c` | DECIMAL(5,2) | Ambang batas suhu maksimum ideal dalam Celsius |
| `hum_min_pct` | DECIMAL(5,2) | Ambang batas kelembapan udara minimum ideal (%) |
| `hum_max_pct` | DECIMAL(5,2) | Ambang batas kelembapan udara maksimum ideal (%) |
| `created_at` / `updated_at` | TIMESTAMP | Timestamp standar Laravel |

#### Tabel: `pengukuran`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary key |
| `sensor_id` | BIGINT UNSIGNED | Foreign key ke `sensors.id` (cascade on delete) |
| `recorded_at` | DATETIME | Waktu pengambilan sampel telemetri |
| `temperature_c` | DECIMAL(5,2) | Suhu udara terukur (°C) |
| `humidity_pct` | DECIMAL(5,2) | Kelembapan udara relatif terukur (%) |
| `soil_moisture_pct`| DECIMAL(5,2) | Kelembapan media tanam (%) (nullable) |
| `soil_temperature_c`| DECIMAL(5,2) | Suhu media tanam (°C) (nullable) |
| `soil_ec` | INT | Konduktivitas listrik tanah (µs/cm) (nullable) |
| `soil_ph` | DECIMAL(4,2) | Derajat keasaman media tanam (pH) (nullable) |
| `nitrogen` | INT | Kadar Nitrogen tanah (ppm) (nullable) |
| `phosphorus` | INT | Kadar Fosfor tanah (ppm) (nullable) |
| `potassium` | INT | Kadar Kalium tanah (ppm) (nullable) |
| `pump_status` | VARCHAR(255) | Status aktuator pompa (ON/OFF/AUTO) (nullable) |
| `kondisi_lahan` | VARCHAR(255) | Status ringkasan kesuburan/kondisi lingkungan (nullable) |

---

## 4. Logika Bisnis dan Fitur Sistem

### 4.1. Autentikasi dan Kontrol Akses
- Registrasi, autentikasi sesi, reset kata sandi, dan pengelolaan 2FA terisolasi per akun.
- Validasi kepemilikan data diterapkan pada seluruh komponen Livewire: akses data entitas sensor, anggrek, dan pengukuran milik pengguna lain akan ditolak langsung dengan status HTTP `403 Forbidden`.

### 4.2. Manajemen Sensor
- Registrasi sensor baru dengan keunikan `mac_address`.
- Pengelompokan sensor berdasarkan site, area, zona, dan rak.
- Fitur pencarian instan dan penyaringan multi-parameter berbasis query string.

### 4.3. Manajemen Anggrek dan Ambang Batas Ideal
- Pendaftaran varietas anggrek disertai penentuan ambang batas toleransi iklim mikro (`temp_min_c`, `temp_max_c`, `hum_min_pct`, `hum_max_pct`).
- Validasi data server-side memastikan nilai minimum selalu lebih kecil daripada nilai maksimum (`temp_min_c < temp_max_c` dan `hum_min_pct < hum_max_pct`).
- Asosiasi fleksibel: anggrek dapat dihubungkan ke sensor spesifik atau berdiri sendiri.

### 4.4. Analisis Pelanggaran Ambang Batas (Violation Engine)
Pada modul rincian pengukuran (`App\Livewire\Pengukuran\Detail`), data sensor dievaluasi terhadap parameter ideal anggrek terasosiasi:

| Kondisi | Status Pelanggaran | Severity |
|---|---|---|
| `temperature_c < temp_min_c` | Suhu di bawah batas aman | `low` |
| `temperature_c > temp_max_c` | Suhu melebihi batas toleransi | `high` |
| `humidity_pct < hum_min_pct` | Kelembapan udara terlalu rendah | `low` |
| `humidity_pct > hum_max_pct` | Kelembapan udara terlalu tinggi | `high` |

Jika satu atau lebih parameter melanggar ambang batas, bendera status `isInRange` disetel ke `false` dan daftar rincian anomali dipaparkan untuk tindakan mitigasi pengguna.

### 4.5. Sinkronisasi Data Firebase Realtime Database
- Layanan `FirebaseService` berkomunikasi langsung dengan Firebase RTDB melalui REST endpoint `monitoring.json` dan `thresholds.json`.
- Perintah CLI `php artisan firebase:sync` membaca data telemetri komprehensif (NPK, pH, EC, suhu, kelembapan, status pompa) dari Firebase lalu mengonversinya menjadi record permanen di tabel `pengukuran`.
- Halaman pengaturan threshold (`/thresholds`) mengizinkan pembaruan nilai batas operasional langsung ke Firebase RTDB.

---

## 5. Integrasi Perangkat IoT (Firmware ESP32)

Firmware mikrokontroler terletak pada berkas `arduino/esp32_anggrek_sensor.ino`.

### 5.1. Skema Pengkabelan (Hardware Wiring)

```
ESP32 DevKit                  DHT22 Sensor
------------------------      ---------------------------------
Pin 3V3 (3.3V Power)    ----> Pin 1: VCC
Pin GPIO 4 (Data In/Out)----> Pin 2: DATA  (+ Resistor Pull-up 10k Ohm ke 3.3V)
Tidak Terhubung (NC)          Pin 3: Tidak Digunakan
Pin GND (Ground)        ----> Pin 4: GND
Pin GPIO 2 (Onboard LED)----> Indikator Status Operasional Internal
```

### 5.2. Kebutuhan Library Arduino
Pasang pustaka berikut melalui Arduino IDE Library Manager:
- `DHT sensor library` oleh Adafruit
- `Adafruit Unified Sensor` oleh Adafruit
- `ArduinoJson` (Versi 7.x) oleh Benoit Blanchon

### 5.3. Konfigurasi Parameter Firmware
Buka berkas `arduino/esp32_anggrek_sensor.ino` dan sesuaikan parameter berikut sebelum melakukan compile dan upload:

```cpp
// Kredensial Jaringan WiFi
const char* WIFI_SSID     = "NAMA_WIFI_ANDA";
const char* WIFI_PASSWORD = "PASSWORD_WIFI_ANDA";

// Alamat Server Laravel
const char* API_BASE_URL  = "http://192.168.1.10:8000";
const char* API_ENDPOINT  = "/api/v1/pengukuran";

// Identitas Sensor (Wajib terdaftar di tabel sensors database)
const char* SENSOR_MAC_ADDRESS = "A1:B2:C3:D4:E5:F1";

// Interval Pengiriman Data (Milidetik)
const unsigned long SEND_INTERVAL_MS = 60000; // 60 detik

// Konfigurasi Hardware
#define DHT_PIN  4
#define DHT_TYPE DHT22
#define LED_PIN  2
```

### 5.4. Pola Diagnostik LED (GPIO 2)
| Pola Kedipan | Makna Operasional |
|---|---|
| Kedip berulang kontinu saat startup | Perangkat sedang proses menghubungkan ke jaringan WiFi |
| 1 kali kedip panjang (500 ms) | Paket data telemetri berhasil terkirim dan diterima API (HTTP 201) |
| 3 kali kedip sedang (200 ms) | Gagal mengirimkan paket ke API setelah seluruh batas percobaan ulang (retry) terlampaui |
| 5 kali kedip cepat (100 ms) | Pembacaan data dari sensor DHT22 gagal atau bernilai NaN |

---

## 6. Spesifikasi REST API Telemetri

### Ingestion Data Pengukuran
Menerima kiriman sampel data telemetri dari perangkat mikrokontroler.

- **URL:** `/api/v1/pengukuran`
- **Method:** `POST`
- **Rate Limit:** 60 request per menit per alamat IP
- **Headers:**
  ```http
  Content-Type: application/json
  Accept: application/json
  ```

#### Parameter Request (Body JSON)
| Field | Tipe Data | Wajib/Opsional | Deskripsi |
|---|---|---|---|
| `mac_address` | String | Wajib | MAC address sensor terdaftar di tabel `sensors` |
| `temperature_c` | Numeric | Wajib | Nilai suhu udara dalam rentang -40 hingga 80 |
| `humidity_pct` | Numeric | Wajib | Nilai kelembapan relatif dalam rentang 0 hingga 100 |
| `recorded_at` | Date/String | Opsional | Format `YYYY-MM-DD HH:MM:SS` (default: timestamp saat request diterima) |
| `soil_moisture_pct` | Numeric | Opsional | Nilai kelembapan tanah (0 - 100 %) |
| `soil_temperature_c` | Numeric | Opsional | Nilai suhu tanah (-20 - 80 °C) |
| `soil_ec` | Integer | Opsional | Konduktivitas listrik tanah (µs/cm) |
| `soil_ph` | Numeric | Opsional | Tingkat keasaman media (0.0 - 14.0) |
| `nitrogen` | Integer | Opsional | Konsentrasi N (ppm) |
| `phosphorus` | Integer | Opsional | Konsentrasi P (ppm) |
| `potassium` | Integer | Opsional | Konsentrasi K (ppm) |
| `pump_status` | String | Opsional | Status aktuator (maks. 50 karakter) |
| `kondisi_lahan` | String | Opsional | Deskripsi kondisi lahan (maks. 255 karakter) |

#### Contoh Payload JSON
```json
{
  "mac_address": "A1:B2:C3:D4:E5:F1",
  "temperature_c": 26.80,
  "humidity_pct": 74.50,
  "soil_moisture_pct": 65.20,
  "soil_temperature_c": 25.10,
  "soil_ec": 450,
  "soil_ph": 6.50,
  "nitrogen": 120,
  "phosphorus": 65,
  "potassium": 140,
  "pump_status": "AUTO",
  "kondisi_lahan": "Optimal"
}
```

#### Respons Berhasil (HTTP 201 Created)
```json
{
  "message": "Pengukuran berhasil disimpan.",
  "data": {
    "id": 101,
    "sensor_id": 1,
    "recorded_at": "2026-10-13 14:00:00",
    "temperature_c": 26.8,
    "humidity_pct": 74.5,
    "soil_moisture_pct": 65.2,
    "soil_temperature_c": 25.1,
    "soil_ec": 450,
    "soil_ph": 6.5,
    "nitrogen": 120,
    "phosphorus": 65,
    "potassium": 140,
    "pump_status": "AUTO",
    "kondisi_lahan": "Optimal"
  }
}
```

#### Respons Kesalahan Validasi (HTTP 422 Unprocessable Entity)
```json
{
  "message": "The mac address field is required.",
  "errors": {
    "mac_address": [
      "The mac address field is required."
    ]
  }
}
```

---

## 7. Panduan Instalasi dan Menjalankan Sistem

### 7.1. Prasyarat Perangkat Lunak
- PHP >= 8.4 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `sqlite3` atau driver basis data pilihan.
- Composer >= 2.x
- Node.js >= 20.x dan npm
- Git

### 7.2. Langkah Pemasangan

1. Clone repositori:
```bash
git clone https://github.com/IbnnuuS/iot-anggrek.git
cd iot-anggrek
```

2. Instal dependensi PHP:
```bash
composer install
```

3. Instal dependensi JavaScript:
```bash
npm install
```

4. Siapkan konfigurasi lingkungan:
```bash
cp .env.example .env
php artisan key:generate
```

5. Konfigurasikan variabel basis data dan Firebase pada berkas `.env`:
```env
DB_CONNECTION=sqlite
# Atau MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=anggrek
# DB_USERNAME=root
# DB_PASSWORD=secret

# URL Firebase Realtime Database
FIREBASE_DATABASE_URL=https://iot-anggrek-default-rtdb.asia-southeast1.firebasedatabase.app
```

Untuk instalasi berbasis SQLite:
```bash
touch database/database.sqlite
```

6. Jalankan migrasi basis data beserta seeder:
```bash
php artisan migrate --seed
```

7. Bangun aset frontend:
```bash
npm run build
```

8. Jalankan server pengembangan Laravel:
```bash
php artisan serve
```

Aplikasi dapat diakses melalui browser pada `http://127.0.0.1:8000`.

### 7.3. Akun Pengujian Bawaan Seeder
| Nama Pengguna | Alamat Email | Kata Sandi | Status Verifikasi |
|---|---|---|---|
| Admin Anggrek | `admin@anggrek.test` | `password` | Terverifikasi |
| John Doe | `john@anggrek.test` | `password` | Terverifikasi |
| Jane Smith | `jane@anggrek.test` | `password` | Terverifikasi |

---

## 8. Perintah CLI Tambahan

### Sinkronisasi Snapshot Telemetri dari Firebase
Untuk menarik data pengamatan terkini langsung dari Firebase Realtime Database dan menyimpannya ke tabel database lokal:

```bash
# Menggunakan sensor default (sensor pertama terdaftar)
php artisan firebase:sync

# Menggunakan ID sensor tertentu
php artisan firebase:sync --sensor=2
```

---

## 9. Pengujian dan Standar Kualitas

### Menjalankan Pengujian Otomatis
Seluruh pengujian fungsionalitas dan keamanan ditulis menggunakan Pest:

```bash
# Menjalankan seluruh test suite
php artisan test --compact

# Menjalankan test modul spesifik
php artisan test --compact tests/Feature/Api/V1/PengukuranApiTest.php
php artisan test --compact tests/Feature/Anggrek/SensorOwnershipTest.php
```

### Memeriksa dan Memperbaiki Format Kode
Gunakan Laravel Pint untuk menjaga kepatuhan format PSR-12:

```bash
vendor/bin/pint
```

---

## 10. Lisensi

Proyek ini bersifat open-source di bawah lisensi MIT.
