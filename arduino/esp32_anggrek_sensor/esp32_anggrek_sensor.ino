#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <BH1750.h>
#include <dhtnew.h>
// Ganti sesuai WiFi / Hotspot HP kamu
#define WIFI_SSID     "YOUR_WIFI_SSID"
#define WIFI_PASSWORD "YOUR_WIFI_PASSWORD"
// URL Firebase kamu
#define FIREBASE_HOST "https://YOUR_FIREBASE_PROJECT.firebasedatabase.app"
// Bot Telegram notifikasi (@iotanggrek_bot)
#define TG_TOKEN "YOUR_BOT_TOKEN"
#define TG_CHAT  "YOUR_CHAT_ID"
// Jeda minimal antar notif untuk jenis yang sama (tahan spam)
#define NOTIF_GAP_MS 300000UL   // 5 menit
#define DHTPIN 25
#define RXD2 16
#define TXD2 17
#define DE_RE 18
#define RELAY_PIN 23
// Ambang Batas Ideal Dinamis (Tersinkron Otomatis dari Web / Firebase)
float airTempMin = 21.0;
float airTempMax = 30.0;
float airHumMin  = 60.0;
float airHumMax  = 80.0;
float luxMin     = 10000.0;
float luxMax     = 20000.0;
int   nMin       = 100;
int   nMax       = 200;
int   pMin       = 50;
int   pMax       = 100;
int   kMin       = 100;
int   kMax       = 200;

// Durasi Pompa (Detik)
#define PUMP_MAX_ON_SEC   5
#define PUMP_COOLDOWN_SEC 20

#define PUMP_MAX_ON_MS   (PUMP_MAX_ON_SEC * 1000UL)
#define PUMP_COOLDOWN_MS (PUMP_COOLDOWN_SEC * 1000UL)

DHTNEW dht(DHTPIN);
BH1750 lightMeter;

const byte query[] = {0x01, 0x03, 0x00, 0x00, 0x00, 0x07, 0x04, 0x08};
byte buf[32];

bool pumpIsOn = false;
bool pumpManual = false;         // pompa menyala karena perintah siram manual dari web
bool pendingManualPump = false;  // perintah siram menunggu dieksekusi di loop
unsigned long pumpStartTime = 0;
unsigned long pumpStopTime  = 0;
unsigned long lastFbSend    = 0;
unsigned long lastThFetch   = 0;
unsigned long lastCtrlFetch = 0;
unsigned long lastPumpSeq   = 0;
bool seqInit = false;            // baseline pump_seq saat boot (perintah lama tidak dieksekusi)
int  pumpDurationSec = PUMP_MAX_ON_SEC;  // durasi siram manual (detik), disinkron dari web
int  ctrlSensorOn = 1;                   // 1 = monitoring aktif, 0 = dihentikan dari web
int  ctrlAutoWater = 0;                  // 1 = penyiraman otomatis aktif, 0 = OFF (default aman)
int  lastSensorOnPrint = 1;

// Status notifikasi: 9 jenis (bit 0..8), selaras dengan parameter di dashboard
// 0 suhu<min 1 suhu>max 2 lembap<min 3 lembap>max 4 cahaya<min 5 cahaya>max
// 6 hara kurang 7 hara berlebih 8 pompa menyiram
unsigned long notifLast[9] = {0, 0, 0, 0, 0, 0, 0, 0, 0};
uint8_t notifPrev = 0;

void relayON()  { pinMode(RELAY_PIN, OUTPUT); digitalWrite(RELAY_PIN, LOW); pumpIsOn = true; }
void relayOFF() { pinMode(RELAY_PIN, INPUT); pumpIsOn = false; }

float getJsonVal(const String& json, const String& key, float defaultVal) {
  int idx = json.indexOf("\"" + key + "\"");
  if (idx == -1) return defaultVal;
  int colon = json.indexOf(':', idx);
  if (colon == -1) return defaultVal;
  int start = colon + 1;
  while (start < json.length() && (json[start] == ' ' || json[start] == '"')) start++;
  int end = start;
  while (end < json.length() && json[end] != ',' && json[end] != '}' && json[end] != '"' && json[end] != '\r' && json[end] != '\n') end++;
  if (start >= end) return defaultVal;
  return json.substring(start, end).toFloat();
}

void printActiveThresholds() {
  Serial.println("[Threshold Aktif di Alat]");
  Serial.printf("  Suhu Udara : %.1f - %.1f C\n", airTempMin, airTempMax);
  Serial.printf("  Lembap Air : %.1f - %.1f %%\n", airHumMin, airHumMax);
  Serial.printf("  Cahaya     : %.0f - %.0f lx\n", luxMin, luxMax);
  Serial.printf("  N : %d-%d | P : %d-%d | K : %d-%d\n", nMin, nMax, pMin, pMax, kMin, kMax);
}

void fetchThresholdsFromFirebase() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[Firebase] Threshold GAGAL: WiFi tidak konek.");
    return;
  }
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient https;
  String url = String(FIREBASE_HOST) + "/thresholds.json";
  if (!https.begin(client, url)) {
    Serial.println("[Firebase] Threshold GAGAL: https.begin() error.");
    return;
  }
  int code = https.GET();
  if (code == 200) {
    String json = https.getString();
    airTempMin = getJsonVal(json, "air_temp_min", airTempMin);
    airTempMax = getJsonVal(json, "air_temp_max", airTempMax);
    airHumMin  = getJsonVal(json, "air_hum_min", airHumMin);
    airHumMax  = getJsonVal(json, "air_hum_max", airHumMax);
    luxMin     = getJsonVal(json, "lux_min", luxMin);
    luxMax     = getJsonVal(json, "lux_max", luxMax);
    nMin       = (int)getJsonVal(json, "n_min", nMin);
    nMax       = (int)getJsonVal(json, "n_max", nMax);
    pMin       = (int)getJsonVal(json, "p_min", pMin);
    pMax       = (int)getJsonVal(json, "p_max", pMax);
    kMin       = (int)getJsonVal(json, "k_min", kMin);
    kMax       = (int)getJsonVal(json, "k_max", kMax);
    Serial.printf("[Firebase] Threshold tersinkron (%d byte)\n", json.length());
    printActiveThresholds();
  } else {
    Serial.printf("[Firebase] Threshold GAGAL: HTTP %d (%s)\n", code, https.errorToString(code).c_str());
  }
  https.end();
}

// Kontrol dari web: /control.json -> { sensor_on, pump_seq, pump_duration }
void fetchControlFromFirebase() {
  if (WiFi.status() != WL_CONNECTED) return;
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient https;
  String url = String(FIREBASE_HOST) + "/control.json";
  if (!https.begin(client, url)) return;
  int code = https.GET();
  if (code == 200) {
    String json = https.getString();
    ctrlSensorOn = (int)getJsonVal(json, "sensor_on", ctrlSensorOn);
    ctrlAutoWater = (int)getJsonVal(json, "auto_water", ctrlAutoWater);
    int dur = (int)getJsonVal(json, "pump_duration", pumpDurationSec);
    if (dur >= 1 && dur <= 60) pumpDurationSec = dur;

    unsigned long seq = (unsigned long)getJsonVal(json, "pump_seq", 0);
    if (!seqInit) {
      seqInit = true;
      lastPumpSeq = seq;  // baseline saat boot: perintah lama tidak dieksekusi
    } else if (seq != lastPumpSeq) {
      lastPumpSeq = seq;
      pendingManualPump = true;
    }
    Serial.printf("[Firebase] Kontrol tersinkron (sensor_on=%d, auto_water=%d, siram_seq=%lu)\n", ctrlSensorOn, ctrlAutoWater, lastPumpSeq);
  } else {
    Serial.printf("[Firebase] Kontrol GAGAL: HTTP %d\n", code);
  }
  https.end();
}
void sendToFirebase(float t_air, float h_air, float lux, float moist, float temp, int ec, float ph, int n, int p, int k, const char* p_status, const char* k_lahan) {
  if (WiFi.status() != WL_CONNECTED) return;
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient https;
  String url = String(FIREBASE_HOST) + "/monitoring.json";
  if (https.begin(client, url)) {
    https.addHeader("Content-Type", "application/json");
    String json = "{";
    json += "\"suhu_udara\":" + String(t_air, 1) + ",";
    json += "\"lembap_udara\":" + String(h_air, 1) + ",";
    json += "\"lux\":" + String(lux, 1) + ",";
    json += "\"lembap_tanah\":" + String(moist, 1) + ",";
    json += "\"suhu_tanah\":" + String(temp, 1) + ",";
    json += "\"ec\":" + String(ec) + ",";
    json += "\"ph\":" + String(ph, 1) + ",";
    json += "\"n\":" + String(n) + ",";
    json += "\"p\":" + String(p) + ",";
    json += "\"k\":" + String(k) + ",";
    json += "\"status_pompa\":\"" + String(p_status) + "\",";
    json += "\"kondisi_lahan\":\"" + String(k_lahan) + "\"";
    json += "}";
    int code = https.PUT(json);
    if (code > 0) {
      Serial.printf("[Firebase] Terkirim (HTTP %d)\n", code);
    } else {
      Serial.printf("[Firebase] Gagal: %s\n", https.errorToString(code).c_str());
    }
    https.end();
  }
}
// ================== NOTIFIKASI TELEGRAM ==================
void sendTelegram(const String& text) {
  if (WiFi.status() != WL_CONNECTED) return;
  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient https;
  String url = "https://api.telegram.org/bot" TG_TOKEN "/sendMessage";
  if (!https.begin(client, url)) {
    Serial.println("[Telegram] Gagal: https.begin() error.");
    return;
  }
  https.addHeader("Content-Type", "application/json");
  https.setTimeout(10000);
  String body = "{\"chat_id\":\"" TG_CHAT "\",\"text\":\"" + text + "\"}";
  int code = https.POST(body);
  Serial.printf("[Telegram] HTTP %d\n", code);
  https.end();
}

String haraKurang(int n, int p, int k) {
  String s = "";
  if (n < nMin) { s += "Nitrogen " + String(n) + " ppm"; }
  if (p < pMin) { if (s.length()) s += ", "; s += "Fosfor " + String(p) + " ppm"; }
  if (k < kMin) { if (s.length()) s += ", "; s += "Kalium " + String(k) + " ppm"; }
  return s;
}

String haraLebih(int n, int p, int k) {
  String s = "";
  if (n > nMax) { s += "Nitrogen " + String(n) + " ppm"; }
  if (p > pMax) { if (s.length()) s += ", "; s += "Fosfor " + String(p) + " ppm"; }
  if (k > kMax) { if (s.length()) s += ", "; s += "Kalium " + String(k) + " ppm"; }
  return s;
}

String notifText(uint8_t bit, float t_air, float h_air, float lux, int n, int p, int k) {
  switch (bit) {
    case 0: return "Suhu Udara " + String(t_air, 1) + " C, di bawah ambang batas (" + String(airTempMin, 0) + "-" + String(airTempMax, 0) + " C). Nyalakan lampu grow light.";
    case 1: return "Suhu Udara " + String(t_air, 1) + " C, di atas ambang batas (" + String(airTempMin, 0) + "-" + String(airTempMax, 0) + " C). Lakukan penyiraman.";
    case 2: return "Kelembapan Udara " + String(h_air, 1) + " %, di bawah ambang batas (" + String(airHumMin, 0) + "-" + String(airHumMax, 0) + " %). Tambahkan kelembapan di sekitar tanaman.";
    case 3: return "Kelembapan Udara " + String(h_air, 1) + " %, di atas ambang batas (" + String(airHumMin, 0) + "-" + String(airHumMax, 0) + " %). Kurangi kelembapan media tanam.";
    case 4: return "Intensitas Cahaya " + String(lux, 0) + " lx, di bawah ambang batas (" + String(luxMin, 0) + "-" + String(luxMax, 0) + " lx). Buka paranet.";
    case 5: return "Intensitas Cahaya " + String(lux, 0) + " lx, di atas ambang batas (" + String(luxMin, 0) + "-" + String(luxMax, 0) + " lx). Tutup paranet atau lakukan penyiraman.";
    case 6: return "Media tanam kekurangan unsur hara (" + haraKurang(n, p, k) + "). Segera lakukan pemupukan atau ganti media tanam.";
    case 7: return "Unsur hara berlebih (" + haraLebih(n, p, k) + "). Kurangi pemberian pupuk dan bilas media tanam.";
    case 8: return "Pompa penyiraman menyala selama " + String(PUMP_MAX_ON_SEC) + " detik.";
  }
  return "";
}

// Kirim notif HANYA saat kondisi berubah, dan maksimal 1x per 5 menit per jenis.
void checkNotifications(uint8_t curBits, unsigned long now, float t_air, float h_air, float lux, int n, int p, int k) {
  uint8_t baru = curBits & ~notifPrev;
  notifPrev = curBits;
  if (!baru) return;

  String msg = "";
  for (uint8_t b = 0; b < 9; b++) {
    if (!(baru & (1 << b))) continue;
    if (notifLast[b] != 0 && (now - notifLast[b] < NOTIF_GAP_MS)) continue;
    String t = notifText(b, t_air, h_air, lux, n, p, k);
    if (t.length() == 0) continue;
    notifLast[b] = now;
    if (msg.length()) msg += "\n\n";
    msg += t;
  }
  if (msg.length()) sendTelegram("PERINGATAN TANAMAN ANGGREK\n\n" + msg);
}

void setup() {
  Serial.begin(115200);
  relayOFF();
  pinMode(DE_RE, OUTPUT);
  digitalWrite(DE_RE, LOW);
  Serial2.begin(4800, SERIAL_8N1, RXD2, TXD2);
  dht.setType(22);
  dht.setDisableIRQ(true);
  Wire.begin(21, 22);
  lightMeter.begin();
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Menghubungkan WiFi");
  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry < 25) {
    delay(500);
    Serial.print(".");
    retry++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.printf("\nWiFi Konek: %s\n", WiFi.localIP().toString().c_str());
    fetchThresholdsFromFirebase();
    fetchControlFromFirebase();
  } else {
    Serial.println("\nWiFi Gagal Konek (Jalan offline)");
  }
  Serial.println("\n==========================================");
  Serial.println("  SISTEM MONITORING 7-IN-1 & FIREBASE");
  Serial.println("==========================================");
}
void loop() {
  delay(2000);

  unsigned long now = millis();

  // Sinkronisasi threshold dari web tiap 10 detik
  if (now - lastThFetch >= 10000 || lastThFetch == 0) {
    lastThFetch = now;
    fetchThresholdsFromFirebase();
  }

  // Sinkronisasi kontrol (start/stop monitoring & siram manual) tiap 5 detik
  if (now - lastCtrlFetch >= 5000 || lastCtrlFetch == 0) {
    lastCtrlFetch = now;
    fetchControlFromFirebase();
  }

  // Perintah siram manual dari web
  if (pendingManualPump) {
    pendingManualPump = false;
    if (!pumpIsOn) {
      relayON();
      pumpStartTime = now;
      pumpManual = true;
      sendTelegram("PERINGATAN TANAMAN ANGGREK\n\nPompa penyiraman menyala (siram manual dari web) selama " + String(pumpDurationSec) + " detik.");
      Serial.println("[Kontrol] Siram manual dari web: pompa MENYALA.");
    } else {
      Serial.println("[Kontrol] Siram manual diabaikan: pompa sedang menyala.");
    }
  }

  // Auto-off pompa siram manual (tetap jalan walau monitoring dimatikan / sensor error)
  if (pumpIsOn && pumpManual && (now - pumpStartTime >= (unsigned long)pumpDurationSec * 1000UL)) {
    relayOFF();
    pumpManual = false;
    pumpStopTime = now;
    Serial.println("[Kontrol] Siram manual selesai: pompa MATI.");
  }

  // Gerbang Start/Stop monitoring dari web
  if (ctrlSensorOn == 0) {
    if (pumpIsOn && !pumpManual) {
      relayOFF();
      pumpStopTime = now;
    }
    if (lastSensorOnPrint != 0) {
      lastSensorOnPrint = 0;
      Serial.println("[Kontrol] Monitoring DIMATIKAN dari web. Menunggu perintah Start...");
    }
    return;
  } else if (lastSensorOnPrint != 1) {
    lastSensorOnPrint = 1;
    Serial.println("[Kontrol] Monitoring DIAKTIFKAN dari web.");
  }

  // 1. Baca DHT22 & BH1750
  bool dhtOk = (dht.read() == DHTLIB_OK);
  float t_air = dht.getTemperature();
  float h_air = dht.getHumidity();
  float lux = lightMeter.readLightLevel();

  // 2. Baca CWT 7-in-1
  digitalWrite(DE_RE, HIGH);
  delayMicroseconds(500);
  Serial2.write(query, sizeof(query));
  Serial2.flush();
  delayMicroseconds(2500);
  digitalWrite(DE_RE, LOW);

  delay(200);

  int len = 0;
  while (Serial2.available() && len < sizeof(buf)) {
    buf[len++] = Serial2.read();
  }

  bool cwtOk = (len >= 19 && buf[0] == 0x01 && buf[1] == 0x03 && buf[2] == 0x0E);

  if (dhtOk && cwtOk) {
    float moist = ((buf[3] << 8) | buf[4]) / 10.0;
    float temp  = ((buf[5] << 8) | buf[6]) / 10.0;
    int   ec    = (buf[7] << 8) | buf[8];
    float ph    = ((buf[9] << 8) | buf[10]) / 10.0;
    int   n     = (buf[11] << 8) | buf[12];
    int   p     = (buf[13] << 8) | buf[14];
    int   k     = (buf[15] << 8) | buf[16];

    // Deteksi Anomali Berdasarkan Threshold Dinamis Web
    String reason = "";
    if (t_air < airTempMin || t_air > airTempMax) reason += "Suhu-Udara ";
    if (h_air < airHumMin  || h_air > airHumMax)  reason += "Lembap-Udara ";
    if (lux < luxMin || lux > luxMax)             reason += "Cahaya ";
    if (n < nMin || n > nMax)                     reason += "N ";
    if (p < pMin || p > pMax)                     reason += "P ";
    if (k < kMin || k > kMax)                     reason += "K ";

    String pumpStatus = "";
    bool wetEvent = false;
    if (pumpManual) {
      unsigned long sisa = ((unsigned long)pumpDurationSec * 1000UL - (now - pumpStartTime) + 999) / 1000;
      pumpStatus = "MENYIRAM (Manual, Sisa " + String(sisa) + "s)";
    } else if (reason.length() > 0) {
      if (ctrlAutoWater == 0) {
        if (pumpIsOn) {
          relayOFF();
          pumpStopTime = now;
        }
        pumpStatus = "MATI (Otomatis OFF)";
      } else if (pumpIsOn) {
        if (now - pumpStartTime >= PUMP_MAX_ON_MS) {
          relayOFF();
          pumpStopTime = now;
          pumpStatus = "CUTOFF";
        } else {
          unsigned long sisa = (PUMP_MAX_ON_MS - (now - pumpStartTime) + 999) / 1000;
          pumpStatus = "MENYIRAM (Sisa " + String(sisa) + "s)";
        }
      } else {
        if (now - pumpStopTime >= PUMP_COOLDOWN_MS || pumpStopTime == 0) {
          relayON();
          pumpStartTime = now;
          wetEvent = true;
          pumpStatus = "AKTIF";
        } else {
          unsigned long sisa = (PUMP_COOLDOWN_MS - (now - pumpStopTime) + 999) / 1000;
          pumpStatus = "STANDBY (Sisa " + String(sisa) + "s)";
        }
      }
    } else {
      if (pumpIsOn) {
        relayOFF();
        pumpStopTime = now;
      }
      pumpStatus = "MATI";
    }
    // Notifikasi Telegram (hanya saat kondisi berubah)
    uint8_t curBits = 0;
    if (t_air < airTempMin) curBits |= (1 << 0);
    if (t_air > airTempMax) curBits |= (1 << 1);
    if (h_air < airHumMin)  curBits |= (1 << 2);
    if (h_air > airHumMax)  curBits |= (1 << 3);
    if (lux < luxMin)       curBits |= (1 << 4);
    if (lux > luxMax)       curBits |= (1 << 5);
    if (n < nMin || p < pMin || k < kMin) curBits |= (1 << 6);
    if (n > nMax || p > pMax || k > kMax) curBits |= (1 << 7);
    if (wetEvent)           curBits |= (1 << 8);
    checkNotifications(curBits, now, t_air, h_air, lux, n, p, k);

    // Kirim data ke Firebase tiap 3 detik
    if (now - lastFbSend >= 3000) {
      lastFbSend = now;
      sendToFirebase(t_air, h_air, lux, moist, temp, ec, ph, n, p, k, pumpStatus.c_str(), (reason.length() > 0 ? ("Abnormal (" + reason + ")").c_str() : "Normal"));
    }

    Serial.println("------------------------------------------");
    Serial.printf("[SENSOR LINGKUNGAN]\n");
    Serial.printf("  Suhu Udara       : %.1f C\n", t_air);
    Serial.printf("  Kelembapan Udara : %.1f %%\n", h_air);
    Serial.printf("  Intensitas Cahaya: %.1f lx\n", lux);
    Serial.printf("[SENSOR TANAH]\n");
    Serial.printf("  1. Kelembapan    : %.1f %%\n", moist);
    Serial.printf("  2. Suhu Tanah    : %.1f C\n", temp);
    Serial.printf("  3. EC (Salinitas): %d us/cm\n", ec);
    Serial.printf("  4. Derajat pH    : %.1f\n", ph);
    Serial.printf("  5. Nitrogen (N)  : %d mg/kg (ppm)\n", n);
    Serial.printf("  6. Fosfor (P)    : %d mg/kg (ppm)\n", p);
    Serial.printf("  7. Kalium (K)    : %d mg/kg (ppm)\n\n", k);
    Serial.printf("  Kondisi Lahan    : %s\n", (reason.length() > 0 ? ("Abnormal (" + reason + ")").c_str() : "Normal"));
    Serial.printf("  Status Pompa     : %s\n", pumpStatus.c_str());
    Serial.println("------------------------------------------");
  } else {
    if (pumpIsOn) relayOFF();
    Serial.println("------------------------------------------");
    Serial.printf("Koneksi Gagal [DHT22: %s | CWT-7in1: %s]\n",
                  dhtOk ? "OK" : "ERROR", cwtOk ? "OK" : "ERROR");
    Serial.println("------------------------------------------");
  }
}