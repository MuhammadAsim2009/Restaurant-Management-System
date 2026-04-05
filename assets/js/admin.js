/** Kitchen 86 - Admin Panel logic */
const adminApp = {
    init() { this.bind(); this.route(); },
    bind() { window.addEventListener('hashchange', () => this.route()); },
    route() {
        const hash = window.location.hash || '#dashboard';
        const map = {
            '#dashboard': 'tpl-dashboard', '#orders': 'tpl-orders',
            '#items': 'tpl-items', '#deals': 'tpl-deals',
            '#categories': 'tpl-categories', '#users': 'tpl-users'
        };
        const tpl = map[hash] || 'tpl-dashboard';
        const ct = document.getElementById('admin-content');
        ct.innerHTML = document.getElementById(tpl).innerHTML;
        this.fetchData(hash);
    },
    async fetchData(hash) {
        try {
            if(hash === '#dashboard' || !hash) {
                let r = await fetch('/api/admin.php?resource=dashboard'); let j = await r.json();
                document.getElementById('dash-stats').innerHTML = `
                    <div class="p-4 bg-k86-surface2 rounded text-center"><p class="text-3xl text-k86-amber font-price">${j.data.orders_today}</p><p class="text-sm mt-1 text-k86-muted">Orders Today</p></div>
                    <div class="p-4 bg-k86-surface2 rounded text-center"><p class="text-3xl text-k86-amber font-price">Rs ${j.data.revenue_today}</p><p class="text-sm mt-1 text-k86-muted">Revenue Today</p></div>
                    <div class="p-4 bg-k86-surface2 rounded text-center"><p class="text-3xl text-k86-amber font-price">${j.data.pending_orders}</p><p class="text-sm mt-1 text-k86-muted">Pending Orders</p></div>
                    <div class="p-4 bg-k86-surface2 rounded text-center"><p class="text-3xl text-k86-amber font-price">${j.data.total_users}</p><p class="text-sm mt-1 text-k86-muted">Total Users</p></div>
                `;
            } else if (hash === '#users') {
                let r = await fetch('/api/admin.php?resource=users'); let j = await r.json();
                document.getElementById('tbl-users').innerHTML = j.data.map(u => `<tr><td class="p-3">${u.name}</td><td class="p-3">${u.email}</td><td class="p-3">${u.phone||'-'}</td><td class="p-3 uppercase font-bold text-xs ${u.role==='admin'?'text-k86-red':'text-k86-muted'}">${u.role}</td></tr>`).join('');
            } else if (hash === '#items') {
                let r = await fetch('/api/admin.php?resource=items'); let j = await r.json();
                document.getElementById('tbl-items').innerHTML = j.data.map(i => `<tr><td class="p-3">${i.name}</td><td class="p-3">${i.category_name}</td><td class="p-3 text-k86-amber font-price text-lg">Rs ${i.price}</td><td class="p-3">${i.is_available?'Yes':'No'}</td></tr>`).join('');
            } else if (hash === '#orders') {
                let r = await fetch('/api/orders.php?admin=1'); let j = await r.json();
                document.getElementById('tbl-orders').innerHTML = j.data.map(o => `<tr class="border-b border-k86-border/50"><td class="p-3">#${o.id}</td><td class="p-3">${o.customer_name}</td><td class="p-3 uppercase text-xs">${o.order_type}</td><td class="p-3 font-price text-k86-amber">Rs ${o.total}</td><td class="p-3 uppercase text-xs font-bold text-k86-warning">${o.status}</td></tr>`).join('');
            } else if (hash === '#categories') {
                let r = await fetch('/api/admin.php?resource=categories'); let j = await r.json();
                document.getElementById('list-categories').innerHTML = j.data.map(c => `<li class="bg-k86-surface2 p-3 rounded flex justify-between"><span>${c.icon} ${c.name}</span><span class="text-k86-muted text-sm">Order: ${c.display_order}</span></li>`).join('');
            }
        } catch(e) { console.error('API Error', e); }
    },
    openModal(title) { document.getElementById('sys-modal').classList.remove('hidden'); document.getElementById('modal-content').innerHTML = `<h3 class="text-xl font-heading mb-4 text-k86-amber">${title}</h3><p class="text-k86-muted">MVP Action Setup Here</p>`; },
    closeModal() { document.getElementById('sys-modal').classList.add('hidden'); },
    logout() { fetch('/api/auth.php?action=logout').then(() => window.location.href = '/'); }
};
document.addEventListener('DOMContentLoaded', () => adminApp.init());
