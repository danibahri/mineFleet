@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-700',
        'lime' => 'bg-lime-50 text-lime-700 ring-lime-200 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900',
        'orange' => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:ring-orange-900',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-900',
    ];
@endphp

<section wire:poll.45s class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 sm:p-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Notifikasi Operasional</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Sinyal penting untuk admin dan approver.</p>
        </div>
        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:ring-orange-900">
            live
        </span>
    </div>

    <div wire:loading class="mt-5 h-48 animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>

    <div wire:loading.remove class="mt-5 grid gap-3">
        @foreach ($this->notifications as $notification)
            <article class="flex items-center gap-3 rounded-lg border border-zinc-100 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900/60">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ring-1 {{ $tones[$notification['tone']] ?? $tones['slate'] }}">
                    <x-dashboard.icon :name="$notification['icon']" class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <p class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ $notification['title'] }}</p>
                        <p class="text-lg font-semibold text-zinc-950 dark:text-white">{{ number_format($notification['value'], 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $notification['description'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>
