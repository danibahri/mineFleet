<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReportWidget extends Component
{
    public array $filters = [];

    #[Computed]
    public function report(): array
    {
        return app(DashboardMetricsService::class)->reportSummary($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.report-widget');
    }
}
