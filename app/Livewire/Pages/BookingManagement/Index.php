<?php

namespace App\Livewire\Pages\BookingManagement;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Services\ActivityLogger;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search = '';
    public string $filterStatus = '';
    public string $filterVehicleId = '';
    public int $perPage = 10;
    public bool $showHistory = false;

    // ── Modal state ──────────────────────────────────────────────────────────
    public string $activeModal = ''; // 'form' | 'detail' | 'cancel'

    // ── Form fields ──────────────────────────────────────────────────────────
    public ?int $bookingId = null;
    public string $requesterId = '';
    public string $vehicleId = '';
    public string $driverId = '';
    public string $purpose = '';
    public string $destination = '';
    public string $departureDate = '';
    public string $returnDate = '';

    // ── Cancel ───────────────────────────────────────────────────────────────
    public ?int $cancelBookingId = null;
    public string $cancelReason = '';

    // ── Detail ───────────────────────────────────────────────────────────────
    public ?int $detailBookingId = null;

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterVehicleId(): void { $this->resetPage(); }
    public function updatingShowHistory(): void { $this->resetPage(); }

    // ── Computed ─────────────────────────────────────────────────────────────
    #[Computed]
    public function bookings()
    {
        return VehicleBooking::query()
            ->with([
                'requester:id,name,email',
                'vehicle:id,code,plate_number,brand,model',
                'driver:id,name,phone',
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('booking_code', 'like', "%{$this->search}%")
                        ->orWhere('purpose', 'like', "%{$this->search}%")
                        ->orWhere('destination', 'like', "%{$this->search}%")
                        ->orWhereHas('requester', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('vehicle', fn($q) => $q->where('plate_number', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterVehicleId !== '', fn($q) => $q->where('vehicle_id', $this->filterVehicleId))
            ->when(! $this->showHistory, fn($q) => $q->whereNotIn('status', ['completed', 'cancelled']))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()
            ->orderBy('code')
            ->get(['id', 'code', 'plate_number', 'brand', 'model', 'status']);
    }

    #[Computed]
    public function availableVehicles()
    {
        return Vehicle::query()
            ->where('status', 'available')
            ->orderBy('code')
            ->get(['id', 'code', 'plate_number', 'brand', 'model']);
    }

    #[Computed]
    public function drivers()
    {
        return Driver::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'license_number']);
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function detailBooking()
    {
        if (! $this->detailBookingId) {
            return null;
        }

        return VehicleBooking::query()
            ->with([
                'requester:id,name,email,phone',
                'vehicle:id,code,plate_number,brand,model,year',
                'driver:id,name,phone,license_number',
                'createdBy:id,name,email',
                'approvals.approver:id,name,email',
            ])
            ->find($this->detailBookingId);
    }

    // ── Status options ────────────────────────────────────────────────────────
    public function statusOptions(): array
    {
        return [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    // ── Modal openers ─────────────────────────────────────────────────────────
    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->requesterId = (string) auth()->id();
        $this->activeModal = 'form';
    }

    public function openEditForm(int $id): void
    {
        $booking = VehicleBooking::query()->findOrFail($id);

        if (! in_array($booking->status, ['pending'])) {
            Flux::toast('Hanya booking dengan status Pending yang dapat diedit.', variant: 'warning');
            return;
        }

        $this->bookingId    = $booking->id;
        $this->requesterId  = (string) $booking->requester_id;
        $this->vehicleId    = (string) $booking->vehicle_id;
        $this->driverId     = (string) ($booking->driver_id ?? '');
        $this->purpose      = (string) $booking->purpose;
        $this->destination  = (string) $booking->destination;
        $this->departureDate = $booking->departure_date->format('Y-m-d\TH:i');
        $this->returnDate    = $booking->return_date->format('Y-m-d\TH:i');
        $this->activeModal  = 'form';
    }

    public function openDetail(int $id): void
    {
        $this->detailBookingId = $id;
        $this->activeModal     = 'detail';
    }

    public function openCancel(int $id): void
    {
        $this->cancelBookingId = $id;
        $this->cancelReason    = '';
        $this->activeModal     = 'cancel';
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $isUpdate = $this->bookingId !== null;

        $validated = $this->validate([
            'requesterId'   => ['required', 'exists:users,id'],
            'vehicleId'     => ['required', 'exists:vehicles,id'],
            'driverId'      => ['nullable', 'exists:drivers,id'],
            'purpose'       => ['required', 'string', 'max:255'],
            'destination'   => ['required', 'string', 'max:255'],
            'departureDate' => ['required', 'date', 'after_or_equal:now'],
            'returnDate'    => ['required', 'date', 'after:departureDate'],
        ]);

        // ── Vehicle availability validation ───────────────────────────────
        $conflict = VehicleBooking::query()
            ->where('vehicle_id', $validated['vehicleId'])
            ->whereIn('status', ['pending', 'approved'])
            ->when($isUpdate, fn($q) => $q->where('id', '!=', $this->bookingId))
            ->where(function ($q) use ($validated): void {
                $q->whereBetween('departure_date', [$validated['departureDate'], $validated['returnDate']])
                  ->orWhereBetween('return_date', [$validated['departureDate'], $validated['returnDate']])
                  ->orWhere(function ($q) use ($validated): void {
                      $q->where('departure_date', '<=', $validated['departureDate'])
                        ->where('return_date', '>=', $validated['returnDate']);
                  });
            })
            ->first();

        if ($conflict) {
            $this->addError('vehicleId', 'Kendaraan sudah dibooking pada rentang tanggal tersebut (' . $conflict->booking_code . ').');
            return;
        }

        $data = [
            'requester_id'   => $validated['requesterId'],
            'vehicle_id'     => $validated['vehicleId'],
            'driver_id'      => $validated['driverId'] !== '' ? $validated['driverId'] : null,
            'purpose'        => $validated['purpose'],
            'destination'    => $validated['destination'],
            'departure_date' => $validated['departureDate'],
            'return_date'    => $validated['returnDate'],
        ];

        if (! $isUpdate) {
            $data['booking_code'] = 'BKG-' . strtoupper(Str::random(8));
            $data['status']       = 'pending';
            $data['created_by']   = auth()->id();
        }

        VehicleBooking::query()->updateOrCreate(
            ['id' => $this->bookingId],
            $data
        );

        $this->resetForm();
        $this->activeModal = '';
        unset($this->bookings);
        ActivityLogger::log('booking', $isUpdate ? 'update' : 'create', ($isUpdate ? 'Update' : 'Buat') . ' booking: ' . ($data['booking_code'] ?? $this->bookingId));
        Flux::toast($isUpdate ? 'Booking berhasil diperbarui.' : 'Booking berhasil dibuat.');
    }

    public function cancelBooking(): void
    {
        $this->validate([
            'cancelReason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $booking = VehicleBooking::query()->findOrFail($this->cancelBookingId);

        if (! in_array($booking->status, ['pending', 'approved'])) {
            Flux::toast('Booking tidak dapat dibatalkan.', variant: 'warning');
            $this->activeModal = '';
            return;
        }

        $booking->update([
            'status'           => 'cancelled',
            'rejection_reason' => $this->cancelReason,
        ]);

        $this->cancelBookingId = null;
        $this->cancelReason    = '';
        $this->activeModal     = '';
        unset($this->bookings);
        ActivityLogger::log('booking', 'cancel', 'Batalkan booking ID: ' . $this->cancelBookingId . ' — ' . $this->cancelReason);
        Flux::toast('Booking berhasil dibatalkan.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->bookingId     = null;
        $this->requesterId   = '';
        $this->vehicleId     = '';
        $this->driverId      = '';
        $this->purpose       = '';
        $this->destination   = '';
        $this->departureDate = '';
        $this->returnDate    = '';
    }

    public function render()
    {
        return view('pages.booking-management.index');
    }
}
