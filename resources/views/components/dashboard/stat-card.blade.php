@props([
    'label',
    'value',
    'trend',
    'tone' => 'emerald',
    'icon',
])

@php
    $tones = [
        'emerald' => 'bg-lime-50 text-lime-700 ring-lime-200 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900',
        'blue' => 'bg-cyan-50 text-cyan-700 ring-cyan-200 dark:bg-cyan-950 dark:text-cyan-300 dark:ring-cyan-900',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-900',
        'slate' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-950 dark:text-violet-300 dark:ring-violet-900',
    ];
@endphp

<article
    class="min-h-36 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-950 sm:p-5">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-normal text-zinc-950 dark:text-white">{{ $value }}</p>
        </div>
        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ring-1 {{ $tones[$tone] }}">
            {!! $icon !!}
        </span>
    </div>
    <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
        <span class="inline-flex items-center gap-1 rounded-full bg-lime-50 px-2 py-1 font-medium text-lime-700 ring-1 ring-lime-100 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 3a.75.75 0 0 1 .53.22l4.25 4.25a.75.75 0 0 1-1.06 1.06l-2.97-2.97V16a.75.75 0 0 1-1.5 0V5.56L6.28 8.53a.75.75 0 0 1-1.06-1.06l4.25-4.25A.75.75 0 0 1 10 3Z"
                    clip-rule="evenodd" />
            </svg>
            {{ $trend }}
        </span>
        <span class="text-zinc-500 dark:text-zinc-400">vs last month</span>
    </div>
</article>
