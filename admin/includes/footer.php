            </div> <!-- END admin-content -->
        </main>
    </div> <!-- END Main Content Wrapper -->

    <!-- Global Modal System -->
    <div id="sys-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="adminApp.closeModal()"></div>
        <!-- Modal Content -->
        <div class="bg-k86-surface p-6 rounded-2xl w-full max-w-lg relative border border-k86-border shadow-2xl transform scale-95 opacity-0 transition-all duration-300 z-10" id="sys-modal-body">
            <button onclick="adminApp.closeModal()" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-k86-surface2 text-k86-muted hover:text-white hover:bg-k86-border transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div id="modal-content" class="text-left mt-2"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/toast.js?v=2"></script>
    <script src="../assets/js/admin.js?v=2"></script>
    
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
</body>
</html>
