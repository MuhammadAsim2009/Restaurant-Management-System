<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen 86 | Cheezious Matka Fries & Fried Chicken</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { 'k86-red': '#FF2020', 'k86-red-dark': '#CC1010', 'k86-amber': '#FFB800', 'k86-black': '#0A0A0A', 'k86-surface': '#161616', 'k86-surface2': '#222222', 'k86-border': '#2A2A2A', 'k86-muted': '#AAAAAA'}, fontFamily: { 'display': ['Bebas Neue', 'cursive'], 'heading': ['Poppins', 'sans-serif'], 'body': ['Barlow', 'sans-serif'], 'price': ['Barlow Condensed', 'sans-serif']} } } }
    </script>
    <link rel="stylesheet" href="assets/css/app.css">
    <style>.drawer-open{transform:translateX(0);}.drawer-closed{transform:translateX(100%);}</style>
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen flex flex-col">
    <nav class="sticky top-0 z-40 bg-k86-surface/95 backdrop-blur border-b border-k86-border px-4 py-3">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="#home" class="text-k86-red font-display text-4xl tracking-wider">KITCHEN 86</a>
            <div class="flex items-center gap-6 text-sm font-semibold tracking-wide">
                <a href="#menu" class="hover:text-k86-red">MENU</a>
                <a href="#orders" id="nav-orders" class="hover:text-k86-red hidden">MY ORDERS</a>
                <a href="javascript:void(0)" onclick="app.auth.openLogin()" id="nav-login" class="hover:text-k86-red">LOGIN</a>
                <a href="javascript:void(0)" onclick="app.auth.logout()" id="nav-logout" class="hover:text-k86-red hidden">LOGOUT</a>
                <button onclick="app.cart.toggleDrawer()" class="relative bg-k86-surface2 hover:bg-k86-red px-3 py-1.5 rounded">
                    CART <span class="absolute -top-2 -right-2 bg-k86-amber text-k86-black text-xs font-bold px-2 rounded-full" id="cart-badge">0</span>
                </button>
            </div>
        </div>
    </nav>
    <main id="app-content" class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full"></main>
    
    <!-- Templates -->
    <template id="tpl-home">
        <div class="text-center py-12"><h1 class="text-6xl font-display text-k86-amber mb-4">BOLD. HOT. FAST.</h1><a href="#menu" class="inline-block bg-k86-red px-8 py-4 text-white font-bold rounded">ORDER NOW</a></div>
    </template>
    <template id="tpl-menu">
        <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">OUR MENU</h2>
        <div id="category-filters" class="flex gap-4 pb-4"></div>
        <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-3 gap-6"></div>
    </template>
    <template id="tpl-checkout">
        <div class="max-w-2xl mx-auto bg-k86-surface p-6 rounded-xl border border-k86-border">
            <h2 class="text-3xl font-heading mb-6 border-b border-k86-border pb-2">CHECKOUT</h2>
            <form id="checkout-form" class="space-y-4">
                <input type="text" id="chk-name" required placeholder="Name" class="w-full bg-k86-black rounded px-3 py-2 border"><input type="tel" id="chk-phone" required placeholder="Phone" class="w-full bg-k86-black rounded px-3 py-2 border"><textarea id="chk-address" required placeholder="Address" class="w-full bg-k86-black rounded px-3 py-2 border"></textarea>
                <div class="flex justify-between items-center mt-4">
                    <p class="text-3xl font-price text-k86-amber" id="chk-total">Rs 0.00</p>
                    <button type="submit" class="bg-k86-red px-8 py-3 font-bold rounded">CONFIRM</button>
                </div>
            </form>
        </div>
    </template>
    <template id="tpl-orders"><h2 class="text-3xl font-heading mb-6 pb-2 border-b">MY ORDERS</h2><div id="orders-list"></div></template>

    <!-- Cart Drawer -->
    <div id="cart-drawer" class="fixed top-0 right-0 h-full w-80 bg-k86-surface border-l z-50 drawer-closed flex flex-col transition-transform">
        <div class="p-4 border-b flex justify-between"><h3 class="font-heading text-xl">CART</h3><button onclick="app.cart.toggleDrawer()">X</button></div>
        <div id="cart-items" class="flex-grow p-4 overflow-y-auto space-y-3"></div>
        <div class="p-4 border-t"><div class="flex justify-between font-bold mb-4"><span>Total</span><span id="cart-total" class="text-k86-amber">Rs 0.00</span></div><a href="#checkout" onclick="app.cart.toggleDrawer()" class="block w-full bg-k86-red text-center font-bold px-4 py-3 rounded" id="btn-checkout">CHECKOUT</a></div>
    </div>
    
    <!-- Auth Modal -->
    <div id="auth-modal" class="fixed inset-0 z-50 hidden bg-black/80 flex items-center justify-center p-4">
        <div class="bg-k86-surface p-8 w-full max-w-sm relative">
            <button onclick="app.auth.closeModal()" class="absolute top-4 right-4 text-k86-muted">X</button>
            <div id="form-login-view">
                <h3 class="text-2xl text-k86-amber mb-4 text-center">LOGIN</h3>
                <form id="form-login" class="space-y-4"><input type="email" id="login-email" required placeholder="Email" class="w-full bg-k86-black px-3 py-2"><input type="password" id="login-pwd" required placeholder="Password" class="w-full bg-k86-black px-3 py-2"><button type="submit" class="w-full bg-k86-red py-2">LOGIN</button><p class="text-sm text-center mt-2"><a href="javascript:void(0)" onclick="app.auth.switchView('register')" class="text-k86-amber">Register</a></p></form>
            </div>
            <div id="form-register-view" class="hidden">
                <h3 class="text-2xl text-k86-amber mb-4 text-center">REGISTER</h3>
                <form id="form-register" class="space-y-4"><input type="text" id="reg-name" required placeholder="Name" class="w-full bg-k86-black px-3 py-2"><input type="email" id="reg-email" required placeholder="Email" class="w-full bg-k86-black px-3 py-2"><input type="password" id="reg-pwd" required placeholder="Password" class="w-full bg-k86-black px-3 py-2"><button type="submit" class="w-full bg-k86-red py-2">REGISTER</button><p class="text-sm text-center mt-2"><a href="javascript:void(0)" onclick="app.auth.switchView('login')" class="text-k86-amber">Login</a></p></form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/auth.js"></script>
    <script src="assets/js/checkout.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
