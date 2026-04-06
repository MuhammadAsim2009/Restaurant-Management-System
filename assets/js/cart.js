window.CartInterface = {
    items: [],
    init() { this.load(); this.ui(); },
    load() { try { this.items = JSON.parse(localStorage.getItem('k86_cart')) || []; } catch(e){ this.items=[]; } },
    save() { 
        localStorage.setItem('k86_cart', JSON.stringify(this.items)); 
        this.ui(); 
        if (window.AuthInterface && window.AuthInterface.isLoggedIn) {
            fetch('api/cart.php?action=save', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({cart: this.items})
            }).catch(()=>{});
        }
    },
    clear() {
        this.items = [];
        localStorage.removeItem('k86_cart');
        this.ui();
    },
    syncWithBackend(cartData) {
        if (this.items.length > 0 && window.AuthInterface && window.AuthInterface.isLoggedIn) {
            this.save();
        } else if (cartData && Array.isArray(cartData) && cartData.length > 0) {
            this.items = cartData;
            localStorage.setItem('k86_cart', JSON.stringify(this.items));
            this.ui();
        }
    },
    addItem(p) { 
        if(!window.AuthInterface || !window.AuthInterface.isLoggedIn) {
            if(window.Toast) window.Toast.show('Please login to add items to your cart!', 'warning');
            window.AuthInterface.openLogin();
            return;
        }
        const type = p.isSpecialDeal ? 'deal' : 'item';
        const cartId = `${type}_${p.id}`;
        let ex = this.items.find(i => i.cartId === cartId); 
        if(ex) {
            ex.qty++; 
        } else {
            this.items.push({
                cartId: cartId,
                id: p.id,
                type: type,
                name: p.name, 
                price: parseFloat(p.price), 
                qty: 1
            });
        }
        this.save(); 
        if(window.Toast) window.Toast.show('Added to cart!', 'success'); 
    },
    updateQty(cartId, d) { 
        let i = this.items.find(i => i.cartId === cartId); 
        if(i) { 
            i.qty += d; 
            if(i.qty <= 0) this.items = this.items.filter(x => x.cartId !== cartId); 
            this.save(); 
        } 
    },
    getTotals() { let sub = this.items.reduce((s,i)=>s+(i.price*i.qty), 0); return {sub, total:sub}; },
    ui() {
        document.getElementById('cart-badge').innerText = this.items.reduce((s,i)=>s+i.qty, 0);
        let h = ''; this.items.forEach(i => h+=`<div class="flex justify-between items-center bg-black p-2 rounded mb-2"><div><p>${i.name}</p><p class="text-k86-amber">Rs ${i.price}</p></div><div class="flex gap-2"><button onclick="app.cart.updateQty('${i.cartId}', -1)">-</button><span class="mx-2">${i.qty}</span><button onclick="app.cart.updateQty('${i.cartId}', 1)">+</button></div></div>`);
        document.getElementById('cart-items').innerHTML = h || '<p class="text-center">Empty Cart</p>';
        document.getElementById('cart-total').innerText = 'Rs ' + this.getTotals().total;
        let c = document.getElementById('btn-checkout'); if(c) c.classList.toggle('opacity-50', !this.items.length);
    },
    toggleDrawer() { let d=document.getElementById('cart-drawer'); d.classList.contains('drawer-closed')?this.openDrawer():this.closeDrawer(); },
    openDrawer() { document.getElementById('cart-drawer').classList.replace('drawer-closed', 'drawer-open'); },
    closeDrawer() { document.getElementById('cart-drawer').classList.replace('drawer-open', 'drawer-closed'); }
};
document.addEventListener('DOMContentLoaded', () => window.CartInterface.init());
