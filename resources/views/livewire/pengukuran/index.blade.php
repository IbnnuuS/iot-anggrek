<div>
    <flux:header>
        <flux:heading>Data Pengukuran</flux:heading>
        <flux:spacer />
        <flux:badge color="blue" icon="chart-bar" size="lg">{{ $pengukurans->total() }} Records</flux:badge>
    </flux:header>

    <flux:main class="space-y-6">
        {{-- Filters Card --}}
        <flux:card>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:select wire:model.live="filterSensor" label="Sensor" placeholder="Semua Sensor" icon="cpu-chip">
                    <option value="">Semua Sensor</option>
                    @foreach($sensors as $sensor)
                        <option value="{{ $sensor->id }}">{{ $sensor->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input 
                    type="date" 
                    wire:model.live="dateFrom" 
                    label="Dari Tanggal" 
                    icon="calendar"
                />

                <flux:input 
                    type="date" 
                    wire:model.live="dateTo" 
                    label="Sampai Tanggal" 
                    icon="calendar"
                />
            </div>
        </flux:card>

        {{-- Table Card --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Waktu Rekam</flux:table.column>
                    <flux:table.column>Sensor</flux:table.column>
                    <flux:table.column>Udara (DHT22)</flux:table.column>
                    <flux:table.column>Tanah (M/pH/EC)</flux:table.column>
                    <flux:table.column>NPK (ppm)</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($pengukurans as $pengukuran)
                        <flux:table.row :key="$pengukuran->id">
                            <flux:table.cell>
                                <div class="space-y-0.5">
                                    <div class="font-medium">{{ $pengukuran->recorded_at->format('d M Y') }}</div>
                                    <flux:text size="sm">{{ $pengukuran->recorded_at->format('H:i:s') }}</flux:text>
                                </div>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <flux:badge color="blue" icon="cpu-chip">
                                    {{ $pengukuran->sensor->name }}
                                </flux:badge>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <flux:icon name="fire" class="text-orange-500" size="xs" />
                                        <span class="font-mono text-xs">{{ number_format($pengukuran->temperature_c, 1) }}°C</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <flux:icon name="cloud" class="text-blue-500" size="xs" />
                                        <span class="font-mono text-xs">{{ number_format($pengukuran->humidity_pct, 1) }}%</span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($pengukuran->soil_moisture_pct)
                                    <div class="space-y-0.5 text-xs">
                                        <div><span class="text-zinc-500">M:</span> {{ number_format($pengukuran->soil_moisture_pct, 1) }}%</div>
                                        <div><span class="text-zinc-500">pH:</span> {{ number_format($pengukuran->soil_ph, 1) }} | <span class="text-zinc-500">EC:</span> {{ $pengukuran->soil_ec }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400">-</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($pengukuran->nitrogen !== null)
                                    <span class="font-mono text-[11px] bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                        {{ $pengukuran->nitrogen }}/{{ $pengukuran->phosphorus }}/{{ $pengukuran->potassium }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400">-</span>
                                @endif
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <div class="flex justify-end">
                                    <flux:button 
                                        size="sm" 
                                        variant="ghost" 
                                        icon="eye" 
                                        :href="route('pengukuran.detail', $pengukuran)" 
                                        wire:navigate
                                        tooltip="Lihat detail"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            @if($pengukurans->isEmpty())
                <div class="text-center py-12">
                    <flux:icon name="chart-bar" size="lg" class="mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                    <flux:heading size="lg" class="mb-1">Tidak ada data pengukuran</flux:heading>
                    <flux:text class="text-zinc-500">Coba ubah filter atau tunggu data dari sensor</flux:text>
                </div>
            @endif

            <div class="mt-6">
                {{ $pengukurans->links() }}
            </div>
        </flux:card>
    </flux:main>
</div>
