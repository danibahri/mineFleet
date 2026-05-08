<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardMetricsService;
use Livewire\Component;
use Livewire\WithPagination;

class RecentBookingsTable extends Component
{
    use WithPagination;

    public array $filters = [];

    public string $search = '';

    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.dashboard.recent-bookings-table', [
            'bookings' => app(DashboardMetricsService::class)
                ->recentBookings($this->filters, trim($this->search), $this->status)
                ->paginate(6),
        ]);
    }
}
