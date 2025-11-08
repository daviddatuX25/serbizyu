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
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-text dark:text-text-secondary">Category Management</h2>
                <p class="text-sm text-text-secondary dark:text-text mt-1">Manage service and listing categories</p>
            </div>
            <button 
                x-data
                @click.prevent="$dispatch('open-modal', 'create-category')"
                type="button"
                class="btn btn-primary inline-flex items-center gap-2">
                <x-icons.plus class="w-5 h-5" />
                <span>Add Category</span>
            </button>
        </div>

        {{-- Filters Section --}}
        <div class="bg-background dark:bg-secondary-dark shadow-sm rounded-xl p-4 mb-6 border border-secondary dark:border-secondary-dark">
            <form method="GET" action="{{ route('creator.categories.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <label class="form-label text-xs">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            class="form-input text-sm" 
                            placeholder="Search categories...">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="form-label text-xs">Status</label>
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <select name="status" class="form-input text-sm">
                            <option value="all" {{ old('status', request('status')) === 'all' ? 'selected' : '' }}>All</option>
                            <option value="active" {{ old('status', request('status')) === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="deleted" {{ old('status', request('status')) === 'deleted' ? 'selected' : '' }}>Deleted Only</option>
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <label class="form-label text-xs">Items per page</label>
                        <select name="per_page" class="form-input text-sm">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn btn-primary text-sm flex-1">
                            Apply
                        </button>
                        <a href="{{ route('creator.categories.index') }}" class="btn btn-secondary text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Category List --}}
        <div class="bg-background dark:bg-secondary-dark shadow-sm rounded-xl p-6 border border-secondary dark:border-secondary-dark">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-text dark:text-text-secondary">
                    Categories 
                    <span class="text-sm text-text-secondary">
                        ({{ $categories->total() }} total)
                    </span>
                </h3>
            </div>

            @if($categories->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-text-secondary dark:text-text mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-text-secondary dark:text-text mb-2">No categories found.</p>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('creator.categories.index') }}" class="text-brand hover:text-brand-dark font-medium text-sm">
                            Clear filters
                        </a>
                    @else
                        <button 
                            x-data
                            @click.prevent="$dispatch('open-modal', 'create-category')"
                            type="button" 
                            class="mt-4 text-brand hover:text-brand-dark font-medium text-sm">
                            Add your first category
                        </button>
                    @endif
                </div>
            @else
                <ul class="divide-y divide-secondary dark:divide-secondary-dark">
                    @foreach($categories as $category)
                        <li class="flex items-center justify-between py-4 hover:bg-background-secondary dark:hover:bg-secondary -mx-6 px-6 transition
                            {{ $category->trashed() ? 'opacity-60' : '' }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-text dark:text-text-secondary">
                                        {{ $category->name }}
                                    </p>
                                    @if($category->trashed())
                                        <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded">
                                            Deleted
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-text-secondary dark:text-text mt-0.5">
                                    {{ $category->description ?? 'No description' }}
                                </p>
                                @if($category->trashed())
                                    <p class="text-xs text-text-secondary dark:text-text mt-1">
                                        Deleted {{ $category->deleted_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2" x-data>
                                @if($category->trashed())
                                    {{-- Restore Button --}}
                                    <form method="POST" action="{{ route('creator.categories.restore', $category) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit"
                                            class="px-3 py-1.5 text-sm bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-md transition">
                                            Restore
                                        </button>
                                    </form>
                                    {{-- Permanent Delete --}}
                                    <button 
                                        @click.prevent="$dispatch('open-modal', 'force-delete-category-{{ $category->id }}')"
                                        type="button"
                                        class="px-3 py-1.5 text-sm bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/60 rounded-md transition">
                                        Delete Forever
                                    </button>
                                @else
                                    {{-- Edit Button --}}
                                    <button 
                                        @click.prevent="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                        type="button"
                                        class="px-3 py-1.5 text-sm bg-background-secondary dark:bg-secondary text-text-secondary dark:text-text hover:bg-secondary dark:hover:bg-secondary-dark rounded-md transition">
                                        Edit
                                    </button>
                                    {{-- Delete Button --}}
                                    <button 
                                        @click.prevent="$dispatch('open-modal', 'delete-category-{{ $category->id }}')"
                                        type="button"
                                        class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/20 text-error hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md transition">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
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
                        <button 
                            type="button" 
                            x-data
                            @click.prevent="$dispatch('close-modal', 'create-category')" 
                            class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- Edit & Delete Modals --}}
        @foreach($categories as $category)
            {{-- Edit Modal --}}
            @if(!$category->trashed())
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
                                <button 
                                    type="button" 
                                    x-data
                                    @click.prevent="$dispatch('close-modal', 'edit-category-{{ $category->id }}')" 
                                    class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>

                {{-- Soft Delete Modal --}}
                <x-modal name="delete-category-{{ $category->id }}" maxWidth="md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <x-icons.exclamation class="w-12 h-12 text-error" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2 text-text dark:text-text-secondary">
                                    Delete Category
                                </h3>
                                <p class="text-text-secondary dark:text-text mb-4">
                                    Are you sure you want to delete <span class="font-medium">{{ $category->name }}</span>?
                                </p>
                                
                                <div class="bg-background-secondary dark:bg-secondary p-3 rounded-lg mb-4">
                                    <p class="text-sm text-text-secondary dark:text-text">
                                        <strong>Soft Delete:</strong> The category will be hidden but can be restored later.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('creator.categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="delete_type" value="soft">
                                    
                                    <div class="flex justify-end gap-3">
                                        <button 
                                            type="button" 
                                            x-data
                                            @click.prevent="$dispatch('close-modal', 'delete-category-{{ $category->id }}')" 
                                            class="btn btn-secondary">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-danger">
                                            Delete Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </x-modal>
            @endif

            {{-- Force Delete Modal (for already soft-deleted items) --}}
            @if($category->trashed())
                <x-modal name="force-delete-category-{{ $category->id }}" maxWidth="md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2 text-error">
                                    Permanent Delete
                                </h3>
                                <p class="text-text-secondary dark:text-text mb-4">
                                    Are you sure you want to <strong>permanently delete</strong> <span class="font-medium">{{ $category->name }}</span>?
                                </p>
                                
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/40 p-3 rounded-lg mb-4">
                                    <p class="text-sm text-red-800 dark:text-red-300">
                                        <strong>⚠️ Warning:</strong> This action cannot be undone. All data associated with this category will be permanently removed.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('creator.categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="delete_type" value="force">
                                    
                                    <div class="flex justify-end gap-3">
                                        <button 
                                            type="button" 
                                            x-data
                                            @click.prevent="$dispatch('close-modal', 'force-delete-category-{{ $category->id }}')" 
                                            class="btn btn-secondary">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-danger">
                                            Delete Forever
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </x-modal>
            @endif
        @endforeach
    </div>
</x-app-layout>