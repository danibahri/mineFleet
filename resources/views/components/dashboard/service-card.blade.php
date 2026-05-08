@props(['vehicle', 'plate', 'odometer', 'date', 'urgency' => 'normal'])

@php
    $styles = [
        'normal' => ['label' => 'Normal', 'class' => 'bg-lime-50 text-lime-700 ring-lime-200 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900'],
        'warning' => ['label' => 'Warning', 'class' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900'],
        'urgent' => ['label' => 'Urgent', 'class' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-900'],
    ];
@endphp

<article class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h3 class="truncate font-semibold text-zinc-950 dark:text-white">{{ $vehicle }}</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $plate }} · {{ $odometer }} km</p>
        </div>
        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $styles[$urgency]['class'] }}">
            {{ $styles[$urgency]['label'] }}
        </span>
    </div>
    <div class="mt-4 flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
        <svg class="h-4 w-4 text-cyan-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 2v4M16 2v4M3.5 9.5h17M5 5.5h14A1.5 1.5 0 0 1 20.5 7v12A1.5 1.5 0 0 1 19 20.5H5A1.5 1.5 0 0 1 3.5 19V7A1.5 1.5 0 0 1 5 5.5Z"
                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
        </svg>
        Next service: {{ $date }}
    </div>
</article>
