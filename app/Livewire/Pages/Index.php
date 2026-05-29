<?php

namespace App\Livewire\Pages;


use Livewire\Component;
use Livewire\Attribute\Layout;

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
