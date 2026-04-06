<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="categories">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-3xl font-heading font-bold">CATEGORIES</h2>
            <p class="text-k86-muted text-sm mt-1">Manage menu categories and sort orders.</p>
        </div>
        <button onclick="adminApp.openAddCategoryModal()" class="bg-k86-red hover:bg-k86-red-dark text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Category
        </button>
    </div>

    <!-- Info Banner -->
    <div class="bg-k86-amber/5 border border-k86-amber/20 rounded-xl p-3 flex items-start gap-3">
        <svg class="w-5 h-5 text-k86-amber shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856C18.07 19 19 17.986 19 16.75V7.25C19 6.014 18.07 5 16.918 5H7.082C5.93 5 5 6.014 5 7.25v9.5C5 17.986 5.93 19 7.082 19H5.062z"></path></svg>
        <p class="text-xs text-k86-amber/80">Menu items belong to categories. <span class="font-bold text-k86-amber">Deleting a category will also delete all items inside it.</span> Use with caution.</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="list-categories">
        <!-- Skeleton Loaders -->
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
