<x-app-layout cssFile="auth.css" title="Confirm Password">
  <section class="form-section">
    <div class="form-card">
      <div class="form-header">
        <div class="form-icon">
          <x-icons.lock class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="form-title">Confirm your password</h2>
        <p class="form-subtitle">For your security, please confirm your password to continue.</p>
      </div>

      <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-5">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-input" required autocomplete="current-password">
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="form-button">Confirm</button>
      </form>

      <p class="form-footer">
        <a href="{{ route('auth.signin') }}" class="text-green-500 hover:underline font-medium">Back to Sign In</a>
      </p>
    </div>
  </section>
</x-app-layout>
