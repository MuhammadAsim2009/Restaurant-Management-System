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
            const r = await fetch('/api/menu.php'); if(r.ok) { /* parse */ } else throw new Error();
        } catch(e) { this.renderDummyMenu(); }
    },
    renderDummyMenu() {
        const grid = document.getElementById('menu-grid');
        grid.innerHTML = [
            {id:1, name:'Matka Fries', price:650, img:'https://placehold.co/200/161616/FF2020?text=Fries'},
            {id:2, name:'Zinger Burger', price:450, img:'https://placehold.co/200/161616/FFB800?text=Burger'},
            {id:3, name:'Fried Chicken', price:950, img:'https://placehold.co/200/161616/FFF?text=Chicken'}
        ].map(i => `<div class="bg-k86-surface border rounded p-4 text-center"><img src="${i.img}" class="w-full h-32 object-cover mb-2"><h3 class="font-bold">${i.name}</h3><p class="text-k86-amber font-price text-xl mb-4">Rs ${i.price}</p><button onclick='app.cart.addItem(${JSON.stringify(i)})' class="w-full bg-k86-red py-2 rounded font-bold">ADD TO CART</button></div>`).join('');
    }
};
document.addEventListener('DOMContentLoaded', () => app.init());
