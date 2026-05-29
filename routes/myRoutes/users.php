<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Services\Discussion;

Route::get('/discussion', Discussion::class)->name('services.discussion');
