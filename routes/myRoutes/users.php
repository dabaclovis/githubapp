<?php

<<<<<<< HEAD
use Illuminate\Support\Facades\Route;
use App\Livewire\Services\Discussion;

Route::get('/discussion', Discussion::class)->name('services.discussion');
=======
use App\Livewire\Services\Discussion;
use App\Livewire\Services\Calendar;
use Illuminate\Support\Facades\Route;

Route::get('calendar', Calendar::class)->name('services.calendar');
Route::get('calendar/{year}/{month}', Calendar::class)->name('services.calendar.month');
Route::get('calendar/{year}/{month}/{day}', Calendar::class)->name('services.calendar.day');
Route::get('calendar/{year}/{month}/{day}/event/{eventId}', Calendar::class)->name('services.calendar.event');
>>>>>>> e88a0c9b0c81e2a3eb7058e616dacbbaab807268
