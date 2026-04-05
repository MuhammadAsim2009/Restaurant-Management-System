<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Kitchen 86</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { 'k86-red': '#FF2020', 'k86-red-dark': '#CC1010', 'k86-amber': '#FFB800', 'k86-black': '#0A0A0A', 'k86-surface': '#161616', 'k86-surface2': '#222222', 'k86-border': '#2A2A2A', 'k86-muted': '#AAAAAA'}, fontFamily: { 'display': ['Bebas Neue', 'cursive'], 'heading': ['Poppins', 'sans-serif'], 'body': ['Barlow', 'sans-serif'], 'price': ['Barlow Condensed', 'sans-serif']} } } }
    </script>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen flex">
    <aside class="w-64 bg-k86-surface border-r border-k86-border min-h-screen p-4 flex flex-col fixed h-full z-10">
        <h1 class="text-k86-red font-display text-4xl mb-8 tracking-wider text-center border-b border-k86-border pb-4">K86 ADMIN</h1>
        <nav class="flex flex-col gap-2">
            <a href="#dashboard" class="px-4 py-2 hover:bg-k86-surface2 rounded text-k86-amber font-semibold">Dashboard</a>
            <a href="#orders" class="px-4 py-2 hover:bg-k86-surface2 rounded">Orders</a>
            <a href="#items" class="px-4 py-2 hover:bg-k86-surface2 rounded">Menu Manager</a>
            <a href="#deals" class="px-4 py-2 hover:bg-k86-surface2 rounded">Deals</a>
            <a href="#categories" class="px-4 py-2 hover:bg-k86-surface2 rounded">Categories</a>
            <a href="#users" class="px-4 py-2 hover:bg-k86-surface2 rounded">Users</a>
        </nav>
        <div class="mt-auto pt-4 border-t border-k86-border">
            <button onclick="adminApp.logout()" class="w-full bg-k86-surface2 hover:bg-k86-red text-white py-2 rounded font-bold">Logout</button>
        </div>
    </aside>

    <main class="flex-grow p-8 ml-64 min-h-screen">
        <div id="admin-content" class="bg-k86-surface p-6 rounded-xl border border-k86-border w-full min-h-full">
            <h2 class="text-2xl text-k86-muted text-center mt-20 animate-pulse">Loading Admin Interface...</h2>
        </div>
    </main>

    <!-- Templates -->
    <template id="tpl-dashboard">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">DASHBOARD</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="dash-stats"></div>
    </template>

    <template id="tpl-orders">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">ORDER MANAGEMENT</h2>
        <div class="overflow-x-auto"><table class="w-full text-left bg-k86-surface2 rounded text-sm">
            <thead class="bg-k86-black border-b border-k86-border"><tr><th class="p-3">ID</th><th class="p-3">Customer</th><th class="p-3">Type</th><th class="p-3">Total</th><th class="p-3">Status</th></tr></thead>
            <tbody id="tbl-orders"></tbody>
        </table></div>
    </template>

    <template id="tpl-items">
        <div class="flex justify-between items-end border-b border-k86-border pb-2 mb-6">
            <h2 class="text-3xl font-heading">MENU ITEMS</h2>
            <button onclick="adminApp.openModal('Add Item')" class="bg-k86-red px-4 py-2 rounded text-sm font-bold">+ ADD ITEM</button>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-left bg-k86-surface2 rounded text-sm">
            <thead class="bg-k86-black border-b border-k86-border"><tr><th class="p-3">Name</th><th class="p-3">Category</th><th class="p-3">Price</th><th class="p-3">Available</th></tr></thead>
            <tbody id="tbl-items"></tbody>
        </table></div>
    </template>

    <template id="tpl-categories">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">CATEGORIES</h2>
        <ul id="list-categories" class="space-y-2"></ul>
    </template>

    <template id="tpl-deals">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">DEALS</h2>
        <p class="text-k86-muted">Deals management ready.</p>
    </template>

    <template id="tpl-users">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">REGISTERED USERS</h2>
        <div class="overflow-x-auto"><table class="w-full text-left bg-k86-surface2 rounded text-sm">
            <thead class="bg-k86-black border-b border-k86-border"><tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Phone</th><th class="p-3">Role</th></tr></thead>
            <tbody id="tbl-users"></tbody>
        </table></div>
    </template>

    <div id="sys-modal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-k86-surface p-6 rounded w-full max-w-sm relative border border-k86-border shadow-2xl">
            <button onclick="adminApp.closeModal()" class="absolute top-4 right-4 text-k86-muted hover:text-white">X</button>
            <div id="modal-content" class="text-center"></div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
