<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BookingGraph extends Component
{
    public array $filters = [];

    #[Computed]
    public function chart(): array
    {
        $chart = app(DashboardMetricsService::class)->usageByMonth($this->filters);
        $values = $chart['series'][0]['data'] ?? [];
        $max = max($values ?: [1]);

        $items = collect($chart['categories'])->map(function (string $label, int $index) use ($values, $max): array {
            $value = (int) ($values[$index] ?? 0);

            return [
                'label' => $label,
                'value' => $value,
                'percent' => round(($value / max($max, 1)) * 100, 1),
            ];
        })->all();

        return [
            'items' => $items,
            'max' => $max,
            'seriesName' => $chart['series'][0]['name'] ?? 'Booking',
        ];
    }

    public function render()
    {
        return view('components.dashboard.booking-graph');
    }
}
