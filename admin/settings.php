<?php 
$current_page = 'settings.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="animate-fade-in space-y-8 pb-12" data-page="settings">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display text-white tracking-widest uppercase">Site Settings</h1>
            <p class="text-k86-muted font-body mt-1">Configure your website's appearance and content in real-time.</p>
        </div>
        <button type="button" onclick="adminApp.saveSettings(event)" id="btn-save-settings" class="px-8 py-3 bg-k86-red text-white rounded-xl text-sm font-bold shadow-lg shadow-k86-red/20 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Save All Changes
        </button>
    </div>

    <!-- Settings Container -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Tabs Sidebar -->
        <div class="lg:col-span-1 space-y-2">
            <button onclick="adminApp.switchSettingsTab('general')" data-tab="general" class="settings-tab w-full flex items-center gap-3 px-5 py-4 rounded-xl font-bold transition-all bg-k86-surface border border-k86-border text-white shadow-lg shadow-k86-red/5">
                <i class="fas fa-cog text-k86-red"></i> General
            </button>
            <button onclick="adminApp.switchSettingsTab('hero')" data-tab="hero" class="settings-tab w-full flex items-center gap-3 px-5 py-4 rounded-xl font-bold transition-all bg-k86-surface2 border border-transparent text-k86-muted hover:text-white hover:bg-k86-surface/50">
                <i class="fas fa-rocket text-k86-amber"></i> Hero Section
            </button>
            <button onclick="adminApp.switchSettingsTab('brand')" data-tab="brand" class="settings-tab w-full flex items-center gap-3 px-5 py-4 rounded-xl font-bold transition-all bg-k86-surface2 border border-transparent text-k86-muted hover:text-white hover:bg-k86-surface/50">
                <i class="fas fa-palette text-blue-400"></i> Brand & Colors
            </button>
            <button onclick="adminApp.switchSettingsTab('footer')" data-tab="footer" class="settings-tab w-full flex items-center gap-3 px-5 py-4 rounded-xl font-bold transition-all bg-k86-surface2 border border-transparent text-k86-muted hover:text-white hover:bg-k86-surface/50">
                <i class="fas fa-shoe-prints text-green-400"></i> Footer info
            </button>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3 bg-k86-surface border border-k86-border rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <i class="fas fa-tools text-9xl"></i>
            </div>

            <form id="form-site-settings" class="relative z-10 space-y-8">
                
                <!-- General Section -->
                <div id="section-general" class="settings-section space-y-6">
                    <div class="border-b border-k86-border pb-4 mb-6">
                        <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">General Settings</h3>
                        <p class="text-k86-muted text-sm">Basic identity and contact information.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Site Name</label>
                            <input type="text" name="site_name" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner" placeholder="Kitchen 86">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Tagline / Slogan</label>
                            <input type="text" name="site_tagline" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner" placeholder="The Taste of Tradition">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">About Us Description</label>
                        <textarea name="about_us" rows="4" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none resize-none shadow-inner"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Contact Phone</label>
                            <input type="text" name="contact_phone" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Contact Email</label>
                            <input type="email" name="contact_email" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Business Address</label>
                        <input type="text" name="address" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Google Maps Embed URL (iframe src)</label>
                        <input type="text" name="map_embed_link" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                    </div>
                </div>

                <!-- Hero Section -->
                <div id="section-hero" class="settings-section hidden space-y-6">
                    <div class="border-b border-k86-border pb-4 mb-6">
                        <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Hero Section</h3>
                        <p class="text-k86-muted text-sm">Customize the main landing section of your homepage.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Main Heading</label>
                        <input type="text" name="hero_heading" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner font-display text-xl tracking-wider uppercase">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Sub-heading / Tagline</label>
                        <input type="text" name="hero_subheading" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Hero Background Image URL</label>
                            <input type="text" name="hero_bg_image" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">CTA Button Text</label>
                            <input type="text" name="hero_cta_text" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none shadow-inner">
                        </div>
                    </div>
                    
                    <!-- Preview simulation -->
                    <div class="relative w-full h-40 rounded-2xl overflow-hidden border border-k86-border mt-8 group">
                        <div id="hero-preview-bg" class="absolute inset-0 bg-cover bg-center transition-all duration-500" style="background-image: url('../assets/images/hero_bg.jpg')"></div>
                        <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center p-4 text-center">
                            <h4 id="hero-preview-heading" class="text-white font-display text-xl tracking-widest uppercase">Hero Heading Preview</h4>
                            <p id="hero-preview-sub" class="text-k86-muted text-[10px] mt-1">Subtitle Preview</p>
                            <button id="hero-preview-cta" class="mt-3 px-4 py-1.5 bg-k86-red text-white text-[10px] font-bold rounded-lg uppercase tracking-widest">Order Now</button>
                        </div>
                    </div>
                </div>

                <!-- Brand Section -->
                <div id="section-brand" class="settings-section hidden space-y-6">
                    <div class="border-b border-k86-border pb-4 mb-6">
                        <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Brand & Colors</h3>
                        <p class="text-k86-muted text-sm">Control the primary theme and typography of your brand.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-white uppercase tracking-widest border-l-2 border-k86-red pl-2">Primary Palette</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-k86-black rounded-2xl border border-k86-border">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-k86-muted">Primary (K86 Red)</span>
                                        <span id="label-primary_color" class="text-xs text-white font-price tracking-wider">#FF2020</span>
                                    </div>
                                    <input type="color" name="primary_color" value="#FF2020" oninput="adminApp.previewColor('primary_color', this.value)" class="w-12 h-12 rounded-lg cursor-pointer bg-transparent border-none appearance-none">
                                </div>
                                <div class="flex items-center justify-between p-4 bg-k86-black rounded-2xl border border-k86-border">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-k86-muted">Accent (K86 Amber)</span>
                                        <span id="label-accent_color" class="text-xs text-white font-price tracking-wider">#FFB800</span>
                                    </div>
                                    <input type="color" name="accent_color" value="#FFB800" oninput="adminApp.previewColor('accent_color', this.value)" class="w-12 h-12 rounded-lg cursor-pointer bg-transparent border-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-white uppercase tracking-widest border-l-2 border-k86-muted pl-2">Surface Palette</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-k86-black rounded-2xl border border-k86-border">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-k86-muted">Background</span>
                                        <span id="label-bg_color" class="text-xs text-white font-price tracking-wider">#161616</span>
                                    </div>
                                    <input type="color" name="bg_color" value="#161616" oninput="adminApp.previewColor('bg_color', this.value)" class="w-12 h-12 rounded-lg cursor-pointer bg-transparent border-none">
                                </div>
                                <div class="flex items-center justify-between p-4 bg-k86-black rounded-2xl border border-k86-border">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-k86-muted">Surface / Cards</span>
                                        <span id="label-surface_color" class="text-xs text-white font-price tracking-wider">#1A1A1A</span>
                                    </div>
                                    <input type="color" name="surface_color" value="#1A1A1A" oninput="adminApp.previewColor('surface_color', this.value)" class="w-12 h-12 rounded-lg cursor-pointer bg-transparent border-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-white uppercase tracking-widest border-l-2 border-k86-muted pl-2">Typography</h4>
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Display Font</label>
                                    <select name="font_display" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red outline-none">
                                        <option value="Bebas Neue">Bebas Neue</option>
                                        <option value="Poppins">Poppins</option>
                                        <option value="Barlow">Barlow</option>
                                        <option value="Barlow Condensed">Barlow Condensed</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Primary Body Font</label>
                                    <select name="font_primary" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red outline-none">
                                        <option value="Barlow">Barlow</option>
                                        <option value="Poppins">Poppins</option>
                                        <option value="Roboto">Roboto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Section -->
                <div id="section-footer" class="settings-section hidden space-y-6">
                    <div class="border-b border-k86-border pb-4 mb-6">
                        <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Footer Settings</h3>
                        <p class="text-k86-muted text-sm">Manage social links and footer descriptive text.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Footer Description</label>
                        <textarea name="footer_description" rows="3" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red transition-all outline-none resize-none shadow-inner"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1"><i class="fab fa-instagram mr-1"></i> Instagram URL</label>
                            <input type="text" name="social_instagram" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red outline-none shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1"><i class="fab fa-facebook mr-1"></i> Facebook URL</label>
                            <input type="text" name="social_facebook" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red outline-none shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1"><i class="fab fa-whatsapp mr-1"></i> WhatsApp link</label>
                            <input type="text" name="social_whatsapp" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red outline-none shadow-inner">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Copyright Text</label>
                        <input type="text" name="footer_copyright" class="w-full bg-k86-black border border-k86-border rounded-xl px-5 py-4 text-white focus:border-k86-red outline-none shadow-inner">
                    </div>
                </div>

            </form>

            <!-- Loading Spinner -->
            <div id="settings-loader" class="absolute inset-0 bg-k86-surface/80 flex items-center justify-center z-50 rounded-3xl hidden">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 border-4 border-k86-red border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-white font-bold animate-pulse">Syncing settings...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Color Picker Styling */
input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
input[type="color"]::-webkit-color-swatch { border: 2px solid #2A2A2A; border-radius: 12px; }
input[type="color"]::-moz-color-swatch { border: 2px solid #2A2A2A; border-radius: 12px; }

.settings-tab.active {
    background-color: #1A1A1A;
    border-color: #FF2020;
    color: white;
    box-shadow: 0 10px 15px -3px rgba(255, 32, 32, 0.1);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
