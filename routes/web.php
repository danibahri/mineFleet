<?php

use App\Livewire\Pages\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages::landing-pages');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');
