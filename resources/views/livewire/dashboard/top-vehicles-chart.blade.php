<x-dashboard.chart-card title="Top Kendaraan Terpakai" subtitle="Lima kendaraan dengan jumlah booking tertinggi">
    <div wire:loading class="h-full animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    <div wire:loading.remove wire:ignore wire:key="top-chart-{{ md5(json_encode($this->chart)) }}" class="h-full"
        x-data="mineFleetChart({
            type: 'bar',
            horizontal: true,
            colors: ['#eab308'],
            categories: @js($this->chart['categories']),
            series: @js($this->chart['series']),
            options: { yaxisTitle: 'Booking' }
        })"
        x-init="render($el)">
    </div>
</x-dashboard.chart-card>
