<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="animate-fade-in space-y-6" data-page="users">
    <div>
        <h2 class="text-3xl font-heading font-bold">REGISTERED USERS</h2>
        <p class="text-k86-muted text-sm mt-1">View user accounts and manage roles.</p>
    </div>
    <div class="bg-k86-surface border border-k86-border rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-k86-surface2 text-k86-muted border-b border-k86-border">
                    <tr>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Name</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Email</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs">Phone</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap">Role</th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody id="tbl-users" class="divide-y divide-k86-border">
                     <tr>
                        <td colspan="5" class="p-4 text-center text-k86-muted py-8">Loading users...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
