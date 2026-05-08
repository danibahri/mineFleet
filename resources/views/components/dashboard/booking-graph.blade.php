<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Booking Per Bulan</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Statistik pemesanan kendaraan sepanjang tahun</p>
        </div>
        <span class="text-xs text-slate-400 dark:text-slate-500">{{ $this->chart['seriesName'] }}</span>
    </div>

    <div class="mt-6">
        <div class="flex h-44 items-end gap-3">
            @foreach ($this->chart['items'] as $item)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div class="flex h-32 w-full items-end">
                        <div class="w-full rounded-lg bg-emerald-100 dark:bg-emerald-500/20"
                            style="height: {{ max($item['percent'], 6) }}%;">
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span>Max {{ number_format($this->chart['max']) }} booking</span>
            <span>Updated {{ now()->format('M Y') }}</span>
        </div>
    </div>
</flux:card>
