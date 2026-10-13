<div>
    <flux:header>
        <div>
            <flux:heading>Sensor Management</flux:heading>
            <flux:text>Monitor dan kelola sensor IoT untuk anggrek Anda</flux:text>
        </div>
        <flux:spacer />
        <flux:button :href="route('sensors.create')" icon="plus" variant="primary" wire:navigate>
            Tambah Sensor Baru
        </flux:button>
    </flux:header>

    <flux:main class="space-y-6">
        {{-- Stats Cards --}}
        <div class="flex flex-wrap gap-4">
            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg mb-3">
                        <flux:icon name="cpu-chip" class="text-blue-600 dark:text-blue-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Total Sensor</flux:text>
                    <flux:heading size="xl">{{ $sensors->total() }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-green-100 dark:bg-green-900/30 rounded-lg mb-3">
                        <flux:icon name="signal" class="text-green-600 dark:text-green-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Active</flux:text>
                    <flux:heading size="xl">{{ $sensors->count() }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg mb-3">
                        <flux:icon name="sparkles" class="text-purple-600 dark:text-purple-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Sites</flux:text>
                    <flux:heading size="xl">{{ $sites->count() }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex-1 min-w-[200px]">
                <div class="text-center p-2">
                    <div class="inline-flex p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg mb-3">
                        <flux:icon name="map" class="text-orange-600 dark:text-orange-400" size="lg" />
                    </div>
                    <flux:text size="sm" class="text-zinc-500 block mb-1">Areas</flux:text>
                    <flux:heading size="xl">{{ $areas->count() }}</flux:heading>
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
                            placeholder="Cari berdasarkan nama, model, serial number, atau MAC address..."
                            icon="magnifying-glass"
                            clearable
                            description="Tekan Enter untuk mencari"
                        />
                    </div>
                    <div class="w-full md:w-48">
                        <flux:select wire:model.live="filterSite" placeholder="Filter Site" icon="building-office-2">
                            <option value="">Semua Site</option>
                            @foreach($sites as $site)
                                <option value="{{ $site }}">{{ $site }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="w-full md:w-48">
                        <flux:select wire:model.live="filterArea" placeholder="Filter Area" icon="map">
                            <option value="">Semua Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
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
                        <flux:heading size="lg">Daftar Sensor</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">Menampilkan {{ $sensors->count() }} dari {{ $sensors->total() }} sensor</flux:text>
                    </div>
                </div>

                <flux:separator />

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Sensor</flux:table.column>
                        <flux:table.column>Model & Identitas</flux:table.column>
                        <flux:table.column>Lokasi</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($sensors as $sensor)
                            <flux:table.row :key="$sensor->id">
                                <flux:table.cell>
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                            <flux:icon name="cpu-chip" class="text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $sensor->name }}</div>
                                            @if($sensor->notes)
                                                <flux:text size="sm" class="text-zinc-500 line-clamp-1">{{ $sensor->notes }}</flux:text>
                                            @endif
                                        </div>
                                    </div>
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    <div class="space-y-2">
                                        @if($sensor->model)
                                            <div>
                                                <flux:badge color="zinc" icon="cube">{{ $sensor->model }}</flux:badge>
                                            </div>
                                        @endif
                                        <div class="space-y-1 text-sm">
                                            @if($sensor->serial_no)
                                                <div class="flex items-center gap-2">
                                                    <flux:icon name="hashtag" size="sm" class="text-zinc-400" />
                                                    <span class="font-mono text-xs">{{ $sensor->serial_no }}</span>
                                                </div>
                                            @endif
                                            @if($sensor->mac_address)
                                                <div class="flex items-center gap-2">
                                                    <flux:icon name="wifi" size="sm" class="text-zinc-400" />
                                                    <span class="font-mono text-xs">{{ $sensor->mac_address }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    @if($sensor->site_name)
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <flux:icon name="building-office-2" size="sm" class="text-zinc-400" />
                                                <span class="font-medium">{{ $sensor->site_name }}</span>
                                            </div>
                                            @if($sensor->area_name)
                                                <div class="flex items-center gap-2">
                                                    <flux:icon name="map" size="sm" class="text-zinc-400" />
                                                    <flux:text size="sm">{{ $sensor->area_name }}</flux:text>
                                                </div>
                                            @endif
                                            @if($sensor->zone_label || $sensor->rack_label)
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if($sensor->zone_label)
                                                        <flux:badge color="blue" size="sm">{{ $sensor->zone_label }}</flux:badge>
                                                    @endif
                                                    @if($sensor->rack_label)
                                                        <flux:badge color="purple" size="sm">{{ $sensor->rack_label }}</flux:badge>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-zinc-400">
                                            <flux:icon name="map-pin" size="sm" />
                                            <flux:text size="sm">Belum diset</flux:text>
                                        </div>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge color="green" icon="check-circle">
                                        Online
                                    </flux:badge>
                                </flux:table.cell>
                                
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost" 
                                            icon="pencil" 
                                            :href="route('sensors.edit', $sensor)" 
                                            wire:navigate
                                        >
                                            Edit
                                        </flux:button>
                                        <flux:button 
                                            size="sm" 
                                            variant="danger" 
                                            icon="trash" 
                                            wire:click="$dispatch('deleteSensor', { id: {{ $sensor->id }} })"
                                        >
                                            Hapus
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>

                @if($sensors->isEmpty())
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-4">
                            <flux:icon name="cpu-chip" size="lg" class="text-zinc-400" />
                        </div>
                        <flux:heading size="lg" class="mb-2">Belum Ada Sensor</flux:heading>
                        <flux:text class="text-zinc-500 mb-6 max-w-md mx-auto">
                            Mulai monitoring anggrek Anda dengan menambahkan sensor IoT pertama. Sensor akan membantu memantau kondisi lingkungan secara real-time.
                        </flux:text>
                        <flux:button :href="route('sensors.create')" icon="plus" variant="primary" wire:navigate>
                            Tambah Sensor Pertama
                        </flux:button>
                    </div>
                @endif
            </div>

            @if($sensors->hasPages())
                <div class="mt-6">
                    {{ $sensors->links() }}
                </div>
            @endif
        </flux:card>

        {{-- Help Card --}}
        @if($sensors->isEmpty())
            <flux:card>
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon name="information-circle" class="text-blue-600 dark:text-blue-400" size="lg" />
                    </div>
                    <div class="flex-1">
                        <flux:heading size="lg" class="mb-2">Tips Memulai</flux:heading>
                        <ul class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Pastikan sensor IoT sudah terpasang dan terhubung dengan jaringan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Catat MAC Address dari sensor untuk registrasi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm" class="text-green-500 mt-0.5" />
                                <span>Berikan nama yang mudah diingat untuk memudahkan identifikasi</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <flux:icon name="check" size="sm " class="text-green-500 mt-0.5" />
                                <span>Atur lokasi (Site, Area, Zone) agar mudah di-track</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </flux:card>
        @endif
    </flux:main>

    {{-- Delete Modal Component --}}
    <livewire:sensor.delete />
</div>
