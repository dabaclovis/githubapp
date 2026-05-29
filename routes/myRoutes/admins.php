<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\Admin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admins\Dashboard::class)->name('admin.dashboard');
    Route::get('/users', \App\Livewire\Admins\UserManagement::class)->name('admin.users.index');
});
