<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AddressManager;
use App\Domains\Users\Models\User;
use App\Domains\Common\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Domains\Common\Services\AddressService;


class AddressManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function component_can_render()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_existing_addresses()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['street' => 'Main Street']);
        $user->addresses()->attach($address->id, ['is_primary' => true]);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->assertSee('Main Street');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_add_a_new_address()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->set('house_no', '123')
            ->set('street', 'Main St 2')
            ->set('barangay', 'Test Barangay')
            ->set('town', 'Anytown')
            ->set('province', 'CA')
            ->set('country', 'USA')
            ->set('is_primary', true)
            ->call('save');

        $this->assertDatabaseHas('addresses', ['street' => 'Main St 2']);
        
        $user = $user->fresh();
        $address = $user->addresses()->withPivot('is_primary')->where('street', 'Main St 2')->first();
        $this->assertNotNull($address);
        $this->assertTrue((bool) $address->pivot->is_primary);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_update_an_existing_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'town' => 'Old City',
            'province' => 'Old Province',
            'country' => 'Old Country',
        ]);
        $user->addresses()->attach($address->id, ['is_primary' => false]);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->call('edit', $address->id)
            ->assertSet('town', 'Old City')
            ->assertSet('province', 'Old Province')
            ->assertSet('country', 'Old Country')
            ->set('town', 'New City')
            ->call('save');

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'town' => 'New City',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_update_another_users_address()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $addressB = Address::factory()->create();
        $userB->addresses()->attach($addressB->id);

        Livewire::actingAs($userA)
            ->test(AddressManager::class)
            // Manually set the addressId to simulate trying to update another user's address
            ->set('addressId', $addressB->id)
            ->set('town', 'Malicious Town')
            ->set('province', 'Test Province')
            ->set('country', 'Test Country')
            ->call('save');
            
        // The service's `findOrFail` should fail, and the component's catch block should flash an error.
        // We assert that the address was NOT updated with the malicious data.
        $this->assertDatabaseMissing('addresses', [
            'id' => $addressB->id,
            'town' => 'Malicious Town',
        ]);
    }


    #[\PHPUnit\Framework\Attributes\Test]
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

        // Refresh user and check pivot data
        $user = $user->fresh();
        $addr1 = $user->addresses()->withPivot('is_primary')->find($address1->id);
        $addr2 = $user->addresses()->withPivot('is_primary')->find($address2->id);
        
        $this->assertFalse((bool) $addr1->pivot->is_primary);
        $this->assertTrue((bool) $addr2->pivot->is_primary);
    }
}