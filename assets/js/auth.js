window.AuthInterface = {
    isLoggedIn: false, user: null,
    async checkSession() {
        try { 
            let r = await fetch('api/auth.php?action=me'); 
            let d = await r.json(); 
            if(d.success && d.data) {
                this.isLoggedIn = true; 
                this.user = d.data; 
                if(window.CartInterface) window.CartInterface.syncWithBackend(d.data.cart_data);
            } else {
                this.isLoggedIn = false;
                this.user = null;
            }
        } catch(e){}
        this.updateUI();
    },
    updateUI() {
        document.querySelectorAll('#nav-login, #nav-login-mob').forEach(e=>e.classList.toggle('hidden', this.isLoggedIn));
        document.querySelectorAll('#nav-logout, #nav-logout-mob, #nav-orders, #nav-orders-mob').forEach(e=>e.classList.toggle('hidden', !this.isLoggedIn));
        
        // Append user name to the logout button
        if (this.isLoggedIn && this.user && this.user.name) {
            document.querySelectorAll('#nav-logout, #nav-logout-mob').forEach(e => {
                e.textContent = `LOGOUT (${this.user.name})`;
            });
        } else {
            document.querySelectorAll('#nav-logout, #nav-logout-mob').forEach(e => {
                e.textContent = `LOGOUT`;
            });
        }
    },
    openLogin() { document.getElementById('auth-modal').classList.remove('hidden'); this.switchView('login'); },
    closeModal() { document.getElementById('auth-modal').classList.add('hidden'); },
    switchView(v) { document.getElementById('form-login-view').classList.toggle('hidden', v!=='login'); document.getElementById('form-register-view').classList.toggle('hidden', v==='login'); },
    bind() {
        let lf = document.getElementById('form-login');
        if(lf) lf.addEventListener('submit', async e => { 
            e.preventDefault(); 
            // Disable button during req
            let btn = lf.querySelector('button[type="submit"]');
            let origTxt = btn.innerText;
            btn.innerText = 'LOGGING IN...';
            btn.disabled = true;

            let email = document.getElementById('login-email').value;
            let pwd = document.getElementById('login-pwd').value;
            try {
                let r = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email, password: pwd})
                });
                let d = await r.json();
                if(d.success) {
                    if(window.Toast) window.Toast.show(d.message || 'Login Successful!', 'success');
                    this.isLoggedIn = true;
                    this.user = d.data;
                    
                    if (this.user.role === 'admin') {
                        window.location.href = 'admin/index.php';
                        return;
                    }
                    
                    this.updateUI(); 
                    if(window.CartInterface) window.CartInterface.syncWithBackend(this.user.cart_data);
                    this.closeModal();
                    lf.reset();
                } else {
                    if(window.Toast) window.Toast.show(d.message || 'Login failed', 'error');
                }
            } catch(e) {
                if(window.Toast) window.Toast.show('Network error, please try again later.', 'error');
            } finally {
                btn.innerText = origTxt;
                btn.disabled = false;
            }
        });

        let rf = document.getElementById('form-register');
        if(rf) rf.addEventListener('submit', async e => {
            e.preventDefault();
            let btn = rf.querySelector('button[type="submit"]');
            let origTxt = btn.innerText;
            btn.innerText = 'REGISTERING...';
            btn.disabled = true;

            let name = document.getElementById('reg-name').value;
            let email = document.getElementById('reg-email').value;
            let phone = document.getElementById('reg-phone')?.value || '';
            let pwd = document.getElementById('reg-pwd').value;

            try {
                let r = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({name, email, phone, password: pwd})
                });
                let d = await r.json();
                if(d.success) {
                    if(window.Toast) window.Toast.show(d.message || 'Registration Successful! Please login.', 'success');
                    rf.reset();
                    // Switch to login view for them to login
                    this.switchView('login');
                    document.getElementById('login-email').value = email;
                } else {
                    if(window.Toast) window.Toast.show(d.message || 'Registration failed', 'error');
                }
            } catch(e) {
                if(window.Toast) window.Toast.show('Network error, please try again later.', 'error');
            } finally {
                btn.innerText = origTxt;
                btn.disabled = false;
            }
        });

        // Bind password toggles
        document.querySelectorAll('.toggle-pwd').forEach(btn => {
            // Remove old listeners to prevent duplicates if bind is called twice
            let newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            newBtn.addEventListener('click', e => {
                let targetId = newBtn.getAttribute('data-target');
                let input = document.getElementById(targetId);
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        newBtn.innerHTML = '<i class="fa-solid fa-eye-slash border border-k86-border px-2 py-1.5 rounded"></i>';
                    } else {
                        input.type = 'password';
                        newBtn.innerHTML = '<i class="fa-solid fa-eye border border-k86-border px-2 py-1.5 rounded"></i>';
                    }
                }
            });
        });

        // Initialize session
        this.checkSession();
    },
    async logout() { 
        try {
            await fetch('api/auth.php?action=logout');
            this.isLoggedIn=false; 
            this.user=null;
            if(window.CartInterface) window.CartInterface.clear();
            this.updateUI(); 
            if(window.location.hash==='#orders') window.location.hash='#home'; 
            if(window.Toast) window.Toast.show('Logged out successfully.', 'success');
        } catch(e) {}
    }
};
document.addEventListener('DOMContentLoaded', () => { window.AuthInterface.bind(); });
