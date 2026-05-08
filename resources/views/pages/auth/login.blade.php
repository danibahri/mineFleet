<div class="flex min-h-screen items-center justify-center p-4">

    {{-- Background decorations --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-emerald-500/5 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Logo & Brand --}}
        <div class="mb-8 flex flex-col items-center gap-3 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 shadow-lg shadow-emerald-600/30">
                <svg class="h-9 w-9 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 15.5 6.6 7h10.8l2.6 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.5 15.5h11M8.5 18.5h.01M15.5 18.5h.01M9 7V4.8h6V7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">MineFleet</h1>
                <p class="text-sm text-slate-400">Fleet Monitoring & Booking System</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-white">Masuk ke Sistem</h2>
                <p class="mt-1 text-sm text-slate-400">Gunakan email dan password yang terdaftar.</p>
            </div>

            <form wire:submit="login" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300">Email</label>
                    <input id="email" type="email" wire:model.defer="email" autocomplete="email" autofocus
                        placeholder="user@minefleet.test"
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500/60 focus:bg-white/10 focus:ring-2 focus:ring-emerald-500/20" />
                    @error('email')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-rose-400">
                            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none"><path d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                    <input id="password" type="password" wire:model.defer="password" autocomplete="current-password"
                        placeholder="••••••••"
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-emerald-500/60 focus:bg-white/10 focus:ring-2 focus:ring-emerald-500/20" />
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-400">
                        <input type="checkbox" wire:model.defer="remember" class="rounded border-slate-600 bg-white/5 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0" />
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="relative w-full overflow-hidden rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-transparent"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Masuk ke Sistem</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Memverifikasi...
                    </span>
                </button>
            </form>

            {{-- Demo credentials --}}
            <div class="mt-6 rounded-xl border border-white/5 bg-white/3 p-4">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Demo Credentials</p>
                <div class="space-y-1.5 text-xs">
                    @foreach ([
                        ['role' => 'Admin', 'email' => 'admin@minefleet.test', 'color' => 'text-violet-400'],
                        ['role' => 'Approver Lv.1', 'email' => 'approver1@minefleet.test', 'color' => 'text-blue-400'],
                        ['role' => 'Approver Lv.2', 'email' => 'approver2@minefleet.test', 'color' => 'text-cyan-400'],
                    ] as $demo)
                        <div class="flex items-center justify-between">
                            <span class="{{ $demo['color'] }} font-medium">{{ $demo['role'] }}</span>
                            <span class="font-mono text-slate-400">{{ $demo['email'] }}</span>
                        </div>
                    @endforeach
                    <p class="mt-1.5 text-slate-500">Password semua akun: <span class="font-mono text-slate-400">password</span></p>
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-600">
            &copy; {{ date('Y') }} MineFleet · Fleet Monitoring System
        </p>
    </div>
</div>
