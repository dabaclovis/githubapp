<?php

namespace App\Livewire\Pages;

<<<<<<< HEAD

use Livewire\Component;
use Livewire\Attribute\Layout;

=======
use Livewire\Component;
use Livewire\Attributes\Layout;

>>>>>>> ab49cd610b6031ab11d90bff061f9625509db10c
#[Layout(
    'components.layouts.app',
    [
        'title' => 'Home',
        'description' => 'Welcome to our website! Explore our services and offerings.',
        'keywords' => 'home, welcome, services, offerings',
    ]
)]
class Index extends Component
{
    public function render()
    {
        return view('livewire.pages.index');
    }
}
