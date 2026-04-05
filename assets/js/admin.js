/** Kitchen 86 - Admin Panel MPA logic */
window.adminApp = {
    revenueChart: null,
    itemsCache: [], // Local cache for actions

    init() { this.route(); },
    
    // Resolve image paths for admin subdirectory
    resolveImg(path) {
        if(!path) return '../assets/images/placeholder.jpg';
        if(path.startsWith('http') || path.startsWith('../')) return path;
        return '../' + path;
    },

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
                const el = document.getElementById('tbl-items');
                if(!el) return;
                let r = await fetch('../api/admin.php?resource=items');
                if(r.ok) {
                    let j = await r.json();
                    if(j.success && j.data) {
                        this.itemsCache = j.data;
                        el.innerHTML = j.data.map(item => this.itemRowTemplate(item)).join('');
                    }
                }
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
                    <img src="${this.resolveImg(i.image)}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(i.name)}&background=161616&color=ff2020'">
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

    // --- Helpers ---    // --- Menu Items Management ---

    async openAddItemModal() {
        // Fetch Categories first
        let catOptions = '<option value="">Select Category</option>';
        try {
            let r = await fetch('../api/admin.php?resource=categories');
            let j = await r.json();
            if(j.success && j.data) {
                catOptions = j.data.map(c => `<option value="${c.id}">${c.icon} ${c.name}</option>`).join('');
            }
        } catch(e) { console.error('Error fetching categories', e); }

        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-red/10 rounded-full flex items-center justify-center text-k86-red">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white">Add New Menu Item</h3>
                        <p class="text-xs text-k86-muted">Create a new culinary masterpiece</p>
                    </div>
                </div>

                <form id="form-add-item" class="space-y-4" onsubmit="adminApp.saveMenuItem(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Item Name</label>
                            <input type="text" name="name" required placeholder="e.g. Matka Fries Deluxe" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Category</label>
                            <select name="category_id" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                                ${catOptions}
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Price (PKR)</label>
                            <input type="number" name="price" required placeholder="450" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Item Image</label>
                            <input type="file" name="item_image" accept="image/*" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none file:bg-k86-surface2 file:border-0 file:text-white file:text-xs file:font-bold file:px-3 file:py-1 file:rounded-lg file:mr-4 file:hover:bg-k86-border file:transition-colors cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Describe the flavors..." class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                        <span class="text-sm text-k86-muted font-bold uppercase tracking-wider">Available immediately</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-red/20 shadow-inner">Save Item</button>
                    </div>
                </form>
            </div>
        `;
        
        this.openModal('Add Item', formHtml);
    },

    async viewMenuItem(id) {
        const item = this.itemsCache.find(i => i.id == id);
        if(!item) return;

        const detailHtml = `
            <div class="space-y-6">
                <div class="relative w-full h-56 rounded-2xl overflow-hidden border border-k86-border shadow-2xl">
                    <img src="${this.resolveImg(item.image)}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-k86-red text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">${item.category_name}</span>
                        <h3 class="text-2xl font-heading font-bold text-white mt-1 uppercase">${item.name}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-k86-surface2 p-4 rounded-xl border border-k86-border">
                        <p class="text-[10px] uppercase font-bold text-k86-muted mb-1">Price</p>
                        <p class="text-xl font-price font-bold text-k86-amber">PKR ${parseFloat(item.price).toLocaleString()}</p>
                    </div>
                    <div class="bg-k86-surface2 p-4 rounded-xl border border-k86-border">
                        <p class="text-[10px] uppercase font-bold text-k86-muted mb-1">Availability</p>
                        <p class="text-base font-bold ${item.is_available ? 'text-k86-success' : 'text-k86-danger'}">${item.is_available ? 'Available' : 'Hidden'}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] uppercase font-bold text-k86-muted ml-1">About this item</p>
                    <p class="text-sm text-white/80 leading-relaxed bg-k86-black p-4 rounded-xl border border-k86-border italic">
                        "${item.description || 'No description provided for this item.'}"
                    </p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button onclick="adminApp.closeModal()" class="w-full px-6 py-3 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest border border-k86-border">Done</button>
                    <button onclick="adminApp.editMenuItem(${item.id})" class="w-full px-6 py-3 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-red/20">Edit Item</button>
                </div>
            </div>
        `;
        this.openModal('Product Details', detailHtml);
    },

    async editMenuItem(id) {
        const item = this.itemsCache.find(i => i.id == id);
        if(!item) return;

        // Fetch Categories
        let catOptions = '';
        try {
            let r = await fetch('../api/admin.php?resource=categories');
            let j = await r.json();
            if(j.success && j.data) {
                catOptions = j.data.map(c => `<option value="${c.id}" ${c.id == item.category_id ? 'selected' : ''}>${c.icon} ${c.name}</option>`).join('');
            }
        } catch(e) { console.error('Error fetching categories', e); }

        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-amber/10 rounded-full flex items-center justify-center text-k86-amber">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white uppercase">Edit Menu Item</h3>
                        <p class="text-xs text-k86-muted tracking-wide">ID: #ITEM-${item.id}</p>
                    </div>
                </div>

                <form id="form-edit-item" class="space-y-4" onsubmit="adminApp.saveMenuItem(event)">
                    <input type="hidden" name="id" value="${item.id}">
                    <input type="hidden" name="existing_image" value="${item.image || ''}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Item Name</label>
                            <input type="text" name="name" value="${item.name}" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Category</label>
                            <select name="category_id" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                                ${catOptions}
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Price (PKR)</label>
                            <input type="number" name="price" value="${item.price}" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Change Image (Optional)</label>
                            <input type="file" name="item_image" accept="image/*" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none file:bg-k86-surface2 file:border-0 file:text-white file:text-xs file:font-bold file:px-3 file:py-1 file:rounded-lg file:mr-4 file:hover:bg-k86-border file:transition-colors cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none resize-none">${item.description}</textarea>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                        <span class="text-sm text-k86-muted font-bold uppercase tracking-wider">Available immediately</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" ${item.is_available ? 'checked' : ''} class="sr-only peer">
                            <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-amber text-white font-bold rounded-xl hover:bg-yellow-600 transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-amber/20 shadow-inner">Update Item</button>
                    </div>
                </form>
            </div>
        `;
        this.openModal('Edit Item', formHtml);
    },

    confirmDelete(id) {
        const item = this.itemsCache.find(i => i.id == id);
        if(!item) return;

        const confirmHtml = `
            <div class="text-center space-y-6 py-4">
                <div class="w-20 h-20 bg-k86-danger/10 rounded-full flex items-center justify-center text-k86-danger mx-auto animate-bounce">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                
                <div>
                    <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Are you absolutely sure?</h3>
                    <p class="text-k86-muted mt-2 px-6">You are about to delete <span class="text-white font-bold">"${item.name}"</span>. This action is irreversible and will remove the item from all menus.</p>
                </div>

                <div class="flex gap-3 px-4">
                    <button onclick="adminApp.closeModal()" class="flex-1 px-6 py-4 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">No, Keep it</button>
                    <button onclick="adminApp.deleteItem(${id})" class="flex-1 px-6 py-4 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-xl shadow-k86-red/40">Yes, Delete Item</button>
                </div>
            </div>
        `;
        this.openModal('Delete Confirmation', confirmHtml);
    },

    async deleteItem(id) {
        try {
            let r = await fetch('../api/admin.php?resource=items', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Deleted successfully', 'success');
                this.closeModal();
                this.fetchData('items');
            }
        } catch(e) { console.error('Delete error', e); }
    },

    async saveMenuItem(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        // Manual override for checkbox if needed (FormData usually includes it only if checked)
        if(!formData.has('is_available')) formData.append('is_available', '0');

        try {
            let r = await fetch('../api/admin.php?resource=items', {
                method: 'POST',
                body: formData // Browser automatically sets multipart/form-data with boundary
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Item added!', 'success');
                this.closeModal();
                this.fetchData('items'); // Refresh table
            } else {
                if(window.Toast) Toast.show(j.message || 'Error saving item', 'error');
            }
        } catch(e) {
            console.error('Save error', e);
            if(window.Toast) Toast.show('Connection error', 'error');
        }
    },

    // --- Helpers ---

    openModal(title, content) { 
        const modal = document.getElementById('sys-modal');
        const modalContent = document.getElementById('modal-content');
        if(!modal || !modalContent) return;
        
        modalContent.innerHTML = content || `<h3 class="text-xl font-heading mb-4 text-k86-amber">${title}</h3><p class="text-k86-muted">MVP Action Setup Here</p>`;
        modal.classList.remove('hidden'); // Fallback visibility
    },

    closeModal() {
        // Animation and close logic is handled by footer.php wrapper around adminApp.closeModal
    },

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
        return `
            <tr class="group hover:bg-k86-surface2/50 transition-colors">
                <td class="p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-k86-black border border-k86-border shrink-0">
                            <img src="${this.resolveImg(i.image)}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(i.name)}&background=161616&color=ff2020'">
                        </div>
                        <div>
                            <p class="font-bold text-white group-hover:text-k86-red transition-colors">${i.name}</p>
                            <p class="text-[10px] text-k86-muted uppercase font-bold tracking-tighter">${i.description ? i.description.substring(0, 30) + '...' : 'No description'}</p>
                        </div>
                    </div>
                </td>
                <td class="p-4 text-k86-muted font-semibold text-xs">${i.category_name || 'Uncategorized'}</td>
                <td class="p-4 text-k86-amber font-price text-lg font-bold">PKR ${parseFloat(i.price).toLocaleString()}</td>
                <td class="p-4">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${i.is_available ? 'bg-k86-success/10 text-k86-success border-k86-success/20' : 'bg-k86-danger/10 text-k86-danger border-k86-danger/20'}">
                        ${i.is_available ? 'Available' : 'Hidden'}
                    </span>
                </td>
                <td class="p-4 text-right space-x-1">
                    <button onclick="adminApp.viewMenuItem(${i.id})" title="View Details" class="text-k86-muted hover:text-white transition-colors p-2 rounded-lg hover:bg-k86-surface2 cursor-pointer">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                    <button onclick="adminApp.editMenuItem(${i.id})" title="Edit Item" class="text-k86-muted hover:text-k86-amber transition-colors p-2 rounded-lg hover:bg-k86-amber/10 cursor-pointer">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button onclick="adminApp.confirmDelete(${i.id})" title="Delete Item" class="text-k86-danger/50 hover:text-k86-danger transition-colors p-2 rounded-lg hover:bg-k86-danger/10 cursor-pointer">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </td>
            </tr>
        `;
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
