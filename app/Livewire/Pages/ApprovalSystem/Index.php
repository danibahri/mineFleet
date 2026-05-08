<?php

namespace App\Livewire\Pages\ApprovalSystem;

use App\Models\BookingApproval;
use App\Models\User;
use App\Models\VehicleBooking;
use App\Services\ActivityLogger;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search       = '';
    public string $filterLevel  = '';
    public string $filterStatus = '';
    public bool   $showHistory  = false;
    public int    $perPage      = 10;

    // ── Modal ─────────────────────────────────────────────────────────────────
    public string $activeModal = ''; // 'approve' | 'reject' | 'detail'

    // ── Approve / Reject form ─────────────────────────────────────────────────
    public ?int   $actionBookingId    = null;
    public int    $actionLevel        = 1;
    public string $approvalNotes      = '';
    public string $approverId         = '';

    // ── Detail ────────────────────────────────────────────────────────────────
    public ?int $detailBookingId = null;

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingFilterLevel(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingShowHistory(): void  { $this->resetPage(); }

    // ── Computed ─────────────────────────────────────────────────────────────
    #[Computed]
    public function bookings()
    {
        $userRole = auth()->user()?->role?->name;
        $userId   = auth()->id();

        return VehicleBooking::query()
            ->with([
                'requester:id,name,email',
                'vehicle:id,code,plate_number,brand,model',
                'driver:id,name',
                'approvals',
            ])
            ->when($userRole === 'approver_level_1', function ($q) use ($userId) {
                if (! $this->showHistory) {
                    $q->where('current_approval_level', 1)
                      ->whereIn('status', ['pending', 'approved']);
                } else {
                    $q->whereHas('approvals', fn($sq) => $sq->where('approval_level', 1)->where('approver_id', $userId));
                }
            })
            ->when($userRole === 'approver_level_2', function ($q) use ($userId) {
                if (! $this->showHistory) {
                    $q->where('current_approval_level', 2)
                      ->whereIn('status', ['pending', 'approved']);
                } else {
                    $q->whereHas('approvals', fn($sq) => $sq->where('approval_level', 2)->where('approver_id', $userId));
                }
            })
            ->when($userRole === 'admin', function ($q) {
                $q->when(! $this->showHistory, fn($q) => $q->whereIn('status', ['pending', 'approved']));
            })
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterLevel !== '', fn($q) => $q->where('current_approval_level', $this->filterLevel))
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($q): void {
                    $q->where('booking_code', 'like', "%{$this->search}%")
                      ->orWhere('purpose', 'like', "%{$this->search}%")
                      ->orWhere('destination', 'like', "%{$this->search}%")
                      ->orWhereHas('requester', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function approvers()
    {
        return User::query()->orderBy('name')->get(['id', 'name', 'email']);
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

    // ── Helpers ───────────────────────────────────────────────────────────────
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
    public function openApprove(int $bookingId, int $level): void
    {
        $this->actionBookingId = $bookingId;
        $this->actionLevel     = $level;
        $this->approvalNotes   = '';
        $this->approverId      = (string) auth()->id();
        $this->activeModal     = 'approve';
    }

    public function openReject(int $bookingId, int $level): void
    {
        $this->actionBookingId = $bookingId;
        $this->actionLevel     = $level;
        $this->approvalNotes   = '';
        $this->approverId      = (string) auth()->id();
        $this->activeModal     = 'reject';
    }

    public function openDetail(int $bookingId): void
    {
        $this->detailBookingId = $bookingId;
        $this->activeModal     = 'detail';
    }

    public function closeModal(): void
    {
        $this->activeModal     = '';
        $this->actionBookingId = null;
        $this->approvalNotes   = '';
        $this->approverId      = '';
    }

    // ── Actions ───────────────────────────────────────────────────────────────
    public function approve(): void
    {
        $this->validate([
            'approverId'    => ['required', 'exists:users,id'],
            'approvalNotes' => ['nullable', 'string', 'max:500'],
        ]);

        $userRole = auth()->user()?->role?->name;
        if ($userRole === 'approver_level_1' && $this->actionLevel !== 1) {
            Flux::toast('Akses ditolak: Anda hanya dapat memproses Level 1.', variant: 'warning');
            $this->closeModal();
            return;
        }
        if ($userRole === 'approver_level_2' && $this->actionLevel !== 2) {
            Flux::toast('Akses ditolak: Anda hanya dapat memproses Level 2.', variant: 'warning');
            $this->closeModal();
            return;
        }

        $booking = VehicleBooking::query()
            ->with('approvals')
            ->findOrFail($this->actionBookingId);

        if (! in_array($booking->status, ['pending', 'approved'])) {
            Flux::toast('Booking ini tidak dapat disetujui.', variant: 'warning');
            $this->closeModal();
            return;
        }

        // Upsert approval record for this level
        BookingApproval::query()->updateOrCreate(
            [
                'booking_id'     => $booking->id,
                'approval_level' => $this->actionLevel,
            ],
            [
                'approver_id' => $this->approverId,
                'status'      => 'approved',
                'notes'       => $this->approvalNotes !== '' ? $this->approvalNotes : null,
                'approved_at' => now(),
            ]
        );

        // Determine next level — max 2 levels in this system
        $maxLevel = 2;
        $nextLevel = $this->actionLevel + 1;

        if ($nextLevel > $maxLevel) {
            // All levels approved
            $booking->update([
                'status'                 => 'approved',
                'current_approval_level' => $this->actionLevel,
                'current_approver_id'    => null,
                'approved_at'            => now(),
            ]);
            Flux::toast('Booking disetujui sepenuhnya (Level ' . $this->actionLevel . ').');
        } else {
            // Move to next level
            $booking->update([
                'status'                 => 'approved',
                'current_approval_level' => $nextLevel,
                'current_approver_id'    => null,
            ]);
            Flux::toast('Approval Level ' . $this->actionLevel . ' selesai. Menunggu Level ' . $nextLevel . '.');
        }

        $this->closeModal();
        unset($this->bookings);
        ActivityLogger::log('approval', 'approve', 'Approve booking ID: ' . $this->actionBookingId . ' Level: ' . $this->actionLevel);
    }

    public function reject(): void
    {
        $this->validate([
            'approverId'    => ['required', 'exists:users,id'],
            'approvalNotes' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $userRole = auth()->user()?->role?->name;
        if ($userRole === 'approver_level_1' && $this->actionLevel !== 1) {
            Flux::toast('Akses ditolak: Anda hanya dapat memproses Level 1.', variant: 'warning');
            $this->closeModal();
            return;
        }
        if ($userRole === 'approver_level_2' && $this->actionLevel !== 2) {
            Flux::toast('Akses ditolak: Anda hanya dapat memproses Level 2.', variant: 'warning');
            $this->closeModal();
            return;
        }

        $booking = VehicleBooking::query()->findOrFail($this->actionBookingId);

        if (! in_array($booking->status, ['pending', 'approved'])) {
            Flux::toast('Booking ini tidak dapat ditolak.', variant: 'warning');
            $this->closeModal();
            return;
        }

        BookingApproval::query()->updateOrCreate(
            [
                'booking_id'     => $booking->id,
                'approval_level' => $this->actionLevel,
            ],
            [
                'approver_id' => $this->approverId,
                'status'      => 'rejected',
                'notes'       => $this->approvalNotes,
                'approved_at' => now(),
            ]
        );

        $booking->update([
            'status'           => 'rejected',
            'rejection_reason' => $this->approvalNotes,
            'current_approver_id' => null,
        ]);

        $this->closeModal();
        unset($this->bookings);
        ActivityLogger::log('approval', 'reject', 'Reject booking ID: ' . $this->actionBookingId . ' — ' . $this->approvalNotes);
        Flux::toast('Booking berhasil ditolak.');
    }

    public function render()
    {
        return view('pages.approval-system.index');
    }
}
