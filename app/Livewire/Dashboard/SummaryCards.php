<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SummaryCards extends Component
{
    public array $filters = [];

    #[Computed]
    public function cards(): array
    {
        return app(DashboardMetricsService::class)->summaryCards($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.summary-cards');
    }
}
