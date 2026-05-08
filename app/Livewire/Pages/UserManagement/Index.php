<?php

namespace App\Livewire\Pages\UserManagement;

use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $roleId   = '';
    public string $status   = '';
    public int    $perPage  = 15;
    public string $activeModal = ''; // 'form' | 'reset_password'

    // Form
    public ?int   $userId        = null;
    public string $formName      = '';
    public string $formEmail     = '';
    public string $formPhone     = '';
    public string $formRoleId    = '';
    public string $formRegionId  = '';
    public string $formStatus    = 'active';
    public string $formPassword  = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleId(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    #[Computed]
    public function users()
    {
        return User::query()
            ->with(['role:id,name', 'region:id,name'])
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleId !== '', fn($q) => $q->where('role_id', $this->roleId))
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function roles()
    {
        return Role::query()->orderBy('name')->get(['id', 'name', 'description']);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function stats()
    {
        return User::query()
            ->selectRaw('role_id, COUNT(*) as count')
            ->with('role:id,name')
            ->groupBy('role_id')
            ->get();
    }

    public function roleLabel(string $name): string
    {
        return match ($name) {
            'admin'            => 'Admin',
            'approver_level_1' => 'Approver Lv.1',
            'approver_level_2' => 'Approver Lv.2',
            'driver'           => 'Driver',
            default            => ucfirst($name),
        };
    }

    public function roleBadgeClass(string $name): string
    {
        return match ($name) {
            'admin'            => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300',
            'approver_level_1' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300',
            'approver_level_2' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
            'driver'           => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
            default            => 'bg-slate-100 text-slate-600',
        };
    }

    // ── Modal ─────────────────────────────────────────────────────────────────
    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->activeModal = 'form';
    }

    public function openEditForm(int $id): void
    {
        $user = User::query()->findOrFail($id);
        $this->userId       = $user->id;
        $this->formName     = $user->name;
        $this->formEmail    = $user->email;
        $this->formPhone    = (string) ($user->phone ?? '');
        $this->formRoleId   = (string) ($user->role_id ?? '');
        $this->formRegionId = (string) ($user->region_id ?? '');
        $this->formStatus   = (string) $user->status;
        $this->formPassword = '';
        $this->activeModal  = 'form';
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->resetForm();
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $isUpdate = $this->userId !== null;

        $rules = [
            'formName'     => ['required', 'string', 'max:100'],
            'formEmail'    => ['required', 'email', 'unique:users,email,' . ($this->userId ?? 'NULL') . ',id'],
            'formPhone'    => ['nullable', 'string', 'max:30'],
            'formRoleId'   => ['required', 'exists:roles,id'],
            'formRegionId' => ['nullable', 'exists:regions,id'],
            'formStatus'   => ['required', 'in:active,inactive'],
            'formPassword' => $isUpdate ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
        ];

        $validated = $this->validate($rules);

        $data = [
            'name'      => $validated['formName'],
            'email'     => $validated['formEmail'],
            'phone'     => $validated['formPhone'] !== '' ? $validated['formPhone'] : null,
            'role_id'   => $validated['formRoleId'],
            'region_id' => $validated['formRegionId'] !== '' ? $validated['formRegionId'] : null,
            'status'    => $validated['formStatus'],
        ];

        if ($validated['formPassword'] !== '') {
            $data['password'] = $validated['formPassword'];
        }

        User::query()->updateOrCreate(['id' => $this->userId], $data);

        ActivityLogger::log('user_management', $isUpdate ? 'update' : 'create', ($isUpdate ? 'Update' : 'Tambah') . ' user: ' . $validated['formName']);

        $this->activeModal = '';
        $this->resetForm();
        unset($this->users, $this->stats);
        Flux::toast($isUpdate ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.');
    }

    public function delete(int $id): void
    {
        $user = User::query()->findOrFail($id);
        ActivityLogger::log('user_management', 'delete', 'Hapus user: ' . $user->name);
        $user->delete();
        unset($this->users, $this->stats);
        Flux::toast('User berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->userId       = null;
        $this->formName     = '';
        $this->formEmail    = '';
        $this->formPhone    = '';
        $this->formRoleId   = '';
        $this->formRegionId = '';
        $this->formStatus   = 'active';
        $this->formPassword = '';
    }

    public function render()
    {
        return view('pages.user-management.index');
    }
}
