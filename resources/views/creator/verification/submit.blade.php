<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify Your Identity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- ID Type -->
                        <div>
                            <x-input-label for="id_type" :value="__('ID Type')" />
                            <select id="id_type" name="id_type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="national_id">{{ __('National ID') }}</option>
                                <option value="drivers_license">{{ __("Driver's License") }}</option>
                                <option value="passport">{{ __('Passport') }}</option>
                            </select>
                        </div>

                        <!-- ID Front -->
                        <div class="mt-4">
                            <x-input-label for="id_front" :value="__('Front of ID')" />
                            <x-text-input id="id_front" class="block mt-1 w-full" type="file" name="id_front" required />
                        </div>

                        <!-- ID Back -->
                        <div class="mt-4">
                            <x-input-label for="id_back" :value="__('Back of ID')" />
                            <x-text-input id="id_back" class="block mt-1 w-full" type="file" name="id_back" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Submit for Verification') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
