<x-app-layout cssFile="auth.css" title="Join">

  <section class="auth-section">
    <div class="auth-card">

      <!-- Header -->
      <div class="auth-header">
        <div class="auth-icon">
          <x-icons.profile class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="auth-title">Create a new account</h2>
        <p class="auth-subtitle">Sign up to get started!</p>
      </div>

      <!-- Form -->
      <form method="POST" action="{{ route('auth.join') }}">
        @csrf
        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="auth-label">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="First name" required class="auth-input">
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
          </div>
          <div>
            <label for="last_name" class="auth-label">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="Last name" required class="auth-input">
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
          </div>
        </div>

        <div class="mb-4">
          <label for="email" class="auth-label">Email</label>
          <input type="email" id="email" name="email" placeholder="you@email.com" required class="auth-input">
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
          <label for="password" class="auth-label">Password</label>
          <input type="password" id="password" name="password" placeholder="Create a password" required class="auth-input">
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
          <label for="password_confirmation" class="auth-label">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required class="auth-input">
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="auth-button">Sign Up</button>
      </form>

      <!-- Divider -->
      <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <span class="auth-divider-text">or</span>
        <div class="auth-divider-line"></div>
      </div>

      <!-- Google Sign Up -->
      <button class="auth-alt-button">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
        <span class="text-gray-700 font-medium">Sign up with Google</span>
      </button>

      <!-- Footer -->
      <p class="auth-footer">
        Already have an account?
        <a href="{{ route('auth.signin') }}" class="text-green-500 hover:underline font-medium">Sign In</a>
      </p>
    </div>
  </section>

</x-app-layout>
