<section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 sm:p-5">
    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Quick Action</h2>
    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
        @foreach ([
            ['label' => 'Tambah booking', 'icon' => 'plus', 'tone' => 'orange'],
            ['label' => 'Tambah kendaraan', 'icon' => 'truck', 'tone' => 'slate'],
            ['label' => 'Approval booking', 'icon' => 'clipboard-document-check', 'tone' => 'amber'],
            ['label' => 'Export laporan', 'icon' => 'arrow-down-tray', 'tone' => 'lime'],
            ['label' => 'Lihat jadwal service', 'icon' => 'wrench-screwdriver', 'tone' => 'cyan'],
        ] as $action)
            <button type="button"
                class="group flex min-h-14 items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-left text-sm font-semibold text-zinc-700 transition hover:border-orange-300 hover:bg-orange-50 hover:text-orange-800 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-orange-900 dark:hover:bg-orange-950/30">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-zinc-700 shadow-sm dark:bg-zinc-950 dark:text-zinc-300">
                    <x-dashboard.icon :name="$action['icon']" class="h-5 w-5" />
                </span>
                <span class="min-w-0">{{ $action['label'] }}</span>
            </button>
        @endforeach
    </div>
</section>
