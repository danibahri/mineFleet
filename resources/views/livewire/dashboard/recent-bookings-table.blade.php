<section wire:poll.60s
    class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
    <div class="border-b border-zinc-200 p-4 dark:border-zinc-800 sm:p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Booking Terbaru</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Data booking dengan relasi kendaraan, driver, pemohon, dan approver.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-[minmax(220px,1fr)_160px]">
                <label class="relative">
                    <span class="sr-only">Cari booking</span>
                    <flux:icon.magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <input wire:model.live.debounce.350ms="search" type="search" placeholder="Cari booking..."
                        class="h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50 pl-9 pr-3 text-sm text-zinc-700 outline-none transition focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-400/10 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">
                </label>
                <select wire:model.live="status"
                    class="h-10 rounded-lg border border-zinc-200 bg-zinc-50 px-3 text-sm text-zinc-700 outline-none transition focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-400/10 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">
                    <option value="">Semua status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <div wire:loading class="p-4">
        <div class="h-56 animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>
    </div>

    <div wire:loading.remove class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
            <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/70 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Kendaraan</th>
                    <th class="px-4 py-3">Driver</th>
                    <th class="px-4 py-3">Pemohon</th>
                    <th class="px-4 py-3">Approver</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-orange-50/40 dark:hover:bg-orange-950/10">
                        <td class="whitespace-nowrap px-4 py-4 font-semibold text-zinc-950 dark:text-white">{{ $booking->booking_code }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-700 dark:text-zinc-200">
                            <div class="font-medium">{{ $booking->vehicle?->code ?? '-' }}</div>
                            <div class="text-xs text-zinc-500">{{ $booking->vehicle?->plate_number }} · {{ $booking->vehicle?->model }}</div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $booking->driver?->name ?? 'Belum ditugaskan' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $booking->requester?->name ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $booking->currentApprover?->name ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $booking->departure_date?->format('d M Y H:i') }}</td>
                        <td class="whitespace-nowrap px-4 py-4"><x-dashboard.status-badge :status="$booking->status" /></td>
                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            <button type="button"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-600 transition hover:border-orange-300 hover:text-orange-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                                <span class="sr-only">Detail {{ $booking->booking_code }}</span>
                                <x-dashboard.icon name="eye" class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada booking sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-zinc-200 p-4 dark:border-zinc-800">
        {{ $bookings->links() }}
    </div>
</section>
