<x-app-layout title="Account Settings">
  <section class="p-4 flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="w-full max-w-5xl mx-auto px-4 md:px-0">
      <h2 class="pt-4 text-2xl font-bold text-center mb-10 text-gray-800">Account Settings</h2>

      <div class="flex flex-col md:flex-row gap-8 items-start">
        {{-- Profile Photo --}}
        <div class="w-full md:flex-1">
          <livewire:profile-photo-upload />
        </div>

        {{-- Profile Information --}}
        <div class="w-full md:flex-1">
          @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="w-full md:flex-1">
          @include('profile.partials.update-password-form')
        </div>
      </div>

      {{-- User Verification --}}
      <div class="mt-10">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <h2 class="text-lg font-medium text-gray-900">
              {{ __('User Verification') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
              {{ __('Submit your documents to get your account verified.') }}
            </p>
            <a href="{{ route('verification.status') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
              {{ __('Go to Verification') }}
            </a>
          </div>
        </div>
      </div>

      {{-- Address Management --}}
      <div class="mt-10">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <livewire:address-manager />
        </div>
      </div>

      {{-- Delete Account --}}
      <div class="mt-10">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </section>
</x-app-layout>
