@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Sistem Monitoring Anggrek" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center">
            <img src="/images/anggrek.png" alt="" aria-hidden="true" style="width:24px;height:24px;object-fit:contain">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Sistem Monitoring Anggrek" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center">
            <img src="/images/anggrek.png" alt="" aria-hidden="true" style="width:24px;height:24px;object-fit:contain">
        </x-slot>
    </flux:brand>
@endif
