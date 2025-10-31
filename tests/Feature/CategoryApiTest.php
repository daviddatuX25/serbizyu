<?php

namespace Tests\Feature;

use App\Domains\Listings\Models\Category;
use App\Domains\Users\Models\User;
use Database\Seeders\RolesSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for testing authenticated actions
         // Always seed roles for tests
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        // Create a regular user for testing unauthorized actions
        $this->regularUser = User::factory()->create();
    }

    /**
     * Test that a guest cannot create a category.
     *
     * @return void
     */
    public function test_guest_cannot_create_category()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that a regular user cannot create a category.
     *
     * @return void
     */
    public function test_regular_user_cannot_create_category()
    {
        $response = $this->actingAs($this->regularUser)->postJson('/api/categories', [
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test that an admin can create a category.
     *
     * @return void
     */
    public function test_admin_can_create_category()
    {
        $response = $this->actingAs($this->adminUser)->postJson('/api/categories', [
            'name' => 'New Category',
            'description' => 'Description for new category',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'New Category',
                     'description' => 'Description for new category',
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
        ]);
    }

    /**
     * Test that categories can be listed.
     *
     * @return void
     */
    public function test_categories_can_be_listed()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Test that categories can be listed with search filter.
     *
     * @return void
     */
    public function test_categories_can_be_listed_with_search_filter()
    {
        Category::factory()->create(['name' => 'Unique Category Name']);
        Category::factory()->count(4)->create();

        $response = $this->getJson('/api/categories?search=Unique');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Unique Category Name']);
    }

    /**
     * Test that a specific category can be retrieved.
     *
     * @return void
     */
    public function test_specific_category_can_be_retrieved()
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $category->id,
                     'name' => $category->name,
                 ]);
    }

    /**
     * Test that a guest cannot update a category.
     *
     * @return void
     */
    public function test_guest_cannot_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that a regular user cannot update a category.
     *
     * @return void
     */
    public function test_regular_user_cannot_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->regularUser)->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test that an admin can update a category.
     *
     * @return void
     */
    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->adminUser)->putJson('/api/categories/' . $category->id, [
            'name' => 'Updated Category Name',
            'description' => 'Updated Description',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Updated Category Name',
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
        ]);
    }

    /**
     * Test that a guest cannot delete a category.
     *
     * @return void
     */
    public function test_guest_cannot_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(401);
    }

    /**
     * Test that a regular user cannot delete a category.
     *
     * @return void
     */
    public function test_regular_user_cannot_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->regularUser)->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(403);
    }

    /**
     * Test that an admin can delete a category.
     *
     * @return void
     */
    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->adminUser)->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }
}
