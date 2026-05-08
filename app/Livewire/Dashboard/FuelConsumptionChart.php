<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FuelConsumptionChart extends Component
{
    public array $filters = [];

    #[Computed]
    public function chart(): array
    {
        return app(DashboardMetricsService::class)->fuelByMonth($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.fuel-consumption-chart');
    }
}
