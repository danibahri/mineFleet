@props(['status'])

@php
    $styles = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900',
        'approved' => 'bg-lime-50 text-lime-700 ring-lime-200 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-900',
        'completed' => 'bg-cyan-50 text-cyan-700 ring-cyan-200 dark:bg-cyan-950 dark:text-cyan-300 dark:ring-cyan-900',
        'cancelled' => 'bg-zinc-50 text-zinc-700 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-800',
    ];
@endphp

<span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold capitalize ring-1 {{ $styles[$status] ?? $styles['pending'] }}">
    {{ $status }}
</span>
