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

<!-- Icon Picker Modal -->
<div id="icon-picker-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="adminApp.closeIconPicker()"></div>
    <!-- Modal Content -->
    <div class="bg-k86-surface p-6 rounded-2xl w-full max-w-2xl relative border border-k86-border shadow-2xl transform scale-95 opacity-0 transition-all duration-300 z-10 max-h-[90vh] flex flex-col" id="icon-picker-body">
        <button onclick="adminApp.closeIconPicker()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-k86-surface2 text-k86-muted hover:text-white hover:bg-k86-border transition-colors z-20">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="mb-4 pr-10">
            <h3 class="text-xl font-heading font-bold text-white uppercase">Choose Category Icon</h3>
            <p class="text-xs text-k86-muted mt-1" id="cat-icon-status">Search and select an icon for your menu category.</p>
        </div>
        
        <div class="relative mb-4 shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-k86-muted text-sm pointer-events-none"></i>
            <input type="text" id="icon-picker-search" oninput="adminApp.filterIconPicker(this.value)" autocomplete="off" placeholder="Search icons (e.g. burger, fire, star)..." class="w-full bg-k86-black border border-k86-border rounded-xl pl-10 pr-10 py-3 text-white focus:border-k86-red transition-all outline-none placeholder-k86-muted">
            <button id="icon-picker-clear" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-k86-muted hover:text-white" onclick="document.getElementById('icon-picker-search').value=''; adminApp.filterIconPicker('');"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 min-h-[300px]" id="icon-picker-scroll">
            <div id="icon-picker-grid" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                <!-- Icons will be injected here -->
            </div>
            <div id="icon-picker-empty" class="hidden flex flex-col items-center justify-center py-12 text-k86-muted">
                <i class="fa-solid fa-ghost text-4xl mb-3 opacity-50"></i>
                <p class="font-bold uppercase tracking-wider text-sm">No icons found</p>
                <p class="text-xs mt-1">Try a different search term</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
