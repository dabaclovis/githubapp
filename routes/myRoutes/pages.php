<?php

use App\Livewire\Auths\Login;
use App\Livewire\Auths\Register;
use App\Livewire\Services\Calendar;
use App\Livewire\Pages\Index;
use Illuminate\Support\Facades\Route;

Route::get('/', Index::class)->name('pages.index');
Route::get('/calendar', Calendar::class)->name('calendar');
Route::get('/calendar/{year}/{month}', Calendar::class)->name('services.calendar.month');

Route::get('login', Login::class)->name('auth.login');
Route::get('register', Register::class)->name('auth.register');
