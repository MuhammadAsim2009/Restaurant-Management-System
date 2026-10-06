<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../includes/site_settings.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | <?= $SETTINGS['site_name'] ?? 'Restaurant' ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@400;600;700&family=Barlow:wght@400;500;600&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="../assets/js/theme.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'k86-red': '<?= $SETTINGS['primary_color'] ?? '#FF2020' ?>',
                        'k86-red-dark': '<?= $SETTINGS['primary_color'] ?? '#CC1010' ?>',
                        'k86-amber': '<?= $SETTINGS['accent_color'] ?? '#FFB800' ?>',
                        'k86-black': 'var(--k86-bg)',
                        'k86-surface': 'var(--k86-surface)',
                        'k86-surface2': 'var(--k86-surface2)',
                        'k86-border': 'var(--k86-border)',
                        'k86-muted': 'var(--k86-text-muted)',
                        'k86-success': '#22C55E',
                        'k86-warning': '#F97316',
                        'k86-danger': '#EF4444'
                    },
                    textColor: {
                        'white': 'var(--k86-text)',
                        'pure-white': '#FFFFFF',
                        'k86-muted': 'var(--k86-text-muted)',
                        'k86-red': '<?= $SETTINGS['primary_color'] ?? '#FF2020' ?>',
                        'k86-amber': '<?= $SETTINGS['accent_color'] ?? '#FFB800' ?>'
                    },
                    fontFamily: {
                        'display': ['Bebas Neue', 'cursive'],
                        'heading': ['Poppins', 'sans-serif'],
                        'body': ['Barlow', 'sans-serif'],
                        'price': ['Barlow Condensed', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out forwards',
                        'ripple': 'ripple 0.6s linear'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        ripple: {
                            'to': {
                                transform: 'scale(4)',
                                opacity: '0'
                            }
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        :root {
            --k86-bg: <?= $SETTINGS['bg_color'] ?? '#0A0A0A' ?>;
            --k86-surface: <?= $SETTINGS['surface_color'] ?? '#161616' ?>;
            --k86-surface2: <?= str_replace('16', '22', $SETTINGS['surface_color'] ?? '#161616') ?>;
            --k86-border: <?= str_replace('16', '2A', $SETTINGS['surface_color'] ?? '#161616') ?>;
            --k86-red: <?= $SETTINGS['primary_color'] ?? '#FF2020' ?>;
            --k86-amber: <?= $SETTINGS['accent_color'] ?? '#FFB800' ?>;
            --k86-text: #FFFFFF;
            --k86-text-muted: #9CA3AF;
            --k86-nav-hover-bg: rgba(255, 255, 255, 0.07);
            --k86-nav-hover-text: #FFFFFF;
            --k86-input-bg: var(--k86-bg);
            --k86-card-bg: var(--k86-surface);
        }

        .light-mode {
            --k86-bg: <?= $SETTINGS['light_bg_color'] ?? '#F3F4F8' ?>;
            --k86-surface: <?= $SETTINGS['light_surface_color'] ?? '#FFFFFF' ?>;
            --k86-surface2: <?= str_replace('FFFFFF', 'F8FAFC', $SETTINGS['light_surface_color'] ?? 'FFFFFF') ?>;
            --k86-border: rgba(0,0,0,0.08);
            --k86-red: <?= $SETTINGS['light_primary_color'] ?? $SETTINGS['primary_color'] ?? '#FF2020' ?>;
            --k86-amber: <?= $SETTINGS['light_accent_color'] ?? $SETTINGS['accent_color'] ?? '#FFB800' ?>;
            --k86-text: <?= $SETTINGS['light_text_color'] ?? '#111827' ?>;
            --k86-text-muted: #6B7280;
            --k86-nav-hover-bg: rgba(0, 0, 0, 0.05);
            --k86-nav-hover-text: var(--k86-text);
            --k86-input-bg: var(--k86-bg);
            --k86-card-bg: var(--k86-surface);
        }

        /* Custom scrollbar for dark theme */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--k86-bg); }
        ::-webkit-scrollbar-thumb { background: var(--k86-border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--k86-red); }
        
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--k86-text-muted);
        }

        .nav-link.active {
            background-color: var(--k86-red);
            color: #FFFFFF !important;
            box-shadow: 0 10px 20px -5px rgba(255, 32, 32, 0.4);
            transform: scale(1.02);
            z-index: 10;
        }
        
        .nav-link:not(.active):hover {
            color: var(--k86-nav-hover-text) !important;
            background: var(--k86-nav-hover-bg);
            transform: translateX(4px);
        }

        .nav-link svg {
            transition: all 0.3s ease;
            color: var(--k86-text-muted);
        }

        .nav-link.active svg {
            color: #FFFFFF;
        }

        .nav-link:hover svg {
            transform: scale(1.1);
            color: var(--k86-red);
        }

        /* Light mode sidebar overrides */
        .light-mode aside {
            background-color: var(--k86-surface);
            border-color: var(--k86-border);
        }

        /* Theme Toggle Icon Swap */
        .light-mode .theme-toggle-btn i::before {
            content: "\f186"; /* Moon icon */
        }
    </style>
</head>
<body class="bg-k86-black text-white font-body antialiased min-h-screen flex overflow-hidden">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col md:ml-64 h-screen overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="bg-k86-surface border-b border-k86-border h-16 flex items-center justify-between px-4 lg:px-8 z-10 shrink-0">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-k86-muted hover:text-white" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <!-- Search -->
                <div class="hidden sm:flex items-center bg-k86-black border border-k86-border rounded-full px-4 py-1.5 focus-within:border-k86-red transition-colors w-64 lg:w-96">
                    <svg class="w-4 h-4 text-k86-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" placeholder="Search orders, items..." class="bg-transparent border-none outline-none text-sm text-white px-3 w-full placeholder-k86-muted">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4 lg:gap-6">
                <!-- Notifications -->
                <button class="relative text-k86-muted hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-k86-red rounded-full ring-2 ring-k86-surface"></span>
                </button>

                <!-- Theme Toggle -->
                <button onclick="window.toggleK86Theme()" class="theme-toggle-btn text-k86-muted hover:text-white transition-colors p-2 rounded-full hover:bg-k86-black/20" title="Toggle Theme">
                    <i class="fa-solid fa-sun"></i>
                </button>
                
                <!-- Profile -->
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-9 h-9 rounded-full bg-k86-surface2 border border-k86-border flex items-center justify-center overflow-hidden">
                        <?php 
                        $admin_name = $_SESSION['user_name'] ?? 'Admin User';
                        $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($admin_name) . "&background=" . str_replace('#', '', $SETTINGS['primary_color'] ?? 'FF2020') . "&color=fff";
                        ?>
                        <img src="<?= $avatar_url ?>" alt="Admin" class="w-full h-full object-cover">
                    </div>
                    <div class="hidden md:block text-sm text-left">
                        <p class="font-semibold text-white leading-tight"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="text-k86-muted text-[10px] font-bold uppercase tracking-widest"><?= $_SESSION['user_role'] ?? 'Superadmin' ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-4 lg:p-8 relative">
            <div id="admin-content" class="max-w-7xl mx-auto min-h-full">
