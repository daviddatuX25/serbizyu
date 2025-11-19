{{-- resources/views/creator/offers/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Create Open Offer
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <livewire:open-offer-form :addresses="$addresses" />
        </div>
    </div>
</x-app-layout>
