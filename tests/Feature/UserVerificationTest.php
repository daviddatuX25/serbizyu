<?php

namespace Tests\Feature;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_verification_submit_page()
    {
        $response = $this->get(route('verification.create'));

        $response->assertRedirect(route('auth.signin'));
    }

    public function test_authenticated_user_can_see_verification_submit_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('verification.create'));

        $response->assertStatus(200);
        $response->assertViewIs('verification.submit');
    }

    public function test_user_can_submit_verification_documents()
    {
        $user = User::factory()->create();
        Storage::fake('local');

        $response = $this->actingAs($user)
            ->from(route('verification.create'))
            ->post(route('verification.store'), [
                'id_type' => 'national_id',
                'id_front' => UploadedFile::fake()->image('front.jpg'),
                'id_back' => UploadedFile::fake()->image('back.jpg'),
            ]);

        $response->assertRedirect(route('verification.status'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_verifications', [
            'user_id' => $user->id,
            'id_type' => 'national_id',
            'status' => 'pending',
        ]);

        $user->refresh();
        $this->assertCount(1, $user->getMedia('verification-id-front'));
        $this->assertCount(1, $user->getMedia('verification-id-back'));
    }
}
