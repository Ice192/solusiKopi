<x-order-layout>
    <x-slot name="title">
        Pesan Menu
    </x-slot>

    @livewire('menu-livewire', ['initialTab' => request('tab')])
</x-order-layout>
