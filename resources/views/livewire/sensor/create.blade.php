<div>
    <flux:header>
        <flux:heading>Tambah Sensor Baru</flux:heading>
    </flux:header>

    <flux:main>
        <flux:card>
            <form wire:submit="store" class="space-y-6">
                {{-- Device Identity --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Identitas Perangkat</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            wire:model="name" 
                            label="Nama Sensor" 
                            badge="Required"
                            placeholder="e.g. Sensor Greenhouse A" 
                        />

                        <flux:input 
                            wire:model="model" 
                            label="Model" 
                            placeholder="e.g. DHT22, BME280" 
                        />

                        <flux:input 
                            wire:model="serial_no" 
                            label="Serial Number" 
                            placeholder="e.g. DHT22-001-2024" 
                        />

                        <flux:input 
                            wire:model="mac_address" 
                            label="MAC Address" 
                            placeholder="e.g. A1:B2:C3:D4:E5:F1"
                            class:input="font-mono"
                        />
                    </div>
                </div>

                <flux:separator />

                {{-- Location --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Lokasi</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            wire:model="site_name" 
                            label="Site Name" 
                            icon="building-office-2"
                            placeholder="e.g. Greenhouse Utama" 
                        />

                        <flux:input 
                            wire:model="area_name" 
                            label="Area Name" 
                            icon="map"
                            placeholder="e.g. Area A" 
                        />

                        <flux:input 
                            wire:model="zone_label" 
                            label="Zone Label" 
                            icon="map-pin"
                            placeholder="e.g. Zone 1" 
                        />

                        <flux:input 
                            wire:model="rack_label" 
                            label="Rack Label" 
                            icon="rectangle-stack"
                            placeholder="e.g. Rack A1" 
                        />
                    </div>
                </div>

                <flux:separator />

                {{-- Notes --}}
                <flux:textarea 
                    wire:model="notes" 
                    label="Notes" 
                    description="Catatan tambahan tentang sensor ini"
                    rows="3" 
                    placeholder="Masukkan catatan..."
                />

                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end pt-4">
                    <flux:button variant="ghost" :href="route('sensors.index')" wire:navigate>
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        Simpan Sensor
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </flux:main>
</div>
