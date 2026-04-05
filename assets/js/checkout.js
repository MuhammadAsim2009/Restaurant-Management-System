window.CheckoutInterface = {
    init() { this.updateTotal(); this.bind(); },
    updateTotal() { let el = document.getElementById('chk-total'); if(el) el.innerText = 'Rs ' + app.cart.getTotals().total; },
    bind() {
        let f = document.getElementById('checkout-form');
        if(f && !f.dataset.b) { 
            f.dataset.b = "1"; 
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
    submit() {
        alert('Order Placed Successfully!\nTotal: Rs ' + app.cart.getTotals().total);
        app.cart.items=[]; app.cart.save(); window.location.hash='#home';
    }
};
