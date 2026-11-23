# IoT Anggrek

Sistem monitoring lingkungan budidaya anggrek berbasis Internet of Things (IoT). Perangkat ESP32 membaca kondisi udara, cahaya, dan media tanam, mengirimkannya ke Firebase Realtime Database, lalu aplikasi web Laravel menampilkan data tersebut secara langsung, menyimpan riwayat pengukuran, dan memberi peringatan ketika kondisi lingkungan keluar dari ambang batas ideal.

Dokumen ini menjelaskan cara kerja sistem, kebutuhan perangkat lunak, langkah pemasangan, konfigurasi, hingga pengujian. Penjelasan ditulis agar dapat dipahami oleh pembaca yang belum mengenal istilah teknis.

---

## 1. Ringkasan Sistem

Sistem terdiri atas tiga bagian:

1. **Perangkat (ESP32).** Membaca sensor, mengirim data ke Firebase setiap tiga detik, menjalankan penyiraman otomatis, dan mengirim notifikasi Telegram ketika terjadi kelainan.
2. **Firebase Realtime Database.** Menyimpan data terbaru, ambang batas, dan perintah kontrol. Berfungsi sebagai penghubung antara perangkat dan aplikasi web.
3. **Aplikasi web (Laravel).** Menampilkan dashboard, mengubah ambang batas, mengirim perintah kontrol ke perangkat, serta menyimpan riwayat pengukuran ke basis data aplikasi.

Alur data singkat:

```
Sensor (DHT22, BH1750, CWT 7-in-1)
        |
        v
     ESP32  --->  Firebase Realtime Database
                        |            ^
                        v            |
                  Aplikasi Laravel --+
                        |
                        v
             Basis data lokal (riwayat pengukuran)
```

Perangkat selalu menjadi pengirim data, sedangkan aplikasi web membaca data terbaru dari Firebase dan menuliskan perintah kontrol ke Firebase. Perangkat mengecek perintah tersebut secara berkala, sehingga tidak diperlukan koneksi langsung dari internet menuju perangkat.

---

## 2. Fitur Utama

- Dashboard kondisi terkini: suhu udara, kelembapan udara, intensitas cahaya, kelembapan tanah, suhu tanah, pH, EC, serta unsur hara nitrogen, fosfor, dan kalium.
- Kendali perangkat dari web: mengaktifkan atau menghentikan monitoring, mengaktifkan atau mematikan penyiraman otomatis.
- Pengaturan ambang batas ideal yang tersimpan di Firebase dan langsung diikuti perangkat dalam waktu kurang dari sepuluh detik.
- Status kondisi lahan (Normal atau Abnormal beserta parameternya) dan status pompa yang ditampilkan pada dashboard.
- Riwayat pengukuran dengan pencarian, penyaringan berdasarkan sensor dan rentang tanggal, penghapusan per data, dan penghapusan seluruh data pada satu tanggal.
- Penyimpanan riwayat otomatis setiap sepuluh menit selama monitoring aktif, ditambah penyimpanan manual kapan saja.
- Notifikasi Telegram otomatis ketika nilai sensor melewati ambang batas atau pompa mulai menyiram.
- Pengelolaan data sensor dan data anggrek beserta ambang batas suhu dan kelembapan per varietas.
- Autentikasi pengguna dengan verifikasi surel dan autentikasi dua faktor (2FA).
- Antarmuka berbahasa Indonesia.

---

## 3. Tumpukan Teknologi

### Aplikasi web

| Bagian | Teknologi |
|---|---|
| Kerangka kerja | Laravel 12 |
| Bahasa | PHP 8.4 |
| Komponen antarmuka | Livewire 3 dan Volt |
| Komponen desain | Flux UI Pro |
| Gaya tampilan | Tailwind CSS 4 |
| Penggabung aset | Vite |
| Autentikasi | Laravel Fortify |
| Pengujian | Pest 4 dan PHPUnit 12 |
| Perapian kode | Laravel Pint |
| Basis data | SQLite (bawaan), dapat diganti MySQL atau PostgreSQL |

### Perangkat

| Bagian | Teknologi |
|---|---|
| Mikrokontroler | ESP32 DevKit (DOIT ESP32 DevKit V1) |
| Suhu dan kelembapan udara | DHT22 |
| Intensitas cahaya | BH1750 (antarmuka I2C) |
| Media tanam 7-in-1 | Sensor CWT melalui RS485 (Modbus RTU): kelembapan, suhu, EC, pH, N, P, K |
| Aktuator | Modul relai pompa air |
| Notifikasi | Telegram Bot API |
| Penyimpanan data | Firebase Realtime Database melalui HTTPS |

Catatan: Firebase pada proyek ini diakses melalui REST tanpa autentikasi pengguna, sehingga aturan basis data bersifat terbuka. Untuk penggunaan di luar lingkungan penelitian, sebaiknya tambahkan aturan keamanan Firebase dan autentikasi perangkat.

---

## 4. Struktur Direktori

```
anggrek/
|-- app/
|   |-- Actions/Fortify/            Aturan validasi registrasi dan reset kata sandi
|   |-- Console/Commands/           Perintah artisan firebase:sync
|   |-- Http/Controllers/Api/V1/    Penerima data telemetri melalui REST API
|   |-- Http/Requests/Api/V1/       Validasi muatan data telemetri
|   |-- Livewire/                   Komponen halaman: Dashboard, ThresholdSetting,
|   |                               Sensor, Anggrek, Pengukuran
|   |-- Models/                     Model User, Sensor, Anggrek, Pengukuran
|   `-- Services/                   FirebaseService: baca dan tulis Firebase
|-- arduino/
|   `-- esp32_anggrek_sensor.ino    Firmware ESP32
|-- config/                         Konfigurasi aplikasi, basis data, dan layanan
|-- database/
|   |-- migrations/                 Skema tabel aplikasi
|   `-- seeders/                    Data contoh untuk pengembangan
|-- docs/                           Dokumentasi internal (basis data, logika, Arduino, Flux)
|-- lang/id/                        Terjemahan antarmuka bahasa Indonesia
|-- public/                         Aset publik (CSS, ikon, gambar)
|-- resources/views/                Tampilan Blade dan komponen Livewire
|-- routes/
|   |-- api.php                     Endpoint REST API
|   |-- console.php                 Penjadwal penyimpanan riwayat otomatis
|   |-- settings.php                Halaman profil, kata sandi, dan 2FA
|   `-- web.php                     Halaman web aplikasi
|-- tests/                          Pengujian Pest
|-- jalankan.bat                    Skrip menjalankan server web dan penjadwal (Windows)
`-- .env.example                    Contoh konfigurasi lingkungan
```

---

## 5. Persyaratan

- PHP 8.4 atau lebih baru, dengan ekstensi `pdo_sqlite` (atau `pdo_mysql`), `mbstring`, `openssl`, dan `curl`. Dependensi Composer pada proyek ini mensyaratkan PHP 8.4.
- Composer 2.
- Node.js 20 atau lebih baru beserta npm.
- Arduino IDE 2 dengan dukungan papan ESP32.
- Proyek Firebase dengan Realtime Database yang aktif.
- Bot Telegram beserta token dan ID percakapan penerima notifikasi.

---

## 6. Pemasangan

Jalankan seluruh perintah berikut dari direktori proyek.

1. Pasang dependensi PHP dan JavaScript.

```
composer install
npm install
```

2. Siapkan berkas konfigurasi lingkungan.

```
cp .env.example .env
php artisan key:generate
```

3. Sesuaikan isi `.env`, terutama `APP_NAME`, `APP_URL`, dan `FIREBASE_DATABASE_URL` (lihat bagian 7).

4. Siapkan basis data.

```
php artisan migrate
```

Berkas SQLite dibuat otomatis pada `database/database.sqlite` bila belum tersedia. Untuk MySQL, ubah `DB_CONNECTION` beserta kredensialnya terlebih dahulu.

5. Isi data contoh (opsional, hanya untuk pengembangan).

```
php artisan db:seed
```

6. Bangun aset antarmuka.

```
npm run build
```

Untuk pengembangan, jalankan `npm run dev` pada terminal terpisah.

7. Jalankan aplikasi.

Di Windows, jalankan berkas `jalankan.bat`. Skrip ini membuka dua jendela: server web melalui `php artisan serve` dan penjadwal `php artisan schedule:work`. Keduanya harus tetap terbuka selama monitoring berlangsung.

Secara manual, gunakan dua terminal:

```
php artisan serve
php artisan schedule:work
```

Aplikasi dapat diakses pada `http://127.0.0.1:8000`.

Akun contoh hasil `db:seed` (kata sandi seluruh akun adalah `password`, hanya untuk pengembangan):

| Nama | Surel |
|---|---|
| Admin Anggrek | admin@anggrek.test |
| John Doe | john@anggrek.test |
| Jane Smith | jane@anggrek.test |

---

## 7. Konfigurasi Lingkungan

Seluruh variabel berikut berada pada berkas `.env`. Berkas `.env` tidak disertakan dalam repositori karena memuat kredensial; gunakan `.env.example` sebagai acuan.

| Variabel | Keterangan | Contoh |
|---|---|---|
| `APP_NAME` | Nama aplikasi yang tampil pada antarmuka | `"IoT Anggrek"` |
| `APP_ENV` | Lingkungan aplikasi | `local` atau `production` |
| `APP_KEY` | Kunci enkripsi aplikasi | hasil `php artisan key:generate` |
| `APP_URL` | Alamat aplikasi | `http://127.0.0.1:8000` |
| `APP_LOCALE` | Bahasa antarmuka | `id` |
| `DB_CONNECTION` | Jenis basis data | `sqlite` |
| `FIREBASE_DATABASE_URL` | Alamat basis data Firebase tanpa garis miring di akhir | `https://nama-proyek.asia-southeast1.firebasedatabase.app` |
| `MAIL_MAILER` | Pengiriman surel; nilai `log` menulis surel ke berkas log | `log` |

Jika `FIREBASE_DATABASE_URL` tidak diisi, aplikasi memakai nilai bawaan pada `config/services.php`.

Firmware ESP32 memiliki konfigurasi tersendiri di bagian atas berkas `arduino/esp32_anggrek_sensor.ino`, yaitu `WIFI_SSID`, `WIFI_PASSWORD`, `FIREBASE_HOST`, `TG_TOKEN`, dan `TG_CHAT`. Nilai tersebut tidak dibaca dari `.env` dan harus diisi langsung sebelum firmware diunggah. Jangan menyimpan token bot Telegram pada repositori publik.

---

## 8. Struktur Data di Firebase

Firebase menyimpan tiga simpul utama.

### 8.1 `monitoring`

Ditulis oleh perangkat setiap tiga detik.

| Kunci | Arti | Satuan |
|---|---|---|
| `suhu_udara` | Suhu udara | derajat Celsius |
| `lembap_udara` | Kelembapan udara relatif | persen |
| `lux` | Intensitas cahaya | lux |
| `lembap_tanah` | Kelembapan media tanam | persen |
| `suhu_tanah` | Suhu media tanam | derajat Celsius |
| `ec` | Konduktivitas listrik media tanam | mikrosiemens per sentimeter |
| `ph` | Derajat keasaman media tanam | pH |
| `n`, `p`, `k` | Kadar nitrogen, fosfor, kalium | ppm |
| `status_pompa` | Status pompa saat pembacaan | teks |
| `kondisi_lahan` | Ringkasan kondisi, misalnya `Normal` atau `Abnormal (Suhu-Udara )` | teks |

### 8.2 `thresholds`

Dibaca aplikasi web dan perangkat, serta ditulis dari halaman Pengaturan Ambang Batas.

| Kunci | Arti | Nilai bawaan |
|---|---|---|
| `air_temp_min`, `air_temp_max` | Batas suhu udara | 21 sampai 30 derajat Celsius |
| `air_hum_min`, `air_hum_max` | Batas kelembapan udara | 60 sampai 80 persen |
| `lux_min`, `lux_max` | Batas intensitas cahaya | 10000 sampai 20000 lux |
| `n_min`, `n_max` | Batas nitrogen | 100 sampai 200 ppm |
| `p_min`, `p_max` | Batas fosfor | 50 sampai 100 ppm |
| `k_min`, `k_max` | Batas kalium | 100 sampai 200 ppm |

Halaman tersebut memvalidasi bahwa setiap nilai minimum tidak melebihi nilai maksimum sebelum dikirim ke Firebase.

### 8.3 `control`

Ditulis dari dashboard, dibaca perangkat setiap lima detik.

| Kunci | Arti | Nilai |
|---|---|---|
| `sensor_on` | Monitoring aktif atau berhenti | `1` aktif, `0` berhenti |
| `auto_water` | Penyiraman otomatis | `1` aktif, `0` nonaktif |
| `pump_seq` | Nomor perintah siram manual; perubahan nilai memicu pompa menyala | bilangan bulat |
| `pump_duration` | Durasi siram manual | detik, 1 sampai 60 |

Saat `sensor_on` bernilai `0`, perangkat berhenti membaca sensor dan aplikasi tidak menyimpan riwayat otomatis. Perintah siram manual tetap dijalankan dan pompa tetap dimatikan setelah durasi berakhir.

---

## 9. REST API Penerimaan Data

Selain melalui Firebase, aplikasi menyediakan endpoint untuk menerima data pengukuran langsung dari perangkat lain.

- Metode: `POST`
- Alamat: `/api/v1/pengukuran`
- Pembatasan: 60 permintaan per menit per alamat IP
- Format: JSON

| Kolom | Wajib | Aturan |
|---|---|---|
| `mac_address` | Ya | Harus terdaftar pada tabel `sensors`, maksimal 32 karakter |
| `temperature_c` | Ya | Angka, -40 sampai 80 |
| `humidity_pct` | Ya | Angka, 0 sampai 100 |
| `soil_moisture_pct` | Tidak | Angka, 0 sampai 100 |
| `soil_temperature_c` | Tidak | Angka, -40 sampai 80 |
| `soil_ec` | Tidak | Bilangan bulat, minimal 0 |
| `soil_ph` | Tidak | Angka, 0 sampai 14 |
| `nitrogen`, `phosphorus`, `potassium` | Tidak | Bilangan bulat, minimal 0 |
| `pump_status` | Tidak | Teks, maksimal 100 karakter |
| `kondisi_lahan` | Tidak | Teks, maksimal 255 karakter |
| `recorded_at` | Tidak | Tanggal dan waktu; bila kosong dipakai waktu server |

Contoh permintaan:

```
curl -X POST http://127.0.0.1:8000/api/v1/pengukuran \
  -H "Content-Type: application/json" \
  -d '{"mac_address":"A1:B2:C3:D4:E5:F1","temperature_c":27.5,"humidity_pct":72.0,"soil_ph":6.5}'
```

Balasan berhasil memakai kode `201` beserta data yang tersimpan. Alamat MAC yang tidak terdaftar menghasilkan kode `404`, dan data yang tidak sesuai aturan menghasilkan kode `422`.

---

## 10. Perangkat dan Firmware

Berkas firmware: `arduino/esp32_anggrek_sensor.ino`.

### 10.1 Sambungan Perangkat Keras

| Perangkat | Kaki perangkat | Kaki ESP32 | Keterangan |
|---|---|---|---|
| DHT22 | VCC | 3V3 | Catu daya 3,3 volt |
| DHT22 | DATA | GPIO 25 | Perlu resistor tarik 10 kiloohm ke 3V3 |
| DHT22 | GND | GND | |
| BH1750 | VCC | 3V3 | |
| BH1750 | GND | GND | |
| BH1750 | SDA | GPIO 21 | Jalur data I2C |
| BH1750 | SCL | GPIO 22 | Jalur detak I2C |
| Konverter RS485 | RO | GPIO 16 | Data terima dari sensor tanah |
| Konverter RS485 | DI | GPIO 17 | Data kirim menuju sensor tanah |
| Konverter RS485 | DE dan RE | GPIO 18 | Kendali arah kirim atau terima |
| Modul relai | IN | GPIO 23 | Relai aktif rendah, nilai LOW menyalakan pompa |
| Modul relai | VCC dan GND | 5V dan GND | Sesuaikan dengan modul yang dipakai |

Sensor tanah CWT dihubungkan ke konverter RS485 melalui jalur A dan B, menggunakan komunikasi Modbus RTU pada 4800 bit per detik, 8 bit data, tanpa paritas, satu bit berhenti, dan alamat perangkat `1`.

### 10.2 Pustaka Arduino

Pasang melalui Library Manager pada Arduino IDE:

| Pustaka | Penulis |
|---|---|
| DHTNEW | Rob Tillaart |
| BH1750 | Christopher Laws |
| WiFi, WiFiClientSecure, HTTPClient, Wire | Bawaan paket papan ESP32 |

### 10.3 Pengaturan Firmware

Sesuaikan lima nilai berikut sebelum proses unggah:

```
#define WIFI_SSID     "NAMA_JARINGAN"
#define WIFI_PASSWORD "KATA_SANDI_JARINGAN"
#define FIREBASE_HOST "https://nama-proyek.asia-southeast1.firebasedatabase.app"
#define TG_TOKEN      "TOKEN_BOT_TELEGRAM"
#define TG_CHAT       "ID_PERCAKAPAN_TELEGRAM"
```

Nilai lain yang dapat disesuaikan: `DHTPIN`, `RXD2`, `TXD2`, `DE_RE`, `RELAY_PIN`, `PUMP_MAX_ON_SEC` (batas maksimal pompa menyala, bawaan 5 detik), `PUMP_COOLDOWN_SEC` (jeda antar penyiraman, bawaan 20 detik), dan `NOTIF_GAP_MS` (jeda minimal notifikasi sejenis, bawaan lima menit).

### 10.4 Perilaku Operasional

- Setelah tersambung ke WiFi, perangkat mengambil ambang batas dan perintah kontrol dari Firebase.
- Ambang batas disegarkan setiap sepuluh detik, perintah kontrol setiap lima detik, dan data monitoring dikirim setiap tiga detik.
- Penyiraman otomatis hanya berjalan jika `auto_water` bernilai `1`. Pompa menyala paling lama lima detik, lalu menunggu dua puluh detik sebelum dapat menyala kembali.
- Jika pembacaan DHT22 atau sensor tanah gagal, pompa dimatikan sebagai tindakan pengamanan dan data tidak dikirim.
- Notifikasi Telegram dikirim hanya ketika status berubah dari normal menjadi menyimpang, paling sering sekali per lima menit untuk jenis kelainan yang sama. Jenis kelainan meliputi suhu rendah, suhu tinggi, kelembapan rendah, kelembapan tinggi, cahaya kurang, cahaya berlebih, unsur hara kurang, unsur hara berlebih, dan pompa mulai menyiram.

---

## 11. Struktur Basis Data Aplikasi

### `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Kunci utama |
| `name` | VARCHAR(255) | Nama pengguna |
| `email` | VARCHAR(255) | Surel, unik |
| `password` | VARCHAR(255) | Kata sandi terenkripsi |
| `two_factor_secret` | TEXT | Kunci 2FA, boleh kosong |
| `two_factor_recovery_codes` | TEXT | Kode pemulihan 2FA, boleh kosong |
| `created_at`, `updated_at` | TIMESTAMP | Waktu dibuat dan diubah |

### `sensors`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Kunci utama |
| `user_id` | BIGINT UNSIGNED | Pemilik sensor, terhapus otomatis bila pengguna dihapus |
| `name` | VARCHAR(100) | Nama sensor |
| `model` | VARCHAR(50) | Model perangkat, boleh kosong |
| `serial_no` | VARCHAR(80) | Nomor seri, boleh kosong |
| `mac_address` | VARCHAR(32) | Alamat MAC, unik, boleh kosong |
| `site_name` | VARCHAR(100) | Nama lokasi, boleh kosong |
| `area_name` | VARCHAR(100) | Area dalam lokasi, boleh kosong |
| `zone_label` | VARCHAR(60) | Zona, boleh kosong |
| `rack_label` | VARCHAR(60) | Rak, boleh kosong |
| `notes` | VARCHAR(255) | Catatan, boleh kosong |
| `created_at`, `updated_at` | TIMESTAMP | Waktu dibuat dan diubah |

### `anggrek`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Kunci utama |
| `user_id` | BIGINT UNSIGNED | Pemilik data, terhapus otomatis bila pengguna dihapus |
| `sensor_id` | BIGINT UNSIGNED | Sensor terkait, boleh kosong, menjadi kosong bila sensor dihapus |
| `jenis_anggrek` | VARCHAR(100) | Jenis atau spesies, misalnya Phalaenopsis |
| `nama_anggrek` | VARCHAR(100) | Nama tanaman, boleh kosong |
| `temp_min_c`, `temp_max_c` | DECIMAL(5,2) | Batas suhu ideal dalam derajat Celsius |
| `hum_min_pct`, `hum_max_pct` | DECIMAL(5,2) | Batas kelembapan ideal dalam persen |
| `created_at`, `updated_at` | TIMESTAMP | Waktu dibuat dan diubah |

### `pengukuran`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Kunci utama |
| `sensor_id` | BIGINT UNSIGNED | Sensor asal data, terhapus otomatis bila sensor dihapus |
| `recorded_at` | DATETIME | Waktu pembacaan |
| `temperature_c` | DECIMAL(5,2) | Suhu udara, derajat Celsius |
| `humidity_pct` | DECIMAL(5,2) | Kelembapan udara, persen |
| `lux` | DECIMAL(8,1) | Intensitas cahaya, boleh kosong |
| `soil_moisture_pct` | DECIMAL(5,2) | Kelembapan tanah, persen, boleh kosong |
| `soil_temperature_c` | DECIMAL(5,2) | Suhu tanah, derajat Celsius, boleh kosong |
| `soil_ec` | INT | Konduktivitas listrik tanah, boleh kosong |
| `soil_ph` | DECIMAL(4,2) | pH tanah, boleh kosong |
| `nitrogen`, `phosphorus`, `potassium` | INT | Unsur hara dalam ppm, boleh kosong |
| `pump_status` | VARCHAR(255) | Status pompa, boleh kosong |
| `kondisi_lahan` | VARCHAR(255) | Ringkasan kondisi, boleh kosong |

Indeks gabungan `sensor_id` dan `recorded_at` mempercepat penyaringan riwayat. Tabel ini tidak memakai kolom waktu bawaan Laravel karena waktu pencatatan sudah diwakili `recorded_at`.

### Relasi

- Satu pengguna memiliki banyak sensor dan banyak data anggrek.
- Satu sensor memiliki banyak data pengukuran.
- Satu data pengukuran hanya dimiliki satu sensor.
- Satu data anggrek dapat dikaitkan ke satu sensor atau berdiri sendiri.

---

## 12. Ambang Batas dan Penentuan Kondisi

Halaman Pengaturan Ambang Batas mengirim nilai batas ke Firebase, kemudian perangkat membandingkan hasil pembacaan dengan nilai tersebut. Kondisi ditentukan sebagai berikut.

| Parameter | Di bawah batas minimum | Di atas batas maksimum |
|---|---|---|
| Suhu udara | Suhu rendah, disarankan menambah lampu pemanas | Suhu tinggi, disarankan penyiraman |
| Kelembapan udara | Kelembapan rendah, disarankan menambah kelembapan sekitar tanaman | Kelembapan tinggi, disarankan mengurangi kelembapan media |
| Intensitas cahaya | Cahaya kurang, disarankan membuka paranet | Cahaya berlebih, disarankan menutup paranet |
| Nitrogen, fosfor, kalium | Unsur hara kurang, disarankan pemupukan | Unsur hara berlebih, disarankan mengurangi pupuk dan membilas media |

Bila satu atau lebih parameter menyimpang, kolom `kondisi_lahan` berisi `Abnormal` beserta daftar parameter penyebabnya, dan penyiraman otomatis dijalankan bila diaktifkan. Bila seluruh parameter berada dalam rentang, nilainya `Normal`.

Setiap data anggrek juga menyimpan rentang suhu dan kelembapan ideal per varietas, dengan aturan validasi bahwa nilai minimum selalu lebih kecil daripada nilai maksimum. Data ini menjadi acuan pemantauan per varietas pada halaman anggrek.

---

## 13. Perintah Artisan

### Menyimpan riwayat dari Firebase

```
php artisan firebase:sync
php artisan firebase:sync --sensor=2
```

Perintah ini membaca simpul `monitoring` beserta `control` dari Firebase. Bila monitoring sedang dihentikan dari web, perintah tidak menyimpan data. Bila data berhasil dibaca, isinya ditampilkan dalam bentuk tabel lalu disimpan ke tabel `pengukuran`. Tanpa opsi `--sensor`, data dikaitkan ke sensor pertama yang terdaftar.

### Penjadwal penyimpanan otomatis

```
php artisan schedule:work
```

Penjadwal menjalankan `firebase:sync` setiap sepuluh menit tanpa tumpang tindih. Proses ini harus berjalan agar riwayat otomatis tersimpan; berkas `jalankan.bat` sudah menjalankannya. Tanpa penjadwal, riwayat hanya tersimpan bila dijalankan manual.

### Perintah lain yang sering dipakai

```
php artisan migrate          Menjalankan migrasi basis data
php artisan db:seed          Mengisi data contoh
php artisan storage:link     Menautkan penyimpanan publik
npm run build                Membangun aset antarmuka
npm run dev                  Menjalankan Vite untuk pengembangan
```

---

## 14. Pengujian dan Mutu Kode

```
php artisan test --compact
```

Menjalankan seluruh pengujian, meliputi autentikasi, verifikasi surel, pengaturan profil dan kata sandi, 2FA, dashboard, kontrol perangkat, pengelolaan sensor beserta kepemilikannya, pengelolaan anggrek, riwayat pengukuran, dan endpoint penerimaan data.

Pengujian modul tertentu:

```
php artisan test --compact tests/Feature/KontrolPerangkatTest.php
php artisan test --compact tests/Feature/RiwayatPengukuranTest.php
php artisan test --compact tests/Feature/Api/V1/PengukuranApiTest.php
```

Merapikan format kode sesuai standar PSR-12:

```
vendor/bin/pint
```

---

## 15. Catatan Keamanan

- Berkas `.env` tidak disertakan dalam repositori. Jangan mengunggah `.env`, token bot Telegram, atau kredensial Firebase.
- Firmware memuat token Telegram secara langsung. Sebelum membagikan berkas firmware, kosongkan kembali nilai `TG_TOKEN` dan `TG_CHAT`.
- Firebase pada proyek ini dapat dibaca dan ditulis tanpa autentikasi. Batasi aturan Firebase jika sistem dipakai di luar lingkungan penelitian.
- Ganti kata sandi seluruh akun contoh sebelum aplikasi dipakai di lingkungan nyata.
- Jalankan aplikasi dengan `APP_DEBUG=false` pada lingkungan produksi.

---

## 16. Dokumentasi Tambahan

| Berkas | Isi |
|---|---|
| `docs/anggrek/logic.md` | Alur logika aplikasi per modul |
| `docs/anggrek/database/` | Rincian setiap tabel basis data |
| `docs/arduino/README.md` | Catatan perangkat, sebagian masih mengacu pada versi firmware awal |
| `docs/flux/` | Acuan komponen Flux UI yang dipakai |

---

## 17. Lisensi

Proyek ini dirilis di bawah lisensi MIT.
