window.OrdersInterface = {
    orders: [],
    async init() {
        await this.loadOrders();
    },
    async loadOrders() {
        const list = document.getElementById('orders-list');
        if (!list) return;
        
        list.innerHTML = `<div class="flex flex-col items-center justify-center py-12 text-k86-muted">
            <div class="w-16 h-16 border-4 border-k86-red/20 border-t-k86-red rounded-full animate-spin mb-4"></div>
            <p class="font-bold uppercase tracking-widest text-xs animate-pulse">Fetching your orders...</p>
        </div>`;

        try {
            const r = await fetch('api/orders.php?my');
            const d = await r.json();
            if (d.success) {
                this.orders = d.data || [];
                this.renderOrders();
            } else {
                list.innerHTML = `<div class="text-center py-12 border border-dashed border-k86-border rounded-2xl">
                    <p class="text-k86-muted font-bold uppercase tracking-widest text-xs">${d.message || 'No orders found'}</p>
                </div>`;
            }
        } catch (e) {
            list.innerHTML = `<div class="text-center py-12 border border-dashed border-k86-border rounded-2xl">
                <p class="text-k86-red font-bold uppercase tracking-widest text-xs">Failed to load orders. Please try again.</p>
            </div>`;
        }
    },
    renderOrders() {
        const list = document.getElementById('orders-list');
        if (!list) return;

        if (this.orders.length === 0) {
            list.innerHTML = `<div class="text-center py-20 bg-k86-surface/50 border border-k86-border rounded-3xl reveal">
                <div class="w-20 h-20 bg-k86-surface2 rounded-full flex items-center justify-center mx-auto mb-6 text-k86-muted">
                    <i class="fa-solid fa-receipt text-3xl"></i>
                </div>
                <h3 class="text-2xl font-display text-white mb-2 uppercase tracking-wide">No Orders Yet</h3>
                <p class="text-k86-muted max-w-xs mx-auto mb-8 font-heading text-sm">You haven't placed any orders yet. Head over to our menu and grab something delicious!</p>
                <a href="#home" onclick="setTimeout(()=>window.scrollToSection('menu-section'), 100)" class="inline-block bg-k86-red px-8 py-3 text-white font-bold rounded-xl text-sm transition-all hover:scale-110 hover:shadow-[0_0_15px_rgba(255,32,32,0.4)]">GO TO MENU</a>
            </div>`;
            return;
        }

        list.innerHTML = this.orders.map(o => {
            const status = o.status.toLowerCase();
            const color = this.getStatusColor(status);
            const icon = this.getStatusIcon(status);
            const date = new Date(o.created_at).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' });
            
            return `
            <div class="bg-k86-surface border border-k86-border rounded-2xl overflow-hidden hover:border-k86-red/30 transition-all duration-300 group reveal">
                <div class="p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-k86-black rounded-xl flex items-center justify-center border border-k86-border text-k86-red group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-box-open text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-k86-muted uppercase font-bold tracking-widest">Order ID: #ORD-${o.id}</p>
                            <h4 class="text-lg font-bold text-white uppercase tracking-tight truncate max-w-[200px]">${date}</h4>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <div class="flex flex-col items-start md:items-end mr-4">
                            <p class="text-[10px] text-k86-muted uppercase font-bold tracking-widest">Amount Paid</p>
                            <p class="text-xl font-price font-bold text-k86-amber">Rs ${parseFloat(o.total).toLocaleString()}</p>
                        </div>
                        <span class="flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border ${color} bg-black/20">
                            <i class="${icon} text-xs"></i>
                            ${status}
                        </span>
                        <button onclick="OrdersInterface.viewOrderDetails(${o.id})" class="ml-auto md:ml-0 bg-k86-surface2 hover:bg-k86-red px-5 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all active:scale-95 border border-k86-border">Details</button>
                    </div>
                </div>
            </div>`;
        }).join('');
        
        // Trigger reveal animation for new content
        if(window.observeElements) window.observeElements();
    },
    async viewOrderDetails(id) {
        if(window.Toast) window.Toast.show('Fetching order details...', 'info');
        
        try {
            const r = await fetch(`api/orders.php?id=${id}`);
            const d = await r.json();
            
            if(d.success && d.data) {
                this.showDetailsModal(d.data);
            } else {
                if(window.Toast) window.Toast.show(d.message || 'Failed to load details', 'error');
            }
        } catch (e) {
            if(window.Toast) window.Toast.show('Network error or session expired.', 'error');
        }
    },
    showDetailsModal(o) {
        // We can reuse the Auth modal container or create a new one. 
        // Let's check if there's a generic modal in index.php. 
        // According to index.php view, there isn't one other than auth-modal.
        // I will create a dynamic modal div if it doesn't exist.
        
        let modal = document.getElementById('orders-modal');
        if(!modal) {
            modal = document.createElement('div');
            modal.id = 'orders-modal';
            modal.className = 'fixed inset-0 z-[70] hidden bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300 opacity-0';
            modal.innerHTML = `
                <div class="bg-k86-surface p-6 md:p-8 rounded-3xl w-full max-w-2xl relative border border-k86-border shadow-[0_0_50px_rgba(0,0,0,0.8)] transform scale-95 transition-all duration-300 overflow-y-auto max-h-[90vh] custom-scrollbar" id="orders-modal-content">
                    <div id="orders-modal-body"></div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        const status = o.status.toLowerCase();
        const color = this.getStatusColor(status);
        const icon = this.getStatusIcon(status);
        
        const itemRows = o.items.map(i => `
            <div class="flex justify-between items-center py-3 border-b border-k86-border/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-k86-surface2 rounded-lg flex items-center justify-center border border-k86-border text-xs font-bold text-k86-muted">${i.quantity}x</div>
                    <div>
                        <p class="text-white font-bold text-sm uppercase">${i.item_name}</p>
                        <p class="text-[10px] text-k86-muted uppercase font-bold tracking-widest">${i.item_type || 'Item'}</p>
                    </div>
                </div>
                <p class="text-k86-amber font-price font-bold">Rs ${parseFloat(i.item_price).toLocaleString()}</p>
            </div>
        `).join('');

        const body = document.getElementById('orders-modal-body');
        body.innerHTML = `
            <button onclick="OrdersInterface.closeModal()" class="absolute top-4 right-4 text-k86-muted hover:text-white transition-colors bg-k86-black p-2 rounded-full border border-k86-border">
                <i class="fa-solid fa-xmark w-5 h-5 flex items-center justify-center"></i>
            </button>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-k86-border pb-6 mb-6 gap-4">
                <div>
                    <h3 class="text-3xl font-display text-white tracking-wide uppercase">ORDER #ORD-${o.id}</h3>
                    <p class="text-xs text-k86-muted font-bold uppercase tracking-widest mt-1">${new Date(o.created_at).toLocaleString()}</p>
                </div>
                <span class="flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest border ${color} bg-black/20">
                    <i class="${icon} text-sm"></i>
                    ${status}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-k86-black/40 p-4 rounded-2xl border border-k86-border/50">
                    <h5 class="text-[10px] text-k86-red font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user-tag text-[10px]"></i> Customer Details
                    </h5>
                    <p class="text-white font-bold text-sm">${o.customer_name}</p>
                    <p class="text-xs text-k86-muted mt-1">${o.phone}</p>
                    <p class="text-[10px] text-k86-amber font-bold mt-2 uppercase italic">${o.payment_method} | ${o.order_type}</p>
                </div>
                <div class="bg-k86-black/40 p-4 rounded-2xl border border-k86-border/50">
                    <h5 class="text-[10px] text-k86-red font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-[10px]"></i> Delivery Address
                    </h5>
                    <p class="text-xs text-k86-muted leading-relaxed">${o.address || 'Pick up from restaurant'}</p>
                </div>
            </div>

            <div class="mb-8">
                <h5 class="text-[10px] text-k86-muted font-bold uppercase tracking-widest border-b border-k86-border pb-2 mb-2">Order Items</h5>
                <div class="max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    ${itemRows}
                </div>
            </div>

            <div class="bg-k86-black p-5 rounded-2xl border border-k86-border shadow-inner">
                <div class="flex justify-between items-center text-xs text-k86-muted uppercase font-bold tracking-widest mb-2">
                    <span>Subtotal</span>
                    <span>Rs ${parseFloat(o.subtotal).toLocaleString()}</span>
                </div>
                <div class="flex justify-between items-center text-xs text-k86-red uppercase font-bold tracking-widest mb-4">
                    <span>Delivery Fee</span>
                    <span>Rs ${parseFloat(o.delivery_fee).toLocaleString()}</span>
                </div>
                <div class="pt-4 border-t border-k86-border/50 flex justify-between items-end">
                    <span class="text-xl font-display text-white uppercase">Grand Total</span>
                    <span class="text-4xl font-price font-bold text-k86-amber drop-shadow-[0_0_10px_rgba(255,184,0,0.3)]">Rs ${parseFloat(o.total).toLocaleString()}</span>
                </div>
            </div>
            
            <div class="mt-8 flex gap-4">
                 <button onclick="OrdersInterface.closeModal()" class="flex-1 bg-k86-surface2 hover:bg-k86-border text-white py-4 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all border border-k86-border active:scale-95">
                    Close Summary
                </button>
            </div>
        `;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            document.getElementById('orders-modal-content').classList.remove('scale-95');
            document.getElementById('orders-modal-content').classList.add('scale-100');
        }, 10);
    },
    closeModal() {
        const modal = document.getElementById('orders-modal');
        const content = document.getElementById('orders-modal-content');
        if (modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    },
    getStatusColor(status) {
        const colors = {
            'pending': 'text-orange-400 border-orange-400/20',
            'confirmed': 'text-blue-400 border-blue-400/20',
            'preparing': 'text-k86-amber border-k86-amber/20',
            'ready': 'text-purple-400 border-purple-400/20',
            'delivered': 'text-green-400 border-green-400/20',
            'cancelled': 'text-red-500 border-red-500/20',
            'out_for_delivery': 'text-orange-500 border-orange-500/20'
        };
        return colors[status] || 'text-k86-muted border-k86-border';
    },
    getStatusIcon(status) {
        const icons = {
            'pending': 'fa-solid fa-clock',
            'confirmed': 'fa-solid fa-check-double',
            'preparing': 'fa-solid fa-fire-burner',
            'ready': 'fa-solid fa-bell',
            'delivered': 'fa-solid fa-house-circle-check',
            'cancelled': 'fa-solid fa-ban',
            'out_for_delivery': 'fa-solid fa-motorcycle'
        };
        return icons[status] || 'fa-solid fa-circle-info';
    }
};
