<div class="card bg-white border border-gray-200 rounded-2xl shadow p-8">
  <h2 class="text-lg font-semibold mb-4 text-gray-800">Profile Information</h2>

  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('patch')

    {{-- first name and last name --}}
    <div>
        <label for="firstname" class="form-label">First Name</label>
        <input id="firstname" name="firstname" type="text" class="form-input" value="{{ old('firstname', $user->firstname) }}" required autofocus>
        @error('firstname')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="lastname" class="form-label">Last Name</label>
        <input id="lastname" name="lastname" type="text" class="form-input" value="{{ old('lastname', $user->lastname) }}" required>
        @error('lastname')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
      <label for="email" class="form-label">Email</label>
      <input id="email" name="email" type="email" class="form-input" value="{{ old('email', $user->email) }}" required>
      @error('email')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-end pt-2">
      <button type="submit" class="btn btn-primary px-6 py-2">Save Changes</button>
    </div>
  </form>
</div>
