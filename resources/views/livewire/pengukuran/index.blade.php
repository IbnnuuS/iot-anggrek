<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Riwayat Monitoring</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Hasil monitoring lengkap (suhu &amp; kelembapan udara, intensitas cahaya, serta unsur hara) tersimpan
                otomatis setiap 5 menit. Riwayat dapat dihapus manual kapan saja.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div
                class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                <span class="relative flex h-2 w-2">
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </span>
                Riwayat Otomatis Aktif
            </div>
        </div>
    </div>

    @if ($savedMessage)
        <div
            class="rounded-xl border border-emerald-500/30 bg-emerald-50 p-4 text-sm font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
            {{ $savedMessage }}

        </div>
    @endif


    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Riwayat</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span
                    class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ number_format($totalRiwayat, 0) }}</span>
                <span class="text-lg font-medium text-zinc-500">data</span>
            </div>
            <div class="mt-2 text-xs text-zinc-400">Tersimpan otomatis setiap 10 menit</div>
        </div>


        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Tercatat Hari Ini</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span
                    class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ number_format($riwayatHariIni, 0) }}</span>
                <span class="text-lg font-medium text-zinc-500">data</span>
            </div>
            <div class="mt-2 text-xs text-zinc-400">Sejak 00:00 WIB</div>
        </div>


        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @php $fresh = $terakhirRekam && $terakhirRekam->recorded_at->gt(now()->subMinutes(10)); @endphp
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Pencatatan Terakhir</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">
                    {{ $terakhirRekam ? $terakhirRekam->recorded_at->format('H:i') : '-' }}

                </span>
                <span class="text-lg font-medium text-zinc-500">WIB</span>
            </div>
            <div class="mt-2 text-xs text-zinc-400">
                @if ($terakhirRekam)
                    {{ $terakhirRekam->recorded_at->locale('id')->diffForHumans() }} ·
                    {{ $terakhirRekam->recorded_at->format('d M Y') }}
                @else
                    Belum ada pencatatan
                @endif
            </div>
        </div>
    </div>


    <div
        class="space-y-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Filter Riwayat
            </h2>
            @if ($search || $filterSensor || $dateFrom || $dateTo)
                <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="resetFilter">Reset Filter
                </flux:button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <flux:input wire:model.live.debounce.400ms="search" label="Cari" icon="magnifying-glass"
                placeholder="Cari kondisi lahan" />

            <flux:select wire:model.live="filterSensor" label="Sensor" icon="cpu-chip">
                <option value="">Semua Sensor</option>
                @foreach ($sensors as $sensor)
                    <option value="{{ $sensor->id }}">{{ $sensor->name }}</option>
                @endforeach
            </flux:select>
            <flux:input type="date" wire:model.live="dateFrom" label="Dari Tanggal" icon="calendar" />

            <flux:input type="date" wire:model.live="dateTo" label="Sampai Tanggal" icon="calendar" />
        </div>
    </div>


    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div
            class="flex flex-col gap-3 border-b border-zinc-200 px-5 py-3.5 xl:flex-row xl:items-center xl:justify-between dark:border-zinc-800">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Data Riwayat
                </h2>
                <span
                    class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                    {{ number_format($pengukurans->total(), 0) }} data
                </span>
            </div>


            <div class="flex flex-wrap items-center gap-2 xl:flex-nowrap xl:justify-end">
                @if ($totalRiwayat > 0)
                    <div
                        class="flex flex-wrap items-center gap-2 rounded-lg bg-zinc-50 p-1.5 pl-2.5 dark:bg-zinc-800/40">
                        <flux:input type="date" size="sm" wire:model="bulkDate" class="sm:w-28 xl:w-36"
                            aria-label="Tanggal yang akan dihapus" />
                        <flux:button size="sm" variant="danger" icon="trash" wire:click="confirmBulkDelete"
                            class="whitespace-nowrap">Hapus Tanggal Ini</flux:button>
                    </div>
                @endif

                <flux:button size="sm" variant="primary" icon="arrow-path" wire:click="captureNow"
                    wire:loading.attr="disabled" wire:target="captureNow" class="shrink-0 whitespace-nowrap"><span
                        wire:loading.remove wire:target="captureNow">Simpan Sekarang</span>
                    <span wire:loading wire:target="captureNow">Mengambil data...</span>
                </flux:button>
            </div>
        </div>

        @if ($pengukurans->isEmpty())
            <div class="px-6 py-16 text-center">
                <div
                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="{{ $totalRiwayat === 0 ? 'clock' : 'magnifying-glass' }}" class="text-zinc-400" />
                </div>

                @if ($totalRiwayat === 0)
                    <flux:heading size="lg" class="mb-1">Belum ada riwayat monitoring</flux:heading>
                    <flux:text class="text-zinc-500">Riwayat terisi otomatis setiap 5 menit, atau tekan "Simpan
                        Sekarang" untuk mencatat manual.</flux:text>
                @else
                    <flux:heading size="lg" class="mb-1">Tidak ada riwayat yang cocok</flux:heading>
                    <flux:text class="text-zinc-500">Coba ubah kata kunci, sensor, atau rentang tanggalnya.</flux:text>
                    <flux:button size="sm" variant="subtle" class="mt-4" icon="arrow-path"
                        wire:click="resetFilter">Reset Filter</flux:button>
                @endif
            </div>
        @else
            <div class="hidden overflow-x-auto p-4 xl:block">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Waktu Rekam</flux:table.column>
                        <flux:table.column>Lingkungan</flux:table.column>
                        <flux:table.column>Tanah</flux:table.column>
                        <flux:table.column>Nutrisi NPK</flux:table.column>
                        <flux:table.column>Kondisi</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($pengukurans as $pengukuran)
                            @php
                                $abnormal = str_starts_with((string) $pengukuran->kondisi_lahan, 'Abnormal');
                                $menyala = str_contains(strtoupper((string) $pengukuran->pump_status), 'MENYALA');
                            @endphp
                            <flux:table.row key="{{ $pengukuran->id }}">
                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $pengukuran->recorded_at->locale('id')->translatedFormat('d M Y') }}

                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        {{ $pengukuran->recorded_at->format('H:i:s') }} WIB
                                    </div>
                                </flux:table.cell>


                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <div class="grid grid-cols-[4.5rem_1fr] gap-y-1 text-xs tabular-nums">
                                        <span class="text-zinc-400 dark:text-zinc-500">Suhu</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->temperature_c !== null ? number_format($pengukuran->temperature_c, 1) . ' °C' : '-' }}

                                        </span>

                                        <span class="text-zinc-400 dark:text-zinc-500">RH</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->humidity_pct !== null ? number_format($pengukuran->humidity_pct, 1) . ' %' : '-' }}

                                        </span>

                                        <span class="text-zinc-400 dark:text-zinc-500">Cahaya</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->lux !== null ? number_format($pengukuran->lux, 0) . ' lx' : '-' }}

                                        </span>
                                    </div>
                                </flux:table.cell>


                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <div class="grid grid-cols-[4.5rem_1fr] gap-y-1 text-xs tabular-nums">
                                        <span class="text-zinc-400 dark:text-zinc-500">Lembap</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->soil_moisture_pct !== null ? number_format($pengukuran->soil_moisture_pct, 1) . ' %' : '-' }}

                                        </span>

                                        <span class="text-zinc-400 dark:text-zinc-500">pH</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->soil_ph !== null ? number_format($pengukuran->soil_ph, 1) : '-' }}

                                        </span>

                                        <span class="text-zinc-400 dark:text-zinc-500">EC</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->soil_ec !== null ? $pengukuran->soil_ec . ' µS/cm' : '-' }}

                                        </span>
                                    </div>
                                </flux:table.cell>


                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <div class="grid grid-cols-[4.5rem_1fr] gap-y-1 text-xs tabular-nums">
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">N</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->nitrogen !== null ? $pengukuran->nitrogen . ' ppm' : '-' }}

                                        </span>

                                        <span class="font-semibold text-purple-600 dark:text-purple-400">P</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->phosphorus !== null ? $pengukuran->phosphorus . ' ppm' : '-' }}

                                        </span>

                                        <span class="font-semibold text-teal-600 dark:text-teal-400">K</span>
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $pengukuran->potassium !== null ? $pengukuran->potassium . ' ppm' : '-' }}

                                        </span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <flux:badge size="sm" color="{{ $abnormal ? 'amber' : 'emerald' }}"
                                        icon="{{ $abnormal ? 'exclamation-triangle' : 'check-circle' }}">
                                        {{ $pengukuran->kondisi_lahan ?? 'Normal' }}
                                    </flux:badge>
                                    <div class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                                        Pompa:
                                        <span
                                            class="font-medium {{ $menyala ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-300' }}">{{ $pengukuran->pump_status ?? '-' }}</span>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            aria-label="Hapus riwayat ini"
                                            class="text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                            wire:click="confirmDelete({{ $pengukuran->id }})"
                                            tooltip="Hapus riwayat" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>


            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:hidden">
                @foreach ($pengukurans as $pengukuran)
                    @php
                        $abnormal = str_starts_with((string) $pengukuran->kondisi_lahan, 'Abnormal');
                        $menyala = str_contains(strtoupper((string) $pengukuran->pump_status), 'MENYALA');
                        // Badge kartu: buang prefix "Abnormal - " agar footer tetap 1 baris (teks penuh via tooltip).
                        $kondisi = trim((string) $pengukuran->kondisi_lahan) ?: 'Normal';
                        $kondisiSingkat =
                            trim((string) preg_replace('/^abnormal\s*[-–—:]\s*/i', '', $kondisi)) ?: $kondisi;
                        // [label, nilai, tooltip, warna label] — 9 metrik per kartu, grid seragam.
                        $labelMuted = 'text-zinc-500 dark:text-zinc-400';
                        $metrik = [
                            [
                                'Suhu',
                                $pengukuran->temperature_c !== null
                                    ? number_format($pengukuran->temperature_c, 1) . ' °C'
                                    : null,
                                'Suhu udara',
                                $labelMuted,
                            ],
                            [
                                'RH',
                                $pengukuran->humidity_pct !== null
                                    ? number_format($pengukuran->humidity_pct, 1) . ' %'
                                    : null,
                                'Kelembapan udara',
                                $labelMuted,
                            ],
                            [
                                'Cahaya',
                                $pengukuran->lux !== null ? number_format($pengukuran->lux, 0) . ' lx' : null,
                                'Intensitas cahaya',
                                $labelMuted,
                            ],
                            [
                                'Lembap',
                                $pengukuran->soil_moisture_pct !== null
                                    ? number_format($pengukuran->soil_moisture_pct, 1) . ' %'
                                    : null,
                                'Kelembapan tanah',
                                $labelMuted,
                            ],
                            [
                                'pH',
                                $pengukuran->soil_ph !== null ? number_format($pengukuran->soil_ph, 1) : null,
                                'pH tanah',
                                $labelMuted,
                            ],
                            [
                                'EC',
                                $pengukuran->soil_ec !== null ? $pengukuran->soil_ec . ' µS/cm' : null,
                                'Daya hantar listrik tanah',
                                $labelMuted,
                            ],
                            [
                                'N',
                                $pengukuran->nitrogen !== null ? $pengukuran->nitrogen . ' ppm' : null,
                                'Nitrogen (ppm)',
                                'text-indigo-600 dark:text-indigo-400',
                            ],
                            [
                                'P',
                                $pengukuran->phosphorus !== null ? $pengukuran->phosphorus . ' ppm' : null,
                                'Fosfor (ppm)',
                                'text-purple-600 dark:text-purple-400',
                            ],
                            [
                                'K',
                                $pengukuran->potassium !== null ? $pengukuran->potassium . ' ppm' : null,
                                'Kalium (ppm)',
                                'text-teal-600 dark:text-teal-400',
                            ],
                        ];
                    @endphp

                    <article wire:key="pengukuran-{{ $pengukuran->id }}"
                        class="relative flex flex-col overflow-hidden rounded-xl border border-l-4 bg-white shadow-sm transition hover:border-zinc-300 hover:shadow-md dark:bg-zinc-900 {{ $abnormal
                            ? 'border-zinc-200 border-l-amber-400 dark:border-zinc-800 dark:border-l-amber-500'
                            : 'border-zinc-200 border-l-emerald-400 dark:border-zinc-800 dark:border-l-emerald-500' }}">


                        <div class="absolute right-2 top-2 z-10">
                            <flux:button size="sm" variant="ghost" icon="trash"
                                aria-label="Hapus riwayat ini"
                                class="size-9 justify-center text-zinc-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30"
                                wire:click="confirmDelete({{ $pengukuran->id }})" tooltip="Hapus riwayat" />
                        </div>

                        <div class="flex flex-1 flex-col gap-3 rounded-xl p-4 pl-5">

                            <div class="pr-12">
                                <div class="flex items-baseline gap-1.5">
                                    <span
                                        class="text-lg font-bold leading-none tabular-nums text-zinc-900 dark:text-zinc-100">
                                        {{ $pengukuran->recorded_at->format('H:i') }}

                                    </span>
                                    <span class="text-[11px] font-medium text-zinc-400">WIB</span>
                                </div>
                                <div class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400"
                                    title="{{ $pengukuran->recorded_at->locale('id')->translatedFormat('d M Y') }}@if ($pengukuran->sensor) · {{ $pengukuran->sensor->name }} @endif">
                                    {{ $pengukuran->recorded_at->locale('id')->translatedFormat('d M Y') }}

                                    @if ($pengukuran->sensor)
                                        · {{ $pengukuran->sensor->name }}
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-1.5 min-[420px]:grid-cols-3">
                                @foreach ($metrik as [$label, $nilai, $hint, $labelClass])
                                    <div class="flex flex-col items-start gap-1 rounded-md bg-zinc-50 px-2 py-1.5 last:col-span-2 min-[420px]:last:col-span-1 dark:bg-zinc-800/60"
                                        title="{{ $nilai === null ? $hint . ' — tidak tersedia' : $hint }}">
                                        <span
                                            class="text-[11px] font-medium leading-none {{ $labelClass }}">{{ $label }}</span>
                                        <span
                                            class="whitespace-nowrap text-xs font-semibold leading-none tabular-nums {{ $nilai === null ? 'text-zinc-300 dark:text-zinc-600' : 'text-zinc-800 dark:text-zinc-100' }}">{{ $nilai ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div
                            class="flex flex-nowrap items-center justify-between gap-x-3 border-t border-zinc-100 px-4 py-2 pl-5 dark:border-zinc-800">
                            <flux:badge size="sm" color="{{ $abnormal ? 'amber' : 'emerald' }}"
                                icon="{{ $abnormal ? 'exclamation-triangle' : 'check-circle' }}"
                                title="{{ $kondisi }}">{{ $kondisiSingkat }}
                            </flux:badge>

                            <span
                                class="inline-flex shrink-0 items-center gap-1 whitespace-nowrap text-[11px] font-medium {{ $menyala ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                <flux:icon name="bolt" variant="micro" />
                                Pompa: {{ $pengukuran->pump_status ?? '-' }}

                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($pengukurans->isNotEmpty())
            <div
                class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
                <span>
                    Menampilkan
                    {{ number_format($pengukurans->firstItem(), 0) }}–{{ number_format($pengukurans->lastItem(), 0) }}

                    dari {{ number_format($pengukurans->total(), 0) }} riwayat
                </span>
                @if ($pengukurans->hasPages())
                    {{ $pengukurans->links() }}
                @endif
            </div>
        @endif
    </div>


    @if ($showDeleteModal)
        <flux:modal wire:model="showDeleteModal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Konfirmasi Hapus Riwayat</flux:heading>
                    <flux:text class="mt-2">Apakah Anda yakin ingin menghapus riwayat monitoring ini? Data akan
                        dihapus permanen dan tidak dapat dikembalikan.</flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" size="sm" wire:click="cancelDelete">Batal</flux:button>
                    <flux:button variant="danger" size="sm" wire:click="destroy">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif


    @if ($showBulkModal)
        <flux:modal wire:model="showBulkModal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Hapus Semua Riwayat Tanggal Ini?</flux:heading>
                    <flux:text class="mt-2">Seluruh riwayat tanggal
                        <span
                            class="font-semibold">{{ \Illuminate\Support\Carbon::parse($bulkDate)->locale('id')->translatedFormat('d F Y') }}</span>
                        sebanyak <span class="font-semibold">{{ number_format($bulkCount, 0) }} data</span> akan
                        dihapus
                        permanen dan tidak dapat dikembalikan.
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" size="sm" wire:click="cancelBulkDelete">Batal</flux:button>
                    <flux:button variant="danger" size="sm" wire:click="destroyByDate"
                        :disabled="$bulkCount === 0">Hapus Semua</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
