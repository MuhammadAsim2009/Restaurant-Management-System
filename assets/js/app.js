const app = {
    cart: window.CartInterface || {}, auth: window.AuthInterface || {}, checkout: window.CheckoutInterface || {},
    init() { this.cache(); this.bind(); this.auth.checkSession().finally(() => this.route()); },
    cache() { this.$main = document.getElementById('app-content'); },
    bind() { window.addEventListener('hashchange', () => this.route()); },
    route() {
        const h = window.location.hash || '#home';
        if(h === '#orders' && !this.auth.isLoggedIn) { window.location.hash = '#home'; return this.auth.openLogin(); }
        switch(h) {
            case '#home': this.render('tpl-home'); break;
            case '#menu': this.render('tpl-menu'); this.loadMenu(); break;
            case '#checkout': if(!this.cart.items.length){window.location.hash='#menu'; return;} this.render('tpl-checkout'); this.checkout.init(); break;
            case '#orders': this.render('tpl-orders'); break;
            default: this.render('tpl-home');
        }
    },
    render(tId) { const t = document.getElementById(tId); this.$main.innerHTML = t ? t.innerHTML : '404'; },
    async loadMenu() {
        try {
            const r = await fetch('api/menu.php'); 
            if(r.ok) { 
                const data = await r.json();
                if(data.success && data.data && data.data.items && data.data.items.length) {
                    this.renderMenuGrid(data.data.items);
                } else {
                    this.renderDummyMenu();
                }
            } else throw new Error();
        } catch(e) { this.renderDummyMenu(); }
    },
    async loadFeatured() {
        try {
            const r = await fetch('api/menu.php?featured=1');
            if(r.ok) {
                const data = await r.json();
                if(data.success && data.data) {
                    const deals = data.data.deals || [];
                    const items = data.data.items || [];
                    this.renderFeatured([...deals.map(d => ({...d, isSpecialDeal: true})), ...items]);
                }
            }
        } catch(e) { console.error('Featured load failed', e); }
    },
    renderFeatured(promos) {
        const grid = document.getElementById('featured-grid');
        if(!grid) return;
        if(!promos.length) {
            grid.innerHTML = '<div class="col-span-full text-center py-10 text-k86-muted border border-dashed border-k86-border rounded-2xl">New exciting deals coming soon!</div>';
            return;
        }
        grid.innerHTML = promos.map((p, idx) => {
            const isDeal = p.isSpecialDeal === true;
            const theme = idx % 2 === 0 ? 'from-k86-surface to-k86-red-dark/20' : 'from-k86-surface to-k86-amber/20';
            const border = idx % 2 === 0 ? 'hover:border-k86-red' : 'hover:border-k86-amber';
            const badgeBg = idx % 2 === 0 ? 'bg-k86-amber text-k86-black' : 'bg-k86-red text-white';
            const badgeText = isDeal ? 'HOT DEAL' : 'FEATURED';
            const objBase64 = btoa(unescape(encodeURIComponent(JSON.stringify(p))));
            
            return `
                <div class="bg-gradient-to-br ${theme} p-6 rounded-2xl border border-k86-border ${border} transition-all duration-300 hover:-translate-y-2 group relative overflow-hidden h-full flex flex-col">
                    <div class="absolute -right-10 -top-10 w-32 h-32 ${idx % 2 === 0 ? 'bg-k86-red' : 'bg-k86-amber'} rounded-full opacity-20 group-hover:scale-150 transition-transform duration-700"></div>
                    <span class="absolute top-4 right-4 ${badgeBg} text-[10px] font-bold px-3 py-1 rounded-full animate-pulse-fast uppercase tracking-widest z-20">${badgeText}</span>
                    <div class="flex gap-4 items-start relative z-10 w-full flex-1">
                        <div class="w-24 h-24 shrink-0 rounded-xl overflow-hidden border border-k86-border shadow-lg bg-k86-black">
                            <img src="${p.image || 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&q=80'}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col h-full">
                            <h3 class="text-2xl font-heading font-bold mb-1 truncate uppercase">${p.name}</h3>
                            ${p.included_items_text ? '<p class="text-[10px] text-k86-amber font-bold mb-2 uppercase tracking-widest"><span class="opacity-50">Includes:</span> ' + p.included_items_text + '</p>' : ''}
                            <p class="text-k86-muted text-xs mb-4 line-clamp-2 italic">"${p.description || "Limited time offer, grab it before it's gone!"}"</p>
                            <div class="mt-auto">
                                <div class="flex flex-col mb-3">
                                    <p class="text-2xl font-price font-bold text-white leading-none">PKR ${parseFloat(p.price).toLocaleString()}</p>
                                    ${p.original_price ? '<p class="text-[10px] text-k86-muted line-through font-bold mt-1">WAS PKR ' + parseFloat(p.original_price).toLocaleString() + '</p>' : ''}
                                </div>
                                <button onclick="app.cart.addItem(JSON.parse(decodeURIComponent(escape(atob('${objBase64}')))))" class="w-full sm:w-auto px-4 py-2 bg-white/5 hover:bg-white text-white hover:text-black rounded-lg transition-all duration-300 font-bold uppercase text-[10px] tracking-widest border border-white/10 flex items-center justify-center gap-2 group-hover:shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                                    Add to Cart 
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');
    },
    renderMenuGrid(items) {
        const grid = document.getElementById('menu-grid');
        if (!grid) return;
        grid.innerHTML = items.map(i => {
            let fallbackImg = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80';
            if(i.name.toLowerCase().includes('fries')) fallbackImg = 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&q=80';
            const img = i.image || fallbackImg;
            const desc = i.description ? `<p class="text-sm text-k86-muted mb-4 line-clamp-2">${i.description}</p>` : '';
            const objBase64 = btoa(unescape(encodeURIComponent(JSON.stringify(i))));
            
            return `
            <div class="bg-gradient-to-br from-k86-surface to-[rgba(34,34,34,0.4)] border border-k86-border rounded-2xl p-5 text-center flex flex-col h-full group relative overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:border-k86-red hover:shadow-[0_10px_25px_-5px_rgba(255,32,32,0.4)]">
                <div class="overflow-hidden rounded-xl mb-4">
                    <img src="${img}" class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110 shadow-md" />
                </div>
                <h3 class="font-heading text-xl font-bold mb-2">${i.name}</h3>
                ${desc}
                <div class="mt-auto pt-2 border-t border-k86-border/30">
                    <p class="text-k86-amber font-price text-3xl font-bold tracking-wider mb-4 drop-shadow-[0_0_8px_rgba(255,184,0,0.3)]">Rs ${i.price}</p>
                    <button onclick="app.cart.addItem(JSON.parse(decodeURIComponent(escape(atob('${objBase64}')))))" class="w-full bg-gradient-to-r from-k86-surface2 to-k86-border hover:from-k86-red hover:to-k86-red-dark py-3 rounded-lg font-bold tracking-wide transition-all duration-300 hover:shadow-[0_0_15px_rgba(255,32,32,0.6)] active:scale-95 group-hover:text-white">ADD TO CART</button>
                </div>
            </div>`;
        }).join('');
    },
    renderDummyMenu() {
        this.renderMenuGrid([
            {id:1, name:'Signature Matka Fries', price:650, description: 'Our loaded fries in a tradition clay matka.', image:'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&q=80'},
            {id:2, name:'Zinger Burger', price:450, description: 'Crispy chicken breast in a toasted bun.', image:'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80'}
        ]);
    }
};
document.addEventListener('DOMContentLoaded', () => app.init());
