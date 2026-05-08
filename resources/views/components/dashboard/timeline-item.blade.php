@props(['title', 'description', 'time', 'tone' => 'emerald', 'icon'])

@php
    $tones = [
        'emerald' => 'bg-lime-500',
        'blue' => 'bg-cyan-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
        'slate' => 'bg-violet-500',
    ];
@endphp

<li class="relative flex gap-3 pb-6 last:pb-0 sm:gap-4">
    <span class="absolute left-4 top-9 h-full w-px bg-zinc-200 last:hidden dark:bg-zinc-800"></span>
    <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white {{ $tones[$tone] }}">
        {!! $icon !!}
    </span>
    <div class="min-w-0 flex-1">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
            <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $title }}</p>
            <time class="shrink-0 text-xs text-zinc-400">{{ $time }}</time>
        </div>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    </div>
</li>
