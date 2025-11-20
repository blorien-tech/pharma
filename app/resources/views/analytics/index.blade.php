@extends('layouts.app')

@section('title', 'Analytics Dashboard - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Visual insights and business intelligence</p>
    </div>

    <!-- Monthly Comparison Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">This Month</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format($monthlyComparison['current'], 2) }}</div>
            <div class="text-xs opacity-75 mt-1">Total Sales</div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">Last Month</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format($monthlyComparison['last'], 2) }}</div>
            <div class="text-xs opacity-75 mt-1">Total Sales</div>
        </div>

        <div class="bg-gradient-to-br from-{{ $monthlyComparison['growth'] >= 0 ? 'green' : 'red' }}-500 to-{{ $monthlyComparison['growth'] >= 0 ? 'green' : 'red' }}-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">Growth</div>
            <div class="text-3xl font-bold mt-2">{{ $monthlyComparison['growth'] >= 0 ? '+' : '' }}{{ number_format($monthlyComparison['growth'], 1) }}%</div>
            <div class="text-xs opacity-75 mt-1">Month over Month</div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Sales Trend (Last 30 Days)</h2>
            <div class="flex gap-2">
                <button onclick="updatePeriod('7days')" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">7 Days</button>
                <button onclick="updatePeriod('30days')" class="px-3 py-1 text-sm bg-blue-600 text-white rounded">30 Days</button>
                <button onclick="updatePeriod('90days')" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">90 Days</button>
            </div>
        </div>
        <canvas id="salesTrendChart" height="80"></canvas>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Methods Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Payment Method</h2>
            <canvas id="paymentMethodChart"></canvas>
        </div>

        <!-- Inventory Status Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory Status</h2>
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Products by Revenue (This Month)</h2>
        <canvas id="topProductsChart" height="80"></canvas>
    </div>

    <!-- Customer Credit Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Credit Utilization</h2>
        <canvas id="creditChart"></canvas>
        <div class="mt-4 grid grid-cols-2 gap-4 text-center">
            <div>
                <div class="text-2xl font-bold text-red-600">৳{{ number_format(array_sum($creditData['values']) > 0 ? $creditData['values'][0] : 0, 2) }}</div>
                <div class="text-sm text-gray-600">Used Credit</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">৳{{ number_format(array_sum($creditData['values']) > 1 ? $creditData['values'][1] : 0, 2) }}</div>
                <div class="text-sm text-gray-600">Available Credit</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Color palette
const colors = {
    primary: 'rgb(37, 99, 235)',
    success: 'rgb(34, 197, 94)',
    warning: 'rgb(234, 179, 8)',
    danger: 'rgb(239, 68, 68)',
    purple: 'rgb(168, 85, 247)',
    orange: 'rgb(249, 115, 22)',
    teal: 'rgb(20, 184, 166)',
};

// Sales Trend Chart
const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
let salesTrendChart = new Chart(salesTrendCtx, {
    type: 'line',
    data: {
        labels: @json($salesTrend['labels']),
        datasets: [
            {
                label: 'Sales (৳)',
                data: @json($salesTrend['sales']),
                borderColor: colors.primary,
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                yAxisID: 'y',
            },
            {
                label: 'Transactions',
                data: @json($salesTrend['transactions']),
                borderColor: colors.purple,
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                fill: true,
                tension: 0.4,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Sales (৳)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Transactions'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.dataset.yAxisID === 'y') {
                                label += '৳' + context.parsed.y.toFixed(2);
                            } else {
                                label += context.parsed.y;
                            }
                        }
                        return label;
                    }
                }
            }
        }
    }
});

// Payment Method Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: {
        labels: @json($paymentMethodData['labels']),
        datasets: [{
            data: @json($paymentMethodData['values']),
            backgroundColor: [
                colors.primary,
                colors.success,
                colors.warning,
                colors.purple,
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ৳' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Inventory Status Chart
const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
new Chart(inventoryCtx, {
    type: 'pie',
    data: {
        labels: @json($inventoryData['labels']),
        datasets: [{
            data: @json($inventoryData['values']),
            backgroundColor: [
                colors.warning,
                colors.success,
                colors.danger,
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Top Products Chart
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: @json($topProductsData['labels']),
        datasets: [{
            label: 'Revenue (৳)',
            data: @json($topProductsData['values']),
            backgroundColor: colors.primary,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Revenue (৳)'
                }
            }
        },
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ৳' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});

// Customer Credit Chart
const creditCtx = document.getElementById('creditChart').getContext('2d');
new Chart(creditCtx, {
    type: 'doughnut',
    data: {
        labels: @json($creditData['labels']),
        datasets: [{
            data: @json($creditData['values']),
            backgroundColor: [
                colors.danger,
                colors.success,
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ৳' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Function to update sales trend period
async function updatePeriod(period) {
    try {
        const response = await fetch(`/api/analytics/sales?period=${period}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        salesTrendChart.data.labels = data.labels;
        salesTrendChart.data.datasets[0].data = data.data;
        salesTrendChart.update();
    } catch (error) {
        console.error('Error updating chart:', error);
    }
}
</script>
@endpush
