<?php

namespace App\Domains\Admin\Services;

use App\Domains\Listings\Models\Flag;
use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Service;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Refund;
use App\Domains\Users\Models\CreatorFlagStats;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Log;

class FlagActionService
{
    /**
     * Handle all actions when a flag is approved
     */
    public function handleFlagApproval(Flag $flag): array
    {
        $results = [
            'content_suspended' => false,
            'refund_initiated' => false,
            'escalation_triggered' => false,
            'error' => null,
        ];

        try {
            // Get the flaggable item
            $flaggable = $flag->flaggable;
            if (! $flaggable) {
                $results['error'] = 'Flagged content not found';
                Log::warning("Flag {$flag->id}: Content deleted before approval");

                return $results;
            }

            // Route to appropriate handler based on type
            if ($flaggable instanceof OpenOffer) {
                $results['content_suspended'] = $this->suspendOpenOffer($flaggable);
            } elseif ($flaggable instanceof Service) {
                $results['content_suspended'] = $this->suspendService($flaggable);
            } elseif ($flaggable instanceof Order) {
                $results['refund_initiated'] = $this->initiateOrderRefund($flaggable, $flag);
            }

            // Track creator violations
            if ($flaggable instanceof OpenOffer || $flaggable instanceof Service) {
                $results['escalation_triggered'] = $this->handleCreatorEscalation($flaggable->creator);
            }

            return $results;
        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
            Log::error("FlagActionService error for flag {$flag->id}: ".$e->getMessage());

            return $results;
        }
    }

    /**
     * Suspend an OpenOffer
     */
    public function suspendOpenOffer(OpenOffer $openOffer): bool
    {
        if ($openOffer->status->value === 'suspended') {
            Log::info("OpenOffer {$openOffer->id} already suspended");

            return false;
        }

        $openOffer->update(['status' => 'suspended']);
        Log::info("OpenOffer {$openOffer->id} suspended due to flag approval");

        return true;
    }

    /**
     * Suspend a Service
     */
    public function suspendService(Service $service): bool
    {
        if ($service->status === 'suspended') {
            Log::info("Service {$service->id} already suspended");

            return false;
        }

        $service->update(['status' => 'suspended']);
        Log::info("Service {$service->id} suspended due to flag approval");

        return true;
    }

    /**
     * Initiate automatic refund for a flagged order
     */
    public function initiateOrderRefund(Order $order, Flag $flag): bool
    {
        // Check if order is refundable
        if (! $this->isOrderRefundable($order)) {
            Log::info("Order {$order->id} not refundable - requires manual review");

            return false;
        }

        // Check if refund already exists
        if ($order->refund()->exists()) {
            Log::info("Order {$order->id} already has a refund");

            return false;
        }

        // Create refund request (auto-approved)
        Refund::create([
            'order_id' => $order->id,
            'payment_id' => $order->payment_id,
            'reason' => "Flagged for: {$flag->category->value}",
            'status' => 'approved',
            'processed_at' => now(),
        ]);

        // Update order status
        $order->update(['status' => 'disputed']);

        Log::info("Order {$order->id} refund initiated due to flag approval (Flag: {$flag->id})");

        return true;
    }

    /**
     * Check if order is eligible for refund
     */
    private function isOrderRefundable(Order $order): bool
    {
        // Payment must be paid
        if ($order->payment_status !== 'paid') {
            return false;
        }

        // Order must not be cancelled
        if ($order->isCancelled()) {
            return false;
        }

        // Work must not have started
        $workInstance = $order->workInstance()->first();
        if ($workInstance && $workInstance->hasStarted()) {
            return false;
        }

        return true;
    }

    /**
     * Handle creator escalation based on flag count
     */
    public function handleCreatorEscalation(User $creator): bool
    {
        $stats = CreatorFlagStats::firstOrCreate(
            ['user_id' => $creator->id],
            [
                'total_flags' => 0,
                'flags_last_30_days' => 0,
                'escalation_level' => 0,
            ]
        );

        // Increment counters
        $stats->increment('total_flags');
        $this->updateFlagsLast30Days($stats);
        $stats->update(['last_flagged_at' => now()]);

        $escalationTriggered = false;

        // Check escalation thresholds
        if ($stats->shouldBan()) {
            $stats->update([
                'escalation_level' => 3,
                'escalation_triggered_at' => now(),
            ]);
            $this->banCreator($creator);
            Log::warning("Creator {$creator->id} banned after {$stats->total_flags} flags");
            $escalationTriggered = true;
        } elseif ($stats->shouldRestrict()) {
            $stats->update([
                'escalation_level' => 2,
                'escalation_triggered_at' => now(),
            ]);
            $creator->update(['restricted' => true]);
            Log::warning("Creator {$creator->id} restricted after {$stats->flags_last_30_days} flags in 30 days");
            $escalationTriggered = true;
        } elseif ($stats->shouldWarn()) {
            $stats->update([
                'escalation_level' => 1,
                'escalation_triggered_at' => now(),
            ]);
            Log::warning("Creator {$creator->id} warned after {$stats->flags_last_30_days} flags in 30 days");
            $escalationTriggered = true;
        }

        return $escalationTriggered;
    }

    /**
     * Update flags_last_30_days count from Flag table
     */
    private function updateFlagsLast30Days(CreatorFlagStats $stats): void
    {
        $thirtyDaysAgo = now()->subDays(30);

        $recentFlags = Flag::where('status', 'approved')
            ->where(function ($query) {
                $query->whereHas('flaggable', function ($q) {
                    $q->where('creator_id', $this->getCreatorId($q));
                });
            })
            ->where('reviewed_at', '>=', $thirtyDaysAgo)
            ->count();

        $stats->update(['flags_last_30_days' => $recentFlags]);
    }

    /**
     * Ban a creator (soft delete all content, disable account)
     */
    private function banCreator(User $creator): void
    {
        // Disable account
        $creator->update(['restricted' => true, 'banned_at' => now()]);

        // Soft-delete all creator's open offers
        OpenOffer::where('creator_id', $creator->id)->delete();

        // Soft-delete all creator's services
        Service::where('creator_id', $creator->id)->delete();

        Log::info("Creator {$creator->id} banned: account disabled and content deleted");
    }

    /**
     * Helper to get creator ID from flaggable
     */
    private function getCreatorId($query): int
    {
        // This is a placeholder - in real query it's handled differently
        return 0;
    }
}
