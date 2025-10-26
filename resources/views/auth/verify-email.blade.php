<x-app-layout title="Verify Email">
  <section class=" flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="my-5 bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md border border-gray-100">
      <div class="form-header">
        <div class="form-icon">
          <x-icons.mail-check class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="form-title">Verify your email</h2>
        <p class="form-subtitle">Before continuing, please check your inbox for a verification link.</p>
      </div>

      @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-green-600 dark:text-green-400">
          A new verification link has been sent to your email address.
        </div>
      @endif

      <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="form-button w-full">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="form-alt-button text-gray-700 font-medium">Log Out</button>
        </form>
      </div>
    </div>
  </section>
</x-app-layout>
