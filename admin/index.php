<?php 
require_once __DIR__ . '/includes/header.php'; 
// Note: header.php already includes sidebar.php and opens the <main> tag and #admin-content div
?>

<div class="animate-fade-in space-y-8 pb-12" data-page="dashboard">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display text-white tracking-widest uppercase">Dashboard Overview</h1>
            <p class="text-k86-muted font-body mt-1">Real-time business performance metrics for <span class="text-k86-red font-semibold"><?= htmlspecialchars($SETTINGS['site_name'] ?? 'Restaurant') ?></span>.</p>
        </div>
        <div class="flex items-center gap-3 bg-k86-surface2 p-1.5 rounded-xl border border-k86-border shadow-inner">
            <button class="px-4 py-2 bg-k86-red text-white rounded-lg text-sm font-bold shadow-lg shadow-k86-red/20 transition-all">Today</button>
            <p class="px-4 py-2 text-k86-muted text-sm font-semibold" id="current-date"><?php echo date('M d, Y'); ?></p>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="dash-stats-grid">
        <!-- Today's Orders Skeleton -->
        <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse">
            <div class="h-4 bg-k86-surface2 rounded w-24 mb-4"></div>
            <div class="h-10 bg-k86-surface2 rounded w-16 mb-4"></div>
            <div class="h-1 bg-k86-surface2 rounded w-full"></div>
        </div>
        <!-- Revenue Skeleton -->
        <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse">
            <div class="h-4 bg-k86-surface2 rounded w-24 mb-4"></div>
            <div class="h-10 bg-k86-surface2 rounded w-32 mb-4"></div>
            <div class="h-1 bg-k86-surface2 rounded w-full"></div>
        </div>
        <!-- Pending Skeleton -->
        <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse">
            <div class="h-4 bg-k86-surface2 rounded w-24 mb-4"></div>
            <div class="h-10 bg-k86-surface2 rounded w-16 mb-4"></div>
            <div class="h-1 bg-k86-surface2 rounded w-full"></div>
        </div>
        <!-- Customers Skeleton -->
        <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse">
            <div class="h-4 bg-k86-surface2 rounded w-24 mb-4"></div>
            <div class="h-10 bg-k86-surface2 rounded w-20 mb-4"></div>
            <div class="h-1 bg-k86-surface2 rounded w-full"></div>
        </div>
    </div>

    <!-- Revenue Chart and Top Selling Items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Overview Chart -->
        <div class="lg:col-span-2 bg-k86-surface border border-k86-border rounded-3xl p-6 lg:p-8 relative">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-heading font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-6 bg-k86-red rounded-full"></span>
                        Revenue Overview (Last 7 Days)
                    </h3>
                </div>
                <div class="text-right">
                    <p class="text-k86-muted text-xs uppercase font-bold tracking-tighter">Current Status</p>
                    <p class="text-xl font-price font-bold text-k86-amber" id="chart-total-rev">PKR --</p>
                </div>
            </div>
            <div class="h-80 w-full relative">
                <div id="chart-loader" class="absolute inset-0 flex items-center justify-center bg-k86-surface/50 z-10">
                    <div class="w-8 h-8 border-4 border-k86-red border-t-transparent rounded-full animate-spin"></div>
                </div>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Selling Items -->
        <div class="bg-k86-surface border border-k86-border rounded-3xl p-6 lg:p-8">
            <h3 class="text-xl font-heading font-bold text-white mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-k86-amber rounded-full"></span>
                Top Selling Items
            </h3>
            <div class="space-y-4 max-h-[380px] overflow-y-auto pr-2 custom-scrollbar" id="dash-top-items">
                <div class="text-center py-10 text-k86-muted text-sm">Loading top items...</div>
            </div>
            <a href="items.php" class="block w-full mt-6 py-3 bg-k86-surface2 text-k86-muted hover:text-white rounded-xl border border-k86-border hover:border-k86-red transition-all text-sm font-bold uppercase tracking-widest text-center">Manage Menu</a>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-k86-surface border border-k86-border rounded-3xl overflow-hidden shadow-2xl">
        <div class="p-6 lg:p-8 border-b border-k86-border flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-2xl font-heading font-bold text-white">Recent Orders</h3>
                <p class="text-k86-muted text-sm">Processing real-time customer requests</p>
            </div>
            <a href="orders.php" class="px-6 py-2 bg-k86-surface2 hover:bg-k86-red hover:text-white text-k86-muted rounded-full border border-k86-border transition-all text-xs font-bold uppercase tracking-widest">Full History</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted uppercase text-[10px] font-bold tracking-[0.2em]">
                    <tr>
                        <th class="p-6 border-b border-k86-border">Order ID</th>
                        <th class="p-6 border-b border-k86-border">Customer</th>
                        <th class="p-6 border-b border-k86-border">Type</th>
                        <th class="p-6 border-b border-k86-border">Total</th>
                        <th class="p-6 border-b border-k86-border">Status</th>
                        <th class="p-6 border-b border-k86-border text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-k86-border" id="dash-recent-orders">
                    <tr><td colspan="6" class="p-10 text-center text-k86-muted">Fetching latest orders...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #2A2A2A; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #FF2020; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
