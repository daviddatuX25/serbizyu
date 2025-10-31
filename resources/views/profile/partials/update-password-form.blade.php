<div class="card bg-white border border-gray-200 rounded-2xl shadow p-8 flex flex-col">
  <h2 class="text-lg font-semibold mb-4 text-gray-800">Update Password</h2>
  <p class="text-sm text-gray-500 mb-6">Keep your account secure by setting a strong password.</p>

  <form method="POST" action="{{ route('password.update') }}" class="flex flex-col flex-1">
    @csrf
    @method('put')

    <div class="flex-1 space-y-4">
      <div>
        <label for="current_password" class="form-label">Current Password</label>
        <input id="current_password" name="current_password" type="password" class="form-input" autocomplete="current-password">
        @error('current_password', 'updatePassword')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="form-label">New Password</label>
        <input id="password" name="password" type="password" class="form-input" autocomplete="new-password">
        @error('password', 'updatePassword')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div class="flex justify-end mt-6">
      <button type="submit" class="btn btn-primary px-6 py-2">Update Password</button>
    </div>
  </form>
</div>
