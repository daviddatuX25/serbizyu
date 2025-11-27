<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles for tests if they don't exist
        $this->createApplicationRoles();
    }

    protected function createApplicationRoles(): void
    {
        $roles = ['admin', 'moderator', 'user'];

        foreach ($roles as $roleName) {
            if (! $this->roleExists($roleName)) {
                \Spatie\Permission\Models\Role::create([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }
        }
    }

    protected function roleExists(string $name): bool
    {
        return \Spatie\Permission\Models\Role::where('name', $name)->where('guard_name', 'web')->exists();
    }
}
