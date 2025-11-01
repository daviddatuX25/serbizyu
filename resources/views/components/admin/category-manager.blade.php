<x-app-layout title="Category Management">
    <div class="max-w-7xl mx-auto px-6 py-10">
        
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-text dark:text-text-secondary">Category Management</h2>
                <p class="text-sm text-text-secondary dark:text-text mt-1">Manage service and listing categories</p>
            </div>
            <button 
                @click="$dispatch('open-modal', 'create-category')"
                class="btn btn-primary inline-flex items-center gap-2">
                <x-icons.plus class="w-5 h-5" />
                <span>Add Category</span>
            </button>
        </div>

        {{-- Category List --}}
        <div class="bg-background dark:bg-secondary-dark shadow-sm rounded-xl p-6 border border-secondary dark:border-secondary-dark">
            <h3 class="text-lg font-semibold text-text dark:text-text-secondary mb-4">Existing Categories</h3>

            @if($categories->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-text-secondary dark:text-text mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-text-secondary dark:text-text">No categories found.</p>
                    <button @click="$dispatch('open-modal', 'create-category')" class="mt-4 text-brand hover:text-brand-dark font-medium text-sm">
                        Add your first category
                    </button>
                </div>
            @else
                <ul class="divide-y divide-secondary dark:divide-secondary-dark">
                    @foreach($categories as $category)
                        <li class="flex items-center justify-between py-4 hover:bg-background-secondary dark:hover:bg-secondary -mx-6 px-6 transition">
                            <div class="flex-1">
                                <p class="font-medium text-text dark:text-text-secondary">{{ $category->name }}</p>
                                <p class="text-sm text-text-secondary dark:text-text mt-0.5">
                                    {{ $category->description ?? 'No description' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                    class="px-3 py-1.5 text-sm bg-background-secondary dark:bg-secondary text-text-secondary dark:text-text hover:bg-secondary dark:hover:bg-secondary-dark rounded-md transition">
                                    Edit
                                </button>
                                <button 
                                    @click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')"
                                    class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-error hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition">
                                    Delete
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Create Modal --}}
        <x-modal name="create-category" maxWidth="lg">
            <div class="p-6">
                <h3 class="text-xl font-bold text-text dark:text-text-secondary mb-4">Add Category</h3>
                
                <form method="POST" action="{{ route('creator.categories.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Category Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}"
                                class="form-input" 
                                placeholder="e.g., Home Repair, Catering"
                                required>
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Description (Optional)</label>
                            <textarea 
                                name="description" 
                                rows="3" 
                                class="form-input"
                                placeholder="Brief description of this category">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-secondary dark:border-secondary-dark mt-6">
                        <button type="button" @click="$dispatch('close-modal', 'create-category')" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Edit Modals (one per category) --}}
        @foreach($categories as $category)
            <x-modal name="edit-category-{{ $category->id }}" maxWidth="lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-text dark:text-text-secondary mb-4">Edit Category</h3>
                    
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
                            <button type="button" @click="$dispatch('close-modal', 'edit-category-{{ $category->id }}')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Delete Modal --}}
            <x-modal name="delete-category-{{ $category->id }}" maxWidth="md">
                <div class="p-6 text-center">
                    <x-icons.exclamation class="w-12 h-12 text-error mx-auto mb-4" />
                    
                    <h3 class="text-lg font-semibold mb-2 text-text dark:text-text-secondary">Delete Category</h3>
                    <p class="text-text-secondary dark:text-text mb-6">
                        Are you sure you want to delete <span class="font-medium">{{ $category->name }}</span>? 
                        This action cannot be undone.
                    </p>
                    
                    <form method="POST" action="{{ route('creator.categories.destroy', $category) }}">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center gap-3">
                            <button type="button" @click="$dispatch('close-modal', 'delete-category-{{ $category->id }}')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                Delete Category
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endforeach
    </div>
</x-app-layout>