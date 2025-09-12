<x-app-layout cssFile="auth.css" title="Sign In" >
    <section class="auth-section">
        <div class="auth-card">
            <div class="auth-header">
            <div class="auth-icon">
                <x-icons.profile></x-icons.profile>
            </div>
            <h2 class="auth-title">Sign In</h2>
            <p class="auth-subtitle">Welcome back! Please login in below.</p>
            </div>

            <form>
            <div class="mb-5">
                <label class="auth-label" for="email">Email</label>
                <input class="auth-input" type="email" id="email" placeholder="you@email.com" required>
            </div>
            <div class="mb-4">
                <label class="auth-label" for="password">Password</label>
                <input class="auth-input" type="password" id="password" placeholder="Enter your password" required>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" class="mr-2 accent-green-500"> Remember me
                </label>
                <a href="#" class="text-green-500 hover:underline text-sm">Forgot password?</a>
            </div>

            <button type="submit" class="auth-button">Sign In</button>
            </form>

            <div class="auth-divider">
            <div class="auth-divider-line"></div>
            <span class="auth-divider-text">or</span>
            <div class="auth-divider-line"></div>
            </div>

            <button class="auth-alt-button">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
            <span class="text-gray-700 font-medium">Sign in with Google</span>
            </button>

            <p class="auth-footer">
            Don't have an account?
            <a href="{{route('auth.join')}}" class="text-green-500 hover:underline font-medium">Sign Up</a>
            </p>
        </div>
    </section>
</x-app-layout>