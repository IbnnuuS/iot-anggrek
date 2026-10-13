<div>
    <flux:header>
        <div>
            <flux:heading>Detail Pengukuran</flux:heading>
            <flux:text>{{ $pengukuran->recorded_at->format('d F Y, H:i:s') }}</flux:text>
        </div>
        <flux:spacer />
        <flux:button variant="ghost" icon="arrow-left" :href="route('pengukuran.index')" wire:navigate>
            Kembali ke Daftar
        </flux:button>
    </flux:header>

    <flux:main class="space-y-6">
        {{-- Status Banner --}}
        <flux:card>
            <div class="flex items-center justify-between p-2">
                <div class="flex items-center gap-4">
                    @if($isInRange)
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon name="check-circle" class="text-green-600 dark:text-green-400" size="lg" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-green-700 dark:text-green-400">Kondisi Normal</flux:heading>
                            <flux:text class="text-green-600 dark:text-green-500">Semua parameter dalam range optimal</flux:text>
                        </div>
                    @else
                        <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon name="exclamation-circle" class="text-red-600 dark:text-red-400" size="lg" />
                        </div>
                        <div>
                            <flux:heading size="lg" class="text-red-700 dark:text-red-400">Peringatan!</flux:heading>
                            <flux:text class="text-red-600 dark:text-red-500">Terdeteksi {{ count($violations) }} pelanggaran parameter</flux:text>
                        </div>
                    @endif
                </div>
            </div>
        </flux:card>

        {{-- Measurement Values - Large Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Temperature --}}
            <flux:card>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <flux:icon name="fire" class="text-orange-600 dark:text-orange-400" size="lg" />
                            </div>
                            <flux:heading size="lg">Temperature</flux:heading>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2">
                            {{ number_format($pengukuran->temperature_c, 1) }}<span class="text-2xl text-zinc-500">°C</span>
                        </div>
                        
                        @if($anggrek && $anggrek->temp_min_c && $anggrek->temp_max_c)
                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:text size="sm" class="text-zinc-500 mb-2">Range Optimal</flux:text>
                                <div class="flex items-center justify-center gap-2">
                                    <flux:badge color="blue">Min: {{ $anggrek->temp_min_c }}°C</flux:badge>
                                    <flux:badge color="blue">Max: {{ $anggrek->temp_max_c }}°C</flux:badge>
                                </div>
                            </div>
                        @else
                            <flux:text size="sm" class="text-zinc-400 mt-4">Range optimal belum diset</flux:text>
                        @endif
                    </div>
                </div>
            </flux:card>

            {{-- Humidity --}}
            <flux:card>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <flux:icon name="cloud" class="text-blue-600 dark:text-blue-400" size="lg" />
                            </div>
                            <flux:heading size="lg">Humidity</flux:heading>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2">
                            {{ number_format($pengukuran->humidity_pct, 1) }}<span class="text-2xl text-zinc-500">%</span>
                        </div>
                        
                        @if($anggrek && $anggrek->hum_min_pct && $anggrek->hum_max_pct)
                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <flux:text size="sm" class="text-zinc-500 mb-2">Range Optimal</flux:text>
                                <div class="flex items-center justify-center gap-2">
                                    <flux:badge color="cyan">Min: {{ $anggrek->hum_min_pct }}%</flux:badge>
                                    <flux:badge color="cyan">Max: {{ $anggrek->hum_max_pct }}%</flux:badge>
                                </div>
                            </div>
                        @else
                            <flux:text size="sm" class="text-zinc-400 mt-4">Range optimal belum diset</flux:text>
                        @endif
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Soil & NPK Parameters (If Recorded) --}}
        @if($pengukuran->soil_moisture_pct !== null || $pengukuran->nitrogen !== null)
            <flux:card>
                <div class="p-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon name="sparkles" class="text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <flux:heading size="lg">Parameter Tanah & Nutrisi NPK (CWT-RS485)</flux:heading>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <flux:text size="sm" class="text-zinc-500">Kelembapan Tanah</flux:text>
                            <div class="text-xl font-bold mt-1">{{ number_format($pengukuran->soil_moisture_pct, 1) }}%</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <flux:text size="sm" class="text-zinc-500">Suhu Tanah</flux:text>
                            <div class="text-xl font-bold mt-1">{{ number_format($pengukuran->soil_temperature_c, 1) }}°C</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <flux:text size="sm" class="text-zinc-500">Derajat pH</flux:text>
                            <div class="text-xl font-bold mt-1">{{ number_format($pengukuran->soil_ph, 1) }}</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <flux:text size="sm" class="text-zinc-500">EC (Salinitas)</flux:text>
                            <div class="text-xl font-bold mt-1">{{ $pengukuran->soil_ec }} µs/cm</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-lg border border-indigo-100 dark:border-indigo-900/40">
                            <div class="flex justify-between items-center">
                                <flux:text size="sm" class="text-indigo-600 dark:text-indigo-400 font-medium">Nitrogen (N)</flux:text>
                                <span class="text-xs text-zinc-400">Target 100-200</span>
                            </div>
                            <div class="text-2xl font-bold mt-1 text-indigo-900 dark:text-indigo-200">{{ $pengukuran->nitrogen ?? '-' }} <span class="text-xs font-normal">ppm</span></div>
                        </div>
                        <div class="p-3 bg-purple-50/50 dark:bg-purple-950/20 rounded-lg border border-purple-100 dark:border-purple-900/40">
                            <div class="flex justify-between items-center">
                                <flux:text size="sm" class="text-purple-600 dark:text-purple-400 font-medium">Fosfor (P)</flux:text>
                                <span class="text-xs text-zinc-400">Target 50-100</span>
                            </div>
                            <div class="text-2xl font-bold mt-1 text-purple-900 dark:text-purple-200">{{ $pengukuran->phosphorus ?? '-' }} <span class="text-xs font-normal">ppm</span></div>
                        </div>
                        <div class="p-3 bg-teal-50/50 dark:bg-teal-950/20 rounded-lg border border-teal-100 dark:border-teal-900/40">
                            <div class="flex justify-between items-center">
                                <flux:text size="sm" class="text-teal-600 dark:text-teal-400 font-medium">Kalium (K)</flux:text>
                                <span class="text-xs text-zinc-400">Target 100-200</span>
                            </div>
                            <div class="text-2xl font-bold mt-1 text-teal-900 dark:text-teal-200">{{ $pengukuran->potassium ?? '-' }} <span class="text-xs font-normal">ppm</span></div>
                        </div>
                    </div>

                    @if($pengukuran->pump_status || $pengukuran->kondisi_lahan)
                        <div class="flex flex-wrap items-center gap-4 text-xs pt-2">
                            @if($pengukuran->pump_status)
                                <div><span class="text-zinc-500">Status Pompa:</span> <span class="font-semibold">{{ $pengukuran->pump_status }}</span></div>
                            @endif
                            @if($pengukuran->kondisi_lahan)
                                <div><span class="text-zinc-500">Evaluasi Lahan:</span> <span class="font-semibold">{{ $pengukuran->kondisi_lahan }}</span></div>
                            @endif
                        </div>
                    @endif
                </div>
            </flux:card>
        @endif

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Sensor Info --}}
            <flux:card>
                <div class="p-4 space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon name="cpu-chip" class="text-blue-600 dark:text-blue-400" />
                        </div>
                        <flux:heading size="lg">Informasi Sensor</flux:heading>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <flux:text size="sm" class="text-zinc-500">Nama Sensor</flux:text>
                            <div class="font-medium text-right">{{ $pengukuran->sensor->name }}</div>
                        </div>

                        @if($pengukuran->sensor->model)
                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Model</flux:text>
                                <flux:badge color="zinc">{{ $pengukuran->sensor->model }}</flux:badge>
                            </div>
                        @endif

                        @if($pengukuran->sensor->serial_no)
                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Serial Number</flux:text>
                                <div class="font-mono text-sm">{{ $pengukuran->sensor->serial_no }}</div>
                            </div>
                        @endif

                        @if($pengukuran->sensor->site_name)
                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Lokasi</flux:text>
                                <div class="text-right">
                                    <div class="font-medium">{{ $pengukuran->sensor->site_name }}</div>
                                    @if($pengukuran->sensor->area_name)
                                        <flux:text size="sm">{{ $pengukuran->sensor->area_name }}</flux:text>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>

            {{-- Anggrek Info --}}
            <flux:card>
                <div class="p-4 space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon name="sparkles" class="text-purple-600 dark:text-purple-400" />
                        </div>
                        <flux:heading size="lg">Informasi Anggrek</flux:heading>
                    </div>

                    @if($anggrek)
                        <div class="space-y-3">
                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Jenis Anggrek</flux:text>
                                <div class="font-medium text-right">{{ $anggrek->jenis_anggrek }}</div>
                            </div>

                            @if($anggrek->nama_anggrek)
                                <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                    <flux:text size="sm" class="text-zinc-500">Nama Anggrek</flux:text>
                                    <div class="text-right">{{ $anggrek->nama_anggrek }}</div>
                                </div>
                            @endif

                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Temp. Optimal</flux:text>
                                <div class="text-right">
                                    @if($anggrek->temp_min_c && $anggrek->temp_max_c)
                                        {{ $anggrek->temp_min_c }}°C - {{ $anggrek->temp_max_c }}°C
                                    @else
                                        <flux:text class="text-zinc-400">Belum diset</flux:text>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:text size="sm" class="text-zinc-500">Humidity Optimal</flux:text>
                                <div class="text-right">
                                    @if($anggrek->hum_min_pct && $anggrek->hum_max_pct)
                                        {{ $anggrek->hum_min_pct }}% - {{ $anggrek->hum_max_pct }}%
                                    @else
                                        <flux:text class="text-zinc-400">Belum diset</flux:text>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon name="information-circle" class="mx-auto text-zinc-300 dark:text-zinc-600 mb-2" />
                            <flux:text class="text-zinc-500">Sensor ini belum dipasang pada anggrek</flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        {{-- Violations Alert --}}
        @if(!empty($violations))
            <flux:card>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon name="exclamation-triangle" class="text-red-600 dark:text-red-400" />
                        </div>
                        <flux:heading size="lg">Pelanggaran Parameter</flux:heading>
                    </div>

                    <div class="space-y-3">
                        @foreach($violations as $violation)
                            <div class="flex items-start gap-3 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                <flux:icon name="exclamation-circle" class="text-red-600 dark:text-red-400 mt-0.5" />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="font-medium text-red-900 dark:text-red-100">
                                            {{ ucfirst($violation['type']) }}
                                        </div>
                                        <flux:badge 
                                            :color="$violation['severity'] === 'high' ? 'red' : 'orange'"
                                            size="sm"
                                        >
                                            {{ $violation['severity'] === 'high' ? 'Terlalu Tinggi' : 'Terlalu Rendah' }}
                                        </flux:badge>
                                    </div>
                                    <flux:text size="sm" class="text-red-700 dark:text-red-300">
                                        {{ $violation['message'] }}
                                    </flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-start gap-2">
                            <flux:icon name="light-bulb" class="text-yellow-600 dark:text-yellow-400 mt-0.5" size="sm" />
                            <flux:text size="sm" class="text-yellow-800 dark:text-yellow-200">
                                <strong>Rekomendasi:</strong> Segera sesuaikan kondisi lingkungan agar kembali ke range optimal. Periksa sistem ventilasi dan penyiraman.
                            </flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>
        @endif
    </flux:main>
</div>
