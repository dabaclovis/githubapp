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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-user text-primary"
                                        aria-hidden="true"></i></span>
                                <input type="text" id="name" wire:model.defer="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter your full name" aria-label="Full Name" required autofocus>
                            </div>
                            @error('name')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-info"
                                        aria-hidden="true"></i></span>
                                <input type="email" id="email" wire:model.defer="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter your email address" aria-label="Email Address" required>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock text-success"
                                        aria-hidden="true"></i></span>
                                <input type="password" id="password" wire:model.defer="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter your password" aria-label="Password" required>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-shield text-warning"
                                        aria-hidden="true"></i></span>
                                <input type="password" id="password_confirmation"
                                    wire:model.defer="password_confirmation" class="form-control"
                                    placeholder="Re-enter your password" aria-label="Confirm Password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
