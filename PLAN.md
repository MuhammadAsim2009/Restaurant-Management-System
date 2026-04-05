# Kitchen 86 MVP — Development Plan

## Phase 1 — Foundation
- [ ] DB schema + seed data (`sql/kitchen86.sql`)
- [ ] `includes/db.php`, `session.php`, `response.php`, `auth_guard.php`
- [ ] Base HTML shell for `index.php` and `admin/index.php`
- [ ] Tailwind config + base CSS variables

## Phase 2 — Customer Frontend
- [ ] Navigation + hash routing in `app.js`
- [ ] Menu section (fetch + render items by category)
- [ ] Cart system (`cart.js` + localStorage)
- [ ] Checkout form + order submission
- [ ] Login/Register modals + session sync (`auth.js`)

## Phase 3 — Backend APIs
- [ ] `api/auth.php` — full auth flow
- [ ] `api/menu.php` — menu + deals fetch
- [ ] `api/orders.php` — order placement + history
- [ ] `api/admin.php` — full admin CRUD

## Phase 4 — Admin Panel
- [ ] Dashboard stats view
- [ ] Menu & deals manager (add/edit/delete)
- [ ] Order management + status updates
- [ ] User list + categories manager

## Phase 5 — Polish
- [ ] Loading states + skeleton screens
- [ ] Toast notifications (success/error)
- [ ] Full mobile responsiveness
- [ ] Image upload handling (server-side)
- [ ] Input validation (client + server)
