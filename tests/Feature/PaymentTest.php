<?php

namespace Tests\Feature;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\PaymentService;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pay_for_an_order_and_webhook_updates_status()
    {
        // 1. Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create(['buyer_id' => $user->id, 'payment_status' => 'pending']);
        $this->actingAs($user);

        $invoiceUrl = 'https://fake-xendit-invoice.com';

        // 2. Mock PaymentService
        $this->mock(PaymentService::class, function (MockInterface $mock) use ($invoiceUrl) {
            $mock->shouldReceive('createInvoice')->andReturn($invoiceUrl);
        });

        // 3. Act - Pay for the order
        $response = $this->post(route('payments.pay', $order));

        // 4. Assert - Redirection
        $response->assertRedirect($invoiceUrl);

        // 5. Arrange - Webhook
        $payment = Payment::first();
        $webhookToken = 'fake-webhook-token';
        Config::set('payment.xendit.webhook_token', $webhookToken);

        $webhookPayload = [
            'external_id' => (string) $payment->id,
            'status' => 'PAID',
        ];

        // 6. Act - Webhook
        $response = $this->postJson(route('payments.webhook'), $webhookPayload, [
            'x-callback-token' => $webhookToken,
        ]);

        // 7. Assert - Webhook response and database state
        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }
}
