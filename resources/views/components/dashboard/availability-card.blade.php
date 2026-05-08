@props(['label', 'value', 'description', 'tone' => 'emerald'])

@php
    $tones = [
        'emerald' => 'bg-lime-500',
        'blue' => 'bg-cyan-500',
        'amber' => 'bg-amber-500',
        'slate' => 'bg-violet-500',
    ];
@endphp

<article class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
    <div class="flex items-center gap-3">
        <span class="h-3 w-3 rounded-full {{ $tones[$tone] }}"></span>
        <h3 class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $label }}</h3>
    </div>
    <p class="mt-4 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $value }}</p>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    <div class="mt-4 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
        <div class="h-full rounded-full {{ $tones[$tone] }}" style="width: {{ min(max((int) $value, 0), 100) }}%"></div>
    </div>
</article>
