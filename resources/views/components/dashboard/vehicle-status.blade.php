<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Status Kendaraan</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Distribusi armada saat ini</p>
        </div>
        <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Realtime</span>
    </div>

    <div class="mt-6 space-y-4">
        @foreach ($this->statuses as $status)
            @php
                $barClass = match ($status['tone']) {
                    'emerald' => 'bg-emerald-500',
                    'cyan' => 'bg-cyan-500',
                    'amber' => 'bg-amber-500',
                    'slate' => 'bg-slate-500',
                    default => 'bg-slate-500',
                };
            @endphp
            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ $status['label'] }}</span>
                    <span class="text-slate-500 dark:text-slate-400">{{ number_format($status['count']) }}
                        ({{ $status['percent'] }}%)</span>
                </div>
                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                    <div class="{{ $barClass }} h-2 rounded-full" style="width: {{ $status['percent'] }}%;"></div>
                </div>
            </div>
        @endforeach
    </div>
</flux:card>
