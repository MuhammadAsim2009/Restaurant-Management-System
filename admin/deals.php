<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="deals">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-4 gap-4">
        <div>
            <h2 class="text-2xl lg:text-3xl font-heading font-bold text-white leading-tight uppercase tracking-wider">DEALS & COMBOS</h2>
            <p class="text-k86-muted text-sm mt-1 uppercase tracking-tighter">Create and manage your latest promotional offers.</p>
        </div>
        <button onclick="adminApp.openAddDealModal()" class="bg-k86-red hover:bg-k86-red-dark text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-[0_0_15px_rgba(255,32,32,0.3)] transition-all flex items-center gap-2 transform active:scale-95 group">
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            NEW DEAL
        </button>
    </div>
    
    <div class="bg-k86-surface border border-k86-border rounded-xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted border-b border-k86-border uppercase text-[10px] font-bold tracking-[0.2em]">
                    <tr>
                        <th class="p-6">Deal Details</th>
                        <th class="p-6">Price Information</th>
                        <th class="p-6">Linked Item / expiry</th>
                        <th class="p-6 text-center">Status</th>
                        <th class="p-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="tbl-deals" class="divide-y divide-k86-border">
                    <tr>
                        <td colspan="4" class="p-10 text-center text-k86-muted py-8">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 border-4 border-k86-red border-t-transparent rounded-full animate-spin"></div>
                                <span>Powering up deals...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
