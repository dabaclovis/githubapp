<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout(

    'components.layouts.app',
    [
        'title' => 'Home - services and offerings',
        'description' => 'Welcome to our website! Explore our services and offerings.',
        'keywords' => 'home, welcome, services, offerings',
    ]
)]
class Index extends Component
{
    public $services = [
        [
            'title' => 'Event Scheduling',
            'description' => 'Create, view, update, and delete events. Stay organized and never miss an important date.',
            'icon' => 'fas fa-calendar-alt',
            'color' => 'primary',
            'btn' => 'primary',
            'link' => 'calendar',
        ],
        [
            'title' => 'Posts Management',
            'description' => 'Share updates, news, and information. Full control over your posts with CRUD operations.',
            'icon' => 'fas fa-edit',
            'color' => 'success',
            'btn' => 'success',
            'link' => 'discussion',
        ],
        [
            'title' => 'Comments & Replies',
            'description' => 'Engage with your community. Manage comments and replies efficiently.',
            'icon' => 'fas fa-comments',
            'color' => 'warning',
            'btn' => 'warning',
            'link' => '#',
        ],
        [
            'title' => 'User Management',
            'description' => 'Add, edit, or remove users. Control access and keep your application secure.',
            'icon' => 'fas fa-users',
            'color' => 'info',
            'btn' => 'info',
            'link' => '#',
        ],
    ];
    public function render()
    {
        return view('livewire.pages.index');
    }
}
