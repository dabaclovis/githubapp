<?php

use App\Livewire\Pages\Index;
use App\Livewire\Services\Discussion;
use App\Livewire\Services\DiscussionShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Index::class)->name('home');

Route::get('/discussion', Discussion::class)->name('discussion.index');
Route::get('/discussion/{post:slug}', DiscussionShow::class)->name('discussion.show');
