<?php

namespace App\Domains\Orders\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use App\Enums\OrderStatus;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;
use LogicException;

class OrderService
{
    public function createOrderFromBid(OpenOfferBid $bid): Order
    {
        // 1. Extract data from the bid and its relationships
        $bidder = $bid->bidder; // The one offering the service (seller)
        $offer = $bid->openOffer;
        $offerCreator = $offer->creator; // The one requesting the service (buyer)
        
        // The service_id is required and should be on the bid.
        if (!$bid->service_id) {
            throw new LogicException('Cannot create an order from a bid that is not linked to a service.');
        }

        // 2. Create the Order
        $order = Order::create([
            'buyer_id' => $offerCreator->id,
            'seller_id' => $bidder->id,
            'service_id' => $bid->service_id,
            'open_offer_id' => $bid->open_offer_id,
            'open_offer_bid_id' => $bid->id,
            'price' => $bid->amount,
            'platform_fee' => $this->calculatePlatformFee($bid->amount),
            'total_amount' => $bid->amount + $this->calculatePlatformFee($bid->amount),
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending', // Default payment status
        ]);

        // 3. Clone the workflow, if one is associated with the service
        $service = $bid->service;
        if ($service && $service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        // 4. Send notifications
        // Mail::to($offerCreator->email)->send(new OrderCreated($order));

        return $order;
    }

    public function createOrderFromService(Service $service, User $buyer): Order
    {
        // 1. Extract data from the service and buyer
        $seller = $service->creator;

        // 2. Create the Order
        $order = Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'service_id' => $service->id,
            'open_offer_id' => null, // Now nullable in DB
            'open_offer_bid_id' => null, // Now nullable in DB
            'price' => $service->price,
            'platform_fee' => $this->calculatePlatformFee($service->price),
            'total_amount' => $service->price + $this->calculatePlatformFee($service->price),
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ]);

        // 3. Clone the workflow, if one is associated with the service
        if ($service->workflowTemplate) {
            $this->cloneWorkflowForOrder($order, $service->workflowTemplate);
        }

        // 4. Send notifications
        // Mail::to($buyer->email)->send(new OrderCreated($order));

        return $order;
    }

    protected function calculatePlatformFee(float $amount): float
    {
        // Replace with actual fee calculation logic
        return $amount * 0.05; // Example: 5% fee
    }

    protected function cloneWorkflowForOrder(Order $order, WorkflowTemplate $workflowTemplate): void
    {
        $workInstance = WorkInstance::create([
            'order_id' => $order->id,
            'workflow_template_id' => $workflowTemplate->id,
            'current_step_index' => 0,
            'status' => 'pending',
        ]);

        foreach ($workflowTemplate->workTemplates as $index => $workTemplate) {
            WorkInstanceStep::create([
                'work_instance_id' => $workInstance->id,
                'work_template_id' => $workTemplate->id,
                'step_index' => $index,
                'status' => 'pending',
            ]);
        }
    }

    public function cancelOrder(Order $order): Order
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            // Or throw an exception
            return $order;
        }

        $order->status = OrderStatus::CANCELLED->value;
        $order->save();

        // TODO: Send OrderCancelled email
        // Mail::to($order->buyer->email)->send(new OrderCancelled($order));

        return $order;
    }
}
