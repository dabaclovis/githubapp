<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Index;

Route::get('/', Index::class)->name('pages.index');
