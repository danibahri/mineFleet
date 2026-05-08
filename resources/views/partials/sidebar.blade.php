@php
    $role = auth()->user()?->role?->name;
    $isAdmin = $role === 'admin';
    $isApprover1 = $role === 'approver_level_1';
    $isApprover2 = $role === 'approver_level_2';
    $isApprover = $isApprover1 || $isApprover2;
@endphp

<flux:sidebar sticky collapsible class="border-r border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
    <flux:sidebar.header>
        <a href="{{ $isAdmin ? route('dashboard') : route('approval-system') }}"
            class="flex items-center gap-3 px-2 py-1.5">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-900 text-white shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 15.5 6.6 7h10.8l2.6 8.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M6.5 15.5h11M8.5 18.5h.01M15.5 18.5h.01M9 7V4.8h6V7" stroke="currentColor"
                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span class="grid leading-tight">
                <span class="text-sm font-semibold text-slate-950 dark:text-white">MineFleet</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Fleet Monitoring</span>
            </span>
        </a>
        <flux:sidebar.collapse
            class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>

    <flux:sidebar.nav>

        @if ($isAdmin)
            <flux:sidebar.item icon="squares-2x2" href="{{ route('dashboard') }}">Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="truck" href="{{ route('vehicle-management') }}">Vehicle Management
            </flux:sidebar.item>
            <flux:sidebar.item icon="calendar-days" href="{{ route('booking-management') }}">Booking Management
            </flux:sidebar.item>
            <flux:sidebar.item icon="clipboard-document-check" href="{{ route('approval-system') }}">Approval Management
            </flux:sidebar.item>
            <flux:sidebar.item icon="identification" href="{{ route('driver-management') }}">Driver Management
            </flux:sidebar.item>
            <flux:sidebar.item icon="beaker" href="{{ route('fuel-monitoring') }}">Fuel Monitoring</flux:sidebar.item>
            <flux:sidebar.item icon="wrench-screwdriver" href="{{ route('service-maintenance') }}">Service & Maintenance
            </flux:sidebar.item>
            <flux:sidebar.item icon="map" href="#">Vehicle Usage</flux:sidebar.item>
            <flux:sidebar.item icon="chart-bar-square" href="{{ route('reports') }}">Reports & Export
            </flux:sidebar.item>
            <flux:sidebar.item icon="clock" href="{{ route('activity-logs') }}">Activity Logs</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="{{ route('user-management') }}">User Management</flux:sidebar.item>
        @endif

        @if ($isApprover)
            <flux:sidebar.item icon="clock" href="{{ route('approval-system') }}"
                :current="request()->routeIs('approval-system')">Approval System
            </flux:sidebar.item>
        @endif

    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    <flux:sidebar.nav>
        @if ($isAdmin)
            <flux:sidebar.item icon="cog-6-tooth" href="{{ route('settings') }}">Settings</flux:sidebar.item>
        @endif
    </flux:sidebar.nav>
</flux:sidebar>
