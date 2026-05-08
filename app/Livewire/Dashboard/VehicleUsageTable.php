<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VehicleUsageTable extends Component
{
    public array $filters = [];

    #[Computed]
    public function rows()
    {
        return app(DashboardMetricsService::class)->vehicleUsageTable($this->filters);
    }

    public function render()
    {
        return view('components.dashboard.vehicle-usage-table');
    }
}
