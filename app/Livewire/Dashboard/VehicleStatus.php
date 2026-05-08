<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VehicleStatus extends Component
{
    public array $filters = [];

    #[Computed]
    public function statuses(): array
    {
        $chart = app(DashboardMetricsService::class)->vehicleStatusChart($this->filters);
        $total = max(array_sum($chart['series']), 1);
        $tones = ['emerald', 'cyan', 'amber', 'slate'];

        return collect($chart['labels'])->map(function (string $label, int $index) use ($chart, $total, $tones): array {
            $count = (int) ($chart['series'][$index] ?? 0);

            return [
                'label' => $label,
                'count' => $count,
                'percent' => round(($count / $total) * 100, 1),
                'tone' => $tones[$index] ?? 'slate',
            ];
        })->all();
    }

    public function render()
    {
        return view('components.dashboard.vehicle-status');
    }
}
