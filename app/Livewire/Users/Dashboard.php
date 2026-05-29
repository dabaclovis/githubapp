<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.users', [
    'title' => 'User Dashboard',
    'description' => 'Welcome to your dashboard. Manage your profile and activities.',
    'keywords' => 'user dashboard, profile, activities',
])]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.users.dashboard');
    }
}
