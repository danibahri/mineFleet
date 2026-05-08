<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::livewire('/login', 'pages.auth.login')->name('login')->middleware('guest');

Route::post('/logout', function () {
    \App\Services\ActivityLogger::log('auth', 'logout', 'Logout: ' . auth()->user()?->name);
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');


Route::middleware(['auth'])->group(function () {


    Route::middleware(['role:admin'])->group(function () {
        Route::livewire('/dashboard', 'pages.dashboard')->name('dashboard');
        Route::livewire('/vehicle-management', 'pages.vechile-management.index')->name('vehicle-management');
        Route::livewire('/driver-management', 'pages.driver-management.index')->name('driver-management');
        Route::livewire('/booking-management', 'pages.booking-management.index')->name('booking-management');
        Route::livewire('/fuel-monitoring', 'pages.vuel-monitoring.index')->name('fuel-monitoring');
        Route::livewire('/service-maintenance', 'pages.service-maintenance.index')->name('service-maintenance');
        Route::livewire('/reports', 'pages.reports.index')->name('reports');
        Route::livewire('/activity-logs', 'pages.activity-log.index')->name('activity-logs');
        Route::livewire('/user-management', 'pages.user-management.index')->name('user-management');
        Route::livewire('/settings', 'pages.setting.index')->name('settings');
    });

    // ── Approval (Level 1 & 2 + Admin) ───────────────────────────────────────
    Route::middleware(['role:admin,approver_level_1,approver_level_2'])->group(function () {
        Route::livewire('/approval-system', 'pages.approval-system.index')->name('approval-system');
    });
});
