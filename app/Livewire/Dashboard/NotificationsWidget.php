<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationsWidget extends Component
{
    public array $filters = [];

    #[Computed]
    public function notifications(): array
    {
        return app(DashboardMetricsService::class)->notifications($this->filters);
    }

    public function render()
    {
        return view('livewire.dashboard.notifications-widget');
    }
}
