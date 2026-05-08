<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">My Profile</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola informasi pribadi dan pengaturan akun Anda.</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Profile Information --}}
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <p class="mb-5 text-sm font-semibold text-slate-900 dark:text-white">Informasi Profil</p>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama Lengkap</label>
                    <input type="text" wire:model.defer="name" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Email</label>
                    <input type="email" wire:model.defer="email" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('email') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nomor Telepon</label>
                    <input type="text" wire:model.defer="phone" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('phone') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="updateProfile">Simpan Profil</flux:button>
                </div>
            </div>
        </flux:card>

        {{-- Password Update --}}
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <p class="mb-5 text-sm font-semibold text-slate-900 dark:text-white">Ubah Password</p>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Password Saat Ini</label>
                    <input type="password" wire:model.defer="currentPassword" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('currentPassword') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Password Baru</label>
                    <input type="password" wire:model.defer="newPassword" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('newPassword') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Konfirmasi Password Baru</label>
                    <input type="password" wire:model.defer="newPasswordConfirmation" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('newPasswordConfirmation') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="updatePassword">Ubah Password</flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</div>
