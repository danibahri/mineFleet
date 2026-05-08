<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RecentActivity extends Component
{
    #[Computed]
    public function logs()
    {
        return app(DashboardMetricsService::class)->recentActivityLogs();
    }

    public function render()
    {
        return view('components.dashboard.recent-activity');
    }
}
