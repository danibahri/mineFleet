<flux:header class="border-b border-slate-200 bg-white/95 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-950/90">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <div class="hidden min-w-0 flex-1 items-center gap-3 lg:flex">
        <div class="relative w-full max-w-xl">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" />
            </svg>
            <input type="search" placeholder="Search bookings, vehicles, drivers..."
                class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:focus:bg-slate-950">
        </div>
    </div>

    <flux:spacer />

    {{-- Notification Bell --}}
    <button type="button"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
        aria-label="Notifications">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 17H9m9-6a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7ZM13.73 21a2 2 0 0 1-3.46 0"
                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span
            class="absolute right-2 top-2 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
    </button>

    {{-- Dark Mode Toggle --}}
    <flux:dropdown x-data align="end" class="mx-2">
        <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
            <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
            <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
            <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
            <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
        </flux:button>

        <flux:menu>
            <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
            <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
            <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
        </flux:menu>
    </flux:dropdown>

    {{-- User Dropdown --}}
    <flux:dropdown position="top" align="start">
        <flux:profile name="{{ auth()->user()?->name ?? 'Guest' }}" />
        <flux:menu>
            {{-- User info --}}
            <div class="px-3 py-2">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()?->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()?->email }}</p>
                @php
                    $roleName = auth()->user()?->role?->name;
                    $roleLabel = match ($roleName) {
                        'admin' => 'Admin',
                        'approver_level_1' => 'Approver Level 1',
                        'approver_level_2' => 'Approver Level 2',
                        'driver' => 'Driver',
                        default => ucfirst($roleName ?? '-'),
                    };
                    $roleColor = match ($roleName) {
                        'admin' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300',
                        'approver_level_1' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300',
                        'approver_level_2' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700/40 dark:text-slate-400',
                    };
                @endphp
                <span class="{{ $roleColor }} mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold">
                    {{ $roleLabel }}
                </span>
            </div>
            <flux:menu.separator />
            <flux:menu.item icon="user-circle" href="{{ route('profile') }}">Profil Saya</flux:menu.item>

            @if (Auth::user()->role->name === 'admin')
                <flux:menu.item icon="cog-6-tooth" href="{{ route('settings') }}">Settings</flux:sidebar.item>
            @endif

            <flux:menu.separator />
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <flux:menu.item icon="arrow-right-start-on-rectangle" class="text-rose-600 dark:text-rose-400"
                    x-on:click="$el.closest('form').submit()">
                    Logout
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
