<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Approval Monitoring</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Persetujuan terbaru yang perlu ditindaklanjuti</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <input type="search" wire:model.debounce.400ms="search" placeholder="Cari booking atau kendaraan"
                class="h-9 w-52 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
            <select wire:model="status"
                class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                <option value="">Semua status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                <tr>
                    <th class="pb-3">Booking Code</th>
                    <th class="pb-3">Vehicle</th>
                    <th class="pb-3">Driver</th>
                    <th class="pb-3">Approver Level</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3">Request Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($this->approvals as $approval)
                    @php
                        $status = $approval->status;
                        $statusClasses = match ($status) {
                            'approved' => 'bg-emerald-50 text-emerald-600',
                            'rejected' => 'bg-rose-50 text-rose-600',
                            default => 'bg-amber-50 text-amber-600',
                        };
                    @endphp
                    <tr class="text-slate-700 dark:text-slate-200">
                        <td class="py-3 font-medium text-slate-900 dark:text-white">
                            {{ $approval->booking->booking_code ?? '-' }}</td>
                        <td class="py-3">
                            {{ $approval->booking->vehicle->code ?? '-' }}
                            <span
                                class="text-xs text-slate-400 dark:text-slate-500">{{ $approval->booking->vehicle->plate_number ?? '' }}</span>
                        </td>
                        <td class="py-3">{{ $approval->booking->driver->name ?? '-' }}</td>
                        <td class="py-3">Level {{ $approval->approval_level }}</td>
                        <td class="py-3">
                            <span class="{{ $statusClasses }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">
                            {{ optional($approval->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada
                            data approval.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->approvals->links() }}
    </div>
</flux:card>
