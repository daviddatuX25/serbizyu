<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify Your Identity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column: Instructions and ID Type -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Submission Requirements') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Please upload clear, color images of your chosen identification document. Ensure all details are readable.
                                </p>
                                <div class="mt-6">
                                    <x-input-label for="id_type" :value="__('ID Type')" />
                                    <select id="id_type" name="id_type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="national_id">{{ __('National ID') }}</option>
                                        <option value="drivers_license">{{ __("Driver's License") }}</option>
                                        <option value="passport">{{ __('Passport') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column: File Uploads -->
                            <div class="space-y-6">
                                <!-- ID Front -->
                                <div x-data="fileUpload('id_front')">
                                    <x-input-label for="id_front" :value="__('Front of ID')" />
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                         x-on:dragover.prevent="onDragOver"
                                         x-on:dragleave.prevent="onDragLeave"
                                         x-on:drop.prevent="onDrop($event)"
                                         x-bind:class="{ 'border-blue-400': isDragging }">
                                        <div class="space-y-1 text-center">
                                            <svg x-show="!previewUrl" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div x-show="previewUrl" class="mt-4">
                                                <img :src="previewUrl" class="max-h-40 mx-auto rounded-md shadow-lg">
                                            </div>
                                            <div class="flex text-sm text-gray-600">
                                                <label :for="name" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input :id="name" :name="name" type="file" class="sr-only" @change="onFileChange($event)">
                                                </label>
                                                <p class="pl-1" x-show="!fileName">or drag and drop</p>
                                                <p class="pl-1" x-show="fileName" x-text="fileName"></p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    <button x-show="fileName" @click="removeFile" type="button" class="mt-2 text-sm text-red-600">Remove</button>
                                </div>

                                <!-- ID Back -->
                                <div x-data="fileUpload('id_back')">
                                    <x-input-label for="id_back" :value="__('Back of ID')" />
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                         x-on:dragover.prevent="onDragOver"
                                         x-on:dragleave.prevent="onDragLeave"
                                         x-on:drop.prevent="onDrop($event)"
                                         x-bind:class="{ 'border-blue-400': isDragging }">
                                        <div class="space-y-1 text-center">
                                            <svg x-show="!previewUrl" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div x-show="previewUrl" class="mt-4">
                                                <img :src="previewUrl" class="max-h-40 mx-auto rounded-md shadow-lg">
                                            </div>
                                            <div class="flex text-sm text-gray-600">
                                                <label :for="name" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input :id="name" :name="name" type="file" class="sr-only" @change="onFileChange($event)">
                                                </label>
                                                <p class="pl-1" x-show="!fileName">or drag and drop</p>
                                                <p class="pl-1" x-show="fileName" x-text="fileName"></p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    <button x-show="fileName" @click="removeFile" type="button" class="mt-2 text-sm text-red-600">Remove</button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <x-primary-button>
                                {{ __('Submit for Verification') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fileUpload(name) {
            return {
                name: name,
                isDragging: false,
                fileName: '',
                previewUrl: '',
                onDragOver() {
                    this.isDragging = true;
                },
                onDragLeave() {
                    this.isDragging = false;
                },
                onDrop(e) {
                    this.isDragging = false;
                    this.handleFile(e.dataTransfer.files[0]);
                },
                onFileChange(e) {
                    this.handleFile(e.target.files[0]);
                },
                handleFile(file) {
                    if (file) {
                        this.fileName = file.name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewUrl = e.target.result;
                        };
                        reader.readAsDataURL(file);

                        // Set the file to the input
                        const input = this.$el.querySelector(`input[type="file"]`);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                    }
                },
                removeFile() {
                    this.fileName = '';
                    this.previewUrl = '';
                    const input = this.$el.querySelector(`input[type="file"]`);
                    input.value = '';
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
