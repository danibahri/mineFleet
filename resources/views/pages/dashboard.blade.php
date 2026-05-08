<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">mineFleet Dashboard</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Ringkasan performa operasional kendaraan tambang.</p>
        </div>
        <div
            class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    <livewire:dashboard.summary-cards :filters="$this->filters" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <livewire:dashboard.booking-graph :filters="$this->filters" />
            <livewire:dashboard.approval-monitoring :filters="$this->filters" />
        </div>
        <div class="space-y-6">
            <livewire:dashboard.vehicle-status :filters="$this->filters" />
            <livewire:dashboard.service-reminder :filters="$this->filters" />
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <livewire:dashboard.vehicle-usage-table :filters="$this->filters" />
        <livewire:dashboard.recent-activity />
    </div>
</div>
