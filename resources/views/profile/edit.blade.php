<x-app-layout title="Account Settings">
  <section class="p-4 flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="w-full max-w-5xl mx-auto px-4 md:px-0">
      <h2 class="pt-4 text-2xl font-bold text-center mb-10 text-gray-800">Account Settings</h2>

      <div class="flex flex-col md:flex-row gap-8 items-start">
        {{-- Profile Information --}}
        <div class="w-full md:flex-1">
          @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="w-full md:flex-1">
          @include('profile.partials.update-password-form')
        </div>
      </div>

      {{-- Delete Account --}}
      <div class="mt-10">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </section>
</x-app-layout>
