<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function checkout(Order $order)
    {
        return view('payments.checkout', compact('order'));
    }

    public function pay(Request $request, Order $order)
    {
        try {
            $invoiceUrl = $this->paymentService->createInvoice($order);
            return redirect()->away($invoiceUrl);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::find($paymentId);
        
        return view('payments.success', compact('payment'));
    }

    public function failed(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::find($paymentId);
        
        return view('payments.failed', compact('payment'));
    }
}
