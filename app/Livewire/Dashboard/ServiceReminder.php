<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServiceReminder extends Component
{
    public array $filters = [];

    #[Computed]
    public function reminders()
    {
        return app(DashboardMetricsService::class)->serviceReminders($this->filters);
    }

    public function render()
    {
        return view('components.dashboard.service-reminder');
    }
}
