<div>
    @if($showModal)
        <flux:modal wire:model="showModal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Konfirmasi Hapus Anggrek</flux:heading>
                    <flux:text class="mt-2">Apakah Anda yakin ingin menghapus data anggrek ini? Data akan dihapus permanen dan tidak dapat dikembalikan.</flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="cancel">Batal</flux:button>
                    <flux:button variant="danger" wire:click="destroy">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
