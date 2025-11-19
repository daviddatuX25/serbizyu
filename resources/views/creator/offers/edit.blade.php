{{-- resources/views/creator/offers/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Open Offer
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <livewire:open-offer-form :offer="$offer" :addresses="$addresses" />
        </div>
    </div>
</x-app-layout>
