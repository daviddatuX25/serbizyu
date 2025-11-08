<?php

namespace Tests\Feature\Feature;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesSeeder']);

        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_guest_is_redirected_from_verification_submit_page()
    {
        $response = $this->get(route('verification.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_see_verification_submit_page()
    {
        $response = $this->actingAs($this->user)->get(route('verification.create'));
        $response->assertStatus(200);
        $response->assertViewIs('verification.submit');
    }

    public function test_user_can_submit_verification_documents()
    {
        Storage::fake('local');

        $response = $this->actingAs($this->user)->post(route('verification.store'), [
            'id_type' => 'passport',
            'id_front' => UploadedFile::fake()->image('front.jpg'),
            'id_back' => UploadedFile::fake()->image('back.jpg'),
        ]);

        $response->assertRedirect(route('verification.status'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_verifications', [
            'user_id' => $this->user->id,
            'id_type' => 'passport',
            'status' => 'pending',
        ]);

        $verification = UserVerification::where('user_id', $this->user->id)->first();
        Storage::disk('local')->assertExists($verification->id_front_path);
        Storage::disk('local')->assertExists($verification->id_back_path);
    }

    public function test_user_is_redirected_if_already_has_pending_verification()
    {
        UserVerification::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->actingAs($this->user)->get(route('verification.create'));
        $response->assertRedirect(route('verification.status'));
    }

    public function test_admin_can_see_pending_verifications_queue()
    {
        UserVerification::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)->get(route('admin.verifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.verifications.index');
        $response->assertSee('Pending Verifications');
    }

    public function test_admin_can_approve_a_verification()
    {
        $verification = UserVerification::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->actingAs($this->admin)->post(route('admin.verifications.approve', $verification));

        $response->assertRedirect(route('admin.verifications.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_verifications', [
            'id' => $verification->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'is_verified' => true,
        ]);
    }

    public function test_admin_can_reject_a_verification()
    {
        $verification = UserVerification::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->actingAs($this->admin)->post(route('admin.verifications.reject', $verification), [
            'rejection_reason' => 'ID was not clear.',
        ]);

        $response->assertRedirect(route('admin.verifications.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_verifications', [
            'id' => $verification->id,
            'status' => 'rejected',
            'rejection_reason' => 'ID was not clear.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'is_verified' => false,
        ]);
    }
}
