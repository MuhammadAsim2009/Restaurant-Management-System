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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'k86-red':      '#FF2020',
              'k86-red-dark': '#CC1010',
              'k86-amber':    '#FFB800',
              'k86-black':    '#0A0A0A',
              'k86-surface':  '#161616',
              'k86-surface2': '#222222',
              'k86-border':   '#2A2A2A',
              'k86-muted':    '#AAAAAA',
            },
            fontFamily: {
              'display': ['Bebas Neue', 'cursive'],
              'heading': ['Poppins', 'sans-serif'],
              'body':    ['Barlow', 'sans-serif'],
              'price':   ['Barlow Condensed', 'sans-serif'],
            }
          }
        }
      }
    </script>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen flex">
    
    <!-- Admin Sidebar -->
    <aside class="w-64 bg-k86-surface border-r border-k86-border flex-shrink-0 min-h-screen p-4 flex flex-col">
        <h1 class="text-k86-red font-display text-4xl mb-8 tracking-wider text-center border-b border-k86-border pb-4">K86 ADMIN</h1>
        <nav class="flex flex-col gap-2">
            <a href="#dashboard" class="px-4 py-2 hover:bg-k86-surface2 rounded text-k86-amber font-semibold transition-colors">Dashboard</a>
            <a href="#orders" class="px-4 py-2 hover:bg-k86-surface2 rounded transition-colors">Orders</a>
            <a href="#menu-manager" class="px-4 py-2 hover:bg-k86-surface2 rounded transition-colors">Menu Manager</a>
            <a href="#deals" class="px-4 py-2 hover:bg-k86-surface2 rounded transition-colors">Deals Manager</a>
            <a href="#categories" class="px-4 py-2 hover:bg-k86-surface2 rounded transition-colors">Categories</a>
            <a href="#users" class="px-4 py-2 hover:bg-k86-surface2 rounded transition-colors">Users</a>
        </nav>
        <div class="mt-auto pt-4 border-t border-k86-border">
            <button class="w-full bg-k86-surface2 hover:bg-k86-red transition-colors px-4 py-2 rounded font-bold">Logout</button>
        </div>
    </aside>

    <!-- Admin Main Content -->
    <main class="flex-grow p-8">
        <div id="admin-content" class="bg-k86-surface p-6 rounded-xl border border-k86-border min-h-full">
            <h2 class="text-2xl font-heading text-white">Admin Dashboard</h2>
            <p class="text-k86-muted mt-2">Loading content...</p>
        </div>
    </main>

    <!-- <script src="../assets/js/admin.js"></script> -->
</body>
</html>
