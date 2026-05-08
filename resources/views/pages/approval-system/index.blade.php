<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Approval System</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Proses persetujuan permintaan booking kendaraan.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    {{-- Table Card --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        {{-- Toolbar --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Daftar Booking Perlu Approval</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Review dan proses setiap permintaan kendaraan.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari kode, tujuan, pemohon…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-52 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="filterLevel"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-36 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua level</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                </select>
                <select wire:model="filterStatus"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua status</option>
                    @foreach ($this->statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <input type="checkbox" wire:model.live="showHistory"
                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600" />
                Tampilkan semua riwayat (ditolak, selesai, dibatalkan)
            </label>
        </div>

        {{-- Table --}}
        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[900px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Booking</th>
                        <th class="pb-3">Pemohon</th>
                        <th class="pb-3">Kendaraan</th>
                        <th class="pb-3">Tanggal</th>
                        <th class="pb-3">Level</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->bookings as $booking)
                        @php
                            $statusClass = match ($booking->status) {
                                'pending'   => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                'approved'  => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                'rejected'  => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                                'completed' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
                                'cancelled' => 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400',
                                default     => 'bg-slate-100 text-slate-600',
                            };
                            $level = $booking->current_approval_level ?? 1;
                            $levelClass = $level === 1
                                ? 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'
                                : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300';
                            
                            $role = auth()->user()?->role?->name;
                            $canAct = in_array($booking->status, ['pending', 'approved']) && (
                                $role === 'admin' ||
                                ($role === 'approver_level_1' && $level === 1) ||
                                ($role === 'approver_level_2' && $level === 2)
                            );
                        @endphp
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $booking->booking_code }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $booking->purpose }}</p>
                            </td>
                            <td class="py-3">
                                <p class="font-medium">{{ $booking->requester?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $booking->requester?->email ?? '' }}</p>
                            </td>
                            <td class="py-3">
                                <p class="font-medium">{{ $booking->vehicle?->code ?? '-' }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $booking->vehicle?->plate_number ?? '' }}</p>
                            </td>
                            <td class="py-3">
                                <p>{{ $booking->departure_date->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">s/d {{ $booking->return_date->format('d M Y') }}</p>
                            </td>
                            <td class="py-3">
                                <span class="{{ $levelClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    Level {{ $level }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="{{ $statusClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ $this->statusOptions()[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openDetail({{ $booking->id }})">
                                        Detail
                                    </flux:button>
                                    @if ($canAct)
                                        <flux:button size="sm" variant="ghost"
                                            class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400"
                                            wire:click="openApprove({{ $booking->id }}, {{ $level }})">
                                            Setuju
                                        </flux:button>
                                        <flux:button size="sm" variant="ghost"
                                            class="text-rose-500 hover:text-rose-600 dark:text-rose-400"
                                            wire:click="openReject({{ $booking->id }}, {{ $level }})">
                                            Tolak
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">
                                Tidak ada booking yang perlu diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->bookings->links() }}
        </div>
    </flux:card>

    {{-- ── MODAL: Approve ───────────────────────────────────────────────────── --}}
    @if ($activeModal === 'approve')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">Setujui Booking</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Approval <span class="font-semibold text-violet-600 dark:text-violet-400">Level {{ $actionLevel }}</span>
                        </p>
                    </div>
                    <button wire:click="closeModal" class="ml-auto text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Approver</label>
                        <select wire:model.defer="approverId"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            <option value="">Pilih approver</option>
                            @foreach ($this->approvers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('approverId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                            Catatan <span class="font-normal text-slate-400">(opsional)</span>
                        </label>
                        <textarea wire:model.defer="approvalNotes" rows="3"
                            placeholder="Tambahkan catatan approval…"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"></textarea>
                        @error('approvalNotes') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="approve">
                        Setujui Level {{ $actionLevel }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MODAL: Reject ────────────────────────────────────────────────────── --}}
    @if ($activeModal === 'reject')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/20">
                        <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">Tolak Booking</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Penolakan <span class="font-semibold text-rose-600 dark:text-rose-400">Level {{ $actionLevel }}</span>
                        </p>
                    </div>
                    <button wire:click="closeModal" class="ml-auto text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Approver</label>
                        <select wire:model.defer="approverId"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            <option value="">Pilih approver</option>
                            @foreach ($this->approvers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('approverId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea wire:model.defer="approvalNotes" rows="3"
                            placeholder="Wajib isi alasan penolakan…"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-rose-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"></textarea>
                        @error('approvalNotes') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" wire:click="reject"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                        Tolak Booking
                    </flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MODAL: Detail ────────────────────────────────────────────────────── --}}
    @if ($activeModal === 'detail' && $this->detailBooking)
        @php $b = $this->detailBooking; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900 max-h-[90vh] overflow-y-auto">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">Detail Booking</p>
                        <p class="font-mono text-xs text-emerald-600 dark:text-emerald-400">{{ $b->booking_code }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $dc = match ($b->status) {
                                'pending'   => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                'approved'  => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                'rejected'  => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                                'completed' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
                                default     => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <span class="{{ $dc }} rounded-full px-3 py-1 text-xs font-semibold">
                            {{ $this->statusOptions()[$b->status] ?? ucfirst($b->status) }}
                        </span>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Pemohon</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->requester?->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $b->requester?->email ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Kendaraan</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $b->vehicle?->code ?? '-' }} — {{ $b->vehicle?->plate_number ?? '' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $b->vehicle?->brand }} {{ $b->vehicle?->model }} {{ $b->vehicle?->year }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Driver</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->driver?->name ?? 'Tanpa driver' }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Keperluan</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->purpose }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Tujuan</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->destination }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Tanggal</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->departure_date->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-slate-500">s/d {{ $b->return_date->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if ($b->rejection_reason)
                    <div class="mt-4 rounded-lg border border-rose-100 bg-rose-50 px-4 py-3 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <p class="text-xs font-semibold text-rose-600 dark:text-rose-400">Alasan Penolakan / Pembatalan</p>
                        <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ $b->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Approval History --}}
                <div class="mt-5">
                    <p class="mb-3 text-xs font-semibold text-slate-600 dark:text-slate-300">Riwayat Approval</p>
                    @if ($b->approvals->isEmpty())
                        <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada riwayat approval.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($b->approvals->sortBy('approval_level') as $approval)
                                @php
                                    $ac = match ($approval->status) {
                                        'approved' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'rejected' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                                        default    => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                    };
                                    $lc = $approval->approval_level === 1
                                        ? 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'
                                        : 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300';
                                @endphp
                                <div class="flex items-start justify-between rounded-lg border border-slate-100 px-3 py-2.5 dark:border-slate-800">
                                    <div class="flex items-start gap-3">
                                        <span class="{{ $lc }} rounded-full px-2 py-0.5 text-xs font-semibold">L{{ $approval->approval_level }}</span>
                                        <div>
                                            <p class="text-xs font-medium text-slate-800 dark:text-slate-200">
                                                {{ $approval->approver?->name ?? 'N/A' }}
                                            </p>
                                            @if ($approval->notes)
                                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $approval->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 flex-col items-end gap-1">
                                        <span class="{{ $ac }} rounded-full px-2.5 py-0.5 text-xs font-semibold">{{ ucfirst($approval->status) }}</span>
                                        @if ($approval->approved_at)
                                            <p class="text-xs text-slate-400">{{ $approval->approved_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Tutup</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
