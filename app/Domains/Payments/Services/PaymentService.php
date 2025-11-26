<?php

namespace App\Domains\Payments\Services;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class PaymentService
{
    protected $invoiceApi;
    protected $isDevelopment;

    public function __construct()
    {
        $this->isDevelopment = config('payment.is_test_mode');
        
        if (!$this->isDevelopment) {
            $apiKey = config('payment.xendit.api_key');
            
            if (!$apiKey) {
                throw new \Exception('XENDIT_API_KEY is not configured in .env');
            }
            
            Log::info('Xendit API Key loaded', ['key_length' => strlen($apiKey), 'key_start' => substr($apiKey, 0, 10)]);
            
            // Configure Xendit API
            $config = Configuration::getDefaultConfiguration();
            $config->setApiKey('Authorization', $apiKey);
            
            $this->invoiceApi = new InvoiceApi(null, $config);
        }
    }

    public function createInvoice(Order $order)
    {
        $platformFeePercentage = config('payment.platform_fee.percentage', 5);
        $platformFee = ($order->price * $platformFeePercentage) / 100;
        $totalAmount = $order->price + $platformFee;

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'amount' => $order->price,
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
            'payment_method' => 'pending',
            'status' => 'pending',
        ]);

        try {
            if ($this->isDevelopment) {
                // Development mode: simulate payment
                Log::info('Development Mode: Simulating Xendit Invoice', [
                    'payment_id' => $payment->id,
                    'amount' => $totalAmount,
                ]);
                
                $payment->provider_reference = 'DEV_INVOICE_' . $payment->id;
                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->save();

                // Auto-process payment in dev mode
                $order->payment_status = 'paid';
                $order->save();

                // Return success URL directly
                return url('/payments/success?payment_id=' . $payment->id);
            } else {
                // Production mode: use real Xendit
                $params = [
                    'external_id' => (string) $payment->id,
                    'payer_email' => Auth::user()->email,
                    'description' => 'Payment for Order #' . $order->id,
                    'amount' => (int) $totalAmount,
                    'success_redirect_url' => url('/payments/success?payment_id=' . $payment->id),
                    'failure_redirect_url' => url('/payments/failed?payment_id=' . $payment->id),
                ];

                Log::info('Creating Xendit Invoice', [
                    'external_id' => $params['external_id'],
                    'amount' => $params['amount'],
                    'email' => $params['payer_email'],
                ]);

                $invoice = $this->invoiceApi->createInvoice($params);

                Log::info('Xendit Invoice Created', [
                    'invoice_id' => $invoice['id'] ?? null,
                    'invoice_url' => $invoice['invoice_url'] ?? null,
                ]);

                $payment->provider_reference = $invoice['id'];
                $payment->save();

                return $invoice['invoice_url'];
            }
        } catch (\Exception $e) {
            // Log error and re-throw
            Log::error('Invoice Creation Failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'exception_class' => get_class($e),
            ]);
            
            throw new \Exception('Payment gateway error: ' . $e->getMessage());
        }
    }
}
