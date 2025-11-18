<?php

namespace App\Domains\Orders\Services;

use App\Domains\Orders\Models\Order;
use App\Enums\OrderStatus;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Work\Models\WorkInstance;
use App\Domains\Work\Models\WorkInstanceStep;

class OrderService
{
    public function createOrderFromBid(string $bidId): Order
    {
        // TODO: In a real implementation, find the bid by $bidId
        // For now, let's create a dummy order for demonstration
        $order = new Order([
            'buyer_id' => 1, // Placeholder
            'seller_id' => 2, // Placeholder
            'service_id' => 1, // Placeholder
            'open_offer_id' => 1, // Placeholder
            'open_offer_bid_id' => $bidId,
            'price' => 100.00,
            'platform_fee' => 5.00,
            'total_amount' => 105.00,
            'status' => OrderStatus::PENDING->value,
            'payment_status' => 'pending',
        ]);
        $order->save(); // Save the dummy order

        // Workflow Cloning Logic
        $service = Service::find($order->service_id);
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


        Mail::to('buyer@example.com')->send(new OrderCreated($order)); // Send email to buyer

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
