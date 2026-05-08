<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->cards as $card)
            @php
                $trendUp = !str_starts_with($card['trend'], '-');
            @endphp
            <div
                class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            @switch($card['icon'])
                                @case('truck')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3 7h11v6h6l-2-4h-4V7" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5 17a2 2 0 1 0 4 0m8 0a2 2 0 1 0 4 0M3 17h2m4 0h7" stroke="currentColor"
                                            stroke-width="1.7" stroke-linecap="round" />
                                    </svg>
                                @break

                                @case('calendar-days')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="M8 3v4m8-4v4M4 9h16M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                @break

                                @case('clock')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 8v4l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor"
                                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @break

                                @case('wrench-screwdriver')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="m14.5 9.5 2 2m-8 8 6-6m4-8-3 3m3-3a3.5 3.5 0 0 0-4.95 4.95L5 19.5a2 2 0 0 0 2.83 2.83l8.55-8.55A3.5 3.5 0 0 0 20.5 9"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                @break

                                @case('identification')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M15 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M15 3v4h4M7 11h6M7 15h8" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @break

                                @case('beaker')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3" stroke="currentColor"
                                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M8 14h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    </svg>
                                @break
                            @endswitch
                        </span>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                {{ $card['label'] }}</p>
                            <p class="text-2xl font-semibold text-slate-900 dark:text-white">
                                {{ number_format($card['value'], 0, ',', '.') }}{{ $card['suffix'] ?? '' }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="{{ $trendUp ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300' }} rounded-full px-2.5 py-1 text-xs font-semibold">
                        {{ $card['trend'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</flux:card>
