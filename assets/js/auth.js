window.AuthInterface = {
    isLoggedIn: false, user: null,
    async checkSession() {
        try { let r = await fetch('/api/auth.php?action=me'); let d = await r.json(); this.isLoggedIn = !!d.data; this.user = d.data; } catch(e){}
        this.updateUI();
    },
    updateUI() {
        document.querySelectorAll('#nav-login').forEach(e=>e.classList.toggle('hidden', this.isLoggedIn));
        document.querySelectorAll('#nav-logout, #nav-orders').forEach(e=>e.classList.toggle('hidden', !this.isLoggedIn));
    },
    openLogin() { document.getElementById('auth-modal').classList.remove('hidden'); this.switchView('login'); },
    closeModal() { document.getElementById('auth-modal').classList.add('hidden'); },
    switchView(v) { document.getElementById('form-login-view').classList.toggle('hidden', v!=='login'); document.getElementById('form-register-view').classList.toggle('hidden', v==='login'); },
    bind() {
        let lf = document.getElementById('form-login');
        if(lf) lf.addEventListener('submit', e => { e.preventDefault(); alert('Mock Login Success'); this.isLoggedIn=true; this.updateUI(); this.closeModal(); });
    },
    logout() { this.isLoggedIn=false; this.updateUI(); if(window.location.hash==='#orders') window.location.hash='#home'; alert('Logged out'); }
};
document.addEventListener('DOMContentLoaded', () => window.AuthInterface.bind());
