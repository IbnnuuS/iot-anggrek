<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Pengaturan Threshold Sensor
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Atur rentang ideal parameter sensor. Nilai di luar batas (min–max) memicu penyiraman otomatis.
            </p>
        </div>
    </div>

    @if ($savedMessage)
        <div
            class="rounded-xl border border-emerald-500/30 bg-emerald-50 p-4 text-sm font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
            {{ $savedMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div
            class="rounded-xl border border-rose-500/30 bg-rose-50 p-4 text-sm font-medium text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- 1. LINGKUNGAN & PENCAHAYAAN --}}
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">1. Lingkungan
                    & Pencahayaan (DHT22 & BH1750)</h2>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Suhu Udara --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Suhu Udara</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">°C</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" step="0.1" wire:model="air_temp_min" label="Min"
                            placeholder="21.0" />
                        <flux:input type="number" step="0.1" wire:model="air_temp_max" label="Max"
                            placeholder="30.0" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 21.0°C – 30.0°C</div>
                </div>

                {{-- Kelembapan Udara --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Kelembapan Udara</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">%</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" step="0.1" wire:model="air_hum_min" label="Min"
                            placeholder="60.0" />
                        <flux:input type="number" step="0.1" wire:model="air_hum_max" label="Max"
                            placeholder="80.0" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 60.0% – 80.0%</div>
                </div>

                {{-- Intensitas Cahaya --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Intensitas Cahaya</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">lx</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" step="100" wire:model="lux_min" label="Min"
                            placeholder="10000" />
                        <flux:input type="number" step="100" wire:model="lux_max" label="Max"
                            placeholder="20000" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 10.000 – 20.000 lx</div>
                </div>
            </div>
        </div>

        {{-- 2. NUTRISI MAKRO (NPK) --}}
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">2. Status
                    Nutrisi Makro (NPK CWT-RS485)</h2>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Nitrogen (N) --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Nitrogen (N)</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">ppm</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" wire:model="n_min" label="Min" placeholder="100" />
                        <flux:input type="number" wire:model="n_max" label="Max" placeholder="200" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 100 – 200 ppm</div>
                </div>

                {{-- Fosfor (P) --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Fosfor (P)</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">ppm</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" wire:model="p_min" label="Min" placeholder="50" />
                        <flux:input type="number" wire:model="p_max" label="Max" placeholder="100" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 50 – 100 ppm</div>
                </div>

                {{-- Kalium (K) --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Kalium (K)</span>
                        <span
                            class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">ppm</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <flux:input type="number" wire:model="k_min" label="Min" placeholder="100" />
                        <flux:input type="number" wire:model="k_max" label="Max" placeholder="200" />
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Batas ideal: 100 – 200 ppm</div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
            <flux:button type="button" variant="ghost" size="sm" wire:click="resetDefaults"
                wire:confirm="Kembalikan semua nilai threshold ke default pabrik?">
                Reset Default
            </flux:button>
            <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan</span>
                <span wire:loading>Menyimpan...</span>
            </flux:button>
        </div>
    </form>
</div>
