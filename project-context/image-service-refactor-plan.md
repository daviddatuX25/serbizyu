# Extended Milestone: Generic Image Service Refactoring

This milestone will replace the domain-specific `ListingImage` implementation with a robust, common `Image` service that can be used by any domain (Listings, User Verifications, etc.) in the future.

---

#### **1. Architectural Vision**

*   **Goal:** To create a single, centralized service for handling all file uploads and database associations, following DDD principles.
*   **Core Concept:** We will use a single `images` table with a polymorphic relationship (`imageable`), allowing any model in the application to have associated images.
*   **Flexibility:** We will add a `collection_name` column to the `images` table. This allows a single model to have multiple, distinct groups of images (e.g., a `User` model can have both an 'avatar' collection and a 'verification_docs' collection).
*   **Service Layer:** We will favor **Composition over Inheritance**. A generic `ImageService` will handle the core logic. Domain-specific services (like a future `ListingImageService` or `UserVerificationImageService`) will *use* (inject) the generic service, adding any specialized rules (like validation or resizing) before passing the data down. This is more flexible than inheritance.

---

#### **2. Detailed Subtasks & Implementation Plan**

**Subtask 1: Database & Model Refactoring**

*   **1.1: Create a Migration to Rename Table:**
    *   **Action:** Generate a new migration file (`php artisan make:migration rename_listing_images_to_images_table`).
    *   **Implementation:** Inside the migration, use `Schema::rename('listing_images', 'images');`.
    *   **Rationale:** This preserves all existing image data without data loss.

*   **1.2: Create a Migration to Add `collection_name` Column:**
    *   **Action:** Generate another migration (`php artisan make:migration add_collection_name_to_images_table`).
    *   **Implementation:** Add a nullable string column: `$table->string('collection_name')->after('imageable_type')->nullable()->default('default');`.
    *   **Rationale:** This provides the flexibility to group images for a single model. We'll make it nullable and give it a default value for backward compatibility.

*   **1.3: Refactor the `Image` Model:**
    *   **Action:** Move `app/Domains/Listings/Models/ListingImage.php` to `app/Domains/Common/Models/Image.php`.
    *   **Implementation:**
        *   Update the namespace to `App\Domains\Common\Models`.
        *   Rename the class to `Image`.
        *   Set the table property: `protected $table = 'images';`.
        *   Remove the old, redundant `listing()` relationship.
    *   **Rationale:** This creates the central, generic `Image` model.

*   **1.4: Update Consuming Models (e.g., `Service`):**
    *   **Action:** Edit `app/Domains/Listings/Models/Service.php`.
    *   **Implementation:**
        *   Change the `images()` relationship to point to the new model: `return $this->morphMany(\App\Domains\Common\Models\Image::class, 'imageable');`.
        *   Update the `thumbnail()` relationship similarly.
        *   (Optional but Recommended) Add a helper relationship for clarity: `public function galleryImages() { return $this->morphMany(Image::class, 'imageable')->where('collection_name', 'gallery'); }`.
    *   **Rationale:** This reconnects the `Service` model to the newly refactored `Image` model.

**Subtask 2: Service Layer Refactoring**

*   **2.1: Create the Generic `ImageService`:**
    *   **Action:** Create a new file: `app/Domains/Common/Services/ImageService.php`.
    *   **Implementation:** This service will contain the core, reusable logic.
        *   `attach(Model $model, UploadedFile $file, string $collection = 'default', array $attributes = [])`: Stores a file, creates an `Image` record, and associates it with the model under a specific collection.
        *   `sync(Model $model, string $collection, array $files = [])`: A powerful method to synchronize a collection of images. It will add new files, and remove any images from that collection that are not present in the new set.
        *   `delete(Image $image)`: Deletes an image record and its corresponding file from storage.
    *   **Rationale:** This centralizes all low-level image operations.

*   **2.2: Update `ServiceService` to Use the New `ImageService`:**
    *   **Action:** Edit `app/Domains/Listings/Services/ServiceService.php`.
    *   **Implementation:**
        *   Inject the new `App\Domains\Common\Services\ImageService` in the constructor.
        *   Modify `createService` and `updateService` to call `imageService->sync()`, passing the model, the collection name (e.g., 'gallery'), and the array of uploaded files.
    *   **Rationale:** This decouples the `ServiceService` from the details of image storage, making it cleaner and more focused on its own domain logic.

**Subtask 3: Final Integration**

*   **3.1: Update `ServiceController`:**
    *   **Action:** Edit `app/Domains/Listings/Http/Controllers/ServiceController.php`.
    *   **Implementation:** Simplify the `store` and `update` methods. They will only be responsible for gathering data from the request (including `newImages` and `images_to_remove`) and passing it in a single data array to the `serviceService->updateService()` method. All complex logic will be gone from the controller.

*   **3.2: Update `ImageUploader` Livewire Component:**
    *   **Action:** Edit `app/Livewire/ImageUploader.php`.
    *   **Implementation:** Ensure the `mount` method correctly queries the new `Image` relationship. The rest of the component should work as is, but we'll need to ensure the input names (`newImages`, `images_to_remove`) are handled correctly by the controller.
