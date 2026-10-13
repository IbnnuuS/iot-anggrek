#include <dhtnew.h>

#define DHTPIN 25
#define RXD2 16
#define TXD2 17
#define DE_RE 18
#define RELAY_PIN 23

// Ambang Batas Ideal
#define AIR_TEMP_MIN 21.0
#define AIR_TEMP_MAX 30.0
#define AIR_HUM_MIN  60.0
#define AIR_HUM_MAX  80.0

#define N_MIN 100
#define N_MAX 200
#define P_MIN 50
#define P_MAX 100
#define K_MIN 100
#define K_MAX 200

// Durasi Pompa (Detik)
#define PUMP_MAX_ON_SEC   5
#define PUMP_COOLDOWN_SEC 20

#define PUMP_MAX_ON_MS   (PUMP_MAX_ON_SEC * 1000UL)
#define PUMP_COOLDOWN_MS (PUMP_COOLDOWN_SEC * 1000UL)

DHTNEW dht(DHTPIN);

const byte query[] = {0x01, 0x03, 0x00, 0x00, 0x00, 0x07, 0x04, 0x08};
byte buf[32];

bool pumpIsOn = false;
unsigned long pumpStartTime = 0;
unsigned long pumpStopTime  = 0;

void relayON()  { pinMode(RELAY_PIN, OUTPUT); digitalWrite(RELAY_PIN, LOW); pumpIsOn = true; }
void relayOFF() { pinMode(RELAY_PIN, INPUT); pumpIsOn = false; }

void setup() {
  Serial.begin(115200);
  relayOFF();

  pinMode(DE_RE, OUTPUT);
  digitalWrite(DE_RE, LOW);

  Serial2.begin(4800, SERIAL_8N1, RXD2, TXD2);
  dht.setType(22);
  dht.setDisableIRQ(true);

  Serial.println("\n==========================================");
  Serial.println("  SISTEM MONITORING 7-IN-1 & PENYIRAMAN");
  Serial.println("==========================================");
}

void loop() {
  delay(2000);

  // 1. Baca DHT22
  bool dhtOk = (dht.read() == DHTLIB_OK);
  float t_air = dht.getTemperature();
  float h_air = dht.getHumidity();

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
    // 7 Parameter Lengkap
    float moist = ((buf[3] << 8) | buf[4]) / 10.0;
    float temp  = ((buf[5] << 8) | buf[6]) / 10.0;
    int   ec    = (buf[7] << 8) | buf[8];
    float ph    = ((buf[9] << 8) | buf[10]) / 10.0;
    int   n     = (buf[11] << 8) | buf[12];
    int   p     = (buf[13] << 8) | buf[14];
    int   k     = (buf[15] << 8) | buf[16];

    // Deteksi Anomali
    String reason = "";
    if (t_air < AIR_TEMP_MIN || t_air > AIR_TEMP_MAX) reason += "Suhu-Udara ";
    if (h_air < AIR_HUM_MIN  || h_air > AIR_HUM_MAX)  reason += "Lembap-Udara ";
    if (n < N_MIN || n > N_MAX)                       reason += "N ";
    if (p < P_MIN || p > P_MAX)                       reason += "P ";
    if (k < K_MIN || k > K_MAX)                       reason += "K ";

    unsigned long now = millis();
    String pumpStatus = "";

    if (reason.length() > 0) {
      if (pumpIsOn) {
        if (now - pumpStartTime >= PUMP_MAX_ON_MS) {
          relayOFF();
          pumpStopTime = now;
          pumpStatus = "CUTOFF (Maksimal 5 detik tercapai)";
        } else {
          unsigned long sisa = (PUMP_MAX_ON_MS - (now - pumpStartTime) + 999) / 1000;
          pumpStatus = "MENYIRAM (Sisa " + String(sisa) + " detik)";
        }
      } else {
        if (now - pumpStopTime >= PUMP_COOLDOWN_MS || pumpStopTime == 0) {
          relayON();
          pumpStartTime = now;
          pumpStatus = "AKTIF (Mulai siram 5 detik)";
        } else {
          unsigned long sisa = (PUMP_COOLDOWN_MS - (now - pumpStopTime) + 999) / 1000;
          pumpStatus = "STANDBY (Jeda sisa " + String(sisa) + " detik)";
        }
      }
    } else {
      if (pumpIsOn) {
        relayOFF();
        pumpStopTime = now;
      }
      pumpStatus = "MATI (Semua parameter ideal)";
    }

    // Tampilan Detail 7-in-1
    Serial.println("------------------------------------------");
    Serial.printf("[SENSOR DHT22]\n");
    Serial.printf("  Suhu Udara       : %.1f C\n", t_air);
    Serial.printf("  Kelembapan Udara : %.1f %%\n", h_air);
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