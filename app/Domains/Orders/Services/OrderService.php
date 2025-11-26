<?php

namespace App\Domains\Orders\Services;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Enums\OrderStatus;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;

class OrderService
{
    public function createOrderFromBid(OpenOfferBid $bid): Order
    {
        $offer = $bid->openOffer;
        $service = $bid->service;

        $orderData = $this->prepareOrderData(
            buyer_id: $offer->creator_id,
            seller_id: $service->creator_id,
            price: $bid->amount,
            service_id: $service->id,
            open_offer_id: $bid->open_offer_id,
            open_offer_bid_id: $bid->id,
        );

        $order = Order::create($orderData);

        if ($service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        $this->sendOrderCreatedEmail($order);

        return $order;
    }

    public function createOrderFromService(Service $service, User $buyer): Order
    {
        $service->refresh();

        $orderData = $this->prepareOrderData(
            buyer_id: $buyer->id,
            seller_id: $service->creator_id,
            price: $service->price,
            service_id: $service->id,
        );

        $order = Order::create($orderData);

        if ($service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        $this->sendOrderCreatedEmail($order);

        return $order;
    }

    private function prepareOrderData(
        int $buyer_id,
        int $seller_id,
        float $price,
        int $service_id,
        ?int $open_offer_id = null,
        ?int $open_offer_bid_id = null,
    ): array {
        return [
            'buyer_id' => $buyer_id,
            'seller_id' => $seller_id,
            'service_id' => $service_id,
            'open_offer_id' => $open_offer_id,
            'open_offer_bid_id' => $open_offer_bid_id,
            'price' => $price,
            'platform_fee' => $this->calculatePlatformFee($price),
            'total_amount' => $price + $this->calculatePlatformFee($price),
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ];
    }

    protected function calculatePlatformFee(float $amount): float
    {
        $percentage = config('payment.platform_fee.percentage', 5);
        return $amount * ($percentage / 100);
    }

    protected function cloneWorkflowForOrder(Order $order, WorkflowTemplate $workflowTemplate): void
    {
        $workInstance = WorkInstance::create([
            'order_id' => $order->id,
            'workflow_template_id' => $workflowTemplate->id,
            'current_step_index' => 0,
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ]);

        foreach ($workflowTemplate->workTemplates as $index => $workTemplate) {
            WorkInstanceStep::create([
                'work_instance_id' => $workInstance->id,
                'work_template_id' => $workTemplate->id,
                'step_index' => $index,
                'status' => 'pending',
                'started_at' => null,
                'completed_at' => null,
            ]);
        }
    }

    protected function sendOrderCreatedEmail(Order $order): void
    {
        try {
            Mail::to($order->buyer->email)->send(new OrderCreated($order));
        } catch (\Exception $e) {
            Log::warning('Failed to send order created email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cancelOrder(Order $order): Order
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            return $order;
        }

        $order->status = OrderStatus::CANCELLED->value;
        $order->save();

        return $order;
    }
}
