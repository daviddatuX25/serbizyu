<?php

namespace App\Domains\Orders\Services;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Messaging\Models\MessageThread;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;
use App\Enums\OrderStatus;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function createOrderFromBid(OpenOfferBid $bid): Order
    {
        $offer = $bid->openOffer;
        $service = $bid->service;

        $orderData = [
            'buyer_id' => $offer->creator_id,
            'seller_id' => $bid->bidder_id,
            'service_id' => $service->id,
            'open_offer_id' => $bid->open_offer_id,
            'open_offer_bid_id' => $bid->id,
            'price' => $bid->amount,
            'platform_fee' => $this->calculatePlatformFee($bid->amount),
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ];

        $orderData['total_amount'] = $orderData['price'] + $orderData['platform_fee'];

        $order = Order::create($orderData);

        // Create message thread for order communication
        $this->createOrderMessageThread($order);

        if ($service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        $this->sendOrderCreatedEmail($order);

        return $order;
    }

    public function createOrderFromService(Service $service, User $buyer): Order
    {
        $service->refresh();

        $orderData = [
            'buyer_id' => $buyer->id,
            'seller_id' => $service->creator_id,
            'service_id' => $service->id,
            'price' => $service->price,
            'platform_fee' => $this->calculatePlatformFee($service->price),
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ];

        $orderData['total_amount'] = $orderData['price'] + $orderData['platform_fee'];

        $order = Order::create($orderData);

        // Create message thread for order communication
        $this->createOrderMessageThread($order);

        if ($service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        $this->sendOrderCreatedEmail($order);

        return $order;
    }

    protected function calculatePlatformFee(float $amount): float
    {
        return $amount * 0.05;
    }

    protected function createOrderMessageThread(Order $order): void
    {
        MessageThread::create([
            'creator_id' => $order->buyer_id,
            'title' => "Order #{$order->id} - Discussion",
            'parent_type' => Order::class,
            'parent_id' => $order->id,
        ]);
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
