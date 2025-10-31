<x-app-layout title="Forgot Password">
  <section class=" flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="my-5 bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md border border-gray-100">
      <div class="form-header">
        <div class="form-icon">
          <x-icons.mail class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="form-title">Forgot your password?</h2>
        <p class="form-subtitle">No worries — we’ll send you a reset link.</p>
      </div>

      @if (session('status'))
        <div class="mb-4 text-sm text-green-600 dark:text-green-400">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-5">
          <label for="email" class="form-label">Email address</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="you@email.com" required autofocus>
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <button type="submit" class="form-button">Send Reset Link</button>
      </form>

      <p class="form-footer">
        <a href="{{ route('auth.signin') }}" class="text-green-500 hover:underline font-medium">Back to Sign In</a>
      </p>
    </div>
  </section>
</x-app-layout>
