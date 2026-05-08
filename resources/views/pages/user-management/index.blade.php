<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">User Management</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola pengguna, role, dan hak akses sistem.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                {{ now()->format('d M Y') }}
            </div>
            <flux:button variant="primary" color="emerald" size="sm" wire:click="openCreateForm">+ Tambah User</flux:button>
        </div>
    </div>

    {{-- Role Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($this->stats as $s)
            @php $rc = $this->roleBadgeClass($s->role?->name ?? ''); @endphp
            <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $this->roleLabel($s->role?->name ?? '-') }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $s->count }}</p>
                <span class="{{ $rc }} mt-2 inline-block rounded-full px-2.5 py-1 text-xs font-semibold">{{ ucfirst(str_replace('_', ' ', $s->role?->name ?? '-')) }}</span>
            </flux:card>
        @endforeach
    </div>

    {{-- Table --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Daftar Pengguna</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Filter berdasarkan role atau status.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari nama, email…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="roleId" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua role</option>
                    @foreach ($this->roles as $r)
                        <option value="{{ $r->id }}">{{ $this->roleLabel($r->name) }}</option>
                    @endforeach
                </select>
                <select wire:model="status" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-32 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[760px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Pengguna</th>
                        <th class="pb-3">Role</th>
                        <th class="pb-3">Region</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->users as $user)
                        @php
                            $sc = $user->status === 'active'
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400';
                            $rc = $this->roleBadgeClass($user->role?->name ?? '');
                        @endphp
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-200">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="{{ $rc }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ $this->roleLabel($user->role?->name ?? '-') }}
                                </span>
                            </td>
                            <td class="py-3">{{ $user->region?->name ?? '-' }}</td>
                            <td class="py-3">
                                <span class="{{ $sc }} rounded-full px-2.5 py-1 text-xs font-semibold">{{ ucfirst($user->status) }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditForm({{ $user->id }})">Edit</flux:button>
                                    <x-confirm-delete name="del-user-{{ $user->id }}" title="Hapus user?" message="Hapus {{ $user->name }} dari sistem." confirm-label="Hapus" button-label="Delete" wire:click="delete({{ $user->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada data user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $this->users->links() }}</div>
    </flux:card>

    {{-- MODAL Form --}}
    @if ($activeModal === 'form')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900 max-h-[90vh] overflow-y-auto">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $userId ? 'Edit User' : 'Tambah User Baru' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $userId ? 'Perbarui data pengguna.' : 'Isi informasi pengguna baru.' }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama</label>
                        <input type="text" wire:model.defer="formName" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        @error('formName') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Email</label>
                            <input type="email" wire:model.defer="formEmail" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formEmail') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nomor HP</label>
                            <input type="text" wire:model.defer="formPhone" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Role</label>
                            <select wire:model.defer="formRoleId" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Pilih role</option>
                                @foreach ($this->roles as $r)
                                    <option value="{{ $r->id }}">{{ $this->roleLabel($r->name) }}</option>
                                @endforeach
                            </select>
                            @error('formRoleId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Region</label>
                            <select wire:model.defer="formRegionId" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">— Semua Region —</option>
                                @foreach ($this->regions as $reg)
                                    <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Status</label>
                            <select wire:model.defer="formStatus" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                                Password {{ $userId ? '(kosongkan jika tidak diubah)' : '*' }}
                            </label>
                            <input type="password" wire:model.defer="formPassword" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formPassword') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="save">{{ $userId ? 'Update' : 'Simpan' }}</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
