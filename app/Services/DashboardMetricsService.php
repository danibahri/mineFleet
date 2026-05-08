<?php

namespace App\Services;

use App\Models\BookingApproval;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\Region;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Models\VehicleService;
use App\Models\VehicleType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardMetricsService
{
    public function filterOptions(): array
    {
        return [
            'regions' => Region::query()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'vehicleTypes' => VehicleType::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'vehicles' => Vehicle::query()
                ->orderBy('code')
                ->get(['id', 'code', 'plate_number', 'brand', 'model']),
        ];
    }

    public function summaryCards(array $filters): array
    {
        $period = $this->period($filters);
        $previous = [
            'period_start' => $period['period_start']->subMonthNoOverflow(),
            'period_end' => $period['period_start']->subSecond(),
        ];

        $vehicleQuery = $this->vehicleQuery($filters);
        $bookingQuery = $this->bookingQuery($filters);
        $fuelQuery = $this->fuelQuery($filters);
        $driverQuery = $this->driverQuery($filters);

        return [
            $this->card('Total kendaraan', $vehicleQuery->clone()->count(), $this->percentageChange(
                $this->vehicleQuery($filters)->whereBetween('created_at', [$period['period_start'], $period['period_end']])->count(),
                $this->vehicleQuery($filters)->whereBetween('created_at', [$previous['period_start'], $previous['period_end']])->count(),
            ), 'truck', 'slate'),
            $this->card('Kendaraan tersedia', $this->vehicleQuery($filters)->where('status', 'available')->count(), $this->share(
                $this->vehicleQuery($filters)->where('status', 'available')->count(),
                max($this->vehicleQuery($filters)->count(), 1),
            ), 'check-circle', 'lime'),
            $this->card('Sedang digunakan', $this->vehicleQuery($filters)->where('status', 'booked')->count(), $this->share(
                $this->vehicleQuery($filters)->where('status', 'booked')->count(),
                max($this->vehicleQuery($filters)->count(), 1),
            ), 'map-pin', 'cyan'),
            $this->card('Kendaraan service', $this->vehicleQuery($filters)->where('status', 'service')->count(), $this->share(
                $this->vehicleQuery($filters)->where('status', 'service')->count(),
                max($this->vehicleQuery($filters)->count(), 1),
            ), 'wrench-screwdriver', 'amber'),
            $this->card('Booking bulan ini', $bookingQuery->clone()->whereBetween('departure_date', [$period['period_start'], $period['period_end']])->count(), $this->percentageChange(
                $this->bookingQuery($filters)->whereBetween('departure_date', [$period['period_start'], $period['period_end']])->count(),
                $this->bookingQuery($filters)->whereBetween('departure_date', [$previous['period_start'], $previous['period_end']])->count(),
            ), 'calendar-days', 'orange'),
            $this->card('Pending approval', $this->bookingQuery($filters)->where('status', 'pending')->count(), $this->percentageChange(
                $this->bookingQuery($filters)->where('status', 'pending')->whereBetween('created_at', [$period['period_start'], $period['period_end']])->count(),
                $this->bookingQuery($filters)->where('status', 'pending')->whereBetween('created_at', [$previous['period_start'], $previous['period_end']])->count(),
            ), 'clock', 'amber'),
            $this->card('Booking approved', $this->bookingQuery($filters)->where('status', 'approved')->count(), $this->percentageChange(
                $this->bookingQuery($filters)->where('status', 'approved')->whereBetween('approved_at', [$period['period_start'], $period['period_end']])->count(),
                $this->bookingQuery($filters)->where('status', 'approved')->whereBetween('approved_at', [$previous['period_start'], $previous['period_end']])->count(),
            ), 'clipboard-document-check', 'lime'),
            $this->card('Booking rejected', $this->bookingQuery($filters)->where('status', 'rejected')->count(), $this->percentageChange(
                $this->bookingQuery($filters)->where('status', 'rejected')->whereBetween('created_at', [$period['period_start'], $period['period_end']])->count(),
                $this->bookingQuery($filters)->where('status', 'rejected')->whereBetween('created_at', [$previous['period_start'], $previous['period_end']])->count(),
            ), 'x-circle', 'rose'),
            $this->card('Driver aktif', $driverQuery->where('status', 'active')->count(), $this->share(
                $this->driverQuery($filters)->where('status', 'active')->count(),
                max($this->driverQuery($filters)->count(), 1),
            ), 'identification', 'cyan'),
            $this->card('BBM bulan ini', (float) $fuelQuery->clone()->whereBetween('fuel_date', [$period['period_start']->toDateString(), $period['period_end']->toDateString()])->sum('liter'), $this->percentageChange(
                (float) $this->fuelQuery($filters)->whereBetween('fuel_date', [$period['period_start']->toDateString(), $period['period_end']->toDateString()])->sum('liter'),
                (float) $this->fuelQuery($filters)->whereBetween('fuel_date', [$previous['period_start']->toDateString(), $previous['period_end']->toDateString()])->sum('liter'),
            ), 'beaker', 'orange', ' L'),
            $this->card('Milik perusahaan', $this->vehicleQuery($filters)->where('ownership_type', 'company')->count(), $this->share(
                $this->vehicleQuery($filters)->where('ownership_type', 'company')->count(),
                max($this->vehicleQuery($filters)->count(), 1),
            ), 'building-office-2', 'slate'),
            $this->card('Kendaraan sewa', $this->vehicleQuery($filters)->where('ownership_type', 'rental')->count(), $this->share(
                $this->vehicleQuery($filters)->where('ownership_type', 'rental')->count(),
                max($this->vehicleQuery($filters)->count(), 1),
            ), 'key', 'amber'),
        ];
    }

    public function usageByMonth(array $filters): array
    {
        $year = $this->year($filters);
        $rows = $this->bookingQuery($filters)
            ->selectRaw('MONTH(departure_date) as month, COUNT(*) as total')
            ->whereYear('departure_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return [
            'categories' => $this->monthLabels(),
            'series' => [
                [
                    'name' => 'Booking kendaraan',
                    'data' => $this->months()->map(fn (int $month) => (int) ($rows[$month] ?? 0))->all(),
                ],
            ],
        ];
    }

    public function vehicleStatusChart(array $filters): array
    {
        $labels = ['available' => 'Tersedia', 'booked' => 'Digunakan', 'service' => 'Service', 'inactive' => 'Maintenance'];
        $rows = $this->vehicleQuery($filters)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => array_values($labels),
            'series' => collect(array_keys($labels))->map(fn (string $status) => (int) ($rows[$status] ?? 0))->all(),
        ];
    }

    public function fuelByMonth(array $filters): array
    {
        $year = $this->year($filters);
        $rows = $this->fuelQuery($filters)
            ->selectRaw('MONTH(fuel_date) as month, SUM(liter) as liters, SUM(total_cost) as cost')
            ->whereYear('fuel_date', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        return [
            'categories' => $this->monthLabels(),
            'series' => [
                [
                    'name' => 'Liter BBM',
                    'data' => $this->months()->map(fn (int $month) => round((float) ($rows[$month]->liters ?? 0), 2))->all(),
                ],
                [
                    'name' => 'Biaya BBM',
                    'data' => $this->months()->map(fn (int $month) => round(((float) ($rows[$month]->cost ?? 0)) / 1000000, 2))->all(),
                ],
            ],
        ];
    }

    public function topVehicles(array $filters): array
    {
        $period = $this->yearRange($filters);

        $rows = $this->bookingQuery($filters)
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_bookings.vehicle_id')
            ->selectRaw("vehicles.id, CONCAT(vehicles.code, ' - ', vehicles.model) as vehicle_name, COUNT(vehicle_bookings.id) as total")
            ->whereBetween('vehicle_bookings.departure_date', [$period['start'], $period['end']])
            ->groupBy('vehicles.id', 'vehicles.code', 'vehicles.model')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'categories' => $rows->pluck('vehicle_name')->all(),
            'series' => [
                [
                    'name' => 'Jumlah booking',
                    'data' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all(),
                ],
            ],
        ];
    }

    public function recentBookings(array $filters, string $search = '', string $status = ''): Builder
    {
        return $this->bookingQuery($filters)
            ->with([
                'vehicle:id,code,plate_number,brand,model',
                'driver:id,name',
                'requester:id,name',
                'currentApprover:id,name',
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('vehicle', fn (Builder $vehicle) => $vehicle
                            ->where('code', 'like', "%{$search}%")
                            ->orWhere('plate_number', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%"))
                        ->orWhereHas('driver', fn (Builder $driver) => $driver->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('requester', fn (Builder $user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->latest('departure_date');
    }

    public function pendingApprovals(array $filters): Builder
    {
        return BookingApproval::query()
            ->with([
                'booking:id,booking_code,vehicle_id,driver_id,requester_id,departure_date,return_date,status',
                'booking.vehicle:id,code,plate_number,brand,model,region_id,vehicle_type_id',
                'booking.driver:id,name',
                'booking.requester:id,name',
                'approver:id,name',
            ])
            ->where('booking_approvals.status', 'pending')
            ->whereHas('booking', fn (Builder $booking) => $this->applyBookingFilters($booking, $filters))
            ->orderBy('approval_level')
            ->latest();
    }

    public function notifications(array $filters): array
    {
        $today = now()->toDateString();
        $serviceThreshold = now()->addDays(14)->toDateString();

        return [
            [
                'title' => 'Booking pending',
                'value' => $this->bookingQuery($filters)->where('status', 'pending')->count(),
                'description' => 'Butuh review approver',
                'tone' => 'amber',
                'icon' => 'clock',
            ],
            [
                'title' => 'Overdue service',
                'value' => $this->serviceQuery($filters)->whereNotNull('next_service_date')->whereDate('next_service_date', '<', $today)->count(),
                'description' => 'Jadwal service terlewat',
                'tone' => 'rose',
                'icon' => 'wrench-screwdriver',
            ],
            [
                'title' => 'Approval menunggu',
                'value' => $this->pendingApprovals($filters)->count(),
                'description' => 'Antrian persetujuan aktif',
                'tone' => 'orange',
                'icon' => 'clipboard-document-check',
            ],
            [
                'title' => 'Tidak tersedia',
                'value' => $this->vehicleQuery($filters)->whereIn('status', ['booked', 'service', 'inactive'])->count(),
                'description' => 'Digunakan, service, atau inactive',
                'tone' => 'slate',
                'icon' => 'exclamation-triangle',
            ],
            [
                'title' => 'Service 14 hari',
                'value' => $this->serviceQuery($filters)->whereBetween('next_service_date', [$today, $serviceThreshold])->count(),
                'description' => 'Perlu disiapkan workshop',
                'tone' => 'lime',
                'icon' => 'calendar-days',
            ],
        ];
    }

    public function monitoring(array $filters): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $activeToday = $this->bookingQuery($filters)
            ->whereIn('status', ['approved', 'completed'])
            ->where('departure_date', '<=', $todayEnd)
            ->where('return_date', '>=', $todayStart)
            ->distinct('vehicle_id')
            ->count('vehicle_id');

        $vehicleTotal = $this->vehicleQuery($filters)->count();

        $upcomingServices = $this->serviceQuery($filters)
            ->with('vehicle:id,code,plate_number,brand,model')
            ->whereNotNull('next_service_date')
            ->whereDate('next_service_date', '>=', now()->toDateString())
            ->orderBy('next_service_date')
            ->limit(4)
            ->get();

        $fuelHungry = $this->fuelQuery($filters)
            ->join('vehicles', 'vehicles.id', '=', 'fuel_logs.vehicle_id')
            ->selectRaw('vehicles.code, vehicles.plate_number, vehicles.model, SUM(fuel_logs.liter) as liters, SUM(fuel_logs.total_cost) as cost')
            ->whereBetween('fuel_logs.fuel_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->groupBy('vehicles.code', 'vehicles.plate_number', 'vehicles.model')
            ->orderByDesc('liters')
            ->first();

        return [
            'activeToday' => $activeToday,
            'idle' => max($vehicleTotal - $activeToday, 0),
            'upcomingServices' => $upcomingServices,
            'fuelHungry' => $fuelHungry,
        ];
    }

    public function reportSummary(array $filters): array
    {
        $period = $this->period($filters);

        return [
            'period' => $period['period_start']->translatedFormat('M Y'),
            'bookings' => $this->bookingQuery($filters)->whereBetween('departure_date', [$period['period_start'], $period['period_end']])->count(),
            'fuelCost' => (float) $this->fuelQuery($filters)->whereBetween('fuel_date', [$period['period_start']->toDateString(), $period['period_end']->toDateString()])->sum('total_cost'),
            'serviceCost' => (float) $this->serviceQuery($filters)->whereBetween('service_date', [$period['period_start']->toDateString(), $period['period_end']->toDateString()])->sum('cost'),
        ];
    }

    private function card(string $label, int|float $value, string $trend, string $icon, string $tone, string $suffix = ''): array
    {
        return compact('label', 'value', 'trend', 'icon', 'tone', 'suffix');
    }

    private function percentageChange(int|float $current, int|float $previous): string
    {
        if ((float) $previous === 0.0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;

        return ($change >= 0 ? '+' : '').number_format($change, 1).'%';
    }

    private function share(int|float $value, int|float $total): string
    {
        return number_format(($value / max($total, 1)) * 100, 1).'%';
    }

    private function vehicleQuery(array $filters): Builder
    {
        return Vehicle::query()
            ->when($this->filterValue($filters, 'region_id'), fn (Builder $query, int|string $regionId) => $query->where('region_id', $regionId))
            ->when($this->filterValue($filters, 'vehicle_type_id'), fn (Builder $query, int|string $typeId) => $query->where('vehicle_type_id', $typeId));
    }

    private function driverQuery(array $filters): Builder
    {
        return Driver::query()
            ->when($this->filterValue($filters, 'region_id'), fn (Builder $query, int|string $regionId) => $query->where('region_id', $regionId));
    }

    private function bookingQuery(array $filters): Builder
    {
        return $this->applyBookingFilters(VehicleBooking::query(), $filters);
    }

    private function fuelQuery(array $filters): Builder
    {
        return FuelLog::query()
            ->when($this->filterValue($filters, 'vehicle_id'), fn (Builder $query, int|string $vehicleId) => $query->where('vehicle_id', $vehicleId))
            ->whereHas('vehicle', fn (Builder $vehicle) => $this->applyVehicleFilters($vehicle, $filters));
    }

    private function serviceQuery(array $filters): Builder
    {
        return VehicleService::query()
            ->whereHas('vehicle', fn (Builder $vehicle) => $this->applyVehicleFilters($vehicle, $filters));
    }

    private function applyBookingFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($this->filterValue($filters, 'date'), function (Builder $query, string $date): void {
                $query->whereDate('departure_date', $date);
            })
            ->when($this->filterValue($filters, 'month'), function (Builder $query, int|string $month) use ($filters): void {
                $query->whereMonth('departure_date', $month)
                    ->whereYear('departure_date', $this->year($filters));
            })
            ->when($this->filterValue($filters, 'year'), fn (Builder $query, int|string $year) => $query->whereYear('departure_date', $year))
            ->when($this->filterValue($filters, 'vehicle_id'), fn (Builder $query, int|string $vehicleId) => $query->where('vehicle_id', $vehicleId))
            ->whereHas('vehicle', fn (Builder $vehicle) => $this->applyVehicleFilters($vehicle, $filters));
    }

    private function applyVehicleFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($this->filterValue($filters, 'region_id'), fn (Builder $query, int|string $regionId) => $query->where('region_id', $regionId))
            ->when($this->filterValue($filters, 'vehicle_type_id'), fn (Builder $query, int|string $typeId) => $query->where('vehicle_type_id', $typeId));
    }

    private function filterValue(array $filters, string $key): mixed
    {
        return blank($filters[$key] ?? null) ? null : $filters[$key];
    }

    private function period(array $filters): array
    {
        $year = $this->year($filters);
        $month = (int) ($this->filterValue($filters, 'month') ?: now()->month);
        $start = CarbonImmutable::create($year, $month, 1)->startOfMonth();

        return [
            'period_start' => $start,
            'period_end' => $start->endOfMonth(),
        ];
    }

    private function yearRange(array $filters): array
    {
        $year = $this->year($filters);

        return [
            'start' => CarbonImmutable::create($year, 1, 1)->startOfYear(),
            'end' => CarbonImmutable::create($year, 12, 31)->endOfYear(),
        ];
    }

    private function year(array $filters): int
    {
        return (int) ($this->filterValue($filters, 'year') ?: now()->year);
    }

    private function months(): Collection
    {
        return collect(range(1, 12));
    }

    private function monthLabels(): array
    {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    }
}
