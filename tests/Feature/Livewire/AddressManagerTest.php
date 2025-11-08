<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AddressManager;
use App\Domains\Users\Models\User;
use App\Domains\Common\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddressManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function component_can_render()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_displays_existing_addresses()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create();
        $user->addresses()->attach($address->id, ['is_primary' => true]);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->assertSee($address->address_line_1);
    }

    /** @test */
    public function can_add_a_new_address()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->set('address_line_1', '123 Main St')
            ->set('city', 'Anytown')
            ->set('state', 'CA')
            ->set('postal_code', '12345')
            ->set('country', 'USA')
            ->set('is_primary', true)
            ->call('save');

        $this->assertTrue($user->addresses()->where('address_line_1', '123 Main St')->exists());
        $this->assertTrue($user->addresses()->first()->pivot->is_primary);
    }

    /** @test */
    public function can_update_an_existing_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create();
        $user->addresses()->attach($address->id);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->call('edit', $address->id)
            ->set('city', 'New City')
            ->call('save');

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'city' => 'New City',
        ]);
    }

    /** @test */
    public function can_delete_an_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create();
        $user->addresses()->attach($address->id);

        $this->assertDatabaseCount('user_addresses', 1);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->call('confirmDelete', $address->id)
            ->call('delete');

        $this->assertDatabaseCount('user_addresses', 0);
    }

    /** @test */
    public function user_cannot_edit_another_users_address()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $addressB = Address::factory()->create();
        $userB->addresses()->attach($addressB->id);

        // Acting as User A, trying to edit User B's address
        Livewire::actingAs($userA)
            ->test(AddressManager::class)
            ->call('edit', $addressB->id)
            ->assertStatus(500); // Should fail with an exception caught by Livewire
    }

    /** @test */
    public function can_set_an_address_as_primary()
    {
        $user = User::factory()->create();
        $address1 = Address::factory()->create();
        $address2 = Address::factory()->create();
        $user->addresses()->attach($address1->id, ['is_primary' => true]);
        $user->addresses()->attach($address2->id, ['is_primary' => false]);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->call('setPrimary', $address2->id);

        $this->assertFalse((bool)$user->addresses()->find($address1->id)->pivot->is_primary);
        $this->assertTrue((bool)$user->addresses()->find($address2->id)->pivot->is_primary);
    }
}