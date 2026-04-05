<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="items">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-4 gap-4">
        <div>
            <h2 class="text-2xl lg:text-3xl font-heading font-bold text-white leading-tight">MENU MANAGER</h2>
            <p class="text-k86-muted text-sm mt-1">Add, edit, or remove menu items.</p>
        </div>
        <button onclick="adminApp.openAddItemModal()" class="bg-k86-red hover:bg-k86-red-dark text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(255,32,32,0.3)] transition-all flex items-center gap-2 transform active:scale-95 group">
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ADD NEW ITEM
        </button>
    </div>
    
    <div class="bg-k86-surface border border-k86-border rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted border-b border-k86-border">
                    <tr>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Item Info</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Category</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Price</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Status</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="tbl-items" class="divide-y divide-k86-border">
                    <tr>
                        <td colspan="5" class="p-4 text-center text-k86-muted py-8">Loading items...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
