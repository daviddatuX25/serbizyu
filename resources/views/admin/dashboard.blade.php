<x-admin-layout>
    <x-slot name="header">
        {{ __('Admin Dashboard') }}
    </x-slot>

    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Total Users Card -->
        <div class="bg-slate-800 overflow-hidden rounded-lg shadow-md border border-slate-700 hover:border-slate-600 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Total Users</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-slate-800 overflow-hidden rounded-lg shadow-md border border-slate-700 hover:border-slate-600 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Total Orders</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-6 h-6 text-green-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-slate-800 overflow-hidden rounded-lg shadow-md border border-slate-700 hover:border-slate-600 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">Total Revenue</p>
                        <p class="mt-2 text-3xl font-bold text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-6 h-6 text-purple-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 overflow-hidden rounded-lg shadow-md border border-slate-700 mt-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Analytics</h3>
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map(dataset => ({
                    ...dataset,
                    backgroundColor: dataset.backgroundColor || 'rgba(59, 130, 246, 0.5)',
                    borderColor: dataset.borderColor || 'rgb(59, 130, 246)',
                }))
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#cbd5e1'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#1e293b'
                        },
                        ticks: {
                            color: '#cbd5e1'
                        }
                    },
                    x: {
                        grid: {
                            color: '#1e293b'
                        },
                        ticks: {
                            color: '#cbd5e1'
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>
