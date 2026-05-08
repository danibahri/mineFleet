<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Booking Management</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola permintaan pemakaian kendaraan operasional tambang.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                {{ now()->format('d M Y') }}
            </div>
            <flux:button variant="primary" color="emerald" size="sm" wire:click="openCreateForm">
                + Buat Booking
            </flux:button>
        </div>
    </div>

    {{-- Table Card --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        {{-- Toolbar --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Daftar Booking</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Semua permintaan booking kendaraan.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari kode, tujuan, pemohon…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-52 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="filterStatus"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua status</option>
                    @foreach ($this->statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model="filterVehicleId"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua kendaraan</option>
                    @foreach ($this->vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->code }} - {{ $v->plate_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3 flex items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <input type="checkbox" wire:model.live="showHistory"
                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600" />
                Tampilkan history (selesai &amp; dibatalkan)
            </label>
        </div>

        {{-- Table --}}
        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[860px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Booking</th>
                        <th class="pb-3">Pemohon</th>
                        <th class="pb-3">Kendaraan</th>
                        <th class="pb-3">Tanggal</th>
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
                                <span class="{{ $statusClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ $this->statusOptions()[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openDetail({{ $booking->id }})">
                                        Detail
                                    </flux:button>
                                    @if ($booking->status === 'pending')
                                        <flux:button size="sm" variant="ghost" wire:click="openEditForm({{ $booking->id }})">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="openCancel({{ $booking->id }})"
                                            class="text-rose-500 hover:text-rose-600 dark:text-rose-400">
                                            Batal
                                        </flux:button>
                                    @elseif ($booking->status === 'approved')
                                        <flux:button size="sm" variant="ghost" wire:click="openCancel({{ $booking->id }})"
                                            class="text-rose-500 hover:text-rose-600 dark:text-rose-400">
                                            Batal
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">
                                Belum ada data booking.
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

    {{-- ── MODAL: Form Create / Edit ─────────────────────────────────────── --}}
    @if ($activeModal === 'form')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $bookingId ? 'Edit Booking' : 'Buat Booking Baru' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $bookingId ? 'Perbarui data booking.' : 'Isi detail permintaan kendaraan.' }}
                        </p>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Requester --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Pemohon</label>
                        <select wire:model.defer="requesterId"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            <option value="">Pilih pemohon</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('requesterId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Vehicle & Driver --}}
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Kendaraan</label>
                            <select wire:model.defer="vehicleId"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Pilih kendaraan</option>
                                @foreach ($this->availableVehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->code }} — {{ $v->plate_number }}</option>
                                @endforeach
                            </select>
                            @error('vehicleId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Driver <span class="font-normal text-slate-400">(opsional)</span></label>
                            <select wire:model.defer="driverId"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Tanpa driver</option>
                                @foreach ($this->drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            @error('driverId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Keperluan</label>
                        <input type="text" wire:model.defer="purpose" placeholder="Inspeksi area tambang…"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        @error('purpose') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Destination --}}
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tujuan</label>
                        <input type="text" wire:model.defer="destination" placeholder="Pit A, Site Kalimantan…"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        @error('destination') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dates --}}
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tanggal Berangkat</label>
                            <input type="datetime-local" wire:model.defer="departureDate"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('departureDate') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tanggal Kembali</label>
                            <input type="datetime-local" wire:model.defer="returnDate"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('returnDate') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="save">
                        {{ $bookingId ? 'Update' : 'Buat Booking' }}
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
            <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
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
                                'cancelled' => 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400',
                                default     => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="{{ $dc }} rounded-full px-3 py-1 text-xs font-semibold">
                            {{ $this->statusOptions()[$b->status] ?? ucfirst($b->status) }}
                        </span>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->vehicle?->code ?? '-' }} — {{ $b->vehicle?->plate_number ?? '' }}</p>
                            <p class="text-xs text-slate-500">{{ $b->vehicle?->brand }} {{ $b->vehicle?->model }} {{ $b->vehicle?->year }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Driver</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->driver?->name ?? 'Tanpa driver' }}</p>
                            @if ($b->driver)
                                <p class="text-xs text-slate-500">SIM: {{ $b->driver->license_number }}</p>
                            @endif
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
                            <p class="text-xs text-slate-400 dark:text-slate-500">Tanggal Keberangkatan</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->departure_date->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Tanggal Kembali</p>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $b->return_date->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if ($b->rejection_reason)
                    <div class="mt-4 rounded-lg border border-rose-100 bg-rose-50 px-4 py-3 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <p class="text-xs font-semibold text-rose-600 dark:text-rose-400">Alasan Penolakan / Pembatalan</p>
                        <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">{{ $b->rejection_reason }}</p>
                    </div>
                @endif

                @if ($b->approvals->isNotEmpty())
                    <div class="mt-5">
                        <p class="mb-2 text-xs font-semibold text-slate-600 dark:text-slate-300">Riwayat Approval</p>
                        <div class="space-y-2">
                            @foreach ($b->approvals as $approval)
                                @php
                                    $ac = match ($approval->status) {
                                        'approved' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'rejected' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                                        default    => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                    };
                                @endphp
                                <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2 dark:border-slate-800">
                                    <div>
                                        <p class="text-xs font-medium text-slate-800 dark:text-slate-200">Level {{ $approval->approval_level }} — {{ $approval->approver?->name ?? '-' }}</p>
                                        @if ($approval->notes)
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $approval->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($approval->approved_at)
                                            <p class="text-xs text-slate-400">{{ $approval->approved_at->format('d M Y') }}</p>
                                        @endif
                                        <span class="{{ $ac }} rounded-full px-2.5 py-0.5 text-xs font-semibold">{{ ucfirst($approval->status) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Tutup</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MODAL: Cancel ────────────────────────────────────────────────────── --}}
    @if ($activeModal === 'cancel')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/20">
                        <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">Batalkan Booking?</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan. Berikan alasan pembatalan.</p>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Alasan Pembatalan</label>
                    <textarea wire:model.defer="cancelReason" rows="3"
                        placeholder="Tulis alasan pembatalan…"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-rose-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"></textarea>
                    @error('cancelReason') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-5 flex items-center justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Kembali</flux:button>
                    <flux:button size="sm" wire:click="cancelBooking"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                        Ya, Batalkan
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
