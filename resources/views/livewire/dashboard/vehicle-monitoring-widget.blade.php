<section wire:poll.60s class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 sm:p-5">
    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Monitoring Kendaraan</h2>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Aktivitas armada harian, service terdekat, dan kendaraan paling boros BBM.</p>

    <div wire:loading class="mt-5 h-64 animate-pulse rounded-lg bg-zinc-100 dark:bg-zinc-900"></div>

    <div wire:loading.remove class="mt-5 space-y-5">
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-lime-200 bg-lime-50 p-4 dark:border-lime-900 dark:bg-lime-950/40">
                <p class="text-sm font-medium text-lime-700 dark:text-lime-300">Aktif hari ini</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $this->monitoring['activeToday'] }}</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Idle</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $this->monitoring['idle'] }}</p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-zinc-950 dark:text-white">Jadwal service terdekat</h3>
            <div class="mt-3 space-y-2">
                @forelse ($this->monitoring['upcomingServices'] as $service)
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-900">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-950 dark:text-white">{{ $service->vehicle?->code }} · {{ $service->vehicle?->model }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $service->service_type }} di {{ $service->workshop_name ?? 'workshop internal' }}</p>
                        </div>
                        <time class="shrink-0 text-xs font-semibold text-orange-700 dark:text-orange-300">{{ $service->next_service_date?->format('d M') }}</time>
                    </div>
                @empty
                    <p class="rounded-lg bg-zinc-50 px-3 py-3 text-sm text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">Belum ada service terjadwal.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-900 dark:bg-orange-950/30">
            <p class="text-sm font-semibold text-orange-800 dark:text-orange-200">Paling boros BBM bulan ini</p>
            @if ($this->monitoring['fuelHungry'])
                <p class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">{{ $this->monitoring['fuelHungry']->code }} · {{ $this->monitoring['fuelHungry']->model }}</p>
                <p class="text-sm text-orange-700 dark:text-orange-300">{{ number_format($this->monitoring['fuelHungry']->liters, 1, ',', '.') }} L · Rp {{ number_format($this->monitoring['fuelHungry']->cost, 0, ',', '.') }}</p>
            @else
                <p class="mt-2 text-sm text-orange-700 dark:text-orange-300">Belum ada log BBM bulan ini.</p>
            @endif
        </div>
    </div>
</section>
