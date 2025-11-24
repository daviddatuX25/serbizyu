<x-app-layout title="Edit Category">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-text dark:text-text-secondary">Edit Category</h2>
            </div>
        </div>

        <div class="bg-white dark:bg-secondary-dark shadow-sm rounded-xl p-6 border border-secondary dark:border-secondary-dark">
            <form method="POST" action="{{ route('creator.categories.update', $category) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Category Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $category->name) }}"
                            class="form-input" 
                            required>
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <textarea 
                            name="description" 
                            rows="3" 
                            class="form-input">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-secondary dark:border-secondary-dark mt-6">
                    <a href="{{ route('creator.categories.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-creator-layout>
