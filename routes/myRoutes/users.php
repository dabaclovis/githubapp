<?php


use App\Livewire\Services\Discussion;
use App\Livewire\Services\Calendar;
use App\Livewire\Services\DiscussionShow;
use App\Livewire\Users\Dashboard;
use App\Livewire\Auths\Logout;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('calendar', Calendar::class)->name('services.calendar');
        Route::get('/discussion/{post:slug}', DiscussionShow::class)->name('services.discussion.show');
        Route::get('calendar/{year}/{month}', Calendar::class)->name('services.calendar.month');
        Route::get('calendar/{year}/{month}/{day}', Calendar::class)->name('services.calendar.day');
        Route::get('calendar/{year}/{month}/{day}/event/{eventId}', Calendar::class)->name('services.calendar.event');
        Route::get('/discussion', Discussion::class)->name('services.discussion');
        Route::get('/dashboard', Dashboard::class)->name('users.dashboard');
    });
    Route::get('/logout', Logout::class)->name('logout');
});
