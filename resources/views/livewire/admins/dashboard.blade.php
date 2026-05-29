<div class="container py-4" style="max-width: 980px;">
    <div class="card border shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1">Admin Dashboard</h4>
                <p class="text-muted mb-0">Manage users, roles, and platform activities.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-2 mt-md-0" wire:navigate>
                Open User Management
            </a>
        </div>
    </div>
</div>
