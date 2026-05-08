<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Service & Maintenance</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jadwal dan riwayat perawatan kendaraan operasional.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                {{ now()->format('d M Y') }}
            </div>
            <flux:button variant="primary" color="emerald" size="sm" wire:click="openCreateForm">+ Tambah Service</flux:button>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <flux:card class="cursor-pointer rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-sm transition hover:shadow-md dark:border-slate-800/80 dark:bg-slate-900/80"
            wire:click="$set('filterServiceStatus', '')">
            <p class="text-xs text-slate-500 dark:text-slate-400">Total Service</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $this->summary['total'] }}</p>
        </flux:card>
        <flux:card class="cursor-pointer rounded-2xl border border-emerald-200/80 bg-emerald-50/80 p-4 shadow-sm transition hover:shadow-md dark:border-emerald-800/40 dark:bg-emerald-900/20"
            wire:click="$set('filterServiceStatus', 'upcoming')">
            <p class="text-xs text-emerald-600 dark:text-emerald-400">Upcoming</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $this->summary['upcoming'] }}</p>
        </flux:card>
        <flux:card class="cursor-pointer rounded-2xl border border-amber-200/80 bg-amber-50/80 p-4 shadow-sm transition hover:shadow-md dark:border-amber-800/40 dark:bg-amber-900/20"
            wire:click="$set('filterServiceStatus', 'due_soon')">
            <p class="text-xs text-amber-600 dark:text-amber-400">Due Soon (7 hari)</p>
            <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $this->summary['due_soon'] }}</p>
        </flux:card>
        <flux:card class="cursor-pointer rounded-2xl border border-rose-200/80 bg-rose-50/80 p-4 shadow-sm transition hover:shadow-md dark:border-rose-800/40 dark:bg-rose-900/20"
            wire:click="$set('filterServiceStatus', 'overdue')">
            <p class="text-xs text-rose-600 dark:text-rose-400">Overdue</p>
            <p class="mt-1 text-2xl font-bold text-rose-700 dark:text-rose-300">{{ $this->summary['overdue'] }}</p>
        </flux:card>
    </div>

    {{-- Table --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Riwayat Service</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Klik kartu status di atas untuk filter.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari jenis, bengkel…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="filterVehicleId" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua kendaraan</option>
                    @foreach ($this->vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->code }} — {{ $v->plate_number }}</option>
                    @endforeach
                </select>
                <select wire:model="filterServiceStatus" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-36 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="due_soon">Due Soon</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
        </div>

        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[860px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Kendaraan</th>
                        <th class="pb-3">Tgl Service</th>
                        <th class="pb-3">Jenis Service</th>
                        <th class="pb-3">Bengkel</th>
                        <th class="pb-3">Biaya</th>
                        <th class="pb-3">Next Service</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->services as $s)
                        @php
                            $st = $this->serviceStatusLabel($s->next_service_date?->toDateString());
                        @endphp
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">
                                <p class="font-medium">{{ $s->vehicle?->code ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $s->vehicle?->plate_number ?? '' }}</p>
                            </td>
                            <td class="py-3">{{ $s->service_date->format('d M Y') }}</td>
                            <td class="py-3 font-medium">{{ $s->service_type }}</td>
                            <td class="py-3">{{ $s->workshop_name ?? '-' }}</td>
                            <td class="py-3 font-semibold">Rp {{ number_format((float)$s->cost, 0, ',', '.') }}</td>
                            <td class="py-3">{{ $s->next_service_date ? $s->next_service_date->format('d M Y') : '-' }}</td>
                            <td class="py-3">
                                <span class="{{ $st['class'] }} rounded-full px-2.5 py-1 text-xs font-semibold">{{ $st['label'] }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditForm({{ $s->id }})">Edit</flux:button>
                                    <x-confirm-delete name="del-svc-{{ $s->id }}" title="Hapus data service?" message="Tindakan ini tidak dapat dibatalkan." confirm-label="Hapus" button-label="Delete" wire:click="delete({{ $s->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada data service.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $this->services->links() }}</div>
    </flux:card>

    @if ($activeModal === 'form')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900 max-h-[90vh] overflow-y-auto">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $serviceId ? 'Edit Service' : 'Tambah Service Baru' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Catat jadwal atau riwayat service kendaraan.</p>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Kendaraan</label>
                            <select wire:model.defer="formVehicleId" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Pilih kendaraan</option>
                                @foreach ($this->vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->code }} — {{ $v->plate_number }}</option>
                                @endforeach
                            </select>
                            @error('formVehicleId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tanggal Service</label>
                            <input type="date" wire:model.defer="formServiceDate" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formServiceDate') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Jenis Service</label>
                            <input type="text" wire:model.defer="formServiceType" placeholder="Ganti oli, tune-up…" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formServiceType') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Bengkel</label>
                            <input type="text" wire:model.defer="formWorkshop" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Biaya (Rp)</label>
                            <input type="number" wire:model.defer="formCost" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formCost') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Odometer (km)</label>
                            <input type="number" wire:model.defer="formOdometer" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Next Service</label>
                            <input type="date" wire:model.defer="formNextService" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formNextService') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Catatan</label>
                        <textarea wire:model.defer="formNotes" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="save">{{ $serviceId ? 'Update' : 'Simpan' }}</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
