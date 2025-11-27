<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify Your Identity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <!-- Instructions -->
                    <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                        <h3 class="font-semibold text-blue-900 mb-2">Verification Information</h3>
                        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                            <li>Upload clear photos of both sides of your ID</li>
                            <li>Maximum file size: 5MB per image</li>
                            <li>Accepted formats: JPG, PNG, GIF, HEIC</li>
                            <li>Your documents will be reviewed within 24-48 hours</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- ID Type -->
                        <div class="mb-8">
                            <x-input-label for="id_type" :value="__('What type of ID are you using?')" class="block text-sm font-semibold text-gray-700 mb-2" />
                            <select id="id_type" name="id_type" class="block w-full px-4 py-2 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ $errors->has('id_type') ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }}">
                                <option value="">-- Select ID Type --</option>
                                <option value="national_id">{{ __('National ID') }}</option>
                                <option value="drivers_license">{{ __("Driver's License") }}</option>
                                <option value="passport">{{ __('Passport') }}</option>
                            </select>
                            @error('id_type')
                                <x-input-error :messages="$message" class="mt-2" />
                            @enderror
                        </div>

                        <!-- ID Front -->
                        <div class="mb-8">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold mr-2">1</span>
                                <x-input-label for="id_front" :value="__('Front Side')" class="block text-sm font-semibold text-gray-700" />
                            </div>
                            <div class="relative">
                                <input id="id_front" class="block w-full px-4 py-3 rounded-lg border-2 border-dashed {{ $errors->has('id_front') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-gray-400' }} transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" type="file" name="id_front" accept="image/*" required />
                                <p class="text-xs text-gray-500 mt-1">Drag & drop or click to upload</p>
                            </div>
                            @error('id_front')
                                <x-input-error :messages="$message" class="mt-2" />
                            @enderror
                        </div>

                        <!-- ID Back -->
                        <div class="mb-8">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold mr-2">2</span>
                                <x-input-label for="id_back" :value="__('Back Side')" class="block text-sm font-semibold text-gray-700" />
                            </div>
                            <div class="relative">
                                <input id="id_back" class="block w-full px-4 py-3 rounded-lg border-2 border-dashed {{ $errors->has('id_back') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-gray-400' }} transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" type="file" name="id_back" accept="image/*" required />
                                <p class="text-xs text-gray-500 mt-1">Drag & drop or click to upload</p>
                            </div>
                            @error('id_back')
                                <x-input-error :messages="$message" class="mt-2" />
                            @enderror
                        </div>

                        <!-- Privacy Notice -->
                        <div class="mb-8 p-3 bg-gray-50 border border-gray-200 rounded text-xs text-gray-600">
                            <strong>Privacy:</strong> Your identity documents are encrypted and securely stored. We will never share your personal information with third parties.
                        </div>

                        <!-- File Size Error -->
                        <div id="fileSizeError" class="mb-4 p-4 bg-red-50 border border-red-300 rounded-lg hidden">
                            <p class="text-red-700 font-semibold">⚠️ File size exceeds 5MB limit</p>
                            <p class="text-red-600 text-sm mt-1">Please choose smaller image files and try again.</p>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('creator.dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">{{ __('Back') }}</a>
                            <x-primary-button>
                                {{ __('Submit for Verification') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
        const errorDiv = document.getElementById('fileSizeError');
        const idFrontInput = document.getElementById('id_front');
        const idBackInput = document.getElementById('id_back');

        function validateFileSize(fileInput, fieldName) {
            if (!fileInput.files[0]) return true;

            if (fileInput.files[0].size > MAX_FILE_SIZE) {
                errorDiv.innerHTML = `
                    <p class="text-red-700 font-semibold">⚠️ ${fieldName} file size is too large</p>
                    <p class="text-red-600 text-sm mt-1">${fieldName} must not exceed 5MB. Current size: ${(fileInput.files[0].size / 1024 / 1024).toFixed(2)}MB</p>
                `;
                errorDiv.classList.remove('hidden');
                return false;
            }
            return true;
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const frontValid = validateFileSize(idFrontInput, 'Front side image');
            const backValid = validateFileSize(idBackInput, 'Back side image');

            if (!frontValid || !backValid) {
                e.preventDefault();
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errorDiv.classList.add('hidden');
            }
        });

        // Real-time validation as files are selected
        idFrontInput.addEventListener('change', function() {
            if (this.files[0] && this.files[0].size > MAX_FILE_SIZE) {
                validateFileSize(this, 'Front side image');
            } else {
                errorDiv.classList.add('hidden');
            }
        });

        idBackInput.addEventListener('change', function() {
            if (this.files[0] && this.files[0].size > MAX_FILE_SIZE) {
                validateFileSize(this, 'Back side image');
            } else {
                errorDiv.classList.add('hidden');
            }
        });
    </script>
</x-creator-layout>
