<?php

namespace App\Domains\Admin\Services;

use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;

class DashboardService
{
    /**
     * Get platform statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total_users' => $this->getTotalUsers(),
            'total_orders' => $this->getTotalOrders(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }

    /**
     * Get the total number of users.
     *
     * @return int
     */
    protected function getTotalUsers(): int
    {
        return User::count();
    }

    /**
     * Get the total number of orders.
     *
     * @return int
     */
    protected function getTotalOrders(): int
    {
        return Order::count();
    }

    /**
     * Get the total revenue.
     *
     * @return float
     */
    protected function getTotalRevenue(): float
    {
        // Assuming 'total_amount' is the column that stores the order's total amount
        return Order::where('payment_status', 'paid')->sum('total_amount');
    }

    /**
     * Get data for charts.
     *
     * @return array
     */
    public function getChartData(): array
    {
        $days = 30;
        $labels = [];
        $usersData = [];
        $ordersData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $usersData[] = User::whereDate('created_at', $date)->count();
            $ordersData[] = Order::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $usersData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'New Orders',
                    'data' => $ordersData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
