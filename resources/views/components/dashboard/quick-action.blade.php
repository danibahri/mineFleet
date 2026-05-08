@props(['label', 'icon', 'tone' => 'slate'])

@php
    $tones = [
        'emerald' => 'hover:border-lime-300 hover:bg-lime-50 hover:text-lime-700 dark:hover:border-lime-800 dark:hover:bg-lime-950 dark:hover:text-lime-300',
        'blue' => 'hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700 dark:hover:border-cyan-800 dark:hover:bg-cyan-950 dark:hover:text-cyan-300',
        'amber' => 'hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 dark:hover:border-amber-800 dark:hover:bg-amber-950 dark:hover:text-amber-300',
        'slate' => 'hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 dark:hover:border-violet-800 dark:hover:bg-violet-950 dark:hover:text-violet-300',
    ];
@endphp

<button type="button"
    class="group flex min-h-16 items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3 text-left text-sm font-semibold text-zinc-700 shadow-sm transition {{ $tones[$tone] }} dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200 sm:p-4">
    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-zinc-700 shadow-sm transition group-hover:bg-white dark:bg-zinc-950 dark:text-zinc-300">
        {!! $icon !!}
    </span>
    <span class="min-w-0">{{ $label }}</span>
</button>
