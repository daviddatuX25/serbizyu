<?php

namespace Tests\Feature;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        // "The first name field is required.",
        // "The last name field is required.",
        // "The email field must be lowercase."
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'firstname' => 'Test1',
                'lastname' => 'User1',
                'email' => 'tes123@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test1', $user->firstname);
        $this->assertSame('tes123@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_upload_profile_photo(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(\App\Livewire\ProfilePhotoUpload::class)
            ->set('newFileUploads', [
                UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(512),
            ])
            ->call('uploadPhoto')
            ->assertDispatchedBrowserEvent('profile-photo-updated')
            ->assertSessionHas('success', 'Profile photo updated successfully');

        // Verify the photo was attached
        $user->refresh();
        $profileImage = $user->media()->where('tag', 'profile_image')->first();
        $this->assertNotNull($profileImage);
        $this->assertSame('avatar.jpg', $profileImage->filename);
    }

    public function test_user_can_replace_existing_profile_photo(): void
    {
        $user = User::factory()->create();

        // Upload first photo
        $firstFile = UploadedFile::fake()->image('avatar1.jpg', 200, 200)->size(512);
        $this->actingAs($user);

        Livewire::test(\App\Livewire\ProfilePhotoUpload::class)
            ->set('newFileUploads', [$firstFile])
            ->call('uploadPhoto');

        $user->refresh();
        $this->assertCount(1, $user->media()->where('tag', 'profile_image')->get());

        // Replace with second photo
        $secondFile = UploadedFile::fake()->image('avatar2.jpg', 200, 200)->size(512);
        Livewire::test(\App\Livewire\ProfilePhotoUpload::class)
            ->set('newFileUploads', [$secondFile])
            ->call('uploadPhoto');

        $user->refresh();
        $profileImages = $user->media()->where('tag', 'profile_image')->get();
        $this->assertCount(1, $profileImages);
        $this->assertSame('avatar2.jpg', $profileImages->first()->filename);
    }

    public function test_user_can_delete_profile_photo(): void
    {
        $user = User::factory()->create();

        // Upload a photo first
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(512);
        $this->actingAs($user);

        Livewire::test(\App\Livewire\ProfilePhotoUpload::class)
            ->set('newFileUploads', [$file])
            ->call('uploadPhoto');

        $user->refresh();
        $this->assertCount(1, $user->media()->where('tag', 'profile_image')->get());

        // Delete the photo
        Livewire::test(\App\Livewire\ProfilePhotoUpload::class)
            ->call('deletePhoto')
            ->assertDispatchedBrowserEvent('profile-photo-deleted')
            ->assertSessionHas('success', 'Profile photo removed');

        $user->refresh();
        $this->assertCount(0, $user->media()->where('tag', 'profile_image')->get());
    }

    public function test_profile_photo_component_is_rendered_on_profile_edit_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertSeeLivewire(\App\Livewire\ProfilePhotoUpload::class);
    }
}
