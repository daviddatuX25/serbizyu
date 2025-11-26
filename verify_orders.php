<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== ORDER DATABASE INTEGRITY CHECK ===\n\n";

$orders = \App\Domains\Orders\Models\Order::select('id', 'buyer_id', 'seller_id', 'status', 'payment_status', 'total_amount')
    ->with('buyer', 'seller')
    ->get();

if ($orders->isEmpty()) {
    echo "❌ NO ORDERS FOUND IN DATABASE\n";
    echo "   You need to create at least one order to test the payment flow.\n";
    echo "   Steps:\n";
    echo "   1. Create a service (as User A)\n";
    echo "   2. User B buys that service\n";
    echo "   3. This creates an Order with buyer_id and seller_id\n\n";
} else {
    echo "✓ Found " . $orders->count() . " order(s)\n\n";

    foreach ($orders as $order) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Order #" . $order->id . "\n";
        echo "  Buyer ID: " . ($order->buyer_id ?? "NULL ❌") . " (" . ($order->buyer?->name ?? "Unknown") . ")\n";
        echo "  Seller ID: " . ($order->seller_id ?? "NULL ❌") . " (" . ($order->seller?->name ?? "Unknown") . ")\n";
        echo "  Status: " . $order->status . "\n";
        echo "  Payment Status: " . $order->payment_status . "\n";
        echo "  Total Amount: ₱" . number_format($order->total_amount, 2) . "\n";

        // Validate
        $isValid = $order->buyer_id && $order->seller_id && $order->buyer_id !== $order->seller_id;
        echo "  Status: " . ($isValid ? "✓ VALID" : "❌ INVALID") . "\n";
    }
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

echo "\n=== USER ACCOUNTS CHECK ===\n\n";
$users = \App\Domains\Users\Models\User::select('id', 'name', 'email')->get();
echo "Total Users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "  User #" . $user->id . ": " . $user->name . " (" . $user->email . ")\n";
}

echo "\n";
?>
