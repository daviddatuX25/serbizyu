<?php

namespace App\Domains\Orders\Services;

use App\Domains\Listings\Models\OpenOfferBid;
use App\Domains\Orders\Models\Order;
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
    public function createOrderFromBid(string $bidId): Order
    {
        $bid = OpenOfferBid::with('openOffer', 'service')->findOrFail($bidId);
        $offer = $bid->openOffer;

        // Set the bid as accepted
        $bid->accepted = true;
        $bid->save();

        $order = new Order([
            'buyer_id' => $offer->buyer_id,
            'seller_id' => $bid->service->creator_id,
            'service_id' => $bid->service_id,
            'open_offer_id' => $bid->open_offer_id,
            'open_offer_bid_id' => $bidId,
            'price' => $bid->proposed_price,
            'platform_fee' => $bid->proposed_price * 0.05, // Assuming a 5% platform fee
            'total_amount' => $bid->proposed_price * 1.05,
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ]);
        $order->save();

        // Workflow Cloning Logic
        $service = $bid->service;
        if ($service && $service->workflowTemplate) {
            $workflowTemplate = $service->workflowTemplate;

            $workInstance = WorkInstance::create([
                'order_id' => $order->id,
                'workflow_template_id' => $workflowTemplate->id,
                'current_step_index' => 0,
                'status' => 'pending', // Initial status for work instance
                'started_at' => null,
                'completed_at' => null,
            ]);

            foreach ($workflowTemplate->workTemplates as $index => $workTemplate) {
                WorkInstanceStep::create([
                    'work_instance_id' => $workInstance->id,
                    'work_template_id' => $workTemplate->id,
                    'step_index' => $index,
                    'status' => 'pending', // Initial status for each step
                    'started_at' => null,
                    'completed_at' => null,
                ]);
            }
        }


        try {
            Mail::to($order->buyer->email)->send(new OrderCreated($order)); // Send email to buyer
        } catch (\Exception $e) {
            // Log the error but don't break the order creation
            Log::warning('Failed to send order created email', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        return $order;
    }

    public function createOrderFromService(string $serviceId, $buyer): Order
    {
        $service = Service::findOrFail($serviceId);

        $order = new Order([
            'buyer_id' => $buyer->id,
            'seller_id' => $service->creator_id,
            'service_id' => $service->id,
            'price' => $service->price,
            'platform_fee' => $service->price * 0.05, // Assuming a 5% platform fee
            'total_amount' => $service->price * 1.05,
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ]);
        $order->save();

        // Workflow Cloning Logic
        if ($service && $service->workflowTemplate) {
            $workflowTemplate = $service->workflowTemplate;

            $workInstance = WorkInstance::create([
                'order_id' => $order->id,
                'workflow_template_id' => $workflowTemplate->id,
                'current_step_index' => 0,
                'status' => 'pending', // Initial status for work instance
                'started_at' => null,
                'completed_at' => null,
            ]);

            foreach ($workflowTemplate->workTemplates as $index => $workTemplate) {
                WorkInstanceStep::create([
                    'work_instance_id' => $workInstance->id,
                    'work_template_id' => $workTemplate->id,
                    'step_index' => $index,
                    'status' => 'pending', // Initial status for each step
                    'started_at' => null,
                    'completed_at' => null,
                ]);
            }
        }


        Mail::to($order->buyer->email)->send(new OrderCreated($order)); // Send email to buyer

        return $order;
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
