/** Kitchen 86 - Admin Panel MPA logic */
const adminApp = {
    revenueChart: null,

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
                    if(j.success && j.data) {
                        this.renderDashboardStats(j.data);
                        this.renderTopItems(j.data.top_items);
                        this.initRevenueChart(j.data.chart_data);
                    }
                }
                
                // Fetch recent orders for dashboard
                let ro = await fetch('../api/orders.php?admin=1');
                if(ro.ok) {
                    let rj = await ro.json();
                    if(rj.success && rj.data) {
                        this.renderRecentOrders(rj.data.slice(0, 5));
                    }
                }

            } else if (page === 'users') {
                this.fetchTableData('../api/admin.php?resource=users', 'tbl-users', this.userRowTemplate);
            } else if (page === 'items') {
                this.fetchTableData('../api/admin.php?resource=items', 'tbl-items', this.itemRowTemplate);
            } else if (page === 'orders') {
                this.fetchTableData('../api/orders.php?admin=1', 'tbl-orders', this.orderRowTemplate);
            } else if (page === 'categories') {
                this.fetchTableData('../api/admin.php?resource=categories', 'list-categories', this.categoryItemTemplate);
            }
        } catch(e) { console.error('API Error', e); }
    },

    // --- Dashboard Renderers ---

    renderDashboardStats(data) {
        const grid = document.getElementById('dash-stats-grid');
        if(!grid) return;

        grid.innerHTML = `
            <!-- Today's Orders -->
            <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl group hover:border-k86-red/30 transition-all duration-300 relative overflow-hidden">
                <p class="text-k86-muted text-sm font-semibold uppercase tracking-wider">Today's Orders</p>
                <div class="flex items-end gap-3 mt-2">
                    <h2 class="text-4xl font-price font-bold text-white leading-none">${data.orders_today}</h2>
                </div>
                <div class="mt-4 h-1 w-full bg-k86-border rounded-full overflow-hidden">
                    <div class="h-full bg-linear-to-r from-k86-red to-k86-red-dark" style="width: ${Math.min(data.orders_today * 5, 100)}%"></div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl group hover:border-k86-amber/30 transition-all duration-300 relative overflow-hidden">
                <p class="text-k86-muted text-sm font-semibold uppercase tracking-wider">Revenue Today</p>
                <div class="flex items-end gap-2 mt-2">
                    <h2 class="text-4xl font-price font-bold text-k86-amber leading-none"><span class="text-xl mr-1 font-body">PKR</span>${parseFloat(data.revenue_today).toLocaleString()}</h2>
                </div>
                <p class="text-[10px] text-k86-muted mt-4 uppercase font-bold tracking-widest">Real-time Earnings</p>
            </div>

            <!-- Pending Orders -->
            <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl group hover:border-k86-warning/30 transition-all duration-300 relative overflow-hidden">
                <p class="text-k86-muted text-sm font-semibold uppercase tracking-wider">Pending Orders</p>
                <div class="flex items-end gap-3 mt-2">
                    <h2 class="text-4xl font-price font-bold text-white leading-none">${data.pending_orders}</h2>
                    ${data.pending_orders > 0 ? '<span class="text-k86-danger text-[10px] font-bold bg-k86-danger/10 px-2 py-0.5 rounded-full mb-1 animate-pulse">Needs Action</span>' : ''}
                </div>
                <div class="mt-4 flex gap-1">
                    <div class="h-1 flex-1 ${data.pending_orders > 0 ? 'bg-k86-red' : 'bg-k86-border'} rounded-full"></div>
                    <div class="h-1 flex-1 bg-k86-border rounded-full"></div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-k86-surface border border-k86-border p-6 rounded-2xl group hover:border-white/10 transition-all duration-300 relative overflow-hidden">
                <p class="text-k86-muted text-sm font-semibold uppercase tracking-wider">Total Customers</p>
                <div class="flex items-end gap-3 mt-2">
                    <h2 class="text-4xl font-price font-bold text-white leading-none">${data.total_users}</h2>
                </div>
                <p class="text-[10px] text-k86-muted mt-4 uppercase font-bold tracking-widest">Registered Growth</p>
            </div>
        `;
    },

    renderTopItems(items) {
        const container = document.getElementById('dash-top-items');
        if(!container) return;

        if(!items || items.length === 0) {
            container.innerHTML = '<div class="text-center py-10 text-k86-muted text-sm">No sales data yet.</div>';
            return;
        }

        container.innerHTML = items.map(i => `
            <div class="flex items-center gap-4 p-3 bg-k86-surface2 rounded-2xl hover:bg-k86-surface border border-transparent hover:border-k86-red/20 transition-all group">
                <div class="w-12 h-12 bg-k86-black rounded-xl overflow-hidden shrink-0 border border-k86-border group-hover:border-k86-red/50 transition-colors">
                    <img src="${i.image || '../assets/images/placeholder.jpg'}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(i.name)}&background=161616&color=ff2020'">
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-white truncate text-sm">${i.name}</h4>
                    <p class="text-k86-muted text-[10px] font-bold uppercase mt-1">${i.sold_count} Sold</p>
                </div>
                <div class="text-right">
                    <p class="font-price font-bold text-k86-amber">PKR ${parseFloat(i.price).toLocaleString()}</p>
                </div>
            </div>
        `).join('');
    },

    renderRecentOrders(orders) {
        const tbody = document.getElementById('dash-recent-orders');
        if(!tbody) return;

        if(!orders || orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="p-10 text-center text-k86-muted">No orders found.</td></tr>';
            return;
        }

        tbody.innerHTML = orders.map(o => `
            <tr class="hover:bg-k86-surface2/50 transition-colors group">
                <td class="p-6 font-price font-bold text-white">#ORD-${o.id}</td>
                <td class="p-6">
                    <p class="font-bold text-white">${o.customer_name}</p>
                    <p class="text-[10px] text-k86-muted">${o.phone}</p>
                </td>
                <td class="p-6"><span class="text-white bg-k86-surface border border-k86-border px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">${o.order_type}</span></td>
                <td class="p-6 font-price font-bold text-k86-amber">PKR ${parseFloat(o.total).toLocaleString()}</td>
                <td class="p-6">${this.getStatusBadge(o.status)}</td>
                <td class="p-6 text-center">
                    <a href="orders.php?id=${o.id}" class="p-2 text-k86-muted hover:text-k86-red transition-colors inline-block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </a>
                </td>
            </tr>
        `).join('');
    },

    initRevenueChart(chartData) {
        const ctx = document.getElementById('revenueChart');
        if(!ctx) return;

        const loader = document.getElementById('chart-loader');
        if(loader) loader.classList.add('hidden');

        // Process Data
        const labels = chartData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('en-US', { weekday: 'short' });
        });
        const values = chartData.map(d => parseFloat(d.revenue));
        
        const totalRev = values.reduce((a, b) => a + b, 0);
        const totalRevEl = document.getElementById('chart-total-rev');
        if(totalRevEl) totalRevEl.innerText = `PKR ${totalRev.toLocaleString()}`;

        // Create Gradient
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(255, 32, 32, 0.4)');
        gradient.addColorStop(1, 'rgba(255, 32, 32, 0)');

        if(this.revenueChart) this.revenueChart.destroy();

        this.revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [{
                    label: 'Revenue (PKR)',
                    data: values.length > 0 ? values : [0],
                    borderColor: '#FF2020',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    pointBackgroundColor: '#FFB800',
                    pointBorderColor: '#161616',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#222222',
                        titleColor: '#AAAAAA',
                        bodyColor: '#FFFFFF',
                        borderColor: '#2A2A2A',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'PKR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#2A2A2A', drawBorder: false },
                        ticks: { 
                            color: '#AAAAAA', 
                            font: { family: 'Barlow Condensed', size: 12 },
                            callback: function(value) { return 'PKR ' + (value >= 1000 ? (value/1000) + 'k' : value); }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#AAAAAA', font: { family: 'Barlow', size: 12 } }
                    }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    },

    // --- Helpers ---

    getStatusBadge(status) {
        const colors = {
            'pending': 'bg-orange-500/10 text-orange-500 border-orange-500/20',
            'confirmed': 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            'preparing': 'bg-k86-amber/10 text-k86-amber border-k86-amber/20',
            'ready': 'bg-purple-500/10 text-purple-500 border-purple-500/20',
            'delivered': 'bg-k86-success/10 text-k86-success border-k86-success/20',
            'cancelled': 'bg-k86-danger/10 text-k86-danger border-k86-danger/20'
        };
        const cls = colors[status.toLowerCase()] || 'bg-k86-muted/10 text-k86-muted border-k86-muted/20';
        return `<span class="px-3 py-1 ${cls} rounded-full text-[10px] font-bold border uppercase tracking-wider">${status}</span>`;
    },

    async fetchTableData(url, targetId, templateFn) {
        const el = document.getElementById(targetId);
        if(!el) return;
        let r = await fetch(url);
        if(r.ok) {
            let j = await r.json();
            if(j.success && j.data) {
                el.innerHTML = j.data.map(item => templateFn.call(this, item)).join('');
            }
        }
    },

    // Row Templates for other pages
    userRowTemplate(u) {
        return `<tr><td class="p-4">${u.name}</td><td class="p-4 text-k86-muted">${u.email}</td><td class="p-4">${u.phone||'-'}</td><td class="p-4"><span class="uppercase font-bold text-xs ${u.role==='admin'?'text-k86-red bg-k86-red/10 px-2 py-1 rounded-full':'text-k86-muted'}">${u.role}</span></td></tr>`;
    },
    itemRowTemplate(i) {
        return `<tr class="group hover:bg-k86-surface2/50 transition-colors"><td class="p-4 font-bold text-white group-hover:text-k86-red transition-colors">${i.name}</td><td class="p-4 text-k86-muted">${i.category_name}</td><td class="p-4 text-k86-amber font-price text-lg">PKR ${i.price}</td><td class="p-4"><span class="px-2 py-1 rounded px-2 py-1 text-xs font-bold ${i.is_available ? 'bg-k86-success/10 text-k86-success' : 'bg-k86-danger/10 text-k86-danger'}">${i.is_available ? 'Available' : 'Hidden'}</span></td><td class="p-4 text-right"><button class="text-k86-muted hover:text-white transition-colors">Edit</button></td></tr>`;
    },
    orderRowTemplate(o) {
        return `<tr class="hover:bg-k86-surface2/50 transition-colors group"><td class="p-4 font-bold text-white group-hover:text-k86-red transition-colors">#ORD-${o.id}</td><td class="p-4">${o.customer_name}</td><td class="p-4"><span class="text-k86-muted bg-k86-surface border border-k86-border px-2 py-1 rounded text-xs uppercase">${o.order_type}</span></td><td class="p-4 font-price text-k86-amber tracking-wide text-base">PKR ${o.total}</td><td class="p-4">${this.getStatusBadge(o.status)}</td><td class="p-4 text-right"><button class="text-k86-red hover:text-k86-red-dark font-bold text-xs uppercase tracking-wider">Update</button></td></tr>`;
    },
    categoryItemTemplate(c) {
        return `<li class="bg-k86-surface2 p-4 rounded-lg flex justify-between items-center group hover:border-k86-red border border-transparent transition-colors"><span class="font-bold text-white text-lg"><span class="text-2xl mr-2">${c.icon}</span> ${c.name}</span><span class="text-k86-muted text-sm bg-k86-black px-3 py-1 rounded-full border border-k86-border">Order: ${c.display_order}</span></li>`;
    },

    logout() { fetch('../api/auth.php?action=logout').then(() => window.location.href = '../'); }
};

document.addEventListener('DOMContentLoaded', () => adminApp.init());
