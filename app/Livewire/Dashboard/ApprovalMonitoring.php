<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalMonitoring extends Component
{
    use WithPagination;

    public array $filters = [];

    public string $search = '';

    public string $status = '';

    public int $perPage = 6;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function approvals()
    {
        return app(DashboardMetricsService::class)
            ->approvalMonitoring($this->filters, $this->search, $this->status)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('components.dashboard.approval-monitoring');
    }
}
