<?php

namespace App\Livewire\Pages\VechileManagement;

use App\Models\Region;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search = '';
    public string $status = '';
    public string $ownership = '';
    public string $regionId = '';
    public string $vehicleTypeId = '';
    public int $perPage = 10;

    // ── Modal ─────────────────────────────────────────────────────────────────
    public string $activeModal = ''; // 'form'

    // ── Form fields ──────────────────────────────────────────────────────────
    public ?int $vehicleId = null;
    public string $code = '';
    public string $plateNumber = '';
    public string $brand = '';
    public string $model = '';
    public string $year = '';
    public string $ownershipType = 'company';
    public string $statusValue = 'available';
    public string $fuelType = '';
    public string $fuelConsumption = '';
    public string $odometer = '';
    public string $notes = '';
    public string $formRegionId = '';
    public string $formVehicleTypeId = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingOwnership(): void { $this->resetPage(); }
    public function updatingRegionId(): void { $this->resetPage(); }
    public function updatingVehicleTypeId(): void { $this->resetPage(); }

    // ── Computed ─────────────────────────────────────────────────────────────
    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()
            ->with([
                'vehicleType:id,name',
                'region:id,name,code',
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('code', 'like', "%{$this->search}%")
                        ->orWhere('plate_number', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->ownership !== '', fn($query) => $query->where('ownership_type', $this->ownership))
            ->when($this->regionId !== '', fn($query) => $query->where('region_id', $this->regionId))
            ->when($this->vehicleTypeId !== '', fn($query) => $query->where('vehicle_type_id', $this->vehicleTypeId))
            ->orderBy('code')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get(['id', 'name', 'code']);
    }

    #[Computed]
    public function vehicleTypes()
    {
        return VehicleType::query()->orderBy('name')->get(['id', 'name']);
    }

    public function statusOptions(): array
    {
        return [
            'available' => 'Available',
            'booked'    => 'In Use',
            'service'   => 'Maintenance',
            'inactive'  => 'Inactive',
        ];
    }

    // ── Modal ─────────────────────────────────────────────────────────────────
    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->activeModal = 'form';
    }

    public function openEditForm(int $id): void
    {
        $vehicle = Vehicle::query()->findOrFail($id);

        $this->vehicleId        = $vehicle->id;
        $this->code             = (string) $vehicle->code;
        $this->plateNumber      = (string) $vehicle->plate_number;
        $this->brand            = (string) $vehicle->brand;
        $this->model            = (string) $vehicle->model;
        $this->year             = (string) $vehicle->year;
        $this->ownershipType    = (string) $vehicle->ownership_type;
        $this->statusValue      = (string) $vehicle->status;
        $this->fuelType         = (string) ($vehicle->fuel_type ?? '');
        $this->fuelConsumption  = (string) ($vehicle->fuel_consumption ?? '');
        $this->odometer         = (string) ($vehicle->odometer ?? '');
        $this->notes            = (string) ($vehicle->notes ?? '');
        $this->formRegionId     = (string) $vehicle->region_id;
        $this->formVehicleTypeId = (string) $vehicle->vehicle_type_id;

        $this->activeModal = 'form';
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->resetForm();
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $maxYear  = now()->year + 1;
        $isUpdate = $this->vehicleId !== null;

        $validated = $this->validate([
            'code'            => ['required', 'string', 'max:50', 'unique:vehicles,code,' . ($this->vehicleId ?? 'NULL') . ',id'],
            'plateNumber'     => ['required', 'string', 'max:50', 'unique:vehicles,plate_number,' . ($this->vehicleId ?? 'NULL') . ',id'],
            'brand'           => ['required', 'string', 'max:50'],
            'model'           => ['required', 'string', 'max:50'],
            'year'            => ['required', 'integer', 'min:1990', 'max:' . $maxYear],
            'ownershipType'   => ['required', 'in:company,rental'],
            'statusValue'     => ['required', 'in:available,booked,service,inactive'],
            'fuelType'        => ['nullable', 'string', 'max:50'],
            'fuelConsumption' => ['nullable', 'numeric', 'min:0'],
            'odometer'        => ['nullable', 'integer', 'min:0'],
            'notes'           => ['nullable', 'string', 'max:500'],
            'formRegionId'    => ['required', 'exists:regions,id'],
            'formVehicleTypeId' => ['required', 'exists:vehicle_types,id'],
        ]);

        Vehicle::query()->updateOrCreate(
            ['id' => $this->vehicleId],
            [
                'code'             => $validated['code'],
                'plate_number'     => $validated['plateNumber'],
                'brand'            => $validated['brand'],
                'model'            => $validated['model'],
                'year'             => (int) $validated['year'],
                'ownership_type'   => $validated['ownershipType'],
                'status'           => $validated['statusValue'],
                'fuel_type'        => $validated['fuelType'] !== '' ? $validated['fuelType'] : null,
                'fuel_consumption' => $validated['fuelConsumption'] !== '' ? (float) $validated['fuelConsumption'] : null,
                'odometer'         => $validated['odometer'] !== '' ? (int) $validated['odometer'] : 0,
                'notes'            => $validated['notes'] !== '' ? $validated['notes'] : null,
                'region_id'        => $validated['formRegionId'],
                'vehicle_type_id'  => $validated['formVehicleTypeId'],
            ]
        );

        $this->activeModal = '';
        $this->resetForm();
        unset($this->vehicles);
        Flux::toast($isUpdate ? 'Kendaraan berhasil diperbarui.' : 'Kendaraan berhasil ditambahkan.');
    }

    public function delete(int $id): void
    {
        Vehicle::query()->findOrFail($id)->delete();
        unset($this->vehicles);
        Flux::toast('Kendaraan berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->vehicleId         = null;
        $this->code              = '';
        $this->plateNumber       = '';
        $this->brand             = '';
        $this->model             = '';
        $this->year              = '';
        $this->ownershipType     = 'company';
        $this->statusValue       = 'available';
        $this->fuelType          = '';
        $this->fuelConsumption   = '';
        $this->odometer          = '';
        $this->notes             = '';
        $this->formRegionId      = '';
        $this->formVehicleTypeId = '';
    }

    public function render()
    {
        return view('pages.vechile-management.index');
    }
}
