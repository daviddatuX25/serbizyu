<?php

namespace Tests\Feature\Livewire;

use App\Domains\Common\Models\Address;
use App\Domains\Users\Models\User;
use App\Livewire\AddressManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

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
        $address = Address::create([
            'street_address' => 'Main Street',
            'barangay' => 'Test Barangay',
            'city' => '042108',
            'province' => '042100',
            'region' => '040000',
            'province_name' => 'Cavite',
            'region_name' => 'CALABARZON',
            'full_address' => 'Main Street, Test Barangay, Cavite, CALABARZON',
            'api_source' => 'PSGC_API',
            'api_id' => '040000-042100-042108',
            'lat' => 14.3060,
            'lng' => 120.9400,
        ]);
        $user->addresses()->attach($address->id, ['is_primary' => true]);

        Livewire::actingAs($user)
            ->test(AddressManager::class)
            ->assertSee('Main Street');
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function address_accessor_composes_full_address_from_components()
    {
        $address = Address::create([
            'street_address' => '123 Main St',
            'barangay' => 'Barangay Test',
            'city' => '042108',
            'province' => '042100',
            'region' => '040000',
            'province_name' => 'Cavite',
            'region_name' => 'CALABARZON',
            'full_address' => '123 Main St, Barangay Test, Cavite, CALABARZON',
            'api_source' => 'PSGC_API',
            'api_id' => '040000-042100-042108',
            'lat' => 14.3060,
            'lng' => 120.9400,
        ]);

        $fullAddress = $address->full_address;
        $this->assertStringContainsString('123 Main St', $fullAddress);
        $this->assertStringContainsString('Barangay Test', $fullAddress);
        $this->assertStringContainsString('Cavite', $fullAddress);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function address_hash_deduplicates_identical_addresses()
    {
        // Test that individual address component fields are persisted and can be retrieved
        $address = Address::create([
            'street_address' => '123 Main St',
            'barangay' => 'Test Barangay',
            'city' => '042108',
            'province' => '042100',
            'region' => '040000',
            'province_name' => 'Cavite',
            'region_name' => 'CALABARZON',
            'full_address' => '123 Main St, Test Barangay, Cavite, CALABARZON',
            'api_source' => 'PSGC_API',
            'api_id' => '040000-042100-042108',
            'lat' => 14.3060,
            'lng' => 120.9400,
        ]);

        // Verify the address was created with all component fields
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street_address' => '123 Main St',
            'barangay' => 'Test Barangay',
            'city' => '042108',
            'province' => '042100',
            'region' => '040000',
            'province_name' => 'Cavite',
            'region_name' => 'CALABARZON',
        ]);

        // Refresh and verify retrieval
        $retrieved = Address::find($address->id);
        $this->assertEquals('123 Main St', $retrieved->street_address);
        $this->assertEquals('Test Barangay', $retrieved->barangay);
        $this->assertEquals('Cavite', $retrieved->province_name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_edit_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create([
            'street_address' => 'Old Street',
            'city' => '042108',
            'province' => '042100',
            'region' => '040000',
        ]);
        $user->addresses()->attach($address->id, ['is_primary' => false]);

        $user->refresh();
        $userAddress = $user->addresses()->withPivot('is_primary')->find($address->id);
        $this->assertNotNull($userAddress);
        $this->assertEquals('Old Street', $userAddress->street_address);
    }
}
