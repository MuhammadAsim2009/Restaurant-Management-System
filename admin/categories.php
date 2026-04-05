<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="categories">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-3xl font-heading font-bold">CATEGORIES</h2>
            <p class="text-k86-muted text-sm mt-1">Manage menu categories and sort orders.</p>
        </div>
        <button class="bg-k86-red hover:bg-k86-red-dark text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Category
        </button>
    </div>
    <div class="bg-k86-surface border border-k86-border rounded-xl p-2">
        <ul id="list-categories" class="space-y-2">
             <li class="p-4 text-center text-k86-muted">Loading categories...</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
