<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Admin\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = $this->dashboardService->getStats();
        $chartData = $this->dashboardService->getChartData();
        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}
