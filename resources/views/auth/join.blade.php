<x-app-layout title="Join">
  <section class=" flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
    <div class="my-5 bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md border border-gray-100">
      <!-- Header -->
      <div class="form-header">
        <div class="form-icon">
          <x-icons.profile class="w-8 h-8 text-green-500" />
        </div>
        <h2 class="form-title">Create a new account</h2>
        <p class="form-subtitle">Sign up to get started!</p>
      </div>

      <!-- Form -->
      <form method="POST" action="{{ route('auth.join') }}">
        @csrf
        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="First name" required class="form-input">
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
          </div>
          <div>
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" id="last_name" name="last_name" placeholder="Last name" required class="form-input">
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
          </div>
        </div>

        <div class="mb-4">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" placeholder="you@email.com" required class="form-input">
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" placeholder="Create a password" required class="form-input">
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required class="form-input">
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="form-button">Sign Up</button>
      </form>

      <!-- Divider -->
      <div class="form-divider">
        <div class="form-divider-line"></div>
        <span class="form-divider-text">or</span>
        <div class="form-divider-line"></div>
      </div>

      <!-- Google Sign Up -->
      <button class="form-alt-button">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
        <span class="text-gray-700 font-medium">Sign up with Google</span>
      </button>

      <!-- Footer -->
      <p class="form-footer">
        Already have an account?
        <a href="{{ route('auth.signin') }}" class="text-green-500 hover:underline font-medium">Sign In</a>
      </p>
    </div>
  </section>

</x-app-layout>
