📌 Mediable Reference Guide
1️⃣ Model – Accessors for Media Tags
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;

class OpenOffer extends Model implements MediableInterface
{
    use Mediable;

    // Single image thumbnail
    public function getThumbnailAttribute()
    {
        return $this->getMedia('thumbnail')->first(); // returns Media or null
    }

    // Multiple images gallery
    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('gallery'); // returns Collection
    }

    // Example: multiple videos
    public function getVideosAttribute()
    {
        return $this->getMedia('video'); // returns Collection
    }

    // Example: multiple documents
    public function getDocumentsAttribute()
    {
        return $this->getMedia('document'); // returns Collection
    }

    // Optional: generic accessor by tag
    public function getMediaByTag(string $tag)
    {
        return $this->getMedia($tag); // Collection of Media
    }
}


✅ Pattern

get<TagName>Attribute() → single image: first(), multiple media: Collection.

Keep tags descriptive: thumbnail, gallery, video, document.

Optional generic method for dynamic tag access: $model->getMediaByTag('custom_tag').

2️⃣ Controller – Eager Load Media
$offers = OpenOffer::with([
    'media',       // all media
    'creator.media', // creator's profile images
    'address'
])->get();


No manual assignment of $model->thumbnail or $model->gallery_images.

Controller remains clean; media access is entirely through model accessors.

3️⃣ Blade – Access Media

Single image (thumbnail):

<img src="{{ $offer->thumbnail?->getUrl() ?? 'fallback.png' }}" 
     alt="{{ $offer->title }} Thumbnail">


Multiple images (gallery):

@foreach($offer->gallery_images as $image)
    <img src="{{ $image->getUrl() }}" alt="Gallery Image">
@endforeach


Videos:

@foreach($offer->videos as $video)
    <video controls>
        <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
    </video>
@endforeach


Documents / PDFs:

@foreach($offer->documents as $doc)
    <a href="{{ $doc->getUrl() }}" target="_blank">{{ $doc->basename }}</a>
@endforeach


Dynamic by tag:

@foreach($offer->getMediaByTag('brochure') as $file)
    <a href="{{ $file->getUrl() }}">{{ $file->basename }}</a>
@endforeach

4️⃣ Livewire Integration

Use Livewire component for uploads (ImageUploader or MediaUploader) and let it handle:

New files: $newMedia

Existing media: $existingMedia

Removal: $mediaToRemove

Pass the model into Livewire and it will automatically fetch existing media:

$this->existingMedia = $model ? $model->media()->orderBy('order_index')->get() : collect();


Validation: only enforce constraints per media type:

$this->validate([
    'newMedia.*' => 'mimes:jpg,png,gif,mp4,pdf|max:2048'
]);

5️⃣ Key Best Practices

Model Encapsulation: Every model defines which media tags it supports.

Controller Simplicity: Only eager load media relationships; no manual looping.

View Simplicity: Use accessors ($model->thumbnail, $model->videos) + getUrl() or getPath().

Livewire Upload: Model handles existing media; component handles new uploads & removal.

Dynamic Access: Use getMediaByTag() for flexible, reusable code.