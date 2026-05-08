@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-700',
        'lime' => 'bg-lime-50 text-lime-700 ring-lime-200 dark:bg-lime-950 dark:text-lime-300 dark:ring-lime-900',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-200 dark:bg-cyan-950 dark:text-cyan-300 dark:ring-cyan-900',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900',
        'orange' => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:ring-orange-900',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:ring-rose-900',
    ];
@endphp

<section wire:poll.45s class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div wire:loading.grid class="contents">
        @foreach (range(1, 4) as $index)
            <div class="h-36 animate-pulse rounded-lg border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-900"></div>
        @endforeach
    </div>

    @foreach ($this->cards as $card)
        <article wire:loading.remove
            class="group min-h-36 rounded-lg border border-zinc-200 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-orange-900 sm:p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</p>
                    <p class="mt-2 truncate text-2xl font-semibold tracking-normal text-zinc-950 dark:text-white">
                        {{ is_float($card['value']) ? number_format($card['value'], 1, ',', '.') : number_format($card['value'], 0, ',', '.') }}{{ $card['suffix'] }}
                    </p>
                </div>

                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg ring-1 {{ $tones[$card['tone']] ?? $tones['slate'] }}">
                    <x-dashboard.icon :name="$card['icon']" class="h-5 w-5" />
                </span>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                <span
                    class="inline-flex items-center gap-1 rounded-full bg-zinc-50 px-2 py-1 font-medium text-zinc-700 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-200 dark:ring-zinc-800">
                    {{ $card['trend'] }}
                </span>
                <span class="text-zinc-500 dark:text-zinc-400">periode berjalan</span>
            </div>
        </article>
    @endforeach
</section>
