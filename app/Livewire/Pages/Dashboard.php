<?php

namespace App\Livewire\Pages;

use App\Services\DashboardMetricsService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public string $date = '';

    public string $month = '';

    public string $year = '';

    public string $regionId = '';

    public string $vehicleTypeId = '';

    public string $vehicleId = '';

    public function mount(): void
    {
        $this->year = (string) now()->year;
        $this->month = (string) now()->month;
    }

    public function resetFilters(): void
    {
        $this->date = '';
        $this->month = (string) now()->month;
        $this->year = (string) now()->year;
        $this->regionId = '';
        $this->vehicleTypeId = '';
        $this->vehicleId = '';
    }

    #[Computed]
    public function filters(): array
    {
        return [
            'date' => $this->date,
            'month' => $this->month,
            'year' => $this->year,
            'region_id' => $this->regionId,
            'vehicle_type_id' => $this->vehicleTypeId,
            'vehicle_id' => $this->vehicleId,
        ];
    }

    #[Computed]
    public function options(): array
    {
        return app(DashboardMetricsService::class)->filterOptions();
    }

    public function render()
    {
        return view('pages.dashboard')->title('mineFleet Dashboard');
    }
}
