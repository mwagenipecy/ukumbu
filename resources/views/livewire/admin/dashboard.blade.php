<div>

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">TSh {{ number_format($totalRevenue / 1000000, 1) }}M</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                        {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalBookings) }}</p>
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-arrow-{{ $bookingsGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                        {{ $bookingsGrowth >= 0 ? '+' : '' }}{{ $bookingsGrowth }}% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Venues -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Venues</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($activeVenues) }}</p>
                    <p class="text-sm text-purple-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $venuesGrowth }} new this week
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                    <p class="text-sm text-yellow-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $usersGrowth }} new this month
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Overview</h3>
                <div class="flex items-center space-x-2">
                    <select wire:model.live="timeRange" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                        <option value="30">Last 30 days</option>
                        <option value="60">Last 60 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="h-64">
                <canvas id="revenueChart" wire:ignore></canvas>
            </div>
        </div>

        <!-- Bookings Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Booking Trends</h3>
                <div class="flex items-center space-x-2">
                    <select wire:model.live="selectedMetric" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                        <option value="weekly">This month</option>
                        <option value="status">By Status</option>
                        <option value="monthly">Monthly Trend</option>
                    </select>
                </div>
            </div>
            <div class="h-64">
                <canvas id="bookingsChart" wire:ignore></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="" 
                   class="flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors duration-200">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <span class="font-medium">Add New Venue</span>
                </a>
                
                <a href="" 
                   class="flex items-center p-3 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-colors duration-200">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-concierge-bell text-green-600"></i>
                    </div>
                    <span class="font-medium">Add New Service</span>
                </a>
                
                <a href="" 
                   class="flex items-center p-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg transition-colors duration-200">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-plus text-purple-600"></i>
                    </div>
                    <span class="font-medium">Add New User</span>
                </a>
                
                <a href="" 
                   class="flex items-center p-3 text-gray-700 hover:bg-orange-50 hover:text-orange-700 rounded-lg transition-colors duration-200">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-bar text-orange-600"></i>
                    </div>
                    <span class="font-medium">Generate Report</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <a href="" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                <!-- Activity Item -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">{!! $activity['description'] !!}</p>
                        <p class="text-xs text-gray-500">{{ $activity['created_at']->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <!-- Default Activity Items if no data -->
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">No recent activities found</p>
                        <p class="text-xs text-gray-500">Check back later for updates</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Approvals & Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">{{ count($pendingApprovals) }} items</span>
            </div>
            <div class="space-y-3">
                @forelse($pendingApprovals as $approval)
                <!-- Pending Item -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-{{ $approval['type_color'] }}-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $approval['icon'] }} text-{{ $approval['type_color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $approval['name'] }}</p>
                            <p class="text-sm text-gray-500">{{ $approval['description'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="approveItem('{{ $approval['model'] }}', {{ $approval['model_id'] }})" class="text-green-600 hover:text-green-800 p-1">
                            <i class="fas fa-check"></i>
                        </button>
                        <button wire:click="rejectItem('{{ $approval['model'] }}', {{ $approval['model_id'] }})" class="text-red-600 hover:text-red-800 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @empty
                <!-- Default Pending Item if no data -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-inbox text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">No pending approvals</p>
                            <p class="text-sm text-gray-500">All items are up to date</p>
                        </div>
                    </div>
                </div>
                @endforelse

                @if(count($pendingApprovals) > 0)
                <!-- View All Button -->
                <a href="" 
                   class="block text-center py-2 text-blue-600 hover:text-blue-800 font-medium">
                    View All Pending Items
                </a>
                @endif
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Top Performing Venues</h3>
                <select wire:model.live="timeRange" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                    <option value="30">This month</option>
                    <option value="60">Last month</option>
                    <option value="90">Last quarter</option>
                </select>
            </div>
            <div class="space-y-4">
                @forelse($topVenues as $index => $venue)
                <!-- Top Performer -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 
                        @if($index === 0) bg-yellow-100
                        @elseif($index === 1) bg-gray-100
                        @elseif($index === 2) bg-orange-100
                        @else bg-blue-100
                        @endif
                        rounded-full flex items-center justify-center">
                        <span class="
                            @if($index === 0) text-yellow-600
                            @elseif($index === 1) text-gray-600
                            @elseif($index === 2) text-orange-600
                            @else text-blue-600
                            @endif
                            font-bold text-sm">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $venue['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $venue['bookings_count'] }} bookings • TSh {{ number_format($venue['revenue'] / 1000000, 1) }}M revenue</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">+{{ $venue['growth'] }}%</p>
                    </div>
                </div>
                @empty
                <!-- Default Top Performers if no data -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-gray-600 font-bold text-sm">-</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">No venue data available</p>
                        <p class="text-sm text-gray-500">Check back after some bookings</p>
                    </div>
                </div>
                @endforelse

                @if(count($topVenues) > 0)
                <!-- View All Button -->
                <a href="" 
                   class="block text-center py-2 text-blue-600 hover:text-blue-800 font-medium">
                    View Detailed Report
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Loading...</span>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
</div>

@script
<script>
    let revenueChart = null;
    let bookingsChart = null;
    
    function initializeCharts() {
        // Revenue Chart (Line Chart)
        const revenueCtx = document.getElementById('revenueChart');
        if (!revenueCtx) return;
        
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        revenueChart = new Chart(revenueCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json(array_column($revenueChartData, 'month')),
                datasets: [{
                    label: 'Revenue (TSh)',
                    data: @json(array_column($revenueChartData, 'revenue')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TSh ' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // Bookings Chart (Bar Chart)
        const bookingsCtx = document.getElementById('bookingsChart');
        if (!bookingsCtx) return;
        
        if (bookingsChart) {
            bookingsChart.destroy();
        }

        // Get chart data based on selected metric
        let chartData, chartLabels, chartType = 'bar';
        
        if ($wire.selectedMetric === 'status') {
            // Show booking status breakdown
            const statusData = @json($bookingStatusData);
            chartLabels = statusData.map(item => item.name);
            chartData = statusData.map(item => item.value);
            chartType = 'doughnut';
        } else if ($wire.selectedMetric === 'monthly') {
            // Show monthly booking trends
            chartLabels = @json(array_column($revenueChartData, 'month'));
            chartData = @json(array_column($revenueChartData, 'bookings'));
        } else {
            // Default weekly view
            chartLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            const monthlyBookings = @json(array_column($revenueChartData, 'bookings'));
            const currentMonth = monthlyBookings[monthlyBookings.length - 1] || 0;
            chartData = [
                Math.floor(currentMonth * 0.2), 
                Math.floor(currentMonth * 0.25), 
                Math.floor(currentMonth * 0.22), 
                Math.floor(currentMonth * 0.33)
            ];
        }
        
        if (chartType === 'doughnut') {
            bookingsChart = new Chart(bookingsCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            bookingsChart = new Chart(bookingsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: chartData,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(245, 158, 11)'
                        ],
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            initializeCharts();
        }, 500);
    });

    // Reinitialize charts when Livewire updates
    Livewire.hook('morph.updated', () => {
        setTimeout(() => {
            initializeCharts();
        }, 100);
    });

    // Listen for chart updates
    $wire.on('charts-updated', () => {
        setTimeout(() => {
            initializeCharts();
        }, 100);
    });
</script>
@endscript



<div>