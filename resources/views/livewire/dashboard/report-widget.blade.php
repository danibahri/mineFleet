<section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 sm:p-5">
    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Laporan</h2>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ringkasan periode {{ $this->report['period'] }}.</p>

    <div class="mt-5 grid gap-3">
        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Booking</p>
            <p class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">{{ number_format($this->report['bookings'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Biaya BBM</p>
            <p class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">Rp {{ number_format($this->report['fuelCost'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Biaya Service</p>
            <p class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">Rp {{ number_format($this->report['serviceCost'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-3 gap-2">
        @foreach ([['Excel', 'arrow-down-tray'], ['PDF', 'document-arrow-down'], ['Print', 'printer']] as [$label, $icon])
            <button type="button"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-semibold text-zinc-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">
                <x-dashboard.icon :name="$icon" class="h-4 w-4" />
                <span class="hidden sm:inline">{{ $label }}</span>
            </button>
        @endforeach
    </div>
</section>
