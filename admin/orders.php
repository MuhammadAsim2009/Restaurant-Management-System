<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="orders">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-4 gap-4">
        <div>
            <h2 class="text-2xl lg:text-3xl font-heading font-bold text-white leading-tight">ORDER MANAGEMENT</h2>
            <p class="text-k86-muted text-sm mt-1">Manage and update customer orders.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0">
            <button class="bg-k86-surface2 text-white px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap border border-k86-red text-k86-red">All Orders</button>
            <button class="bg-k86-surface2 text-k86-muted hover:text-white hover:bg-k86-surface px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap border border-k86-border">Pending</button>
            <button class="bg-k86-surface2 text-k86-muted hover:text-white hover:bg-k86-surface px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap border border-k86-border">Processing</button>
        </div>
    </div>
    
    <div class="bg-k86-surface border border-k86-border rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted border-b border-k86-border">
                    <tr>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">ID</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Customer</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Type</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Total</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Status</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="tbl-orders" class="divide-y divide-k86-border">
                    <tr>
                        <td colspan="6" class="p-4 text-center text-k86-muted">Loading orders...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
