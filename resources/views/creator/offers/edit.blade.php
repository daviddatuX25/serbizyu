{{-- resources/views/creator/offers/edit.blade.php --}}
<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Open Offer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @livewire('open-offer-form', [
                'offer' => $offer, 
                'addresses' => auth()->user()->addresses()->get()
            ])
        </div>
    </div>
</x-creator-layout>
