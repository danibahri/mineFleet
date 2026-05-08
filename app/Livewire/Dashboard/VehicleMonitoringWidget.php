<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VehicleMonitoringWidget extends Component
{
    public array $filters = [];

    #[Computed]
    public function monitoring(): array
    {
        return app(DashboardMetricsService::class)->monitoring($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.vehicle-monitoring-widget');
    }
}
