<?php
$current_page = $current_page ?? 'index.php'; // Fallback config

function getNavClass($page, $current) {
    if ($page === $current) {
        return "nav-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold bg-k86-red text-white shadow-[0_0_15px_rgba(255,32,32,0.4)] transition-all";
    }
    return "nav-link flex items-center gap-3 px-4 py-3 rounded-lg font-semibold text-k86-muted hover:bg-k86-surface2 transition-all";
}
?>
<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/80 z-20 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-k86-surface border-r border-k86-border min-h-screen flex flex-col fixed h-full z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
    <div class="p-6 border-b border-k86-border flex items-center justify-between">
        <h1 class="text-k86-red font-display text-4xl tracking-wider m-0 leading-none">KITCHEN <span class="text-white">86</span></h1>
        <button class="md:hidden text-k86-muted hover:text-white" onclick="toggleSidebar()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    
    <nav class="flex-1 flex flex-col gap-2 p-4 overflow-y-auto" id="sidebar-nav">
        <a href="index.php" class="<?= getNavClass('index.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>
        <a href="orders.php" class="<?= getNavClass('orders.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Orders
        </a>
        <a href="items.php" class="<?= getNavClass('items.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Menu Manager
        </a>
        <a href="deals.php" class="<?= getNavClass('deals.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
            Deals Manager
        </a>
        <a href="categories.php" class="<?= getNavClass('categories.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Categories
        </a>
        <a href="users.php" class="<?= getNavClass('users.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Users
        </a>
    </nav>

    <div class="p-4 border-t border-k86-border">
        <button onclick="adminApp.logout()" class="w-full bg-k86-surface2 hover:bg-k86-red text-white py-3 rounded-lg font-bold transition-colors flex items-center justify-center gap-2 relative overflow-hidden group">
            <span class="relative z-10 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </span>
        </button>
    </div>
</aside>
