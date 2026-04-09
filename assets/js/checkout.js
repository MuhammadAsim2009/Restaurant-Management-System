window.CheckoutInterface = {
    init() { this.updateTotal(); this.bind(); },
    updateTotal() { 
        let sub = K86.cart.getTotals().sub;
        
        // Get selected order type
        const orderTypeEl = document.querySelector('input[name="order_type"]:checked');
        const orderType = orderTypeEl ? orderTypeEl.value : 'delivery';
        
        let delivery = (orderType === 'delivery') ? 100 : 0;
        let total = sub + delivery;
        
        // Update UI
        let subEl = document.getElementById('chk-subtotal');
        if(subEl) subEl.innerText = 'Rs ' + sub.toLocaleString();
        
        let delRow = document.getElementById('delivery-row');
        if(delRow) delRow.style.display = (orderType === 'delivery') ? 'flex' : 'none';
        
        let totalEl = document.getElementById('chk-total'); 
        if(totalEl) totalEl.innerText = 'Rs ' + total.toLocaleString(); 
        
        // Populate form if user is logged in
        if (window.AuthInterface && window.AuthInterface.user) {
            let u = window.AuthInterface.user;
            let n = document.getElementById('chk-name'); if(n && !n.value) n.value = u.name || '';
            let p = document.getElementById('chk-phone'); if(p && !p.value) p.value = u.phone || '';
        }
    },
    bind() {
        let f = document.getElementById('checkout-form');
        if(f && !f.dataset.b) { 
            f.dataset.b = "1"; 
            
            // Listen for radio changes
            f.querySelectorAll('input[name="order_type"]').forEach(radio => {
                radio.addEventListener('change', () => this.updateTotal());
            });

            f.addEventListener('submit', e => { 
                e.preventDefault(); 
                if(!window.AuthInterface || !window.AuthInterface.isLoggedIn) {
                    if(window.Toast) window.Toast.show('Please login to complete your checkout!', 'warning');
                    window.AuthInterface.openLogin();
                    return;
                }
                this.submit(); 
            }); 
        }
    },
    async submit() {
        const btn = document.querySelector('#checkout-form button[type="submit"]');
        const origTxt = btn.innerText;
        btn.innerText = 'PROCESSING...';
        btn.disabled = true;

        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        const orderData = {
            name: document.getElementById('chk-name').value,
            phone: document.getElementById('chk-phone').value,
            address: document.getElementById('chk-address').value,
            type: orderType,
            payment: paymentMethod,
            items: K86.cart.items
        };

        try {
            const r = await fetch('api/orders.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(orderData)
            });
            const d = await r.json();
            
            if (d.success) {
                const msg = orderType === 'delivery' 
                    ? 'Order placed successfully! PKR 100 delivery fee included.' 
                    : 'Order placed successfully!';
                if(window.Toast) window.Toast.show(msg, 'success');
                K86.cart.clear(); 
                window.location.hash = '#home';
            } else {
                if(window.Toast) window.Toast.show(d.message || 'Order failed', 'error');
            }
        } catch (e) {
            if(window.Toast) window.Toast.show('Network error, please try again.', 'error');
        } finally {
            btn.innerText = origTxt;
            btn.disabled = false;
        }
    }
};
