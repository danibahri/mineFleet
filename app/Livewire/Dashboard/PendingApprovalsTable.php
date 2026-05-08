<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Component;
use Livewire\WithPagination;

class PendingApprovalsTable extends Component
{
    use WithPagination;

    public array $filters = [];

    public function render()
    {
        return view('livewire.dashboard.pending-approvals-table', [
            'approvals' => app(DashboardMetricsService::class)
                ->pendingApprovals($this->filters)
                ->paginate(5),
        ]);
    }
}
