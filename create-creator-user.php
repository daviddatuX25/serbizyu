<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\Users\Models\User;

$user = User::firstOrCreate(
    ['email' => 'creator@serbizyu.local'],
    [
        'firstname' => 'Creator',
        'lastname' => 'Admin',
        'password' => bcrypt('password'),
        'is_verified' => true,
        'email_verified_at' => now(),
    ]
);

// Assign admin role if not already assigned
if (!$user->hasRole('admin')) {
    $user->assignRole('admin');
}

echo "✅ User exists/created: {$user->email} (ID: {$user->id})\n";
echo "✅ Admin role assigned\n";
