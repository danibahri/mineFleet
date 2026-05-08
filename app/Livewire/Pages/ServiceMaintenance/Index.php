<?php

namespace App\Livewire\Pages\ServiceMaintenance;

use App\Models\Vehicle;
use App\Models\VehicleService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search           = '';
    public string $filterVehicleId  = '';
    public string $filterServiceStatus = ''; // upcoming | due_soon | overdue | all
    public int    $perPage          = 15;
    public string $activeModal      = ''; // 'form'

    public ?int   $serviceId         = null;
    public string $formVehicleId     = '';
    public string $formServiceDate   = '';
    public string $formServiceType   = '';
    public string $formWorkshop      = '';
    public string $formCost          = '';
    public string $formOdometer      = '';
    public string $formNextService   = '';
    public string $formNotes         = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterVehicleId(): void { $this->resetPage(); }
    public function updatingFilterServiceStatus(): void { $this->resetPage(); }

    #[Computed]
    public function services()
    {
        $today    = now()->toDateString();
        $soonDate = now()->addDays(7)->toDateString();

        return VehicleService::query()
            ->with('vehicle:id,code,plate_number')
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->when($this->search !== '', function ($q): void {
                $q->where('service_type', 'like', "%{$this->search}%")
                  ->orWhere('workshop_name', 'like', "%{$this->search}%")
                  ->orWhereHas('vehicle', fn($q) => $q->where('code', 'like', "%{$this->search}%"));
            })
            ->when($this->filterServiceStatus === 'upcoming', fn($q) => $q->where('next_service_date', '>', $soonDate))
            ->when($this->filterServiceStatus === 'due_soon', fn($q) => $q->whereBetween('next_service_date', [$today, $soonDate]))
            ->when($this->filterServiceStatus === 'overdue', fn($q) => $q->where('next_service_date', '<', $today))
            ->orderByDesc('service_date')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()->orderBy('code')->get(['id', 'code', 'plate_number']);
    }

    #[Computed]
    public function summary()
    {
        $today    = now()->toDateString();
        $soonDate = now()->addDays(7)->toDateString();

        return [
            'upcoming'  => VehicleService::whereNotNull('next_service_date')->where('next_service_date', '>', $soonDate)->count(),
            'due_soon'  => VehicleService::whereNotNull('next_service_date')->whereBetween('next_service_date', [$today, $soonDate])->count(),
            'overdue'   => VehicleService::whereNotNull('next_service_date')->where('next_service_date', '<', $today)->count(),
            'total'     => VehicleService::count(),
        ];
    }

    public function serviceStatusLabel(?string $nextDate = null): array
    {
        if (! $nextDate) {
            return ['label' => '—', 'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400'];
        }
        $date  = \Carbon\Carbon::parse($nextDate);
        $today = now()->startOfDay();
        $days  = $today->diffInDays($date, false);

        if ($days < 0) {
            return ['label' => 'Overdue', 'class' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300'];
        }
        if ($days <= 7) {
            return ['label' => 'Due Soon (' . $days . 'd)', 'class' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'];
        }
        return ['label' => 'Upcoming', 'class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'];
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->formServiceDate = now()->format('Y-m-d');
        $this->activeModal     = 'form';
    }

    public function openEditForm(int $id): void
    {
        $s = VehicleService::query()->findOrFail($id);
        $this->serviceId       = $s->id;
        $this->formVehicleId   = (string) $s->vehicle_id;
        $this->formServiceDate = $s->service_date->format('Y-m-d');
        $this->formServiceType = (string) $s->service_type;
        $this->formWorkshop    = (string) ($s->workshop_name ?? '');
        $this->formCost        = (string) $s->cost;
        $this->formOdometer    = (string) ($s->odometer ?? '');
        $this->formNextService = $s->next_service_date ? $s->next_service_date->format('Y-m-d') : '';
        $this->formNotes       = (string) ($s->notes ?? '');
        $this->activeModal     = 'form';
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->resetForm();
    }

    public function save(): void
    {
        $isUpdate  = $this->serviceId !== null;
        $validated = $this->validate([
            'formVehicleId'   => ['required', 'exists:vehicles,id'],
            'formServiceDate' => ['required', 'date'],
            'formServiceType' => ['required', 'string', 'max:100'],
            'formWorkshop'    => ['nullable', 'string', 'max:100'],
            'formCost'        => ['required', 'numeric', 'min:0'],
            'formOdometer'    => ['nullable', 'integer', 'min:0'],
            'formNextService' => ['nullable', 'date', 'after:formServiceDate'],
            'formNotes'       => ['nullable', 'string', 'max:500'],
        ]);

        VehicleService::query()->updateOrCreate(
            ['id' => $this->serviceId],
            [
                'vehicle_id'       => $validated['formVehicleId'],
                'service_date'     => $validated['formServiceDate'],
                'service_type'     => $validated['formServiceType'],
                'workshop_name'    => $validated['formWorkshop'] !== '' ? $validated['formWorkshop'] : null,
                'cost'             => (float) $validated['formCost'],
                'odometer'         => $validated['formOdometer'] !== '' ? (int) $validated['formOdometer'] : null,
                'next_service_date'=> $validated['formNextService'] !== '' ? $validated['formNextService'] : null,
                'notes'            => $validated['formNotes'] !== '' ? $validated['formNotes'] : null,
            ]
        );

        $this->activeModal = '';
        $this->resetForm();
        unset($this->services, $this->summary);
        Flux::toast($isUpdate ? 'Service diperbarui.' : 'Service ditambahkan.');
    }

    public function delete(int $id): void
    {
        VehicleService::query()->findOrFail($id)->delete();
        unset($this->services, $this->summary);
        Flux::toast('Data service dihapus.');
    }

    private function resetForm(): void
    {
        $this->serviceId       = null;
        $this->formVehicleId   = '';
        $this->formServiceDate = '';
        $this->formServiceType = '';
        $this->formWorkshop    = '';
        $this->formCost        = '';
        $this->formOdometer    = '';
        $this->formNextService = '';
        $this->formNotes       = '';
    }

    public function render()
    {
        return view('pages.service-maintenance.index');
    }
}
