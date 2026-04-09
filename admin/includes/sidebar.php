<?php
$current_page = $current_page ?? 'index.php'; // Fallback config

function getNavClass($page, $current) {
    if ($page === $current) {
        return "nav-link active flex items-center gap-3 px-4 py-3 rounded-xl font-bold bg-k86-red text-pure-white shadow-[0_10px_20px_-5px_rgba(255,32,32,0.4)] transition-all scale-[1.02] z-10";
    }
    return "nav-link flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-k86-muted hover:text-pure-white hover:bg-k86-red/10 hover:translate-x-1 transition-all duration-300 group";
}
?>
<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/80 z-20 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-k86-surface border-r border-k86-border min-h-screen flex flex-col fixed h-full z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
    <div class="p-6 border-b border-k86-border flex items-center justify-between">
        <?php 
        $site_name = $SETTINGS['site_name'] ?? 'KITCHEN 86';
        $parts = explode(' ', $site_name);
        if (count($parts) >= 2): 
        ?>
            <h1 class="text-k86-red font-display text-4xl tracking-wider m-0 leading-none uppercase">
                <?= $parts[0] ?> <span class="text-white"><?= $parts[1] ?></span>
            </h1>
        <?php else: ?>
            <h1 class="text-k86-red font-display text-4xl tracking-wider m-0 leading-none uppercase">
                <?= $site_name ?>
            </h1>
        <?php endif; ?>
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
        <a href="categories.php" class="<?= getNavClass('categories.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Categories
        </a>
        <a href="items.php" class="<?= getNavClass('items.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Menu Manager
        </a>
        <a href="deals.php" class="<?= getNavClass('deals.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
            Deals Manager
        </a>
        <a href="users.php" class="<?= getNavClass('users.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Users
        </a>
        <a href="messages.php" class="<?= getNavClass('messages.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            Messages
        </a>
        <a href="settings.php" class="<?= getNavClass('settings.php', $current_page) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Site Settings
        </a>
    </nav>

    <div class="p-4 border-t border-k86-border">
        <button onclick="adminApp.logout()" class="w-full bg-k86-surface2 hover:bg-k86-red text-white hover:text-pure-white py-3 rounded-lg font-bold transition-all flex items-center justify-center gap-2 relative overflow-hidden group">
            <span class="relative z-10 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </span>
        </button>
    </div>
</aside>
