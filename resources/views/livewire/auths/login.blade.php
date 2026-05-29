<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Welcome Back</h4>
                </div>

                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    <form wire:submit.prevent="login" aria-label="Login Form">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-info"
                                        aria-hidden="true"></i></span>
                                <input type="email" id="email" wire:model.defer="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter your email address" aria-label="Email Address" required
                                    autofocus>
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

                        <div class="form-check mb-3">
                            <input type="checkbox" id="remember" wire:model="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled"
                            wire:target="login">
                            <span wire:loading wire:target="login" class="spinner-border spinner-border-sm me-1"></span>
                            Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
