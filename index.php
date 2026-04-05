<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen 86 | Cheezious Matka Fries & Fried Chicken</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN) with Custom Config -->
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

    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen">
    <!-- Navbar Shell -->
    <nav class="sticky top-0 z-50 bg-k86-surface border-b border-k86-border px-4 py-3">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="#home" class="text-k86-red font-display text-3xl tracking-wider">KITCHEN 86</a>
            <div class="flex items-center gap-6">
                <a href="#menu" class="hover:text-k86-red transition-colors">Menu</a>
                <a href="#orders" class="hover:text-k86-red transition-colors">My Orders</a>
                <a href="#cart" class="relative group">
                    <span class="hover:text-k86-red transition-colors">Cart</span>
                    <span class="absolute -top-2 -right-3 bg-k86-red text-white text-xs font-bold px-1.5 py-0.5 rounded-full" id="cart-badge">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Shell -->
    <main id="app-content" class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-display text-k86-red text-center mt-20">Loading Kitchen 86...</h1>
    </main>

    <!-- Scripts Shell -->
    <!-- <script src="assets/js/app.js"></script> -->
</body>
</html>
