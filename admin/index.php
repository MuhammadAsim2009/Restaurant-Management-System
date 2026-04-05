<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="dashboard">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-heading font-bold text-white leading-tight">OVERVIEW</h2>
            <p class="text-k86-muted text-sm">Welcome back. Here's what's happening at Kitchen 86.</p>
        </div>
        <button class="bg-k86-surface2 hover:bg-k86-surface text-sm px-4 py-2 rounded-lg border border-k86-border font-semibold flex items-center gap-2 transition-all hover:border-k86-muted">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Export Report
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6" id="dash-stats">
        <div class="p-4 text-center text-k86-muted col-span-full">Loading stats...</div>
    </div>

    <!-- Charts & Tables Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-k86-surface border border-k86-border rounded-xl p-5">
            <h3 class="text-lg font-heading font-bold mb-4">Revenue Overview</h3>
            <div class="w-full h-64 relative">
                <!-- Chart container -->
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Items -->
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5">
            <h3 class="text-lg font-heading font-bold mb-4">Top Selling Items</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-3 bg-k86-surface2 rounded-lg hover:-translate-y-1 transition-transform border border-transparent hover:border-k86-border">
                    <div class="w-12 h-12 bg-k86-black rounded-md overflow-hidden shrink-0 border border-k86-border">
                        <div class="w-full h-full bg-k86-border flex items-center justify-center">🍟</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white truncate shadow-sm">Cheezious Matka Fries</h4>
                        <p class="text-k86-muted text-xs">84 orders today</p>
                    </div>
                    <div class="text-right">
                        <p class="font-price text-k86-amber font-bold">PKR 450</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-3 bg-k86-surface2 rounded-lg hover:-translate-y-1 transition-transform border border-transparent hover:border-k86-border">
                    <div class="w-12 h-12 bg-k86-black rounded-md overflow-hidden shrink-0 border border-k86-border">
                        <div class="w-full h-full bg-k86-border flex items-center justify-center">🍔</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white truncate shadow-sm">K86 Special Zinger</h4>
                        <p class="text-k86-muted text-xs">62 orders today</p>
                    </div>
                    <div class="text-right">
                        <p class="font-price text-k86-amber font-bold">PKR 650</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-3 bg-k86-surface2 rounded-lg hover:-translate-y-1 transition-transform border border-transparent hover:border-k86-border">
                    <div class="w-12 h-12 bg-k86-black rounded-md overflow-hidden shrink-0 border border-k86-border">
                        <div class="w-full h-full bg-k86-border flex items-center justify-center">🔥</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white truncate shadow-sm">Fire Wings (6pc)</h4>
                        <p class="text-k86-muted text-xs">45 orders today</p>
                    </div>
                    <div class="text-right">
                        <p class="font-price text-k86-amber font-bold">PKR 550</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-k86-surface border border-k86-border rounded-xl shadow-lg mt-6 overflow-hidden">
        <div class="p-5 border-b border-k86-border flex justify-between items-center">
            <h3 class="text-lg font-heading font-bold">Recent Orders</h3>
            <a href="orders.php" class="text-sm font-semibold text-k86-red hover:text-k86-red-dark">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted">
                    <tr>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Order ID</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Customer</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Type</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Total</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-k86-border" id="dash-recent-orders">
                    <tr><td colspan="5" class="p-4 text-center text-k86-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
    // Chart.js placeholder initialization
    const ctx = document.getElementById('revenueChart');
    if(!ctx) return;
    
    // Gradient for chart
    let gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(255, 32, 32, 0.5)'); // k86-red
    gradient.addColorStop(1, 'rgba(255, 32, 32, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue (PKR)',
                data: [45000, 52000, 48000, 61000, 84500, 95000, 82000],
                borderColor: '#FF2020',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#FFB800',
                pointBorderColor: '#161616',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#2A2A2A', drawBorder: false },
                    ticks: { color: '#AAAAAA', font: { family: 'Barlow Condensed' } }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#AAAAAA', font: { family: 'Barlow' } }
                }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
