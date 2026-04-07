<?php 
$current_page = 'messages.php';
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="animate-fade-in space-y-8 pb-12" data-page="messages">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display text-white tracking-widest uppercase">User Messages</h1>
            <p class="text-k86-muted font-body mt-1">Manage communications and feedback from your customers.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="messagesApp.loadMessages()" class="px-5 py-2.5 bg-k86-surface2 border border-k86-border text-white rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-k86-surface transition-all">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="bg-k86-surface border border-k86-border rounded-3xl overflow-hidden shadow-2xl relative min-h-[400px]">
        <div id="messages-loader" class="absolute inset-0 bg-k86-surface/80 flex items-center justify-center z-20 hidden">
            <div class="flex flex-col items-center gap-4">
                <div class="w-10 h-10 border-4 border-k86-red border-t-transparent rounded-full animate-spin"></div>
                <p class="text-white font-bold animate-pulse">Loading messages...</p>
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead class="bg-k86-black/50 border-b border-k86-border">
                <tr>
                    <th class="px-6 py-4 text-[10px] uppercase font-bold text-k86-muted tracking-widest">Sender</th>
                    <th class="px-6 py-4 text-[10px] uppercase font-bold text-k86-muted tracking-widest">Message Preview</th>
                    <th class="px-4 py-4 text-[10px] uppercase font-bold text-k86-muted tracking-widest">Date</th>
                    <th class="px-6 py-4 text-[10px] uppercase font-bold text-k86-muted tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="messages-table-body" class="divide-y divide-k86-border">
                <!-- Data injected via JS -->
            </tbody>
        </table>

        <!-- Empty State -->
        <div id="messages-empty" class="hidden flex flex-col items-center justify-center p-20 text-center">
            <div class="w-20 h-20 bg-k86-surface2 rounded-full flex items-center justify-center text-k86-muted mb-4 border border-k86-border">
                <i class="fas fa-envelope-open text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-white">No messages yet</h3>
            <p class="text-k86-muted max-w-xs mt-2">When users contact you via the website, their messages will appear here.</p>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="message-modal" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-k86-surface border border-k86-border rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl transform scale-95 transition-all duration-300" id="message-modal-content">
        <div class="p-6 border-b border-k86-border flex justify-between items-center bg-k86-black/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-k86-red/20 rounded-full flex items-center justify-center text-k86-red">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white leading-tight" id="modal-sender-name">Sender Name</h3>
                    <p class="text-xs text-k86-muted" id="modal-sender-email">sender@example.com</p>
                </div>
            </div>
            <button onclick="messagesApp.closeModal()" class="text-k86-muted hover:text-white transition-colors p-2 bg-k86-black rounded-full border border-k86-border">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8 overflow-y-auto flex-1">
             <div class="bg-k86-black/40 border border-k86-border p-6 rounded-2xl">
                 <p class="text-white font-body leading-relaxed whitespace-pre-wrap" id="modal-message-text">Message content goes here...</p>
             </div>
             <div class="mt-6 flex items-center justify-between text-xs text-k86-muted">
                 <span id="modal-received-at"><i class="far fa-clock mr-1"></i> Received at: 2024-03-24</span>
                 <span id="modal-status-badge" class="px-2 py-1 rounded-md bg-k86-red/10 text-k86-red font-bold uppercase tracking-widest text-[9px]">Unread</span>
             </div>
        </div>
        <div class="p-6 border-t border-k86-border bg-k86-black/20 flex justify-end gap-3">
            <button onclick="messagesApp.deleteCurrent()" class="px-6 py-2.5 bg-red-500/10 text-red-500 border border-red-500/30 rounded-xl text-sm font-bold hover:bg-red-500 hover:text-white transition-all">
                Delete Message
            </button>
            <button onclick="messagesApp.closeModal()" class="px-6 py-2.5 bg-k86-surface2 border border-k86-border text-white rounded-xl text-sm font-bold hover:bg-k86-surface transition-all">
                Close
            </button>
        </div>
    </div>
</div>

<script>
const messagesApp = {
    messages: [],
    currentId: null,

    async init() {
        await this.loadMessages();
    },

    async loadMessages() {
        const loader = document.getElementById('messages-loader');
        loader.classList.remove('hidden');
        
        try {
            const res = await (await fetch('../api/admin.php?resource=messages')).json();
            if (res.success) {
                this.messages = res.data;
                this.render();
            }
        } catch (err) {
            console.error('Failed to load messages', err);
        } finally {
            loader.classList.add('hidden');
        }
    },

    render() {
        const tbody = document.getElementById('messages-table-body');
        const emptyState = document.getElementById('messages-empty');
        tbody.innerHTML = '';

        if (this.messages.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        this.messages.forEach(msg => {
            const row = document.createElement('tr');
            row.className = `group hover:bg-white/5 transition-colors cursor-pointer ${msg.status === 'unread' ? 'bg-k86-red/5' : ''}`;
            row.onclick = () => this.viewMessage(msg.id);
            
            row.innerHTML = `
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-k86-surface2 to-k86-black flex items-center justify-center border border-k86-border font-bold text-xs text-white">
                            ${msg.name[0].toUpperCase()}
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm line-clamp-1">${msg.name} ${msg.status === 'unread' ? '<span class="inline-block w-2 h-2 bg-k86-red rounded-full ml-1"></span>' : ''}</p>
                            <p class="text-[10px] text-k86-muted">${msg.email}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <p class="text-xs text-k86-muted font-body line-clamp-1">${msg.message}</p>
                </td>
                <td class="px-4 py-5 whitespace-nowrap">
                    <span class="text-[10px] font-body text-k86-muted uppercase">${new Date(msg.created_at).toLocaleDateString()}</span>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="event.stopPropagation(); messagesApp.deleteMessage('${msg.id}')" class="p-2 bg-red-500/10 text-red-500 border border-red-500/20 rounded-lg hover:bg-red-500 hover:text-white transition-all text-xs" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <button class="p-2 bg-k86-surface2 text-white border border-k86-border rounded-lg hover:border-k86-red transition-all text-xs" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    },

    viewMessage(id) {
        const msg = this.messages.find(m => m.id == id);
        if (!msg) return;

        this.currentId = id;
        document.getElementById('modal-sender-name').textContent = msg.name;
        document.getElementById('modal-sender-email').textContent = msg.email;
        document.getElementById('modal-message-text').textContent = msg.message;
        document.getElementById('modal-received-at').innerHTML = `<i class="far fa-clock mr-1"></i> Received at: ${msg.created_at}`;
        
        const badge = document.getElementById('modal-status-badge');
        if (msg.status === 'unread') {
            badge.textContent = 'Unread';
            badge.className = 'px-2 py-1 rounded-md bg-k86-red/10 text-k86-red font-bold uppercase tracking-widest text-[9px]';
            // Also mark as read in DB if it was unread
            this.markAsRead(id);
        } else {
            badge.textContent = 'Read';
            badge.className = 'px-2 py-1 rounded-md bg-green-500/10 text-green-500 font-bold uppercase tracking-widest text-[9px]';
        }

        const modal = document.getElementById('message-modal');
        const content = document.getElementById('message-modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    },

    async markAsRead(id) {
        try {
             const res = await (await fetch('../api/admin.php?resource=messages', {
                 method: 'POST',
                 headers: { 'Content-Type': 'application/json' },
                 body: JSON.stringify({ id })
             })).json();
             
             if (res.success) {
                  // Update local state
                  const idx = this.messages.findIndex(m => m.id == id);
                  if (idx !== -1) {
                      this.messages[idx].status = 'read';
                      this.render();
                  }
             }
        } catch (err) {
             console.error('Failed to mark as read', err);
        }
    },

    closeModal() {
        const modal = document.getElementById('message-modal');
        const content = document.getElementById('message-modal-content');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    },

    async deleteMessage(id) {
        if (!confirm('Are you sure you want to delete this message?')) return;
        
        try {
            const res = await (await fetch('../api/admin.php?resource=messages', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })).json();

            if (res.success) {
                if(window.showToast) window.showToast('Message deleted');
                this.messages = this.messages.filter(m => m.id != id);
                this.render();
                this.closeModal();
            }
        } catch (err) {
            console.error('Failed to delete message', err);
        }
    },

    deleteCurrent() {
        if(this.currentId) this.deleteMessage(this.currentId);
    }
};

document.addEventListener('DOMContentLoaded', () => messagesApp.init());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
