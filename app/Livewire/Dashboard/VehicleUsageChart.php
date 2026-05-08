<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VehicleUsageChart extends Component
{
    public array $filters = [];

    #[Computed]
    public function chart(): array
    {
        return app(DashboardMetricsService::class)->usageByMonth($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.vehicle-usage-chart');
    }
}
