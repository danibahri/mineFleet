<x-dashboard.chart-card title="Status Kendaraan" subtitle="Distribusi ketersediaan armada saat ini">
    <div wire:loading class="h-full animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    <div wire:loading.remove wire:ignore wire:key="status-chart-{{ md5(json_encode($this->chart)) }}" class="h-full"
        x-data="mineFleetChart({
            type: 'donut',
            colors: ['#84cc16', '#06b6d4', '#f59e0b', '#71717a'],
            labels: @js($this->chart['labels']),
            series: @js($this->chart['series'])
        })"
        x-init="render($el)">
    </div>
</x-dashboard.chart-card>
