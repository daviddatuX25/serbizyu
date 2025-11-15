<div>
    @if ($errors->has('save'))
        <div class="text-red-600">{{ $errors->first('save') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div>
            <label for="title">Title</label>
            <input id="title" wire:model.defer="title" type="text" />
            @error('title') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" wire:model.defer="description"></textarea>
            @error('description') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="budget">Budget</label>
            <input id="budget" wire:model.defer="budget" type="number" step="0.01" />
            @error('budget') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="category_id">Category</label>
            <select id="category_id" wire:model.defer="category_id">
                <option value="">-- select --</option>
                @foreach($categories as $c)
                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                @endforeach
            </select>
            @error('category_id') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="workflow_template_id">Workflow (optional)</label>
            <select id="workflow_template_id" wire:model.defer="workflow_template_id">
                <option value="">-- none --</option>
                @foreach($workflowTemplates as $w)
                    <option value="{{ $w['id'] }}">{{ $w['name'] }}</option>
                @endforeach
            </select>
            @error('workflow_template_id') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label>
                <input type="checkbox" wire:model.defer="pay_first" />
                Pay first
            </label>
        </div>

        <div>
            <label for="address_id">Address (optional)</label>
            <input id="address_id" wire:model.defer="address_id" type="number" />
            @error('address_id') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        {{-- existing images (edit mode) --}}
        @if ($offer)
            <div>
                <h4>Existing images</h4>
                @foreach($offer->getMedia('gallery') ?? [] as $media)
                    <div style="display:inline-block;margin:4px">
                        <img src="{{ route('media.serve', ['payload' => encrypt(json_encode(['media_id' => $media->id]))]) }}" alt="" style="width:96px;height:96px;object-fit:cover" />
                        <button type="button" wire:click="removeExistingMedia({{ $media->id }})">Remove</button>
                    </div>
                @endforeach
            </div>
        @endif

        
        {{-- Media Upload Section --}}
        @include('livewire.partials.media-upload', [
            'newFiles' => $newFiles,
            'existingImages' => $existingImages
        ])

        <div>
            <button type="submit">{{ $offer ? 'Update Offer' : 'Create Offer' }}</button>
        </div>
    </form>
</div>
