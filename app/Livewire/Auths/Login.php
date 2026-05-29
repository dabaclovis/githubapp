<?php

namespace App\Livewire\Auths;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout(
    'components.layouts.app',
    [
        'title' => 'Login - Access Your Account',
        'description' => 'Welcome back! Please log in to access your account and explore our features.',
        'keywords' => 'login, sign in, access account, welcome back',
    ]
)]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'remember' => 'boolean',
    ];

    public function login(): void
    {
        $this->authenticate();
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->validate();
        $throttleKey = $this->throttleKey();

        if (! Auth::attempt([
            'email' => strtolower(trim($credentials['email'])),
            'password' => $credentials['password'],
        ], $credentials['remember'])) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            if (Route::has('admin.dashboard')) {
                $this->redirect(route('admin.dashboard'), navigate: true);
                return;
            }

            $this->redirect('/admin/dashboard', navigate: true);
            return;
        }

        if ($user && $user->role === 'user') {
            if (Route::has('users.dashboard')) {
                $this->redirect(route('users.dashboard'), navigate: true);
                return;
            }

            $this->redirect('/users/dashboard', navigate: true);
            return;
        }

        if (Route::has('pages.index')) {
            $this->redirect(route('pages.index'), navigate: true);
            return;
        }

        $this->redirect('/', navigate: true);
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::lower(trim($this->email)) . '|' . request()->ip();
    }

    public function render()
    {
        return view('livewire.auths.login');
    }
}
