<?php

namespace App\Livewire\Pages\Profile;

use App\Services\ActivityLogger;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Index extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        ActivityLogger::log('profile', 'update', 'Update profil pengguna.');

        Flux::toast('Profil berhasil diperbarui.', variant: 'success');
    }

    public function updatePassword()
    {
        $user = auth()->user();

        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', 'min:8', 'same:newPasswordConfirmation'],
        ]);

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);

        ActivityLogger::log('profile', 'update_password', 'Update password pengguna.');

        Flux::toast('Password berhasil diperbarui.', variant: 'success');
    }

    public function render()
    {
        return view('pages.profile.index');
    }
}
