<div>
    <flux:header>
        <div>
            <flux:heading>Data Anggrek</flux:heading>
            <flux:text>Kelola koleksi anggrek dan parameter optimalnya</flux:text>
        </div>
        <flux:spacer />
        <flux:button :href="route('anggrek.create')" icon="plus" variant="primary" wire:navigate>
            Tambah Anggrek Baru
        </flux:button>
    </flux:header>

    <flux:main class="space-y-6">
        {{-- Stats Cards --}}
        <div class="flex flex-wrap gap-4">
            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg mb-3">
                        <flux:icon name="sparkles" class="text-purple-600 dark:text-purple-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Total Anggrek</flux:text>
                    <flux:heading size="xl">{{ $anggreks->total() }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-green-100 dark:bg-green-900/30 rounded-lg mb-3">
                        <flux:icon name="cpu-chip" class="text-green-600 dark:text-green-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Dengan Sensor</flux:text>
                    <flux:heading size="xl">{{ $anggreks->filter(fn($a) => $a->sensor_id)->count() }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg mb-3">
                        <flux:icon name="fire" class="text-orange-600 dark:text-orange-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Avg. Temp Range</flux:text>
                    <flux:heading size="xl">{{ number_format($anggreks->avg('temp_min_c'), 0) }}-{{ number_format($anggreks->avg('temp_max_c'), 0) }}°</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg mb-3">
                        <flux:icon name="cloud" class="text-blue-600 dark:text-blue-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Avg. Humidity Range</flux:text>
                    <flux:heading size="xl">{{ number_format($anggreks->avg('hum_min_pct'), 0) }}-{{ number_format($anggreks->avg('hum_max_pct'), 0) }}%</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- Search and Filters Card --}}
        <flux:card>
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="funnel" class="text-zinc-500" />
                    <flux:heading size="lg">Filter & Pencarian</flux:heading>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <flux:input 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Cari berdasarkan jenis atau nama anggrek..."
                            icon="magnifying-glass"
                            clearable
                        />
                    </div>
                    <div class="w-full md:w-64">
                        <flux:select wire:model.live="filterSensor" placeholder="Filter Sensor" icon="cpu-chip">
                            <option value="">Semua Sensor</option>
                            @foreach($sensors as $sensor)
                                <option value="{{ $sensor->id }}">{{ $sensor->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- Table Card --}}
        <flux:card>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">Koleksi Anggrek</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">Menampilkan {{ $anggreks->count() }} dari {{ $anggreks->total() }} anggrek</flux:text>
                    </div>
                </div>

                <flux:separator />

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Anggrek</flux:table.column>
                        <flux:table.column>Sensor</flux:table.column>
                        <flux:table.column>Kondisi Optimal</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($anggreks as $anggrek)
                            <flux:table.row :key="$anggrek->id">
                                <flux:table.cell>
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                            <flux:icon name="sparkles" class="text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $anggrek->jenis_anggrek }}</div>
                                            @if($anggrek->nama_anggrek)
                                                <flux:text size="sm" class="text-zinc-500">{{ $anggrek->nama_anggrek }}</flux:text>
                                            @else
                                                <flux:text size="sm" class="text-zinc-400 italic">Belum diberi nama</flux:text>
                                            @endif
                                        </div>
                                    </div>
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    @if($anggrek->sensor)
                                        <flux:badge color="green" icon="cpu-chip">
                                            {{ $anggrek->sensor->name }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="zinc" icon="x-mark">
                                            Tidak ada sensor
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    <div class="space-y-2">
                                        @if($anggrek->temp_min_c && $anggrek->temp_max_c)
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 bg-orange-100 dark:bg-orange-900/30 rounded">
                                                    <flux:icon name="fire" class="text-orange-600 dark:text-orange-400" size="sm" />
                                                </div>
                                                <span class="text-sm">{{ $anggrek->temp_min_c }}° - {{ $anggrek->temp_max_c }}°C</span>
                                            </div>
                                        @endif
                                        @if($anggrek->hum_min_pct && $anggrek->hum_max_pct)
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded">
                                                    <flux:icon name="cloud" class="text-blue-600 dark:text-blue-400" size="sm" />
                                                </div>
                                                <span class="text-sm">{{ $anggrek->hum_min_pct }}% - {{ $anggrek->hum_max_pct }}%</span>
                                            </div>
                                        @endif
                                        @if(!$anggrek->temp_min_c && !$anggrek->hum_min_pct)
                                            <flux:text size="sm" class="text-zinc-400 italic">Belum diset</flux:text>
                                        @endif
                                    </div>
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost" 
                                            icon="pencil" 
                                            :href="route('anggrek.edit', $anggrek)" 
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>
                                        <flux:button 
                                            size="sm" 
                                            variant="danger" 
                                            icon="trash" 
                                            wire:click="$dispatch('deleteAnggrek', { id: {{ $anggrek->id }} })"
                                        >
                                            Hapus
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                @if($anggreks->isEmpty())
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-4">
                            <flux:icon name="sparkles" size="lg" class="text-zinc-400" />
                        </div>
                        <flux:heading size="lg" class="mb-2">Belum Ada Data Anggrek</flux:heading>
                        <flux:text class="text-zinc-500 mb-6 max-w-md mx-auto">
                            Mulai kelola koleksi anggrek Anda dengan menambahkan data jenis anggrek beserta parameter optimal temperature dan humidity-nya.
                        </flux:text>
                        <flux:button :href="route('anggrek.create')" icon="plus" variant="primary" wire:navigate>
                            Tambah Anggrek Pertama
                        </flux:button>
                    </div>
                @endif
            </div>

            @if($anggreks->hasPages())
                <div class="mt-6">
                    {{ $anggreks->links() }}
                </div>
            @endif
        </flux:card>

        {{-- Tips Card --}}
        @if($anggreks->isEmpty())
            <flux:card>
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon name="light-bulb" class="text-purple-600 dark:text-purple-400" size="lg" />
                    </div>
                    <div class="flex-1">
                        <flux:heading size="lg" class="mb-2">Tips Perawatan Anggrek</flux:heading>
                        <ul class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Atur parameter optimal sesuai jenis anggrek yang Anda miliki</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Hubungkan dengan sensor untuk monitoring real-time</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Sistem akan memberikan alert jika kondisi diluar range optimal</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </flux:card>
        @endif
    </flux:main>

    {{-- Delete Modal Component --}}
    <livewire:anggrek.delete />
</div>
