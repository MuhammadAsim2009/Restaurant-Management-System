/** Kitchen 86 - Admin Panel MPA logic */
const adminApp = {
    init() { this.route(); },
    route() {
        const pageEl = document.querySelector('[data-page]');
        if(!pageEl) return;
        
        const page = pageEl.getAttribute('data-page');
        this.fetchData(page);
    },
    async fetchData(page) {
        try {
            if(page === 'dashboard') {
                let r = await fetch('../api/admin.php?resource=dashboard'); 
                if(r.ok) {
                    let j = await r.json();
                    if(j.data) {
                        document.getElementById('dash-stats').innerHTML = `
                            <div class="bg-k86-surface p-5 rounded-xl border border-k86-border hover:border-k86-red/50 transition-colors">
                                <p class="text-k86-muted text-sm font-semibold mb-1">Today's Orders</p>
                                <h3 class="text-3xl font-display text-white">${j.data.orders_today}</h3>
                            </div>
                            <div class="bg-k86-surface p-5 rounded-xl border border-k86-border hover:border-k86-amber/50 transition-colors">
                                <p class="text-k86-muted text-sm font-semibold mb-1">Revenue Today</p>
                                <h3 class="text-3xl font-price text-k86-amber tracking-wide">PKR ${j.data.revenue_today}</h3>
                            </div>
                            <div class="bg-k86-surface p-5 rounded-xl border border-k86-border hover:border-k86-warning/50 transition-colors">
                                <p class="text-k86-muted text-sm font-semibold mb-1">Pending Orders</p>
                                <h3 class="text-3xl font-display text-white">${j.data.pending_orders}</h3>
                            </div>
                            <div class="bg-k86-surface p-5 rounded-xl border border-k86-border hover:border-white/20 transition-colors">
                                <p class="text-k86-muted text-sm font-semibold mb-1">Total Users</p>
                                <h3 class="text-3xl font-display text-white">${j.data.total_users}</h3>
                            </div>
                        `;
                    }
                }
                
                // Fetch recent orders for dashboard
                let ro = await fetch('../api/orders.php?admin=1');
                if(ro.ok) {
                    let rj = await ro.json();
                    if(rj.data && document.getElementById('dash-recent-orders')) {
                        // Take top 5 recent orders
                        const recent = rj.data.slice(0, 5);
                        document.getElementById('dash-recent-orders').innerHTML = recent.map(o => `
                            <tr class="hover:bg-k86-surface2/50 transition-colors group">
                                <td class="p-4 font-bold text-white group-hover:text-k86-red transition-colors">#ORD-${o.id}</td>
                                <td class="p-4">${o.customer_name}</td>
                                <td class="p-4"><span class="text-k86-muted bg-k86-surface border border-k86-border px-2 py-1 rounded text-xs uppercase">${o.order_type}</span></td>
                                <td class="p-4 font-price text-k86-amber tracking-wide text-base">PKR ${o.total}</td>
                                <td class="p-4"><span class="bg-k86-surface2 text-k86-white border border-k86-border px-3 py-1 rounded-full text-xs font-bold uppercase">${o.status}</span></td>
                            </tr>
                        `).join('');
                        if(recent.length === 0) document.getElementById('dash-recent-orders').innerHTML = `<tr><td colspan="5" class="p-4 text-center text-k86-muted">No recent orders found.</td></tr>`;
                    }
                }

            } else if (page === 'users') {
                let r = await fetch('../api/admin.php?resource=users'); 
                if(r.ok) {
                    let j = await r.json();
                    document.getElementById('tbl-users').innerHTML = j.data.map(u => `
                        <tr>
                            <td class="p-4">${u.name}</td>
                            <td class="p-4 text-k86-muted">${u.email}</td>
                            <td class="p-4">${u.phone||'-'}</td>
                            <td class="p-4"><span class="uppercase font-bold text-xs ${u.role==='admin'?'text-k86-red bg-k86-red/10 px-2 py-1 rounded-full':'text-k86-muted'}">${u.role}</span></td>
                        </tr>
                    `).join('');
                }
            } else if (page === 'items') {
                let r = await fetch('../api/admin.php?resource=items');
                if(r.ok) {
                    let j = await r.json();
                    document.getElementById('tbl-items').innerHTML = j.data.map(i => `
                        <tr class="group hover:bg-k86-surface2/50 transition-colors">
                            <td class="p-4 font-bold text-white group-hover:text-k86-red transition-colors">${i.name}</td>
                            <td class="p-4 text-k86-muted">${i.category_name}</td>
                            <td class="p-4 text-k86-amber font-price text-lg">PKR ${i.price}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded px-2 py-1 text-xs font-bold ${i.is_available ? 'bg-k86-success/10 text-k86-success' : 'bg-k86-danger/10 text-k86-danger'}">${i.is_available ? 'Available' : 'Hidden'}</span>
                            </td>
                            <td class="p-4 text-right">
                                <button class="text-k86-muted hover:text-white transition-colors">Edit</button>
                            </td>
                        </tr>
                    `).join('');
                }
            } else if (page === 'orders') {
                let r = await fetch('../api/orders.php?admin=1');
                if(r.ok) {
                    let j = await r.json();
                    document.getElementById('tbl-orders').innerHTML = j.data.map(o => `
                        <tr class="hover:bg-k86-surface2/50 transition-colors group">
                            <td class="p-4 font-bold text-white group-hover:text-k86-red transition-colors">#ORD-${o.id}</td>
                            <td class="p-4">${o.customer_name}</td>
                            <td class="p-4"><span class="text-k86-muted bg-k86-surface border border-k86-border px-2 py-1 rounded text-xs uppercase">${o.order_type}</span></td>
                            <td class="p-4 font-price text-k86-amber tracking-wide text-base">PKR ${o.total}</td>
                            <td class="p-4"><span class="bg-k86-surface2 text-k86-white border border-k86-border px-3 py-1 rounded-full text-xs font-bold uppercase">${o.status}</span></td>
                            <td class="p-4 text-right">
                                <button class="text-k86-red hover:text-k86-red-dark font-bold text-xs uppercase tracking-wider">Update</button>
                            </td>
                        </tr>
                    `).join('');
                }
            } else if (page === 'categories') {
                let r = await fetch('../api/admin.php?resource=categories');
                if(r.ok) {
                    let j = await r.json();
                    document.getElementById('list-categories').innerHTML = j.data.map(c => `
                        <li class="bg-k86-surface2 p-4 rounded-lg flex justify-between items-center group hover:border-k86-red border border-transparent transition-colors">
                            <span class="font-bold text-white text-lg"><span class="text-2xl mr-2">${c.icon}</span> ${c.name}</span>
                            <span class="text-k86-muted text-sm bg-k86-black px-3 py-1 rounded-full border border-k86-border">Order: ${c.display_order}</span>
                        </li>
                    `).join('');
                }
            }
        } catch(e) { console.error('API Error', e); }
    },
    openModal(title) { document.getElementById('sys-modal').classList.remove('hidden'); document.getElementById('modal-content').innerHTML = `<h3 class="text-xl font-heading mb-4 text-k86-amber">${title}</h3><p class="text-k86-muted">MVP Action Setup Here</p>`; },
    closeModal() { document.getElementById('sys-modal').classList.add('hidden'); },
    logout() { fetch('../api/auth.php?action=logout').then(() => window.location.href = '../'); }
};
document.addEventListener('DOMContentLoaded', () => adminApp.init());
