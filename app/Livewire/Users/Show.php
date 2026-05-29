<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.users', [
    'title' => 'User Profile',
    'description' => 'View and manage your user profile information.',
    'keywords' => 'user profile, account settings, personal information',
])]
class Show extends Component
{
    public function render()
    {
        return view('livewire.users.show');
    }
}
