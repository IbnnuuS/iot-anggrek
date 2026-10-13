<div>
    <flux:header>
        <flux:heading>Edit Sensor: {{ $sensor->name }}</flux:heading>
    </flux:header>

    <flux:main>
        <flux:card>
            <form wire:submit="update" class="space-y-6">
                {{-- Device Identity --}}
                <div class="space-y-4">
                    <flux:heading size="lg">Identitas Perangkat</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input 
                            wire:model="name" 
                            label="Nama Sensor" 
                            badge="Required"
                        />

                        <flux:input 
                            wire:model="model" 
                            label="Model" 
                        />

                        <flux:input 
                            wire:model="serial_no" 
                            label="Serial Number" 
                        />

                        <flux:input 
                            wire:model="mac_address" 
                            label="MAC Address"
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
                        />

                        <flux:input 
                            wire:model="area_name" 
                            label="Area Name" 
                            icon="map"
                        />

                        <flux:input 
                            wire:model="zone_label" 
                            label="Zone Label" 
                            icon="map-pin"
                        />

                        <flux:input 
                            wire:model="rack_label" 
                            label="Rack Label" 
                            icon="rectangle-stack"
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
                />

                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end pt-4">
                    <flux:button variant="ghost" :href="route('sensors.index')" wire:navigate>
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="check">
                        Update Sensor
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </flux:main>
</div>
