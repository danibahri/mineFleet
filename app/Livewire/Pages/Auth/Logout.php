<?php

namespace App\Livewire\Pages\Auth;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function logout(): void
    {
        ActivityLogger::log('auth', 'logout', 'Logout: ' . auth()->user()?->name);
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return <<<'HTML'
        <span></span>
        HTML;
    }
}
