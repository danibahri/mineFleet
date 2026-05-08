<section wire:poll.45s
    class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
    <div class="border-b border-zinc-200 p-4 dark:border-zinc-800 sm:p-5">
        <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Approval Pending</h2>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Antrian approval berdasarkan level persetujuan aktif.</p>
    </div>

    <div wire:loading class="p-4">
        <div class="h-48 animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    </div>

    <div wire:loading.remove class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
            <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/70 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Level</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Kendaraan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Approver</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                @forelse ($approvals as $approval)
                    <tr class="hover:bg-amber-50/40 dark:hover:bg-amber-950/10">
                        <td class="whitespace-nowrap px-4 py-4">
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900">
                                Level {{ $approval->approval_level }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-zinc-950 dark:text-white">{{ $approval->booking?->booking_code }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-700 dark:text-zinc-200">
                            <div class="font-medium">{{ $approval->booking?->vehicle?->code ?? '-' }}</div>
                            <div class="text-xs text-zinc-500">{{ $approval->booking?->vehicle?->plate_number }} · {{ $approval->booking?->vehicle?->model }}</div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $approval->booking?->departure_date?->format('d M Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $approval->approver?->name ?? 'Belum ditentukan' }}</td>
                        <td class="whitespace-nowrap px-4 py-4"><x-dashboard.status-badge :status="$approval->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">Tidak ada approval pending.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-zinc-200 p-4 dark:border-zinc-800">
        {{ $approvals->links() }}
    </div>
</section>
