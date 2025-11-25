<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Disbursement;
use App\Domains\Payments\Models\Payment;
use Illuminate\Support\Facades\Config;

class DisbursementService
{
    /**
     * Hold payment in escrow on order creation
     */
    public function holdInEscrow(Order $order, Payment $payment): Disbursement
    {
        $platformFeePercentage = Config::get('payment.platform_fee.percentage', 5);
        $platformFee = ($payment->amount * $platformFeePercentage) / 100;
        $sellerAmount = $payment->amount - $platformFee;

        return Disbursement::create([
            'order_id' => $order->id,
            'seller_id' => $order->seller_id,
            'amount' => $sellerAmount,
            'platform_fee_amount' => $platformFee,
            'status' => 'pending', // Payment received, awaiting buyer confirmation
        ]);
    }

    /**
     * Release payment to seller after buyer confirms work completion
     */
    public function releasePayment(Disbursement $disbursement): bool
    {
        if (!$disbursement->isPending()) {
            throw new \Exception('Only pending disbursements can be released.');
        }

        $disbursement->update([
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        return true;
    }

    /**
     * Request disbursement as seller (move from pending to requested)
     */
    public function requestDisbursement(Disbursement $disbursement, array $bankDetails): bool
    {
        if (!$disbursement->isPending()) {
            throw new \Exception('Only pending disbursements can be requested.');
        }

        $disbursement->update([
            'status' => 'requested',
            'bank_details' => $bankDetails,
            'requested_at' => now(),
        ]);

        return true;
    }

    /**
     * Process disbursement (admin action)
     */
    public function processDisbursement(Disbursement $disbursement): bool
    {
        if (!$disbursement->isRequested()) {
            throw new \Exception('Only requested disbursements can be processed.');
        }

        $disbursement->update([
            'status' => 'processing',
        ]);

        return true;
    }

    /**
     * Complete disbursement (admin confirms payment sent)
     */
    public function completeDisbursement(Disbursement $disbursement): bool
    {
        if (!$disbursement->isProcessing()) {
            throw new \Exception('Only processing disbursements can be completed.');
        }

        $disbursement->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return true;
    }

    /**
     * Calculate seller earnings for a given period
     */
    public function calculateSellerEarnings(int $sellerId, ?\DateTime $fromDate = null, ?\DateTime $toDate = null): array
    {
        $query = Disbursement::where('seller_id', $sellerId)
            ->where('status', 'completed');

        if ($fromDate) {
            $query->whereDate('processed_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('processed_at', '<=', $toDate);
        }

        $disbursements = $query->get();

        $totalEarnings = $disbursements->sum('amount');
        $totalFees = $disbursements->sum('platform_fee_amount');
        $count = $disbursements->count();

        return [
            'total_earnings' => $totalEarnings,
            'total_fees' => $totalFees,
            'gross_amount' => $totalEarnings + $totalFees,
            'count' => $count,
            'disbursements' => $disbursements,
        ];
    }

    /**
     * Get pending balance for a seller
     */
    public function getPendingBalance(int $sellerId)
    {
        return Disbursement::where('seller_id', $sellerId)
            ->whereIn('status', ['pending', 'requested', 'processing'])
            ->sum('amount');
    }

    /**
     * Get completed balance for a seller
     */
    public function getCompletedBalance(int $sellerId)
    {
        return Disbursement::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get total platform fees collected
     */
    public function getTotalFeesCollected(?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Disbursement::whereIn('status', ['completed', 'processing']);

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        return $query->sum('platform_fee_amount');
    }
}
