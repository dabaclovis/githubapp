<?php

namespace App\Livewire\Auths;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Logout extends Component
{
    public function mount(): void
    {
        $this->logout();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (Route::has('auth.login')) {
            $this->redirect(route('auth.login'), navigate: true);
            return;
        }

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auths.logout');
    }
}
