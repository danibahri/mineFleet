<?php

namespace App\Livewire\Pages\VuelMonitoring;

use App\Models\FuelLog;
use App\Models\User;
use App\Models\Vehicle;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterVehicleId = '';
    public string $filterMonth = '';
    public string $filterYear  = '';
    public int    $perPage     = 15;
    public string $activeModal = ''; // 'form'

    public ?int   $logId          = null;
    public string $formVehicleId  = '';
    public string $formFilledBy   = '';
    public string $formFuelDate   = '';
    public string $formLiter      = '';
    public string $formPricePerLiter = '';
    public string $formOdometer   = '';
    public string $formNotes      = '';

    public function mount(): void
    {
        $this->filterYear  = (string) now()->year;
        $this->filterMonth = (string) now()->month;
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterVehicleId(): void { $this->resetPage(); }
    public function updatingFilterMonth(): void { $this->resetPage(); }
    public function updatingFilterYear(): void { $this->resetPage(); }

    #[Computed]
    public function fuelLogs()
    {
        return FuelLog::query()
            ->with(['vehicle:id,code,plate_number', 'filledBy:id,name'])
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('fuel_date', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('fuel_date', $this->filterMonth))
            ->when($this->search !== '', function ($q): void {
                $q->whereHas('vehicle', fn($q) => $q->where('code', 'like', "%{$this->search}%")->orWhere('plate_number', 'like', "%{$this->search}%"));
            })
            ->orderByDesc('fuel_date')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()->orderBy('code')->get(['id', 'code', 'plate_number']);
    }

    #[Computed]
    public function users()
    {
        return User::query()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function summaryByVehicle()
    {
        return FuelLog::query()
            ->selectRaw('vehicle_id, SUM(liter) as total_liter, SUM(total_cost) as total_cost, COUNT(*) as fill_count')
            ->with('vehicle:id,code,plate_number')
            ->when($this->filterYear !== '', fn($q) => $q->whereYear('fuel_date', $this->filterYear))
            ->when($this->filterMonth !== '', fn($q) => $q->whereMonth('fuel_date', $this->filterMonth))
            ->groupBy('vehicle_id')
            ->orderByDesc('total_liter')
            ->get();
    }

    #[Computed]
    public function chartData()
    {
        // Last 6 months aggregated consumption
        $rows = FuelLog::query()
            ->selectRaw("DATE_FORMAT(fuel_date, '%Y-%m') as month, SUM(liter) as total")
            ->whereYear('fuel_date', $this->filterYear !== '' ? $this->filterYear : now()->year)
            ->groupByRaw("DATE_FORMAT(fuel_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        return [
            'labels' => $rows->pluck('month')->toArray(),
            'data'   => $rows->pluck('total')->map(fn($v) => round((float) $v, 2))->toArray(),
        ];
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->formFuelDate  = now()->format('Y-m-d');
        $this->formFilledBy  = (string) auth()->id();
        $this->activeModal   = 'form';
    }

    public function openEditForm(int $id): void
    {
        $log = FuelLog::query()->findOrFail($id);
        $this->logId             = $log->id;
        $this->formVehicleId     = (string) $log->vehicle_id;
        $this->formFilledBy      = (string) ($log->filled_by ?? '');
        $this->formFuelDate      = $log->fuel_date->format('Y-m-d');
        $this->formLiter         = (string) $log->liter;
        $this->formPricePerLiter = (string) $log->price_per_liter;
        $this->formOdometer      = (string) ($log->odometer ?? '');
        $this->formNotes         = (string) ($log->notes ?? '');
        $this->activeModal       = 'form';
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->resetForm();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formVehicleId'     => ['required', 'exists:vehicles,id'],
            'formFilledBy'      => ['nullable', 'exists:users,id'],
            'formFuelDate'      => ['required', 'date'],
            'formLiter'         => ['required', 'numeric', 'min:0.01'],
            'formPricePerLiter' => ['required', 'numeric', 'min:0'],
            'formOdometer'      => ['nullable', 'integer', 'min:0'],
            'formNotes'         => ['nullable', 'string', 'max:500'],
        ]);

        $liter    = (float) $validated['formLiter'];
        $price    = (float) $validated['formPricePerLiter'];
        $isUpdate = $this->logId !== null;

        FuelLog::query()->updateOrCreate(
            ['id' => $this->logId],
            [
                'vehicle_id'      => $validated['formVehicleId'],
                'filled_by'       => $validated['formFilledBy'] !== '' ? $validated['formFilledBy'] : null,
                'fuel_date'       => $validated['formFuelDate'],
                'liter'           => $liter,
                'price_per_liter' => $price,
                'total_cost'      => round($liter * $price, 2),
                'odometer'        => $validated['formOdometer'] !== '' ? (int) $validated['formOdometer'] : null,
                'notes'           => $validated['formNotes'] !== '' ? $validated['formNotes'] : null,
            ]
        );

        $this->activeModal = '';
        $this->resetForm();
        unset($this->fuelLogs, $this->summaryByVehicle, $this->chartData);
        Flux::toast($isUpdate ? 'Data BBM diperbarui.' : 'Data BBM ditambahkan.');
    }

    public function delete(int $id): void
    {
        FuelLog::query()->findOrFail($id)->delete();
        unset($this->fuelLogs, $this->summaryByVehicle, $this->chartData);
        Flux::toast('Data BBM dihapus.');
    }

    private function resetForm(): void
    {
        $this->logId             = null;
        $this->formVehicleId     = '';
        $this->formFilledBy      = '';
        $this->formFuelDate      = '';
        $this->formLiter         = '';
        $this->formPricePerLiter = '';
        $this->formOdometer      = '';
        $this->formNotes         = '';
    }

    public function render()
    {
        return view('pages.vuel-monitoring.index');
    }
}
