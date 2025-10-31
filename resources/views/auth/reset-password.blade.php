<x-app-layout title="Reset Password">
  <section class=" flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="my-5 bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md border border-gray-100">
      <div class="form-header">
        <div class="form-icon">
          <x-icons.lock class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="form-title">Reset your password</h2>
        <p class="form-subtitle">Enter your new password below.</p>
      </div>

      <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-4">
          <label for="email" class="form-label">Email</label>
          <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $request->email) }}" required autofocus>
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">New Password</label>
          <input id="password" name="password" type="password" class="form-input" required>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required>
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="form-button">Reset Password</button>
      </form>

      <p class="form-footer">
        <a href="{{ route('auth.signin') }}" class="text-green-500 hover:underline font-medium">Back to Sign In</a>
      </p>
    </div>
  </section>
</x-app-layout>
