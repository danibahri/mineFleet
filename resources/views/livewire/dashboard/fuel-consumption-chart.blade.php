<x-dashboard.chart-card title="Konsumsi BBM" subtitle="Liter BBM dan estimasi biaya per bulan">
    <x-slot:action>
        <span class="rounded-full bg-zinc-50 px-3 py-1 text-xs font-semibold text-zinc-700 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-200 dark:ring-zinc-800">
            biaya dalam juta rupiah
        </span>
    </x-slot:action>

    <div wire:loading class="h-full animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    <div wire:loading.remove wire:ignore wire:key="fuel-chart-{{ md5(json_encode($this->chart)) }}" class="h-full"
        x-data="mineFleetChart({
            type: 'area',
            colors: ['#f97316', '#eab308'],
            categories: @js($this->chart['categories']),
            series: @js($this->chart['series']),
            options: { yaxisTitle: 'Liter / Juta Rp' }
        })"
        x-init="render($el)">
    </div>
</x-dashboard.chart-card>
