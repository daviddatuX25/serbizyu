<?php

namespace App\Domains\Payments\Http\Controllers;

use App\Domains\Payments\Models\Disbursement;
use App\Domains\Payments\Services\DisbursementService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisbursementController extends Controller
{
    protected $disbursementService;

    public function __construct(DisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    /**
     * Display seller earnings and disbursements
     */
    public function index()
    {
        $sellerId = Auth::id();

        $disbursements = Disbursement::where('seller_id', $sellerId)
            ->with('order.service')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingBalance = $this->disbursementService->getPendingBalance($sellerId);
        $completedBalance = $this->disbursementService->getCompletedBalance($sellerId);
        $totalEarnings = $pendingBalance + $completedBalance;

        return view('earnings.index', compact(
            'disbursements',
            'pendingBalance',
            'completedBalance',
            'totalEarnings'
        ));
    }

    /**
     * Request disbursement (seller action)
     */
    public function request(Request $request, Disbursement $disbursement)
    {
        // Authorization check
        if ($disbursement->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        try {
            $bankDetails = [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
            ];

            $this->disbursementService->requestDisbursement($disbursement, $bankDetails);

            return back()->with('success', 'Disbursement request submitted. Our team will review and process it soon.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show disbursement request form
     */
    public function show(Disbursement $disbursement)
    {
        // Authorization check
        if ($disbursement->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('earnings.show', compact('disbursement'));
    }
}
