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
            const r = await fetch('/api/menu.php'); 
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
    renderMenuGrid(items) {
        const grid = document.getElementById('menu-grid');
        if (!grid) return;
        
        grid.innerHTML = items.map(i => {
            // Default high quality Unsplash photos for each category if none is provided via DB
            let fallbackImg = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80'; // Burger
            if(i.name.toLowerCase().includes('fries')) fallbackImg = 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&q=80';
            else if(i.name.toLowerCase().includes('chicken')) fallbackImg = 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=500&q=80';
            else if(i.name.toLowerCase().includes('wrap')) fallbackImg = 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500&q=80';
            
            const img = i.image || fallbackImg;
            const desc = i.description ? `<p class="text-sm text-k86-muted mb-4 line-clamp-2">${i.description}</p>` : '';
            
            return `
            <div class="bg-gradient-to-br from-k86-surface to-[rgba(34,34,34,0.4)] border border-k86-border rounded-2xl p-5 text-center flex flex-col h-full group relative overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:border-k86-red hover:shadow-[0_10px_25px_-5px_rgba(255,32,32,0.4)]">
                <div class="overflow-hidden rounded-xl mb-4">
                    <img src="${img}" class="w-full h-48 object-cover transform transition-transform duration-500 group-hover:scale-110 shadow-md">
                </div>
                <h3 class="font-heading text-xl font-bold mb-2">${i.name}</h3>
                ${desc}
                <div class="mt-auto pt-2 border-t border-k86-border/30">
                    <p class="text-k86-amber font-price text-3xl font-bold tracking-wider mb-4 drop-shadow-[0_0_8px_rgba(255,184,0,0.3)]">Rs ${i.price}</p>
                    <button onclick='app.cart.addItem(${JSON.stringify(i)})' class="w-full bg-gradient-to-r from-k86-surface2 to-k86-border hover:from-k86-red hover:to-k86-red-dark py-3 rounded-lg font-bold tracking-wide transition-all duration-300 hover:shadow-[0_0_15px_rgba(255,32,32,0.6)] active:scale-95 group-hover:text-white">ADD TO CART</button>
                </div>
            </div>`;
        }).join('');
    },
    renderDummyMenu() {
        this.renderMenuGrid([
            {id:1, name:'Signature Matka Fries', price:650, description: 'Our famous loaded fries in a traditional clay matka, bubbling with hot cheese and spicy chicken chunks.', image:'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=500&q=80'},
            {id:2, name:'Zinger Burger', price:450, description: 'Crispy fried chicken breast, fresh lettuce, and our secret spicy mayo in a soft toasted bun.', image:'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80'},
            {id:3, name:'Spicy Fried Chicken (3 Pcs)', price:950, description: 'Three pieces of our signature hot and crispy bone-in fried chicken.', image:'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=500&q=80'},
            {id:4, name:'Chicken Cheese Wrap', price:550, description: 'Grilled chicken strips, molten cheese, and veggies wrapped tightly in a soft tortilla.', image:'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500&q=80'},
            {id:5, name:'Loaded Pizza Fries', price:800, description: 'Crinkle cut fries topped with pizza sauce, olives, jalapenos, and mozzarella cheese.', image:'https://images.unsplash.com/photo-1534080564583-6be75777b70a?w=500&q=80'},
            {id:6, name:'Tikka Maccheroni', price:700, description: 'Spicy chicken tikka boti tossed with pasta in a rich, creamy cheese sauce.', image:'https://images.unsplash.com/photo-1621996316585-021067de6666?w=500&q=80'}
        ]);
    }
};
document.addEventListener('DOMContentLoaded', () => app.init());
