<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Recent Activity</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Aktivitas terbaru sistem</p>
        </div>
        <span class="text-xs text-slate-400 dark:text-slate-500">Live</span>
    </div>

    <div class="mt-6 space-y-5">
        @forelse ($this->logs as $log)
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    <span class="h-full w-px bg-slate-200 dark:bg-slate-800"></span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">
                        {{ $log->user->name ?? 'System' }}
                        <span
                            class="text-xs font-normal text-slate-400 dark:text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $log->description ?? ($log->action ?? '-') }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada aktivitas.</p>
        @endforelse
    </div>
</flux:card>
