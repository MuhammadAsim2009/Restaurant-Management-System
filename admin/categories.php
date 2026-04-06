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
    <div class="bg-k86-amber/5 border border-k86-amber/20 rounded-xl p-3 flex items-start gap-3">
        <svg class="w-5 h-5 text-k86-amber shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856C18.07 19 19 17.986 19 16.75V7.25C19 6.014 18.07 5 16.918 5H7.082C5.93 5 5 6.014 5 7.25v9.5C5 17.986 5.93 19 7.082 19H5.062z"></path></svg>
        <p class="text-xs text-k86-amber/80">Menu items belong to categories. <span class="font-bold text-k86-amber">Deleting a category will also delete all items inside it.</span></p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="list-categories">
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
        <div class="bg-k86-surface border border-k86-border rounded-xl p-5 skeleton-loader opacity-30 h-24"></div>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!--           ICON PICKER MODAL                -->
<!-- ═══════════════════════════════════════════ -->
<div id="icon-picker-overlay" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" onclick="adminApp.onIconPickerOverlayClick(event)">
    <div id="icon-picker-panel" class="bg-[#161616] border border-[#2A2A2A] rounded-2xl w-full max-w-3xl flex flex-col shadow-2xl" style="max-height:85vh; animation: ipFadeIn 0.2s ease-out both;">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#2A2A2A] shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-[#FF2020]/10 rounded-full flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-icons text-[#FF2020]"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white font-heading">Choose Category Icon</h3>
                    <p class="text-[10px] text-[#AAAAAA]" id="icon-picker-status">Loading...</p>
                </div>
            </div>
            <button onclick="adminApp.closeIconPicker()" class="w-8 h-8 flex items-center justify-center rounded-lg text-[#AAAAAA] hover:text-white hover:bg-[#2A2A2A] transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Search -->
        <div class="px-4 py-3 border-b border-[#2A2A2A] shrink-0">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#AAAAAA] text-sm pointer-events-none"></i>
                <input type="text" id="icon-picker-search"
                    oninput="adminApp.debouncedIconSearch(this.value)"
                    placeholder="Search icons... e.g. burger, fire, user, star"
                    autocomplete="off"
                    class="w-full bg-[#0A0A0A] border border-[#2A2A2A] rounded-xl pl-10 pr-9 py-2.5 text-white text-sm focus:border-[#FF2020] transition-all outline-none">
                <button id="icon-search-clear" onclick="adminApp.clearIconSearch()"
                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-[#AAAAAA] hover:text-white transition-colors">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Grid -->
        <div id="icon-picker-grid" class="flex-1 overflow-y-auto p-4" style="min-height:300px;">
            <div class="flex flex-col items-center justify-center h-48 text-[#AAAAAA]">
                <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-[#FF2020]"></i>
                <p class="text-sm">Loading Font Awesome icons...</p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes ipFadeIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.ip-icon-btn {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:10px 4px 7px; border-radius:10px; border:1px solid #2A2A2A;
    cursor:pointer; transition:all .15s; gap:5px; background:transparent; min-height:70px; width:100%;
}
.ip-icon-btn:hover { border-color:#FF2020; background:rgba(255,32,32,.06); transform:translateY(-1px); box-shadow:0 0 10px rgba(255,32,32,.15); }
.ip-icon-btn.ip-selected { border-color:#FF2020; background:rgba(255,32,32,.12); }
.ip-icon-btn i { font-size:1.35rem; color:#AAAAAA; transition:color .15s; }
.ip-icon-btn:hover i, .ip-icon-btn.ip-selected i { color:#FF2020; }
.ip-icon-btn span { font-size:8px; color:#555; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:100%; line-height:1.2; font-family:monospace; }
.ip-icon-btn.ip-selected span { color:#FF8080; }
#icon-picker-grid::-webkit-scrollbar { width:5px; }
#icon-picker-grid::-webkit-scrollbar-track { background:#0A0A0A; }
#icon-picker-grid::-webkit-scrollbar-thumb { background:#2A2A2A; border-radius:3px; }
#icon-picker-grid::-webkit-scrollbar-thumb:hover { background:#FF2020; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
