<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Create a New Account</h4>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    @endif
                    <form wire:submit.prevent="register">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" wire:model.defer="name"
                                class="form-control @error('name') is-invalid @enderror" required autofocus>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" wire:model.defer="email"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" wire:model.defer="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation" wire:model.defer="password_confirmation"
                                class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>