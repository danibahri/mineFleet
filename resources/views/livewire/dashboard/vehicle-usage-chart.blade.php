<x-dashboard.chart-card title="Pemakaian Kendaraan" subtitle="Jumlah booking kendaraan per bulan pada tahun terpilih">
    <x-slot:action>
        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 ring-1 ring-orange-200 dark:bg-orange-950 dark:text-orange-300 dark:ring-orange-900">
            {{ $filters['year'] ?? now()->year }}
        </span>
    </x-slot:action>

    <div wire:loading class="h-full animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    <div wire:loading.remove wire:ignore wire:key="usage-chart-{{ md5(json_encode($this->chart)) }}" class="h-full"
        x-data="mineFleetChart({
            type: 'bar',
            colors: ['#f97316'],
            categories: @js($this->chart['categories']),
            series: @js($this->chart['series']),
            options: { yaxisTitle: 'Booking', distributed: false }
        })"
        x-init="render($el)">
    </div>
</x-dashboard.chart-card>
