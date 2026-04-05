window.CheckoutInterface = {
    init() { this.updateTotal(); this.bind(); },
    updateTotal() { let el = document.getElementById('chk-total'); if(el) el.innerText = 'Rs ' + app.cart.getTotals().total; },
    bind() {
        let f = document.getElementById('checkout-form');
        if(f && !f.dataset.b) { f.dataset.b="1"; f.addEventListener('submit', e => { e.preventDefault(); this.submit(); }); }
    },
    submit() {
        alert('Order Placed Successfully!\nTotal: Rs ' + app.cart.getTotals().total);
        app.cart.items=[]; app.cart.save(); window.location.hash='#home';
    }
};
