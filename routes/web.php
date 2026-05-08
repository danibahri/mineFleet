<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages::landing-pages');
});

Route::livewire('/dashboard', 'pages.dashboard')->name('dashboard');
Route::livewire('/vehicle-management', 'pages.vechile-management.index')->name('vehicle-management');
