<div>
    <flux:header>
        <flux:heading>Edit Anggrek: {{ $anggrek->jenis_anggrek }}</flux:heading>
    </flux:header>

    <flux:main>
        <flux:card>
            <form wire:submit="update" class="space-y-6">
                {{-- Basic Info --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Informasi Dasar</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            wire:model="jenis_anggrek" 
                            label="Jenis Anggrek" 
                            badge="Required"
                            icon="sparkles"
                        />

                        <flux:input 
                            wire:model="nama_anggrek" 
                            label="Nama Anggrek" 
                        />
                    </div>

                    <flux:select wire:model="sensor_id" label="Sensor" placeholder="Pilih sensor (opsional)" icon="cpu-chip">
                        <option value="">Tidak ada sensor</option>
                        @foreach($sensors as $sensor)
                            <option value="{{ $sensor->id }}">{{ $sensor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:separator />

                {{-- Temperature Range --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Range Temperature Optimal</flux:heading>
                    <flux:text size="sm" class="text-zinc-500">Temperature minimum harus lebih kecil dari maximum</flux:text>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            type="number" 
                            step="0.01" 
                            wire:model="temp_min_c" 
                            label="Temperature Minimum" 
                            icon="fire"
                        >
                            <x-slot name="iconTrailing">
                                <span class="text-sm text-zinc-500">°C</span>
                            </x-slot>
                        </flux:input>

                        <flux:input 
                            type="number" 
                            step="0.01" 
                            wire:model="temp_max_c" 
                            label="Temperature Maximum" 
                            icon="fire"
                        >
                            <x-slot name="iconTrailing">
                                <span class="text-sm text-zinc-500">°C</span>
                            </x-slot>
                        </flux:input>
                    </div>
                </div>

                <flux:separator />

                {{-- Humidity Range --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Range Humidity Optimal</flux:heading>
                    <flux:text size="sm" class="text-zinc-500">Humidity minimum harus lebih kecil dari maximum</flux:text>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            type="number" 
                            step="0.01" 
                            wire:model="hum_min_pct" 
                            label="Humidity Minimum" 
                            icon="cloud"
                        >
                            <x-slot name="iconTrailing">
                                <span class="text-sm text-zinc-500">%</span>
                            </x-slot>
                        </flux:input>

                        <flux:input 
                            type="number" 
                            step="0.01" 
                            wire:model="hum_max_pct" 
                            label="Humidity Maximum" 
                            icon="cloud"
                        >
                            <x-slot name="iconTrailing">
                                <span class="text-sm text-zinc-500">%</span>
                            </x-slot>
                        </flux:input>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end pt-4">
                    <flux:button variant="ghost" :href="route('anggrek.index')" wire:navigate>
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        Update Anggrek
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </flux:main>
</div>
