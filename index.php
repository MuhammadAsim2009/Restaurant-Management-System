<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen 86 | Cheezious Matka Fries & Fried Chicken</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { 
          theme: { 
              extend: { 
                  colors: { 
                      'k86-red': '#FF2020', 
                      'k86-red-dark': '#CC1010', 
                      'k86-amber': '#FFB800', 
                      'k86-black': '#0A0A0A', 
                      'k86-surface': '#161616', 
                      'k86-surface2': '#222222', 
                      'k86-border': '#2A2A2A', 
                      'k86-muted': '#AAAAAA'
                  }, 
                  fontFamily: { 
                      'display': ['Bebas Neue', 'cursive'], 
                      'heading': ['Poppins', 'sans-serif'], 
                      'body': ['Barlow', 'sans-serif'], 
                      'price': ['Barlow Condensed', 'sans-serif']
                  },
                  animation: {
                      'pulse-fast': 'pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                      'bounce-slow': 'bounce 3s infinite',
                  }
              } 
          } 
      }
    </script>
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .drawer-open { transform: translateX(0); }
        .drawer-closed { transform: translateX(100%); }
        
        /* Menu Cards Hover Enhancements for JS injected content */
        #menu-grid > div {
            transition: all 0.3s ease-out;
            border-color: #2A2A2A;
            background: linear-gradient(145deg, #161616, #111111);
            position: relative;
            overflow: hidden;
        }
        #menu-grid > div:hover {
            transform: translateY(-0.75rem);
            border-color: #FF2020;
            box-shadow: 0 10px 25px -5px rgba(255, 32, 32, 0.4);
            z-index: 10;
        }
        #menu-grid > div img {
            transition: transform 0.5s ease-out;
        }
        #menu-grid > div:hover img {
            transform: scale(1.1);
        }
        #menu-grid > div button {
            transition: all 0.3s ease-out;
        }
        #menu-grid > div button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 32, 32, 0.6);
            background-color: #CC1010;
        }
        #menu-grid > div button:active {
            transform: scale(0.95);
        }

        /* Scroll Reveal Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Cart Badge Popup */
        @keyframes popIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.4); }
            100% { transform: scale(1); }
        }
        .animate-pop {
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        /* Hero Background Overlay */
        .hero-bg {
            background: radial-gradient(circle at center, rgba(255, 32, 32, 0.15) 0%, rgba(10, 10, 10, 1) 70%);
        }
    </style>
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav id="main-nav" class="sticky top-0 z-40 bg-k86-surface/80 backdrop-blur-md border-b border-k86-border px-4 py-3 transition-colors duration-300">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="#home" class="text-k86-red font-display text-4xl tracking-wider hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(255,32,32,0.5)]">KITCHEN 86</a>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-6 text-sm font-semibold tracking-wide">
                <a href="javascript:void(0)" onclick="scrollToSection('menu-section')" class="relative group hover:text-k86-red transition-colors duration-300">
                    MENU
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-k86-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="javascript:void(0)" onclick="scrollToSection('about-section')" class="relative group hover:text-k86-red transition-colors duration-300">
                    ABOUT
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-k86-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="javascript:void(0)" onclick="scrollToSection('contact-section')" class="relative group hover:text-k86-red transition-colors duration-300">
                    CONTACT
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-k86-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="#orders" id="nav-orders" class="relative group hover:text-k86-red transition-colors duration-300 hidden">
                    MY ORDERS
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-k86-red transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="javascript:void(0)" onclick="app.auth.openLogin()" id="nav-login" class="hover:text-k86-red transition-colors duration-300">LOGIN</a>
                <a href="javascript:void(0)" onclick="app.auth.logout()" id="nav-logout" class="hover:text-k86-red transition-colors duration-300 hidden">LOGOUT</a>
                <button onclick="app.cart.toggleDrawer()" class="relative bg-k86-surface2 hover:bg-k86-red px-4 py-2 rounded transition-all duration-300 hover:shadow-[0_0_15px_rgba(255,32,32,0.5)] active:scale-95">
                    CART 
                    <span class="absolute -top-2 -right-2 bg-k86-amber text-k86-black text-xs font-bold px-2 rounded-full shadow-lg" id="cart-badge">0</span>
                </button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="md:hidden text-k86-red p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-2 flex flex-col gap-4 text-center text-lg font-heading">
            <a href="javascript:void(0)" class="hover:text-k86-red transition-colors" onclick="scrollToSection('menu-section'); this.parentElement.classList.add('hidden')">MENU</a>
            <a href="javascript:void(0)" class="hover:text-k86-red transition-colors" onclick="scrollToSection('about-section'); this.parentElement.classList.add('hidden')">ABOUT</a>
            <a href="javascript:void(0)" class="hover:text-k86-red transition-colors" onclick="scrollToSection('contact-section'); this.parentElement.classList.add('hidden')">CONTACT</a>
            <a href="#orders" id="nav-orders-mob" class="hover:text-k86-red hidden" onclick="this.parentElement.classList.add('hidden')">MY ORDERS</a>
            <a href="javascript:void(0)" onclick="app.auth.openLogin(); this.parentElement.classList.add('hidden')" id="nav-login-mob" class="hover:text-k86-red">LOGIN</a>
            <a href="javascript:void(0)" onclick="app.auth.logout(); this.parentElement.classList.add('hidden')" id="nav-logout-mob" class="hover:text-k86-red hidden">LOGOUT</a>
            <button onclick="app.cart.toggleDrawer(); this.parentElement.classList.add('hidden')" class="bg-k86-red px-4 py-2 rounded mx-4 text-white font-bold">CART (<span id="cart-badge-mob">0</span>)</button>
        </div>
    </nav>
    
    <main id="app-content" class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full"></main>
    
    <!-- Templates -->
    <template id="tpl-home">
        <!-- Hero Section -->
        <div class="relative py-24 md:py-32 flex flex-col items-center justify-center text-center hero-bg rounded-3xl overflow-hidden shadow-2xl border border-k86-surface2 mb-16 reveal">
            <!-- decorative background shapes -->
            <div class="absolute top-10 left-10 w-32 h-32 bg-k86-red rounded-full opacity-10 blur-3xl animate-pulse-fast"></div>
            <div class="absolute bottom-10 right-10 w-48 h-48 bg-k86-amber rounded-full opacity-10 blur-3xl animate-pulse-fast" style="animation-delay: 1s;"></div>
            
            <div class="relative z-10">
                <span class="inline-block bg-k86-red/20 text-k86-red border border-k86-red px-3 py-1 rounded-full text-sm font-bold tracking-widest mb-6 animate-bounce-slow shadow-[0_0_10px_rgba(255,32,32,0.5)]"><i class="fa-solid fa-fire text-k86-red mr-1"></i> HOT & SPICY</span>
                <h1 class="text-7xl md:text-8xl font-display text-transparent bg-clip-text bg-gradient-to-r from-k86-amber via-yellow-300 to-k86-red mb-6 drop-shadow-lg transform transition-transform duration-700 hover:scale-105">BOLD. HOT. FAST.</h1>
                <p class="text-k86-muted text-lg md:text-xl font-heading max-w-2xl mx-auto mb-10">Experience the ultimate Cheezious Matka Fries & legendary Fried Chicken in Larkana.</p>
                <a href="javascript:void(0)" onclick="scrollToMenu()" class="inline-block bg-k86-red px-10 py-4 text-white font-bold rounded-lg text-lg transition-all duration-300 hover:scale-110 hover:shadow-[0_0_20px_rgba(255,32,32,0.6)] active:scale-95 group">
                    ORDER NOW
                    <span class="inline-block ml-2 group-hover:translate-x-2 transition-transform duration-300">→</span>
                </a>
            </div>
        </div>

        <!-- Deals Section -->
        <div class="py-12 reveal">
            <h2 class="text-4xl font-display mb-8 text-k86-amber flex items-center gap-3">
                <svg class="w-8 h-8 text-k86-red" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                HOT DEALS
            </h2>
            <div id="featured-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Skeletons -->
                <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse h-48"></div>
                <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl animate-pulse h-48"></div>
            </div>
        </div>

        <!-- About Us Section -->
        <div id="about-section" class="py-16 reveal relative overflow-hidden rounded-3xl bg-k86-surface/50 border border-k86-surface2 my-12 shadow-[0_0_40px_rgba(0,0,0,0.5)]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center p-8 md:p-12">
                <div class="space-y-6">
                    <span class="text-k86-red font-bold tracking-widest text-sm uppercase flex items-center gap-2">
                        <span class="w-8 h-0.5 bg-k86-red inline-block"></span> Our Story
                    </span>
                    <h2 class="text-5xl md:text-6xl font-display text-white drop-shadow-md">Born in the Heart of Larkana</h2>
                    <p class="text-k86-muted text-lg leading-relaxed font-body">Kitchen 86 started with a simple belief: fast food shouldn't be boring. We brought the authentic, mouth-watering experience of Cheezious Matka Fries and perfectly spiced Fried Chicken straight to your neighborhood. Every bite is a testament to our passion for bold flavors, fresh ingredients, and serious heat.</p>
                    <p class="text-k86-muted text-lg leading-relaxed font-body">Whether you're grabbing a quick bite or feasting with friends, you're not just eating—you're experiencing the legendary K86 energy.</p>
                </div>
                <div class="relative group">
                    <div class="absolute -inset-2 bg-gradient-to-r from-k86-red to-k86-amber rounded-2xl opacity-20 group-hover:opacity-40 blur-xl transition duration-500"></div>
                    <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80" alt="Kitchen 86 Experience" class="relative rounded-2xl w-full h-[400px] object-cover shadow-2xl transform transition duration-500 group-hover:scale-[1.02] border border-k86-border">
                </div>
            </div>
        </div>

        <!-- Menu Section Inline -->
        <div id="menu-section" class="py-12 reveal">
            <h2 class="text-5xl font-display mb-8 pb-3 border-b-2 border-k86-red inline-block text-transparent bg-clip-text bg-gradient-to-r from-k86-amber to-k86-red">OUR MENU</h2>
            <div id="category-filters" class="flex flex-wrap gap-4 pb-8"></div>
            <!-- The grid will be populated by app.js, and CSS transitions added to head handle the hover animations -->
            <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8"></div>
        </div>
        
        <!-- Contact Us Section -->
        <div id="contact-section" class="py-16 reveal my-12">
            <div class="text-center mb-12 flex flex-col items-center">
                <span class="text-k86-red font-bold tracking-widest text-sm uppercase flex items-center gap-2 mb-2">Reach Out</span>
                <h2 class="text-5xl font-display pb-3 border-b-2 border-k86-red inline-block text-transparent bg-clip-text bg-gradient-to-r from-k86-amber to-k86-red">GET IN TOUCH</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-gradient-to-br from-k86-surface to-[rgba(10,10,10,0.8)] border border-k86-surface2 rounded-3xl p-6 lg:p-10 shadow-[0_0_40px_rgba(0,0,0,0.6)] relative overflow-hidden">
                <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-k86-amber rounded-full opacity-5 blur-[100px] pointer-events-none"></div>
                <!-- Contact Form -->
                <div class="space-y-6 relative z-10 mr-0 md:mr-4">
                    <h3 class="text-3xl font-heading font-bold text-white mb-2">Drop us a line</h3>
                    <p class="text-k86-muted mb-8">Have a question, feedback, or a large event order? We'd love to hear from you.</p>
                    <form class="space-y-5" onsubmit="event.preventDefault(); if(window.Toast) window.Toast.show('Message sent successfully!', 'success'); this.reset();">
                        <div>
                            <input type="text" required placeholder="Your Name" class="w-full bg-k86-black rounded-xl px-5 py-4 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all shadow-inner">
                        </div>
                        <div>
                            <input type="email" required placeholder="Email Address" class="w-full bg-k86-black rounded-xl px-5 py-4 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all shadow-inner">
                        </div>
                        <div>
                            <textarea required placeholder="Your Message" rows="4" class="w-full bg-k86-black rounded-xl px-5 py-4 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all shadow-inner resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-k86-red to-k86-red-dark hover:from-red-500 hover:to-red-700 py-4 rounded-xl font-bold tracking-widest transition-all active:scale-95 shadow-[0_5px_15px_rgba(255,32,32,0.3)]">SEND MESSAGE</button>
                    </form>
                </div>
                <!-- Google Maps Embed -->
                <div class="flex flex-col h-full min-h-[350px] relative rounded-2xl overflow-hidden border border-k86-surface2 group mt-8 md:mt-0 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-b from-k86-red/20 to-k86-black/60 group-hover:opacity-0 transition-opacity duration-700 z-10 pointer-events-none mix-blend-color"></div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113645.92209142858!2d68.14081041935661!3d27.545802213768292!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x393504fb4d2eb7b7%3A0xe5a36aa27ed7ebf!2sLarkana%2C%20Sindh%2C%20Pakistan!5e0!3m2!1sen!2s!4v1711200000000!5m2!1sen!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 grayscale contrast-125 opacity-70 group-hover:grayscale-[0.5] group-hover:opacity-100 transition-all duration-700"></iframe>
                    <div class="absolute bottom-4 left-4 right-4 bg-k86-surface/90 backdrop-blur-md border border-k86-border p-4 rounded-xl z-20 shadow-xl transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                        <p class="font-heading font-bold text-white mb-1"><svg class="w-4 h-4 inline-block mr-1 text-k86-red mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Kitchen 86 HQ</p>
                        <p class="text-xs text-k86-muted ml-5">VIP Road, Larkana, Sindh, PK</p>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-checkout">
        <div class="max-w-2xl mx-auto bg-k86-surface p-8 rounded-2xl border border-k86-border shadow-[0_0_30px_rgba(0,0,0,0.5)] reveal relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-k86-red via-k86-amber to-k86-red"></div>
            <h2 class="text-4xl font-display mb-8 pb-4 border-b border-k86-border text-center">SECURE CHECKOUT</h2>
            <form id="checkout-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-k86-muted mb-2">Full Name</label>
                    <input type="text" id="chk-name" required placeholder="John Doe" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-surface2 focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all duration-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-k86-muted mb-2">Phone Number</label>
                    <input type="tel" id="chk-phone" required placeholder="03xx-xxxxxxx" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-surface2 focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all duration-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-k86-muted mb-2">Delivery Address</label>
                    <textarea id="chk-address" required placeholder="Street, Sector, City..." rows="3" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-surface2 focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all duration-300"></textarea>
                </div>

                <!-- Order Type & Payment Method -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-k86-muted uppercase tracking-widest">Order Type</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="order_type" value="delivery" checked class="hidden peer">
                                <div class="bg-k86-black border border-k86-border p-4 rounded-xl text-center peer-checked:border-k86-red peer-checked:bg-k86-red/10 transition-all group-hover:border-k86-red/50">
                                    <i class="fa-solid fa-truck-fast mb-2 block text-xl"></i>
                                    <span class="text-xs font-bold uppercase">Delivery</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="order_type" value="pickup" class="hidden peer">
                                <div class="bg-k86-black border border-k86-border p-4 rounded-xl text-center peer-checked:border-k86-red peer-checked:bg-k86-red/10 transition-all group-hover:border-k86-red/50">
                                    <i class="fa-solid fa-store mb-2 block text-xl"></i>
                                    <span class="text-xs font-bold uppercase">Pickup</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-k86-muted uppercase tracking-widest">Payment Method</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="payment_method" value="cash" checked class="hidden peer">
                                <div class="bg-k86-black border border-k86-border p-4 rounded-xl text-center peer-checked:border-k86-red peer-checked:bg-k86-red/10 transition-all group-hover:border-k86-red/50">
                                    <i class="fa-solid fa-money-bill-1-wave mb-2 block text-xl"></i>
                                    <span class="text-xs font-bold uppercase">COD</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="payment_method" value="card" class="hidden peer">
                                <div class="bg-k86-black border border-k86-border p-4 rounded-xl text-center peer-checked:border-k86-red peer-checked:bg-k86-red/10 transition-all group-hover:border-k86-red/50">
                                    <i class="fa-solid fa-credit-card mb-2 block text-xl"></i>
                                    <span class="text-xs font-bold uppercase">Card</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-k86-black p-6 rounded-lg border border-k86-surface2 space-y-3 mt-6">
                    <div class="flex justify-between items-center text-sm font-semibold text-k86-muted uppercase tracking-wider">
                        <span>Items Subtotal</span>
                        <span id="chk-subtotal">Rs 0.00</span>
                    </div>
                    <div id="delivery-row" class="flex justify-between items-center text-sm font-semibold text-k86-red uppercase tracking-wider">
                        <span>Delivery Fee</span>
                        <span id="chk-delivery">Rs 100.00</span>
                    </div>
                    <div class="pt-3 border-t border-k86-border flex justify-between items-center">
                        <span class="text-lg font-heading text-white">Grand Total</span>
                        <p class="text-4xl font-price text-k86-amber drop-shadow-[0_0_8px_rgba(255,184,0,0.4)]" id="chk-total">Rs 100.00</p>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-k86-red to-k86-red-dark hover:from-red-500 hover:to-red-700 py-4 font-bold rounded-lg text-lg transition-all duration-300 transform active:scale-95 shadow-[0_0_15px_rgba(255,32,32,0.4)]">CONFIRM ORDER</button>
            </form>
        </div>
    </template>

    <template id="tpl-orders">
        <div class="reveal">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                <div>
                    <h2 class="text-5xl font-display pb-3 border-b-2 border-k86-red inline-block text-transparent bg-clip-text bg-gradient-to-r from-k86-amber to-k86-red">MY ORDERS</h2>
                    <p class="text-k86-muted text-sm font-heading mt-2 uppercase tracking-widest font-bold">Track your cravings in real-time</p>
                </div>
                <button onclick="OrdersInterface.loadOrders()" class="bg-k86-surface2 hover:bg-k86-red px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all border border-k86-border flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
            <div id="orders-list" class="space-y-6 pb-20">
                <!-- Skeletons -->
                <div class="bg-k86-surface border border-k86-border p-8 rounded-2xl animate-pulse h-24"></div>
                <div class="bg-k86-surface border border-k86-border p-8 rounded-2xl animate-pulse h-24"></div>
                <div class="bg-k86-surface border border-k86-border p-8 rounded-2xl animate-pulse h-24"></div>
            </div>
        </div>
    </template>

    <!-- Cart Drawer -->
    <div id="cart-drawer" class="fixed top-0 right-0 h-full w-full md:w-96 bg-k86-surface border-l border-k86-border z-50 drawer-closed flex flex-col transition-transform duration-500 shadow-[-10px_0_30px_rgba(0,0,0,0.8)]">
        <div class="p-6 border-b border-k86-border flex justify-between items-center bg-gradient-to-b from-k86-surface2 to-k86-surface">
            <h3 class="font-display text-3xl text-k86-amber tracking-wide flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                CART
            </h3>
            <button onclick="app.cart.toggleDrawer()" class="text-k86-muted hover:text-white transition-colors p-2 rounded-full hover:bg-k86-surface2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="cart-items" class="flex-grow p-4 overflow-y-auto space-y-4 scrollbar-thin scrollbar-thumb-k86-surface2"></div>
        <div class="p-6 border-t border-k86-border bg-gradient-to-t from-k86-surface2 to-k86-surface">
            <div class="flex justify-between items-end mb-6">
                <span class="font-heading text-k86-muted uppercase tracking-wider text-sm">Subtotal</span>
                <span id="cart-total" class="font-price text-3xl text-k86-amber font-bold">Rs 0.00</span>
            </div>
            <a href="#checkout" onclick="app.cart.toggleDrawer()" class="block w-full bg-k86-red text-center font-bold px-4 py-4 rounded-lg transform transition-all duration-300 hover:bg-red-500 hover:shadow-[0_0_20px_rgba(255,32,32,0.6)] active:scale-95" id="btn-checkout">PROCEED TO CHECKOUT</a>
        </div>
    </div>
    
    <!-- Auth Modal -->
    <div id="auth-modal" class="fixed inset-0 z-[60] hidden bg-black/90 backdrop-blur-[2px] flex items-center justify-center p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-k86-surface p-8 rounded-2xl w-full max-w-md relative border border-k86-border shadow-[0_0_40px_rgba(0,0,0,0.8)] transform scale-95 transition-all duration-300" id="auth-modal-content">
            <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-k86-red rounded-full opacity-20 blur-2xl"></div>
            <button onclick="app.auth.closeModal()" class="absolute top-4 right-4 text-k86-muted hover:text-white transition-colors bg-k86-black p-2 rounded-full hover:bg-k86-surface2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div id="form-login-view" class="relative z-10">
                <h3 class="text-4xl font-display text-k86-amber mb-6 text-center tracking-wide">WELCOME BACK</h3>
                <form id="form-login" class="space-y-5">
                    <div>
                        <input type="email" id="login-email" required placeholder="Email Address" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all">
                    </div>
                    <div class="relative">
                        <input type="password" id="login-pwd" required placeholder="Password" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all pr-12">
                        <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-k86-muted hover:text-white toggle-pwd" data-target="login-pwd" title="Toggle Password">
                            <i class="fa-solid fa-eye border border-k86-border px-2 py-1.5 rounded"></i>
                        </button>
                    </div>
                    <button type="submit" class="w-full bg-k86-red py-3 rounded-lg font-bold tracking-wider hover:bg-red-500 transition-colors shadow-lg shadow-k86-red/20 active:scale-95">LOGIN</button>
                    <p class="text-center text-k86-muted mt-4">
                        New here? <a href="javascript:void(0)" onclick="app.auth.switchView('register')" class="text-k86-amber hover:text-yellow-400 underline transition-colors">Join the family</a>
                    </p>
                </form>
            </div>
            
            <div id="form-register-view" class="hidden relative z-10">
                <h3 class="text-4xl font-display text-k86-amber mb-6 text-center tracking-wide">JOIN KITCHEN 86</h3>
                <form id="form-register" class="space-y-5">
                    <div>
                        <input type="text" id="reg-name" required placeholder="Full Name" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all">
                    </div>
                    <div>
                        <input type="email" id="reg-email" required placeholder="Email Address" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all">
                    </div>
                    <div>
                        <input type="tel" id="reg-phone" placeholder="Phone Number" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all">
                    </div>
                    <div class="relative">
                        <input type="password" id="reg-pwd" required placeholder="Password" class="w-full bg-k86-black rounded-lg px-4 py-3 border border-k86-border focus:border-k86-red focus:ring-1 focus:ring-k86-red outline-none transition-all pr-12">
                        <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-k86-muted hover:text-white toggle-pwd" data-target="reg-pwd" title="Toggle Password">
                            <i class="fa-solid fa-eye border border-k86-border px-2 py-1.5 rounded"></i>
                        </button>
                    </div>
                    <button type="submit" class="w-full bg-k86-red py-3 rounded-lg font-bold tracking-wider hover:bg-red-500 transition-colors shadow-lg shadow-k86-red/20 active:scale-95">CREATE ACCOUNT</button>
                    <p class="text-center text-k86-muted mt-4">
                        Already have an account? <a href="javascript:void(0)" onclick="app.auth.switchView('login')" class="text-k86-amber hover:text-yellow-400 underline transition-colors">Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/auth.js?v=2"></script>
    <script src="assets/js/checkout.js"></script>
    <script src="assets/js/orders.js"></script>
    <script src="assets/js/app.js"></script>
    
    <!-- UI Enhancement Scripts (Vanilla JS) -->
    <script>
        // Smooth scroll proxy attached to window
        window.scrollToSection = function(id) {
            const el = document.getElementById(id);
            if (el) {
                const navHeight = document.getElementById('main-nav')?.offsetHeight || 0;
                const targetPosition = el.getBoundingClientRect().top + window.scrollY - navHeight - 20;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            } else {
                window.location.hash = '#home';
                setTimeout(() => window.scrollToSection(id), 100);
            }
        };
        
        // Backward compatibility
        window.scrollToMenu = () => window.scrollToSection('menu-section');

        document.addEventListener('DOMContentLoaded', () => {
            // Setup Intersection Observer for scroll animations
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            // Function to observe dynamically added templates
            window.observeElements = () => {
                document.querySelectorAll('.reveal:not(.active)').forEach(el => {
                    observer.observe(el);
                    // Slight delay for DOM to render properly before observing
                    setTimeout(() => el.classList.add('active'), 50);
                });
            };

            // Hook into Hash Change to observe new content
            window.addEventListener('hashchange', () => {
                setTimeout(observeElements, 100);
            });
            setTimeout(observeElements, 100);

            // Hook into Cart Add for Animation (Overriding existing cart update slightly or sniffing)
            const observer2 = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.target.id === 'cart-badge' || mutation.target.id === 'cart-badge-mob') {
                        // Trigger pop animation
                        mutation.target.classList.remove('animate-pop');
                        void mutation.target.offsetWidth; // Trigger reflow
                        mutation.target.classList.add('animate-pop');
                    }
                });
            });
            
            const cartBadge = document.getElementById('cart-badge');
            const cartBadgeMob = document.getElementById('cart-badge-mob');
            if (cartBadge) observer2.observe(cartBadge, { childList: true, characterData: true, subtree: true });
            if (cartBadgeMob) observer2.observe(cartBadgeMob, { childList: true, characterData: true, subtree: true });

            // Smooth Navbar Background on Scroll
            window.addEventListener('scroll', () => {
                const nav = document.getElementById('main-nav');
                if (window.scrollY > 20) {
                    nav.classList.add('shadow-lg', 'bg-k86-surface/95');
                    nav.classList.remove('bg-k86-surface/80');
                } else {
                    nav.classList.remove('shadow-lg', 'bg-k86-surface/95');
                    nav.classList.add('bg-k86-surface/80');
                }
            });

            // Enhanced Modal Show/Hide
            const originalOpenModal = app.auth.openLogin;
            const originalCloseModal = app.auth.closeModal;
            
            app.auth.openLogin = function() {
                if(originalOpenModal) originalOpenModal.call(app.auth);
                const modal = document.getElementById('auth-modal');
                const content = document.getElementById('auth-modal-content');
                if(modal) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.classList.add('opacity-100');
                        content.classList.remove('scale-95');
                        content.classList.add('scale-100');
                    }, 10);
                }
            };
            
            app.auth.closeModal = function() {
                const modal = document.getElementById('auth-modal');
                const content = document.getElementById('auth-modal-content');
                if(modal) {
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0');
                    content.classList.remove('scale-100');
                    content.classList.add('scale-95');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        if(originalCloseModal) originalCloseModal.call(app.auth);
                    }, 300);
                }
            };

            // Override app.render to load menu into home automatically
            const originalRender = app.render;
            app.render = function(tId) {
                // If they route to #menu, redirect to #home 
                if (tId === 'tpl-menu') {
                    window.location.replace(window.location.pathname + window.location.search + '#home');
                    setTimeout(() => window.scrollToMenu(), 50);
                    return;
                }
                
                originalRender.call(this, tId);
                
                if (tId === 'tpl-home') {
                    // Auto-load the menu into the inline #menu-grid
                    if(typeof this.loadMenu === 'function') {
                        this.loadMenu();
                    }
                    if(typeof this.loadFeatured === 'function') {
                        this.loadFeatured();
                    }
                }
            };
        });
    </script>
    
    <!-- Footer -->
    <footer class="bg-k86-surface border-t border-k86-border pt-16 pb-8 mt-12 bg-gradient-to-b from-k86-surface to-k86-black relative overflow-hidden reveal">
        <div class="absolute top-0 right-0 w-64 h-64 bg-k86-red rounded-full opacity-5 blur-[100px]"></div>
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-1">
                <a href="#home" onclick="window.scrollTo({top:0, behavior:'smooth'})" class="text-k86-red font-display text-4xl tracking-wider inline-block mb-4 hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(255,32,32,0.3)]">KITCHEN 86</a>
                <p class="text-k86-muted text-sm mb-6 font-heading">The boldest flavor in Larkana. Serving up Cheezious Matka Fries & Legendary Fried Chicken directly to your door.</p>
                <div class="flex gap-4">
                    <a href="javascript:void(0)" class="w-10 h-10 rounded-full bg-k86-surface2 border border-k86-border flex items-center justify-center text-white hover:border-k86-red hover:text-k86-red hover:shadow-[0_0_10px_rgba(255,32,32,0.4)] transition-all transform hover:-translate-y-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                    <a href="javascript:void(0)" class="w-10 h-10 rounded-full bg-k86-surface2 border border-k86-border flex items-center justify-center text-white hover:border-k86-red hover:text-k86-red hover:shadow-[0_0_10px_rgba(255,32,32,0.4)] transition-all transform hover:-translate-y-1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="font-heading font-bold text-white mb-6 uppercase tracking-wider text-sm border-b border-k86-surface2 pb-2 inline-block">Explore</h4>
                <ul class="space-y-3 text-k86-muted text-sm">
                    <li><a href="javascript:void(0)" onclick="scrollToSection('menu-section')" class="hover:text-k86-red transition-colors inline-block hover:translate-x-1 transform duration-300">Our Menu</a></li>
                    <li><a href="javascript:void(0)" onclick="scrollToSection('about-section')" class="hover:text-k86-red transition-colors inline-block hover:translate-x-1 transform duration-300">Our Story</a></li>
                    <li><a href="javascript:void(0)" onclick="scrollToSection('contact-section')" class="hover:text-k86-red transition-colors inline-block hover:translate-x-1 transform duration-300">Contact/Location</a></li>
                    <li><a href="javascript:void(0)" onclick="app.auth.openLogin()" class="hover:text-k86-red transition-colors inline-block hover:translate-x-1 transform duration-300">My Account</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="font-heading font-bold text-white mb-6 uppercase tracking-wider text-sm border-b border-k86-surface2 pb-2 inline-block">Contact Us</h4>
                <ul class="space-y-4 text-k86-muted text-sm">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-k86-amber shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>VIP Road, Larkana<br>Sindh, Pakistan</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-k86-amber shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span>+92 300 1234567</span>
                    </li>
                </ul>
            </div>
            
            <!-- Opening Hours -->
            <div>
                <h4 class="font-heading font-bold text-white mb-6 uppercase tracking-wider text-sm border-b border-k86-surface2 pb-2 inline-block">Opening Hours</h4>
                <ul class="space-y-3 text-sm text-k86-muted flex flex-col">
                    <li class="flex justify-between border-b border-k86-surface2 pb-2"><span>Mon - Thu</span> <span class="text-white">12:00 PM - 1:00 AM</span></li>
                    <li class="flex justify-between border-b border-k86-surface2 pb-2"><span>Fri - Sun</span> <span class="text-white font-bold">12:00 PM - 3:00 AM</span></li>
                </ul>
            </div>
        </div>
        
        <div class="max-w-6xl mx-auto px-4 mt-12 pt-8 border-t border-k86-surface2 text-center text-xs text-k86-muted/60 relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; 2026 Kitchen 86. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="javascript:void(0)" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="javascript:void(0)" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>
