<?php

namespace App\Livewire\Pages\Auth;

use App\Services\ActivityLogger;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Rule(['required', 'email'])]
    public string $email = '';

    #[Rule(['required', 'string'])]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        $user = Auth::user();

        // Block inactive users
        if ($user->status !== 'active') {
            Auth::logout();
            $this->addError('email', 'Akun Anda tidak aktif. Hubungi administrator.');
            return;
        }

        ActivityLogger::log('auth', 'login', 'Login: ' . $user->name . ' (' . ($user->role?->name ?? '-') . ')');

        session()->regenerate();

        $role = $user->role?->name;

        // Redirect based on role
        $this->redirect(match ($role) {
            'admin'            => route('dashboard'),
            'approver_level_1' => route('approval-system'),
            'approver_level_2' => route('approval-system'),
            default            => route('dashboard'),
        }, navigate: true);
    }

    public function render()
    {
        return view('pages.auth.login');
    }
}
