<x-app-layout title="Sign In" >
    <section class=" flex items-center justify-center min-h-screen bg-gradient-to-br from-green-100 via-white to-gray-100">
        <div class="my-5 bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md border border-gray-100">
            <div class="form-header">
            <div class="form-icon">
                <x-icons.profile></x-icons.profile>
            </div>
            <h2 class="form-title">Sign In</h2>
            <p class="form-subtitle">Welcome back! Please login in below.</p>
            </div>

            <form method="POST" action="{{ route('auth.signin') }}">
                @csrf
                <div class="mb-5">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" name="email" type="email" id="email" placeholder="you@email.com" required>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" name="password" type="password" id="password" placeholder="Enter your password" required  autocomplete="current-password" >
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" class="mr-2 accent-green-500"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-green-500 hover:underline text-sm">Forgot password?</a>
                </div>

                <button type="submit" class="form-button">Sign In</button>
            </form>

            <div class="form-divider">
            <div class="form-divider-line"></div>
            <span class="form-divider-text">or</span>
            <div class="form-divider-line"></div>
            </div>

            <button class="form-alt-button">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
            <span class="text-gray-700 font-medium">Sign in with Google</span>
            </button>

            <p class="form-footer">
            Don't have an account?
            <a href="{{route('auth.join')}}" class="text-green-500 hover:underline font-medium">Sign Up</a>
            </p>
        </div>
    </section>
</x-app-layout>