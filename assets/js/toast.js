window.Toast = {
    init() {
        if (!document.getElementById('toast-container')) {
            const div = document.createElement('div');
            div.id = 'toast-container';
            div.className = 'fixed bottom-4 right-4 z-[9999] flex flex-col gap-2';
            document.body.appendChild(div);
        }
    },
    show(msg, type = 'success') {
        const bg = type === 'success' ? 'bg-k86-success' : (type === 'error' ? 'bg-k86-danger' : 'bg-k86-amber text-k86-black');
        const t = document.createElement('div');
        t.className = `${bg} text-white px-4 py-3 rounded shadow-lg transition-all duration-300 transform translate-y-full opacity-0 font-bold tracking-wide`;
        t.innerText = msg;
        document.getElementById('toast-container').appendChild(t);
        
        requestAnimationFrame(() => t.classList.remove('translate-y-full', 'opacity-0'));
        
        setTimeout(() => {
            t.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => t.remove(), 300);
        }, 3000);
    }
};
document.addEventListener('DOMContentLoaded', () => window.Toast.init());
// Override native alert to seamlessly integrate toast feedback into MVP
window.alert = (msg) => { if(window.Toast) window.Toast.show(msg, 'success'); else console.log(msg); };
