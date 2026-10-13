# ESP32 Anggrek Sensor - Monitoring Suhu & Kelembapan

Perangkat ini membaca data suhu dan kelembapan dari sensor DHT22 lalu mengirimkannya ke API Laravel secara berkala.

## Endpoint API

- **Method:** `POST`
- **URL:** `/api/v1/pengukuran`
- **Body JSON:**

```json
{
    "mac_address": "A1:B2:C3:D4:E5:F1",
    "temperature_c": 25.50,
    "humidity_pct": 70.00
}
```

## Library yang Dibutuhkan

Install via **Arduino Library Manager**:

| Library | Author |
|---|---|
| DHT sensor library | Adafruit |
| Adafruit Unified Sensor | Adafruit |
| ArduinoJson (v7+) | Benoit Blanchon |

## Konfigurasi

Semua variabel konfigurasi berada di bagian atas file `esp32_anggrek_sensor.ino`.

### WiFi

| Variabel | Tipe | Keterangan |
|---|---|---|
| `WIFI_SSID` | `const char*` | Nama jaringan WiFi |
| `WIFI_PASSWORD` | `const char*` | Password WiFi |

### Jaringan Statis (Opsional)

Atur `USE_STATIC_IP = true` untuk menggunakan IP statis. Jika `false`, ESP32 menggunakan DHCP.

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `USE_STATIC_IP` | `bool` | `false` | Aktifkan/nonaktifkan IP statis |
| `STATIC_IP` | `IPAddress` | `192.168.1.100` | Alamat IP statis ESP32 |
| `GATEWAY` | `IPAddress` | `192.168.1.1` | Alamat gateway |
| `SUBNET` | `IPAddress` | `255.255.255.0` | Subnet mask |
| `DNS_PRIMARY` | `IPAddress` | `8.8.8.8` | DNS primer |
| `DNS_SECONDARY` | `IPAddress` | `8.8.4.4` | DNS sekunder |

### Server API Laravel

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `API_BASE_URL` | `const char*` | `http://192.168.1.10:8000` | URL dasar server Laravel (scheme + host + port) |
| `API_ENDPOINT` | `const char*` | `/api/v1/pengukuran` | Path endpoint API |

### Identitas Sensor

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `SENSOR_MAC_ADDRESS` | `const char*` | `A1:B2:C3:D4:E5:F1` | MAC address yang terdaftar di tabel `sensors` |

MAC address harus sesuai dengan yang terdaftar di database. Format: `A1:B2:C3:D4:E5:F1`.

### Pin & Tipe Sensor DHT

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `DHT_PIN` | `#define` | `4` | Pin GPIO yang terhubung ke jalur data sensor DHT |
| `DHT_TYPE` | `#define` | `DHT22` | Tipe sensor: `DHT11`, `DHT22`, atau `DHT21` |

### Interval Pengiriman

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `SEND_INTERVAL_MS` | `unsigned long` | `60000` | Interval pengiriman data dalam milidetik (default: 60 detik) |

### Retry & Timeout

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `WIFI_CONNECT_TIMEOUT_MS` | `int` | `15000` | Batas waktu koneksi WiFi (ms). Jika habis, ESP32 restart. |
| `HTTP_TIMEOUT_MS` | `int` | `10000` | Batas waktu HTTP request (ms) |
| `MAX_RETRY` | `int` | `3` | Jumlah percobaan ulang pengiriman jika gagal |
| `RETRY_DELAY_MS` | `int` | `2000` | Jeda antar percobaan ulang (ms) |

### LED Indikator

| Variabel | Tipe | Default | Keterangan |
|---|---|---|---|
| `LED_PIN` | `#define` | `2` | Pin GPIO LED bawaan ESP32 |

## LED Indikator

LED bawaan ESP32 (GPIO 2) digunakan sebagai indikator status:

| Pola | Arti |
|---|---|
| 1x blink lambat (500ms) | Data berhasil dikirim ke API |
| 3x blink sedang (200ms) | Gagal mengirim data setelah semua retry |
| 5x blink cepat (100ms) | Gagal membaca sensor DHT |
| Berkedip saat startup | Sedang menyambung ke WiFi |

## Alur Kerja

1. **Setup:** ESP32 inisialisasi serial, LED, sensor DHT, lalu menyambung ke WiFi.
2. **Loop:** Setiap `SEND_INTERVAL_MS` milidetik:
   - Periksa koneksi WiFi, sambung ulang jika terputus.
   - Baca suhu dan kelembapan dari sensor DHT.
   - Validasi data (jika `NaN`, blink error dan skip).
   - Kirim data ke API Laravel via HTTP POST (JSON).
   - Retry hingga `MAX_RETRY` kali jika gagal.
3. **WiFi Reconnect:** Jika WiFi terputus saat loop, otomatis menyambung ulang. Jika timeout, ESP32 restart.

## Wiring Sensor DHT22

```
ESP32          DHT22
------         ------
3.3V   -----> VCC (Pin 1)
GPIO4  -----> DATA (Pin 2)  + resistor pull-up 10kΩ ke 3.3V
(NC)          (Pin 3 tidak digunakan)
GND    -----> GND (Pin 4)
```

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `[DHT] Gagal membaca sensor!` | Periksa wiring, resistor pull-up, dan tipe sensor yang benar |
| `[WIFI] Timeout! Gagal menyambung` | Periksa SSID dan password, jarak ke router |
| `[API] HTTP Code: 422` | MAC address tidak ditemukan di database, atau format data salah |
| `[API] HTTP Code: -1` | Server tidak dapat dijangkau, periksa `API_BASE_URL` |
| `[API] HTTP Code: 500` | Error di server Laravel, periksa log Laravel |
