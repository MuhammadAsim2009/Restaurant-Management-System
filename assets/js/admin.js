/** Kitchen 86 - Admin Panel MPA logic */
window.adminApp = {
    revenueChart: null,
    itemsCache: [], // Local cache for products
    usersCache: [], // Local cache for users
    dealsCache: [], // Local cache for deals
    categoriesCache: [], // Local cache for categories
    faIconsCache: null,   // Font Awesome full icon metadata cache
    faIconsLoading: false, // FA fetch in-flight flag

    init() { this.initPriceSync(); this.route(); },
    
    renderTableSkeleton(tbodyId, columns = 6, rows = 5) {
        const tbody = document.getElementById(tbodyId);
        if(!tbody) return;
        
        let html = '';
        for(let i = 0; i < rows; i++) {
            html += `<tr class="border-b border-k86-border/50">`;
            for(let j = 0; j < columns; j++) {
                html += `
                    <td class="p-6">
                        <div class="h-4 skeleton-loader rounded-lg w-${j === 0 ? '16' : (j === 1 ? '32' : '24')} opacity-20"></div>
                    </td>
                `;
            }
            html += `</tr>`;
        }
        tbody.innerHTML = html;
    },

    // Global listener for dynamic deal checkboxes
    initPriceSync() {
        document.addEventListener('change', (e) => {
            if (e.target.name === 'items_json[]') {
                this.calculateOriginalPrice();
            }
        });
    },

    calculateOriginalPrice() {
        const checkboxes = document.querySelectorAll('input[name="items_json[]"]:checked');
        let total = 0;
        checkboxes.forEach(cb => {
            const itemId = cb.value;
            const item = this.itemsCache.find(i => i.id == itemId);
            if (item) total += parseFloat(item.price || 0);
        });
        
        const originalPriceInput = document.getElementById('deal-original-price');
        if (originalPriceInput) {
            originalPriceInput.value = total;
        }
    },
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
                this.renderTableSkeleton('dash-recent-orders', 6, 5);
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
                this.renderTableSkeleton('tbl-users', 5, 5);
                let r = await fetch('../api/admin.php?resource=users');
                if(r.ok) {
                    let j = await r.json();
                    if(j.success && j.data) {
                        this.usersCache = j.data;
                        document.getElementById('tbl-users').innerHTML = j.data.map(item => this.userRowTemplate(item)).join('');
                    }
                }
            } else if (page === 'items') {
                this.renderTableSkeleton('tbl-items', 6, 5);
                let r = await fetch('../api/admin.php?resource=items');
                if(r.ok) {
                    let j = await r.json();
                    if(j.success && j.data) {
                        this.itemsCache = j.data;
                        document.getElementById('tbl-items').innerHTML = j.data.map(item => this.itemRowTemplate(item)).join('');
                    }
                }
            } else if (page === 'orders') {
                this.renderTableSkeleton('tbl-orders', 6, 8);
                this.fetchTableData('../api/orders.php?admin=1', 'tbl-orders', this.orderRowTemplate);
            } else if (page === 'categories') {
                let r = await fetch('../api/admin.php?resource=categories');
                if(r.ok) {
                    let j = await r.json();
                    if(j.success && j.data) {
                        this.categoriesCache = j.data;
                        this.renderCategoryGrid(j.data);
                    }
                }
            } else if (page === 'deals') {
                this.renderTableSkeleton('tbl-deals', 6, 5);
                let r = await fetch('../api/admin.php?resource=deals');
                if(r.ok) {
                    let j = await r.json();
                    if(j.success && j.data) {
                        this.dealsCache = j.data;
                        document.getElementById('tbl-deals').innerHTML = j.data.map(item => this.dealRowTemplate(item)).join('');
                    }
                }
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                            <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Available</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                            <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Hot Deal?</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-amber"></div>
                            </label>
                        </div>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                            <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Available</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" ${item.is_available ? 'checked' : ''} class="sr-only peer">
                                <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                            <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Hot Deal?</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" ${item.is_featured ? 'checked' : ''} class="sr-only peer">
                                <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-amber"></div>
                            </label>
                        </div>
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
        if(!formData.has('is_featured')) formData.append('is_featured', '0');

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
    
    // --- Deals Management ---
    
    async ensureItemsLoaded() {
        if (!this.itemsCache || !this.itemsCache.length) {
            try {
                let r = await fetch('../api/admin.php?resource=items');
                let j = await r.json();
                if (j.success) this.itemsCache = j.data;
            } catch (e) { console.error('Cache load error', e); }
        }
    },

    async openAddDealModal() {
        await this.ensureItemsLoaded();
        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-red/10 rounded-full flex items-center justify-center text-k86-red">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white">Add New Deal</h3>
                        <p class="text-xs text-k86-muted uppercase tracking-tighter">Create an irresistible offer</p>
                    </div>
                </div>

                <form id="form-add-deal" class="space-y-4" onsubmit="adminApp.saveDeal(event)">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Deal Name</label>
                        <input type="text" name="name" required placeholder="e.g. Matka Combo for 2" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Sale Price (PKR)</label>
                            <input type="number" name="price" required placeholder="850" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Original Price (Strike)</label>
                            <input type="number" name="original_price" id="deal-original-price" placeholder="1100" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price" readonly>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Included Menu Items</label>
                        <div class="w-full bg-k86-black border border-k86-border rounded-xl p-4 max-h-48 overflow-y-auto space-y-2 custom-scrollbar">
                            ${this.itemsCache && this.itemsCache.length ? 
                                this.itemsCache.map(i => `
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="items_json[]" value="${i.id}" class="w-4 h-4 rounded border-k86-border bg-k86-surface text-k86-red focus:ring-k86-red">
                                        <span class="text-sm text-k86-muted group-hover:text-white transition-colors">${i.name} <small class="text-[10px] opacity-50">(PKR ${parseFloat(i.price)})</small></span>
                                    </label>
                                `).join('') : '<p class="text-[10px] text-k86-muted italic text-center py-2">No items found. Go to Menu Manager first.</p>'
                            }
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Valid Until (Optional)</label>
                            <input type="date" name="valid_until" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Deal Image</label>
                        <input type="file" name="deal_image" accept="image/*" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none file:bg-k86-surface2 file:border-0 file:text-white file:text-xs file:font-bold file:px-3 file:py-1 file:rounded-lg file:mr-3 cursor-pointer">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Description</label>
                        <textarea name="description" rows="3" placeholder="What's in the box? (e.g. 2x Zinger, 1x Fries...)" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                        <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Active Status</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-red/20 shadow-inner">Launch Deal</button>
                    </div>
                </form>
            </div>
        `;
        this.openModal('Add New Deal', formHtml);
    },

    async viewDeal(id) {
        const deal = this.dealsCache.find(d => d.id == id);
        if(!deal) return;
        const detailHtml = `
            <div class="space-y-6">
                <div class="relative w-full h-56 rounded-2xl overflow-hidden border border-k86-border shadow-2xl">
                    <img src="${this.resolveImg(deal.image)}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-k86-amber text-k86-black px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Special Deal</span>
                        <h3 class="text-2xl font-heading font-bold text-white mt-1 uppercase">${deal.name}</h3>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-k86-surface2 p-4 rounded-xl border border-k86-border">
                        <p class="text-[10px] uppercase font-bold text-k86-muted mb-1">Sale Price</p>
                        <p class="text-xl font-price font-bold text-k86-amber">PKR ${parseFloat(deal.price).toLocaleString()}</p>
                    </div>
                    <div class="bg-k86-surface2 p-4 rounded-xl border border-k86-border">
                        <p class="text-[10px] uppercase font-bold text-k86-muted mb-1">Original Price</p>
                        <p class="text-xl font-price font-bold text-k86-muted line-through">PKR ${deal.original_price ? parseFloat(deal.original_price).toLocaleString() : '-'}</p>
                    </div>
                </div>
                <p class="text-sm text-white/80 leading-relaxed bg-k86-black p-4 rounded-xl border border-k86-border italic">"${deal.description || 'No description'}"</p>
                <button onclick="adminApp.closeModal()" class="w-full px-6 py-3 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest border border-k86-border">Done</button>
            </div>
        `;
        this.openModal('Deal Preview', detailHtml);
    },

    async editDeal(id) {
        const deal = this.dealsCache.find(d => d.id == id);
        if(!deal) return;
        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-amber/10 rounded-full flex items-center justify-center text-k86-amber">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white uppercase">Update Deal</h3>
                        <p class="text-xs text-k86-muted tracking-wide">ID: #DEAL-${deal.id}</p>
                    </div>
                </div>

                <form id="form-edit-deal" class="space-y-4" onsubmit="adminApp.saveDeal(event)">
                    <input type="hidden" name="id" value="${deal.id}">
                    <input type="hidden" name="existing_image" value="${deal.image || ''}">

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Deal Name</label>
                        <input type="text" name="name" value="${deal.name}" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Sale Price (PKR)</label>
                            <input type="number" name="price" value="${deal.price}" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Original Price</label>
                            <input type="number" name="original_price" id="deal-original-price" value="${deal.original_price || ''}" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price" readonly>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Included Menu Items</label>
                        <div class="w-full bg-k86-black border border-k86-border rounded-xl p-4 max-h-48 overflow-y-auto space-y-2 custom-scrollbar">
                            ${this.itemsCache && this.itemsCache.length ? 
                                this.itemsCache.map(i => {
                                    let itemsInDeal = [];
                                    try { itemsInDeal = JSON.parse(deal.items_json || '[]'); } catch(e) {}
                                    const isChecked = itemsInDeal.includes(String(i.id)) || itemsInDeal.includes(Number(i.id));
                                    return `
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <input type="checkbox" name="items_json[]" value="${i.id}" ${isChecked ? 'checked' : ''} class="w-4 h-4 rounded border-k86-border bg-k86-surface text-k86-red focus:ring-k86-red">
                                            <span class="text-sm text-k86-muted group-hover:text-white transition-colors">${i.name}</span>
                                        </label>
                                    `;
                                }).join('') : '<p class="text-[10px] text-k86-muted italic">No items found.</p>'
                            }
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Valid Until</label>
                            <input type="date" name="valid_until" value="${deal.valid_until || ''}" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Change Image (Optional)</label>
                        <input type="file" name="deal_image" accept="image/*" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none file:bg-k86-surface2 file:border-0 file:text-white file:text-xs file:font-bold file:px-3 file:py-1 file:rounded-lg file:mr-3 cursor-pointer">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none resize-none">${deal.description}</textarea>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-k86-surface2 rounded-xl border border-k86-border">
                        <span class="text-xs text-k86-muted font-bold uppercase tracking-wider">Active Status</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" ${deal.is_active ? 'checked' : ''} class="sr-only peer">
                            <div class="w-11 h-6 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-k86-red"></div>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-amber text-white font-bold rounded-xl hover:bg-yellow-600 transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-amber/20 shadow-inner">Update Deal</button>
                    </div>
                </form>
            </div>
        `;
        this.openModal('Edit Deal', formHtml);
    },

    async saveDeal(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        if(!formData.has('is_active')) formData.append('is_active', '0');
        
        // Convert multi-select checkboxes to items_json array for API
        const selectedItems = Array.from(form.querySelectorAll('input[name="items_json[]"]:checked')).map(cb => cb.value);
        
        // For standard fetch with JSON, we might need to convert FormData
        // But our API handles both. Let's send selectedItems as a field.
        // PHP will see items_json[] as an array in $_POST if we use FormData.
        
        try {
            let r = await fetch('../api/admin.php?resource=deals', { method: 'POST', body: formData });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Deal saved!', 'success');
                this.closeModal();
                this.fetchData('deals');
            } else {
                if(window.Toast) Toast.show(j.message || 'Error saving deal', 'error');
            }
        } catch(e) { console.error('Save deal error', e); }
    },

    confirmDeleteDeal(id) {
        const deal = this.dealsCache.find(d => d.id == id);
        if(!deal) return;
        const confirmHtml = `
            <div class="text-center space-y-6 py-4">
                <div class="w-20 h-20 bg-k86-danger/10 rounded-full flex items-center justify-center text-k86-danger mx-auto animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Delete this Deal?</h3>
                    <p class="text-k86-muted mt-2 px-6">You are deleting <span class="text-white font-bold">"${deal.name}"</span>. This will remove it from the homepage instantly.</p>
                </div>
                <div class="flex gap-3 px-4">
                    <button onclick="adminApp.closeModal()" class="flex-1 px-6 py-4 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Keep it</button>
                    <button onclick="adminApp.deleteDeal(${id})" class="flex-1 px-6 py-4 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-xl shadow-k86-red/40">Yes, Delete</button>
                </div>
            </div>
        `;
        this.openModal('Delete Confirmation', confirmHtml);
    },

    async deleteDeal(id) {
        try {
            let r = await fetch('../api/admin.php?resource=deals', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Deleted successfully', 'success');
                this.closeModal();
                this.fetchData('deals');
            }
        } catch(e) { console.error('Delete error', e); }
    },

    dealRowTemplate(d) {
        return `
            <tr class="group hover:bg-k86-surface2/50 transition-colors">
                <td class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-k86-black border border-k86-border shrink-0">
                            <img src="${this.resolveImg(d.image)}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-white group-hover:text-k86-red transition-colors uppercase tracking-wider">${d.name}</p>
                            <p class="text-[10px] text-k86-muted uppercase font-bold tracking-tighter truncate max-w-[200px]">${d.description || 'No description'}</p>
                        </div>
                    </div>
                </td>
                <td class="p-6">
                    <div class="flex flex-col">
                        <p class="text-k86-amber font-price text-xl font-bold leading-none">PKR ${parseFloat(d.price).toLocaleString()}</p>
                        ${d.original_price ? `<p class="text-[10px] text-k86-muted line-through font-bold mt-1">PKR ${parseFloat(d.original_price).toLocaleString()}</p>` : ''}
                    </div>
                </td>
                <td class="p-6">
                    <div class="flex flex-col gap-1">
                        <span class="text-white font-bold text-xs uppercase tracking-tighter truncate max-w-[150px]">
                            ${(() => {
                                try {
                                    const ids = JSON.parse(d.items_json || '[]');
                                    if(!ids.length) return 'N/A';
                                    const names = ids.map(id => {
                                        const item = this.itemsCache.find(i => i.id == id);
                                        return item ? item.name : null;
                                    }).filter(n => n);
                                    return names.length > 2 ? `${names.slice(0, 2).join(', ')} +${names.length-2} more` : names.join(', ');
                                } catch(e) { return 'N/A'; }
                            })()}
                        </span>
                        <span class="text-[10px] text-k86-muted uppercase font-bold italic">${d.valid_until ? 'Ends: ' + d.valid_until : 'No Expiry'}</span>
                    </div>
                </td>
                <td class="p-6">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${d.is_active ? 'bg-k86-success/10 text-k86-success border-k86-success/20' : 'bg-k86-danger/10 text-k86-danger border-k86-danger/20'}">
                        ${d.is_active ? 'Active' : 'Paused'}
                    </span>
                </td>
                <td class="p-6 text-right space-x-1">
                    <button onclick="adminApp.viewDeal(${d.id})" title="View Details" class="text-k86-muted hover:text-white transition-colors p-2 rounded-lg hover:bg-k86-surface2 cursor-pointer">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </button>
                    <button onclick="adminApp.editDeal(${d.id})" title="Edit Deal" class="text-k86-muted hover:text-k86-amber transition-colors p-2 rounded-lg hover:bg-k86-amber/10 cursor-pointer">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button onclick="adminApp.confirmDeleteDeal(${d.id})" title="Delete Deal" class="text-k86-danger/50 hover:text-k86-danger transition-colors p-2 rounded-lg hover:bg-k86-danger/10 cursor-pointer">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </td>
            </tr>
        `;
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
        const isAdmin = u.role === 'admin';
        return `
            <tr class="hover:bg-k86-surface2/50 transition-colors group">
                <td class="p-4 font-bold text-white">${u.name}</td>
                <td class="p-4 text-k86-muted text-xs truncate max-w-[150px]">${u.email}</td>
                <td class="p-4 text-xs font-mono text-white/70">${u.phone || '-'}</td>
                <td class="p-4">
                    <span class="uppercase font-bold text-[9px] tracking-widest px-3 py-1 rounded-full border ${isAdmin ? 'text-k86-red bg-k86-red/10 border-k86-red/20' : 'text-k86-amber bg-k86-amber/10 border-k86-amber/20'}">
                        ${u.role}
                    </span>
                </td>
                <td class="p-4 text-right space-x-2 flex items-center justify-end gap-2">
                    <label class="relative inline-flex items-center cursor-pointer" title="${isAdmin ? 'Revoke Admin' : 'Make Admin'}">
                        <input type="checkbox" ${isAdmin ? 'checked' : ''} onchange="adminApp.toggleUserRole(${u.id}, '${u.role}')" class="sr-only peer">
                        <div class="w-9 h-5 bg-k86-border peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-k86-red"></div>
                    </label>
                    <button onclick="adminApp.confirmDeleteUser(${u.id})" class="p-2 text-k86-danger/50 hover:text-k86-danger hover:bg-k86-danger/10 rounded-lg transition-all" title="Delete User">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </td>
            </tr>
        `;
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
                <td class="p-4 flex flex-col gap-1 items-start">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${i.is_available ? 'bg-k86-success/10 text-k86-success border-k86-success/20' : 'bg-k86-danger/10 text-k86-danger border-k86-danger/20'}">
                        ${i.is_available ? 'Available' : 'Hidden'}
                    </span>
                    ${i.is_featured ? '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-k86-amber/10 text-k86-amber border-k86-amber/20">🔥 Hot Deal</span>' : ''}
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
    resolveCatIcon(icon) {
        // FA keys are lowercase ASCII letters, numbers, and hyphens only
        // Emojis and old data will fail this test and fall back to a default
        if (!icon) return 'layer-group';
        return /^[a-z0-9-]+$/.test(icon.trim()) ? icon.trim() : 'layer-group';
    },

    renderCategoryGrid(categories) {
        const grid = document.getElementById('list-categories');
        if(!grid) return;
        if(!categories || categories.length === 0) {
            grid.innerHTML = `<div class="col-span-3 text-center py-16 text-k86-muted">
                <div class="text-5xl mb-4 text-k86-border"><i class="fa-solid fa-layer-group"></i></div>
                <p class="font-bold uppercase tracking-wider">No categories yet</p>
                <p class="text-xs mt-1">Click "Add Category" to get started.</p>
            </div>`;
            return;
        }
        grid.innerHTML = categories.map(c => this.categoryCardTemplate(c)).join('');
    },

    categoryCardTemplate(c) {
        const itemCount = c.item_count || 0;
        return `
            <div class="bg-k86-surface border border-k86-border rounded-xl p-5 flex flex-col gap-3 hover:border-k86-red/40 transition-all group">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-k86-black rounded-xl flex items-center justify-center border border-k86-border group-hover:border-k86-red/30 transition-colors shrink-0">
                            <i class="fa-solid fa-${this.resolveCatIcon(c.icon)} text-xl text-k86-red"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-white text-lg leading-tight">${c.name}</h3>
                            <p class="text-[10px] text-k86-muted uppercase font-bold tracking-widest mt-0.5">Sort Order: ${c.display_order}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-auto pt-3 border-t border-k86-border">
                    <span class="text-xs font-bold px-2 py-1 rounded-lg bg-k86-black border border-k86-border text-k86-muted">
                        ${itemCount} item${itemCount !== 1 ? 's' : ''}
                    </span>
                    <div class="flex gap-1">
                        <button onclick="adminApp.editCategory(${c.id})" title="Edit Category" class="text-k86-muted hover:text-k86-amber transition-colors p-2 rounded-lg hover:bg-k86-amber/10 cursor-pointer">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </button>
                        <button onclick="adminApp.confirmDeleteCategory(${c.id})" title="Delete Category" class="text-k86-danger/50 hover:text-k86-danger transition-colors p-2 rounded-lg hover:bg-k86-danger/10 cursor-pointer">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    openAddCategoryModal() {
        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-red/10 rounded-full flex items-center justify-center text-k86-red">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white">Add New Category</h3>
                        <p class="text-xs text-k86-muted">Group your menu items together</p>
                    </div>
                </div>

                <form id="form-add-category" class="space-y-4" onsubmit="adminApp.saveCategory(event)">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Category Name</label>
                        <input type="text" name="name" required placeholder="e.g. Burgers" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                    </div>
                    ${this.renderIconPicker('')}
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Sort Order</label>
                        <input type="number" name="display_order" placeholder="1" min="0" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-red/20">Save Category</button>
                    </div>
                </form>
            </div>
        `;
        this.openModal('Add Category', formHtml);
        setTimeout(() => this.loadFAIcons(), 80);
    },

    editCategory(id) {
        const cat = this.categoriesCache.find(c => c.id == id);
        if(!cat) return;

        const formHtml = `
            <div class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-k86-amber/10 rounded-full flex items-center justify-center text-k86-amber">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-white uppercase">Edit Category</h3>
                        <p class="text-xs text-k86-muted">ID: #CAT-${cat.id}</p>
                    </div>
                </div>

                <form id="form-edit-category" class="space-y-4" onsubmit="adminApp.saveCategory(event)">
                    <input type="hidden" name="id" value="${cat.id}">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Category Name</label>
                        <input type="text" name="name" value="${cat.name}" required class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none">
                    </div>
                    ${this.renderIconPicker(cat.icon || '')}
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Sort Order</label>
                        <input type="number" name="display_order" value="${cat.display_order}" min="0" class="w-full bg-k86-black border border-k86-border rounded-xl px-4 py-3 text-white focus:border-k86-red transition-all outline-none font-price">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="adminApp.closeModal()" class="flex-1 px-6 py-3 bg-k86-surface2 text-k86-muted font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-k86-amber text-white font-bold rounded-xl hover:bg-yellow-600 transition-colors uppercase text-xs tracking-widest shadow-lg shadow-k86-amber/20">Update Category</button>
                    </div>
                </form>
            </div>
        `;
        this.openModal('Edit Category', formHtml);
        setTimeout(() => this.loadFAIcons(), 80);
    },

    async saveCategory(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            let r = await fetch('../api/admin.php?resource=categories', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Category saved!', 'success');
                this.closeModal();
                this.fetchData('categories');
            } else {
                if(window.Toast) Toast.show(j.message || 'Error saving category', 'error');
            }
        } catch(err) {
            console.error('Save category error', err);
            if(window.Toast) Toast.show('Connection error', 'error');
        }
    },

    confirmDeleteCategory(id) {
        const cat = this.categoriesCache.find(c => c.id == id);
        if(!cat) return;
        const itemCount = cat.item_count || 0;

        const confirmHtml = `
            <div class="text-center space-y-5 py-4">
                <div class="w-20 h-20 bg-k86-danger/10 rounded-full flex items-center justify-center text-k86-danger mx-auto animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>

                <div>
                    <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Delete Category?</h3>
                    <p class="text-k86-muted mt-2 px-4">You are about to delete <span class="text-white font-bold">${cat.icon} ${cat.name}</span>.</p>
                </div>

                ${itemCount > 0 ? `
                <div class="mx-4 p-3 bg-k86-red/10 border border-k86-red/30 rounded-xl flex items-start gap-3 text-left">
                    <svg class="w-5 h-5 text-k86-red shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
                    <p class="text-xs text-k86-red font-bold leading-relaxed">⚠️ WARNING: This category contains <span class="underline">${itemCount} menu item${itemCount !== 1 ? 's' : ''}</span>. Deleting it will <span class="uppercase">permanently delete all those items</span> too. This cannot be undone.</p>
                </div>` : `
                <div class="mx-4 p-3 bg-k86-surface2 border border-k86-border rounded-xl flex items-center gap-3">
                    <svg class="w-4 h-4 text-k86-success shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-k86-muted">This category has no items — safe to delete.</p>
                </div>`}

                <div class="flex gap-3 px-4">
                    <button onclick="adminApp.closeModal()" class="flex-1 px-6 py-4 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                    <button onclick="adminApp.deleteCategory(${id})" class="flex-1 px-6 py-4 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-xl shadow-k86-red/40">Yes, Delete</button>
                </div>
            </div>
        `;
        this.openModal('Delete Confirmation', confirmHtml);
    },

    async deleteCategory(id) {
        try {
            let r = await fetch('../api/admin.php?resource=categories', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'Category deleted', 'success');
                this.closeModal();
                this.fetchData('categories');
            } else {
                if(window.Toast) Toast.show(j.message || 'Error deleting category', 'error');
            }
        } catch(err) { console.error('Delete category error', err); }
    },

    getCategoryIcons() {
        return [
            // ─── Food & Drink ───
            { key: 'burger',               label: 'Burger' },
            { key: 'pizza-slice',          label: 'Pizza' },
            { key: 'bowl-food',            label: 'Bowl' },
            { key: 'drumstick-bite',       label: 'Chicken' },
            { key: 'fish',                 label: 'Fish' },
            { key: 'shrimp',              label: 'Shrimp' },
            { key: 'mug-hot',             label: 'Hot Drink' },
            { key: 'glass-water',         label: 'Drink' },
            { key: 'martini-glass-citrus',label: 'Juice' },
            { key: 'ice-cream',           label: 'Ice Cream' },
            { key: 'carrot',              label: 'Veggie' },
            { key: 'leaf',                label: 'Salad' },
            { key: 'wheat-awn',           label: 'Wrap' },
            { key: 'cookie',              label: 'Snack' },
            { key: 'egg',                 label: 'Egg' },
            { key: 'bacon',               label: 'Bacon' },
            { key: 'pepper-hot',          label: 'Spicy' },
            { key: 'bread-slice',         label: 'Bread' },
            { key: 'apple-whole',         label: 'Fruit' },
            { key: 'lemon',               label: 'Lemon' },
            { key: 'bottle-water',        label: 'Water' },
            { key: 'wine-glass',          label: 'Wine' },
            { key: 'champagne-glasses',   label: 'Celebration' },
            { key: 'mug-saucer',         label: 'Coffee' },
            // ─── Kitchen & Dining ───
            { key: 'utensils',            label: 'Utensils' },
            { key: 'plate-wheat',         label: 'Meal' },
            { key: 'kitchen-set',         label: 'Kitchen' },
            { key: 'blender',             label: 'Blender' },
            { key: 'fire-flame-curved',   label: 'Hot/Flame' },
            { key: 'fire-burner',         label: 'Burner' },
            // ─── People & Users ───
            { key: 'user',                label: 'User' },
            { key: 'users',               label: 'Users' },
            { key: 'user-tie',            label: 'Manager' },
            { key: 'user-chef',           label: 'Chef' },
            { key: 'user-group',          label: 'Group' },
            { key: 'person',              label: 'Person' },
            { key: 'child',               label: 'Child' },
            // ─── Business & Commerce ───
            { key: 'store',               label: 'Store' },
            { key: 'shop',                label: 'Shop' },
            { key: 'cash-register',       label: 'Cash Register' },
            { key: 'receipt',             label: 'Receipt' },
            { key: 'bag-shopping',        label: 'Shopping Bag' },
            { key: 'cart-shopping',       label: 'Cart' },
            { key: 'credit-card',         label: 'Credit Card' },
            { key: 'money-bill',          label: 'Money' },
            { key: 'tag',                 label: 'Tag' },
            { key: 'tags',                label: 'Tags' },
            { key: 'percent',             label: 'Discount' },
            // ─── Navigation & UI ───
            { key: 'house',               label: 'Home' },
            { key: 'gear',                label: 'Settings' },
            { key: 'bars',                label: 'Menu' },
            { key: 'magnifying-glass',    label: 'Search' },
            { key: 'bell',                label: 'Notification' },
            { key: 'bookmark',            label: 'Bookmark' },
            { key: 'heart',               label: 'Favourite' },
            { key: 'star',                label: 'Star' },
            { key: 'award',               label: 'Award' },
            { key: 'trophy',              label: 'Trophy' },
            { key: 'crown',               label: 'Crown' },
            { key: 'layer-group',         label: 'General' },
            // ─── Location & Map ───
            { key: 'location-dot',        label: 'Location' },
            { key: 'map',                 label: 'Map' },
            { key: 'compass',             label: 'Compass' },
            { key: 'route',               label: 'Route' },
            { key: 'truck',               label: 'Delivery' },
            { key: 'motorcycle',          label: 'Bike Delivery' },
            { key: 'car',                 label: 'Car' },
            { key: 'bicycle',             label: 'Bicycle' },
            // ─── Communication ───
            { key: 'phone',               label: 'Phone' },
            { key: 'envelope',            label: 'Email' },
            { key: 'comment',             label: 'Comment' },
            { key: 'comments',            label: 'Chat' },
            { key: 'message',             label: 'Message' },
            { key: 'share-nodes',         label: 'Share' },
            // ─── Technology ───
            { key: 'mobile-screen',       label: 'Mobile' },
            { key: 'tablet-screen-button',label: 'Tablet' },
            { key: 'laptop',              label: 'Laptop' },
            { key: 'desktop',             label: 'Desktop' },
            { key: 'wifi',                label: 'WiFi' },
            { key: 'qrcode',              label: 'QR Code' },
            { key: 'barcode',             label: 'Barcode' },
            { key: 'print',               label: 'Print' },
            // ─── Time & Calendar ───
            { key: 'clock',               label: 'Clock' },
            { key: 'calendar-days',       label: 'Calendar' },
            { key: 'calendar-check',      label: 'Schedule' },
            { key: 'hourglass-half',      label: 'Hourglass' },
            // ─── Charts & Analytics ───
            { key: 'chart-bar',           label: 'Bar Chart' },
            { key: 'chart-line',          label: 'Line Chart' },
            { key: 'chart-pie',           label: 'Pie Chart' },
            { key: 'list-check',          label: 'Checklist' },
            { key: 'table-list',          label: 'Table' },
            // ─── Alerts & Status ───
            { key: 'circle-check',        label: 'Success' },
            { key: 'circle-xmark',        label: 'Error' },
            { key: 'circle-exclamation',  label: 'Warning' },
            { key: 'circle-info',         label: 'Info' },
            { key: 'ban',                 label: 'Banned' },
            { key: 'shield-halved',       label: 'Security' },
            { key: 'lock',                label: 'Lock' },
            // ─── Nature & Misc ───
            { key: 'sun',                 label: 'Sun' },
            { key: 'moon',                label: 'Moon' },
            { key: 'cloud',               label: 'Weather' },
            { key: 'snowflake',           label: 'Cold' },
            { key: 'tree',                label: 'Tree' },
            { key: 'seedling',            label: 'Organic' },
            { key: 'recycle',             label: 'Eco' },
            { key: 'hand-holding-heart',  label: 'Care' },
            { key: 'gift',                label: 'Gift' },
            { key: 'box',                 label: 'Package' },
            { key: 'boxes-stacked',       label: 'Inventory' },
        ];
    },

    renderIconPicker(selectedIcon) {
        const icons = this.getCategoryIcons();
        const current = selectedIcon || '';
        const previewIcon = current || 'layer-group';
        const defaultBtns = icons.map(i => this.iconPickerBtnHtml(i.key, current, i.label)).join('');
        return `
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] uppercase font-bold text-k86-muted ml-1">Icon</label>
                    <div class="flex items-center gap-2 bg-k86-black border border-k86-border rounded-lg px-3 py-1.5">
                        <i class="fa-solid fa-${previewIcon} text-k86-red text-sm" id="cat-icon-preview"></i>
                        <span class="text-[10px] text-k86-muted font-mono" id="cat-icon-preview-name">${current || 'none selected'}</span>
                    </div>
                </div>
                <input type="hidden" name="icon" id="cat-icon-input" value="${current}">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-k86-muted text-xs pointer-events-none"></i>
                    <input type="text" id="cat-icon-search" oninput="adminApp.filterIcons(this.value)" autocomplete="off" placeholder="Search all Font Awesome icons..." class="w-full bg-k86-black border border-k86-border rounded-xl pl-8 pr-4 py-2.5 text-sm text-white focus:border-k86-red transition-all outline-none placeholder-k86-muted">
                </div>
                <p id="cat-icon-status" class="text-[10px] text-k86-muted ml-1">Showing ${icons.length} popular icons — type to search all</p>
                <div id="cat-icon-picker" class="grid grid-cols-6 gap-1.5 bg-k86-black border border-k86-border rounded-xl p-3 max-h-44 overflow-y-auto custom-scrollbar">
                    ${defaultBtns}
                </div>
            </div>
        `;
    },

    iconPickerBtnHtml(key, selectedKey, label) {
        const isSelected = key === selectedKey;
        return `<button type="button"
            data-icon="${key}"
            onclick="adminApp.selectCatIcon('${key}')"
            title="${label || key}"
            class="cat-icon-btn w-full aspect-square flex items-center justify-center rounded-lg border transition-all text-base ${isSelected ? 'border-k86-red bg-k86-red/10 text-k86-red' : 'border-k86-border text-k86-muted hover:border-k86-red/40 hover:text-white'}">
            <i class="fa-solid fa-${key}"></i>
        </button>`;
    },

    async loadFAIcons() {
        if (this.faIconsCache !== null || this.faIconsLoading) return;
        this.faIconsLoading = true;

        const statusEl = document.getElementById('cat-icon-status');
        if (statusEl) statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Loading Font Awesome icons...';

        const urls = [
            'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.0.1/metadata/icons.json',
            'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.7.2/metadata/icons.json',
            'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/metadata/icons.json',
        ];

        let data = null;
        for (const url of urls) {
            try {
                const r = await fetch(url);
                if (r.ok) { data = await r.json(); break; }
            } catch(e) { console.warn('FA CDN failed:', url); }
        }

        if (data) {
            this.faIconsCache = {};
            for (const [name, info] of Object.entries(data)) {
                // Include ALL icons that have any solid style listed (free or otherwise)
                // This gives the user access to the complete solid icon set
                const allStyles = [
                    ...(info.free || []),
                    ...(info.styles || []),
                    ...(info.familyStylesByLicense?.free?.map(f => f.style) || [])
                ];
                if (allStyles.includes('solid') || allStyles.length === 0) {
                    this.faIconsCache[name] = [name, ...(info.search?.terms || [])].map(t => t.toLowerCase());
                }
            }
            const count = Object.keys(this.faIconsCache).length;
            if (statusEl) statusEl.textContent = `${count} icons ready — type to search`;
        } else {
            // All URLs failed — fall back to curated list
            this.faIconsCache = {};
            this.getCategoryIcons().forEach(i => {
                this.faIconsCache[i.key] = [i.key, i.label.toLowerCase()];
            });
            if (statusEl) statusEl.textContent = 'Search unavailable — showing defaults';
        }

        this.faIconsLoading = false;
        const query = document.getElementById('cat-icon-search')?.value || '';
        this.filterIcons(query); // Re-render picker with loaded data
    },

    filterIcons(query) {
        const picker = document.getElementById('cat-icon-picker');
        const statusEl = document.getElementById('cat-icon-status');
        if (!picker) return;

        const current = document.getElementById('cat-icon-input')?.value || '';

        if (!query || query.length === 0) {
            if (this.faIconsCache && Object.keys(this.faIconsCache).length > 0) {
                // Show first 72 icons from full FA set
                const allKeys = Object.keys(this.faIconsCache);
                picker.innerHTML = allKeys.slice(0, 72).map(k => this.iconPickerBtnHtml(k, current, k)).join('');
                if (statusEl) statusEl.textContent = `${allKeys.length} total icons — type to filter`;
            } else {
                // FA not loaded yet, show curated
                const defaults = this.getCategoryIcons();
                picker.innerHTML = defaults.map(i => this.iconPickerBtnHtml(i.key, current, i.label)).join('');
                if (statusEl) statusEl.textContent = `Showing ${defaults.length} popular icons — type to search all`;
            }
            return;
        }

        if (this.faIconsCache === null) {
            if (!this.faIconsLoading) this.loadFAIcons();
            picker.innerHTML = `<div class="col-span-6 py-4 text-center text-k86-muted text-xs"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Loading icons…</div>`;
            return;
        }

        const q = query.toLowerCase();
        const results = Object.entries(this.faIconsCache)
            .filter(([, terms]) => terms.some(t => t.includes(q)))
            .slice(0, 72);

        if (results.length === 0) {
            picker.innerHTML = `<div class="col-span-6 py-6 text-center text-k86-muted text-xs"><i class="fa-solid fa-circle-question text-xl mb-2 block"></i>No icons found</div>`;
            if (statusEl) statusEl.textContent = `No results for "${query}"`;
            return;
        }

        picker.innerHTML = results.map(([key]) => this.iconPickerBtnHtml(key, current, key)).join('');
        if (statusEl) statusEl.textContent = `${results.length}${results.length === 72 ? '+' : ''} result${results.length !== 1 ? 's' : ''}`;
    },

    selectCatIcon(iconKey) {
        const input = document.getElementById('cat-icon-input');
        const preview = document.getElementById('cat-icon-preview');
        const previewName = document.getElementById('cat-icon-preview-name');
        if(input) input.value = iconKey;
        if(preview) preview.className = `fa-solid fa-${iconKey} text-k86-red text-sm`;
        if(previewName) previewName.textContent = iconKey;
        document.querySelectorAll('#cat-icon-picker .cat-icon-btn').forEach(btn => {
            const isSelected = btn.dataset.icon === iconKey;
            btn.className = `cat-icon-btn w-full aspect-square flex items-center justify-center rounded-lg border transition-all text-base ${isSelected ? 'border-k86-red bg-k86-red/10 text-k86-red' : 'border-k86-border text-k86-muted hover:border-k86-red/40 hover:text-white'}`;
        });
    },

    categoryItemTemplate(c) {
        return this.categoryCardTemplate(c);
    },

    async toggleUserRole(id, currentRole) {
        const newRole = currentRole === 'admin' ? 'customer' : 'admin';
        try {
            let r = await fetch('../api/admin.php?resource=users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, new_role: newRole })
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || `Promoted to ${newRole}`, 'success');
                this.fetchData('users'); // Refresh table
            } else {
                if(window.Toast) Toast.show(j.message || 'Error updating role', 'error');
            }
        } catch(e) { console.error('Toggle role error', e); }
    },

    confirmDeleteUser(id) {
        const user = this.usersCache.find(u => u.id == id);
        if(!user) return;

        const modalHtml = `
            <div class="text-center space-y-6 py-4">
                <div class="w-20 h-20 bg-k86-danger/10 rounded-full flex items-center justify-center text-k86-danger mx-auto">
                    <i class="fa-solid fa-user-xmark text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-heading font-bold text-white uppercase tracking-wider">Remove User?</h3>
                    <p class="text-k86-muted mt-2 px-6">Are you sure you want to delete <span class="text-white font-bold">${user.name}</span>? This will permanently remove their account and order history.</p>
                </div>
                <div class="flex gap-3 px-4">
                    <button onclick="adminApp.closeModal()" class="flex-1 px-6 py-4 bg-k86-surface2 text-white font-bold rounded-xl hover:bg-k86-border transition-colors uppercase text-xs tracking-widest">Cancel</button>
                    <button onclick="adminApp.deleteUser(${id})" class="flex-1 px-6 py-4 bg-k86-red text-white font-bold rounded-xl hover:bg-k86-red-dark transition-colors uppercase text-xs tracking-widest shadow-xl shadow-k86-red/40">Yes, Delete</button>
                </div>
            </div>
        `;
        this.openModal('Delete User', modalHtml);
    },

    async deleteUser(id) {
        try {
            let r = await fetch('../api/admin.php?resource=users', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            let j = await r.json();
            if(j.success) {
                if(window.Toast) Toast.show(j.message || 'User deleted', 'success');
                this.closeModal();
                this.fetchData('users');
            }
        } catch(e) { console.error('Delete user error', e); }
    },

    logout() { fetch('../api/auth.php?action=logout').then(() => window.location.href = '../'); }
};

document.addEventListener('DOMContentLoaded', () => adminApp.init());
