<?php

namespace App\Domains\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Orders\Models\Order;
use App\Domains\Work\Models\WorkInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Orders where the user is either buyer or seller (limit for the dashboard)
        $orders = Order::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with('service', 'buyer', 'seller')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $orderStats = [
            'total' => Order::where('buyer_id', $user->id)->orWhere('seller_id', $user->id)->count(),
            'pending' => Order::where(['status' => 'pending',])->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->count(),
            'completed' => Order::where(['status' => 'completed',])->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->count(),
        ];

        // Work instances where the user is the seller, with their steps and activities
        $workInstances = WorkInstance::whereHas('order', function ($query) use ($user) {
            $query->where('seller_id', $user->id);
        })->with([
            'order.service',
            'order.buyer',
            'workInstanceSteps.workTemplate',
            'workInstanceSteps.activityThread.messages'
        ])->orderByDesc('created_at')->take(6)->get();

        $workStats = [
            'total' => $workInstances->count(),
            'in_progress' => $workInstances->where('status', 'in_progress')->count(),
            'completed' => $workInstances->where('status', 'completed')->count(),
        ];

        return view('creator.dashboard', compact('orders', 'orderStats', 'workInstances', 'workStats'));
    }
}
