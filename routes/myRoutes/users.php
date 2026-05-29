<?php


use App\Livewire\Services\Discussion;
use App\Livewire\Services\Calendar;
use Illuminate\Support\Facades\Route;

Route::get('calendar', Calendar::class)->name('services.calendar');
Route::get('calendar/{year}/{month}', Calendar::class)->name('services.calendar.month');
Route::get('calendar/{year}/{month}/{day}', Calendar::class)->name('services.calendar.day');
Route::get('calendar/{year}/{month}/{day}/event/{eventId}', Calendar::class)->name('services.calendar.event');
Route::get('/discussion', Discussion::class)->name('services.discussion');
