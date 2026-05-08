<?php

namespace App\Livewire\Pages\DriverManagement;

use App\Models\Driver;
use App\Models\Region;
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
    public string $availability = '';
    public string $regionId = '';
    public int $perPage = 10;

    // ── Modal ─────────────────────────────────────────────────────────────────
    public string $activeModal = ''; // 'form'

    // ── Form fields ──────────────────────────────────────────────────────────
    public ?int $driverId = null;
    public string $name = '';
    public string $phone = '';
    public string $licenseNumber = '';
    public string $statusValue = 'active';
    public string $formRegionId = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingAvailability(): void { $this->resetPage(); }
    public function updatingRegionId(): void { $this->resetPage(); }

    // ── Computed ─────────────────────────────────────────────────────────────
    #[Computed]
    public function drivers()
    {
        $todayStart = now()->startOfDay();
        $todayEnd   = now()->endOfDay();

        return Driver::query()
            ->with(['region:id,name,code'])
            ->withCount([
                'bookings as active_bookings_count' => function ($query) use ($todayStart, $todayEnd): void {
                    $query->whereIn('status', ['approved', 'completed'])
                        ->where('departure_date', '<=', $todayEnd)
                        ->where('return_date', '>=', $todayStart);
                },
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('license_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->regionId !== '', fn($query) => $query->where('region_id', $this->regionId))
            ->when($this->availability === 'available', function ($query) use ($todayStart, $todayEnd): void {
                $query->whereDoesntHave('bookings', function ($query) use ($todayStart, $todayEnd): void {
                    $query->whereIn('status', ['approved', 'completed'])
                        ->where('departure_date', '<=', $todayEnd)
                        ->where('return_date', '>=', $todayStart);
                });
            })
            ->when($this->availability === 'busy', function ($query) use ($todayStart, $todayEnd): void {
                $query->whereHas('bookings', function ($query) use ($todayStart, $todayEnd): void {
                    $query->whereIn('status', ['approved', 'completed'])
                        ->where('departure_date', '<=', $todayEnd)
                        ->where('return_date', '>=', $todayStart);
                });
            })
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get(['id', 'name', 'code']);
    }

    public function statusOptions(): array
    {
        return [
            'active'   => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    public function availabilityOptions(): array
    {
        return [
            'available' => 'Available',
            'busy'      => 'In Use',
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
        $driver = Driver::query()->findOrFail($id);

        $this->driverId       = $driver->id;
        $this->name           = (string) $driver->name;
        $this->phone          = (string) ($driver->phone ?? '');
        $this->licenseNumber  = (string) $driver->license_number;
        $this->statusValue    = (string) $driver->status;
        $this->formRegionId   = (string) ($driver->region_id ?? '');

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
        $isUpdate = $this->driverId !== null;

        $validated = $this->validate([
            'name'          => ['required', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'licenseNumber' => ['required', 'string', 'max:50', 'unique:drivers,license_number,' . ($this->driverId ?? 'NULL') . ',id'],
            'statusValue'   => ['required', 'in:active,inactive'],
            'formRegionId'  => ['nullable', 'exists:regions,id'],
        ]);

        Driver::query()->updateOrCreate(
            ['id' => $this->driverId],
            [
                'name'           => $validated['name'],
                'phone'          => $validated['phone'] !== '' ? $validated['phone'] : null,
                'license_number' => $validated['licenseNumber'],
                'status'         => $validated['statusValue'],
                'region_id'      => $validated['formRegionId'] !== '' ? $validated['formRegionId'] : null,
            ]
        );

        $this->activeModal = '';
        $this->resetForm();
        unset($this->drivers);
        Flux::toast($isUpdate ? 'Driver berhasil diperbarui.' : 'Driver berhasil ditambahkan.');
    }

    public function delete(int $id): void
    {
        Driver::query()->findOrFail($id)->delete();
        unset($this->drivers);
        Flux::toast('Driver berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->driverId      = null;
        $this->name          = '';
        $this->phone         = '';
        $this->licenseNumber = '';
        $this->statusValue   = 'active';
        $this->formRegionId  = '';
    }

    public function render()
    {
        return view('pages.driver-management.index');
    }
}
