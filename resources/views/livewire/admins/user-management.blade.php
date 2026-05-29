<div class="container py-4" style="max-width: 1100px;">
    <header class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">User Management</h4>
            <small class="text-muted">Manage system users and roles.</small>
        </div>
        <button class="btn btn-primary" type="button" wire:click="openCreateModal">+ Add User</button>
    </header>

    @include('partials.messages.form')

    <div class="card border shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Search by name, email, username, role"
                        wire:model.live.debounce.300ms="search" aria-label="Search users">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <button class="btn btn-outline-primary btn-sm me-1" type="button"
                                        wire:click="openEditModal({{ $user->id }})">Edit</button>
                                    <button class="btn btn-outline-danger btn-sm" type="button"
                                        wire:click="deleteUser({{ $user->id }})">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.45);" wire:click.self="closeModal"
            @keydown.escape.window="$wire.closeModal()">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingUserId ? 'Edit User' : 'Create User' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"
                            aria-label="Close">&times;</button>
                    </div>

                    <form wire:submit.prevent="saveUser">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="userName">Name</label>
                                    <input id="userName" type="text"
                                        class="form-control @error('name') is-invalid @enderror" wire:model.defer="name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="userEmail">Email</label>
                                    <input id="userEmail" type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        wire:model.defer="email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="userRole">Role</label>
                                    <select id="userRole" class="form-control @error('role') is-invalid @enderror"
                                        wire:model.defer="role">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        for="userPassword">{{ $editingUserId ? 'Password (optional)' : 'Password' }}</label>
                                    <input id="userPassword" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        wire:model.defer="password" {{ $editingUserId ? '' : 'required' }}>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="userPasswordConfirmation">Confirm Password</label>
                                    <input id="userPasswordConfirmation" type="password" class="form-control"
                                        wire:model.defer="password_confirmation"
                                        {{ $editingUserId ? '' : 'required' }}>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="saveUser">
                                <span class="spinner-border spinner-border-sm me-1" wire:loading
                                    wire:target="saveUser"></span>
                                {{ $editingUserId ? 'Update User' : 'Create User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
