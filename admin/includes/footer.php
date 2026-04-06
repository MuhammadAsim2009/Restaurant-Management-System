            </div> <!-- END admin-content -->
        </main>
    </div> <!-- END Main Content Wrapper -->

    <!-- Global Modal System -->
    <div id="sys-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="adminApp.closeModal()"></div>
        <!-- Modal Content -->
        <div class="bg-k86-surface p-6 rounded-2xl w-full max-w-lg relative border border-k86-border shadow-2xl transform scale-95 opacity-0 transition-all duration-300 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar" id="sys-modal-body">
            <button onclick="adminApp.closeModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-k86-surface2 text-k86-muted hover:text-white hover:bg-k86-border transition-colors z-20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div id="modal-content" class="text-left mt-2"></div>
        </div>
    </div>

    <!-- ═══ ICON PICKER MODAL (global) ═══ -->
    <div id="icon-picker-overlay" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" onclick="adminApp.onIconPickerOverlayClick(event)">
        <div id="icon-picker-panel" class="bg-[#161616] border border-[#2A2A2A] rounded-2xl w-full max-w-3xl flex flex-col shadow-2xl" style="max-height:85vh;animation:ipFadeIn .2s ease-out both">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#2A2A2A] shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-[#FF2020]/10 rounded-full flex items-center justify-center shrink-0"><i class="fa-solid fa-icons text-[#FF2020]"></i></div>
                    <div>
                        <h3 class="text-base font-bold text-white">Choose Category Icon</h3>
                        <p class="text-[10px] text-[#AAAAAA]" id="icon-picker-status">Ready</p>
                    </div>
                </div>
                <button onclick="adminApp.closeIconPicker()" class="w-8 h-8 flex items-center justify-center rounded-lg text-[#AAAAAA] hover:text-white hover:bg-[#2A2A2A] transition-all"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <div class="px-4 py-3 border-b border-[#2A2A2A] shrink-0">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#AAAAAA] text-sm pointer-events-none"></i>
                    <input type="text" id="icon-picker-search" oninput="adminApp.debouncedIconSearch(this.value)" placeholder="Search icons... e.g. burger, fire, user, arrow" autocomplete="off" class="w-full bg-[#0A0A0A] border border-[#2A2A2A] rounded-xl pl-10 pr-9 py-2.5 text-white text-sm focus:border-[#FF2020] transition-all outline-none">
                    <button id="icon-search-clear" onclick="adminApp.clearIconSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-[#AAAAAA] hover:text-white transition-colors"><i class="fa-solid fa-circle-xmark"></i></button>
                </div>
            </div>
            <div id="icon-picker-grid" class="flex-1 overflow-y-auto p-4" style="min-height:300px;">
                <div class="flex flex-col items-center justify-center h-40 text-[#AAAAAA]">
                    <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-[#FF2020]"></i>
                    <p class="text-sm">Click "Choose Icon" to load icons</p>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/toast.js?v=2"></script>
    <script src="../assets/js/admin.js?v=3"></script>
    
    <!-- UI Logic Script -->
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('mobile-overlay').classList.toggle('hidden');
        }

        // Modal entry animation sync
        const originalOpenModal = window.adminApp ? adminApp.openModal : null;
        if(window.adminApp) {
            adminApp.openModal = function(title, content) {
                if(originalOpenModal) originalOpenModal.call(adminApp, title, content);
                const modal = document.getElementById('sys-modal');
                const body = document.getElementById('sys-modal-body');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    body.classList.remove('scale-95', 'opacity-0');
                    body.classList.add('scale-100', 'opacity-100');
                }, 10);
            };
            
            const originalCloseModal = adminApp.closeModal;
            adminApp.closeModal = function() {
                const body = document.getElementById('sys-modal-body');
                body.classList.remove('scale-100', 'opacity-100');
                body.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    if(originalCloseModal) originalCloseModal.call(adminApp);
                    document.getElementById('sys-modal').classList.add('hidden');
                }, 300);
            };
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #FF2020; }

        @keyframes ipFadeIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
        #icon-picker-grid::-webkit-scrollbar { width: 5px; }
        #icon-picker-grid::-webkit-scrollbar-track { background: #0A0A0A; }
        #icon-picker-grid::-webkit-scrollbar-thumb { background: #2A2A2A; border-radius: 3px; }
        #icon-picker-grid::-webkit-scrollbar-thumb:hover { background: #FF2020; }
        .ip-icon-btn {
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:10px 4px 7px;border-radius:10px;border:1px solid #2A2A2A;
            cursor:pointer;transition:all .15s;gap:5px;background:transparent;min-height:70px;width:100%;
        }
        .ip-icon-btn:hover{border-color:#FF2020;background:rgba(255,32,32,.06);transform:translateY(-1px);box-shadow:0 0 10px rgba(255,32,32,.15);}
        .ip-icon-btn.ip-selected{border-color:#FF2020;background:rgba(255,32,32,.12);}
        .ip-icon-btn i{font-size:1.35rem;color:#AAAAAA;transition:color .15s;}
        .ip-icon-btn:hover i,.ip-icon-btn.ip-selected i{color:#FF2020;}
        .ip-icon-btn span{font-size:8px;color:#555;text-align:center;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;line-height:1.2;font-family:monospace;}
        .ip-icon-btn.ip-selected span{color:#FF8080;}

        @keyframes skeleton-loading {
            0% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .skeleton-loader {
            background: linear-gradient(90deg, #1A1A1A 25%, #2A2A2A 50%, #1A1A1A 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite linear;
        }
    </style>
</body>
</html>
