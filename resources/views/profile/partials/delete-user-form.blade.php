<div class="card bg-white border border-gray-200 rounded-2xl shadow p-8 flex flex-col">
  <h2 class="text-lg font-semibold mb-3 text-gray-800">Delete Account</h2>
  <p class="text-sm text-gray-500 mb-6 leading-relaxed">
    Once your account is deleted, all of its data will be permanently removed. Please confirm your password before continuing.
  </p>

  <form method="POST" action="{{ route('profile.destroy') }}" class="flex flex-col flex-1">
    @csrf
    @method('delete')

    <div class="flex-1 space-y-4">
      <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" class="form-input" placeholder="Enter your password" required>
        @error('password')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div class="flex justify-end mt-6">
      <button type="submit" class="btn btn-danger px-6 py-2">Delete Account</button>
    </div>
  </form>
</div>
