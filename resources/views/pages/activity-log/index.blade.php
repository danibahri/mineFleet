<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Activity Logs</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Rekam jejak seluruh aktivitas sistem secara real-time.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([['label'=>'Hari Ini','key'=>'today','class'=>'text-emerald-600 dark:text-emerald-400'],['label'=>'Minggu Ini','key'=>'week','class'=>'text-blue-600 dark:text-blue-400'],['label'=>'Total Log','key'=>'total','class'=>'text-slate-900 dark:text-white'],['label'=>'Pengguna Aktif','key'=>'users','class'=>'text-violet-600 dark:text-violet-400']] as $s)
            <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
                <p class="mt-1 text-2xl font-bold {{ $s['class'] }}">{{ number_format($this->stats[$s['key']]) }}</p>
            </flux:card>
        @endforeach
    </div>

    {{-- Table --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Log Aktivitas</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Filter berdasarkan user, modul, aksi, atau rentang tanggal.</p>
            </div>
            <div class="flex w-full flex-wrap gap-3 lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari deskripsi, modul…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-48 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="userId" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua user</option>
                    @foreach ($this->users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <select wire:model="module" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-36 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua modul</option>
                    @foreach ($this->modules as $m)
                        <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
                <select wire:model="action" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-32 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua aksi</option>
                    @foreach ($this->actions as $a)
                        <option value="{{ $a }}">{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-500 dark:text-slate-400">Dari:</label>
                <input type="date" wire:model="dateFrom" class="h-8 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-500 dark:text-slate-400">Sampai:</label>
                <input type="date" wire:model="dateTo" class="h-8 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
            </div>
            @if ($search || $userId || $module || $action || $dateFrom || $dateTo)
                <button wire:click="$set('search','');$set('userId','');$set('module','');$set('action','');$set('dateFrom','');$set('dateTo','')"
                    class="h-8 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    Reset filter
                </button>
            @endif
        </div>

        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[780px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Waktu</th>
                        <th class="pb-3">User</th>
                        <th class="pb-3">Modul</th>
                        <th class="pb-3">Aksi</th>
                        <th class="pb-3">Deskripsi</th>
                        <th class="pb-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->logs as $log)
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">
                                <p class="text-xs font-medium">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</p>
                            </td>
                            <td class="py-3">
                                <p class="font-medium">{{ $log->user?->name ?? 'System' }}</p>
                                <p class="text-xs text-slate-400">{{ $log->user?->email ?? '' }}</p>
                            </td>
                            <td class="py-3">
                                <span class="{{ $this->moduleClass($log->module) }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ ucfirst($log->module) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="{{ $this->actionClass($log->action) }} text-xs font-semibold uppercase tracking-wide">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 max-w-xs">
                                <p class="truncate text-xs text-slate-600 dark:text-slate-300">{{ $log->description ?? '-' }}</p>
                            </td>
                            <td class="py-3 font-mono text-xs text-slate-400">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $this->logs->links() }}</div>
    </flux:card>
</div>
