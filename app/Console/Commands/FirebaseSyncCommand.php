<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;

class FirebaseSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync {--sensor= : ID Sensor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ambil snapshot telemetry dari Firebase Realtime Database dan simpan ke database pengukuran';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseService $firebase): int
    {
        $sensorId = $this->option('sensor') ? (int) $this->option('sensor') : null;

        $this->info('Mengambil data dari Firebase...');
        $data = $firebase->getMonitoringData();

        if (!$data) {
            $this->error('Gagal mengambil data dari Firebase Realtime Database.');
            return self::FAILURE;
        }

        $this->table(
            ['Parameter', 'Nilai'],
            [
                ['Suhu Udara', ($data['suhu_udara'] ?? '-') . ' °C'],
                ['Lembap Udara', ($data['lembap_udara'] ?? '-') . ' %'],
                ['Intensitas Cahaya', ($data['lux'] ?? $data['cahaya'] ?? '-') . ' lx'],
                ['Lembap Tanah', ($data['lembap_tanah'] ?? '-') . ' %'],
                ['Suhu Tanah', ($data['suhu_tanah'] ?? '-') . ' °C'],
                ['pH Tanah', $data['ph'] ?? '-'],
                ['EC', ($data['ec'] ?? '-') . ' µs/cm'],
                ['Nitrogen (N)', ($data['n'] ?? '-') . ' ppm'],
                ['Fosfor (P)', ($data['p'] ?? '-') . ' ppm'],
                ['Kalium (K)', ($data['k'] ?? '-') . ' ppm'],
                ['Status Pompa', $data['status_pompa'] ?? '-'],
                ['Kondisi Lahan', $data['kondisi_lahan'] ?? '-'],
            ]
        );

        $record = $firebase->recordSnapshot($sensorId);

        if ($record) {
            $this->info("Berhasil disimpan ke pengukuran (ID: {$record->id}, Sensor ID: {$record->sensor_id})");
            return self::SUCCESS;
        }

        $this->warn('Data berhasil dibaca tapi gagal disimpan ke database.');
        return self::FAILURE;
    }
}
