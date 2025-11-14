<?php

namespace Tests\Feature;

use App\Domains\Users\Models\User;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\Category;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_service_with_images()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $workflowTemplate = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        Storage::fake('public');

        $response = $this->actingAs($user)
            ->from(route('creator.services.create'))
            ->post(route('creator.services.store'), [
                'title' => 'Test Service',
                'description' => 'Test Description',
                'price' => 100,
                'category_id' => $category->id,
                'workflow_template_id' => $workflowTemplate->id,
                'address_id' => null,
                'newImages' => [
                    UploadedFile::fake()->image('image1.jpg'),
                    UploadedFile::fake()->image('image2.jpg'),
                ],
            ]);

        $response->assertRedirect(route('creator.services.index'));
        $response->assertSessionHas('success');

        $service = Service::first();
        $this->assertCount(2, $service->getMedia('gallery'));
    }
}
