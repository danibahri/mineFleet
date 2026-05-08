<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Fuel Monitoring</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat dan total konsumsi BBM per kendaraan.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                {{ now()->format('d M Y') }}
            </div>
            <flux:button variant="primary" color="emerald" size="sm" wire:click="openCreateForm">+ Input BBM</flux:button>
        </div>
    </div>

    {{-- Summary cards --}}
    @if ($this->summaryByVehicle->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->summaryByVehicle->take(4) as $row)
                <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $row->vehicle?->code ?? '-' }} · {{ $row->vehicle?->plate_number ?? '' }}</p>
                    <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ number_format((float)$row->total_liter, 1) }} L</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Rp {{ number_format((float)$row->total_cost, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $row->fill_count }}x pengisian</p>
                </flux:card>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Riwayat Pengisian BBM</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Semua transaksi pengisian bahan bakar.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <select wire:model="filterVehicleId" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua kendaraan</option>
                    @foreach ($this->vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->code }} — {{ $v->plate_number }}</option>
                    @endforeach
                </select>
                <select wire:model="filterMonth" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-32 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua bulan</option>
                    @foreach (range(1,12) as $m)
                        <option value="{{ $m }}">{{ now()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
                <select wire:model="filterYear" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-28 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    @foreach (range(now()->year, now()->year - 4) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[780px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Tanggal</th>
                        <th class="pb-3">Kendaraan</th>
                        <th class="pb-3">Liter</th>
                        <th class="pb-3">Harga/L</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Odometer</th>
                        <th class="pb-3">Diisi Oleh</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->fuelLogs as $log)
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">{{ $log->fuel_date->format('d M Y') }}</td>
                            <td class="py-3">
                                <p class="font-medium">{{ $log->vehicle?->code ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $log->vehicle?->plate_number ?? '' }}</p>
                            </td>
                            <td class="py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format((float)$log->liter, 2) }} L</td>
                            <td class="py-3">Rp {{ number_format((float)$log->price_per_liter, 0, ',', '.') }}</td>
                            <td class="py-3 font-semibold">Rp {{ number_format((float)$log->total_cost, 0, ',', '.') }}</td>
                            <td class="py-3">{{ $log->odometer ? number_format($log->odometer) . ' km' : '-' }}</td>
                            <td class="py-3">{{ $log->filledBy?->name ?? '-' }}</td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditForm({{ $log->id }})">Edit</flux:button>
                                    <x-confirm-delete name="del-fuel-{{ $log->id }}" title="Hapus data BBM?" message="Tindakan ini tidak dapat dibatalkan." confirm-label="Hapus" button-label="Delete" wire:click="delete({{ $log->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada data BBM.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $this->fuelLogs->links() }}</div>
    </flux:card>

    @if ($activeModal === 'form')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $logId ? 'Edit Data BBM' : 'Input BBM Baru' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Catat konsumsi bahan bakar kendaraan.</p>
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
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tanggal</label>
                            <input type="date" wire:model.defer="formFuelDate" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formFuelDate') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Liter</label>
                            <input type="number" step="0.01" wire:model.defer="formLiter" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formLiter') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Harga/Liter (Rp)</label>
                            <input type="number" wire:model.defer="formPricePerLiter" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('formPricePerLiter') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Odometer (km)</label>
                            <input type="number" wire:model.defer="formOdometer" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Diisi Oleh</label>
                            <select wire:model.defer="formFilledBy" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">— Tidak Diketahui —</option>
                                @foreach ($this->users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Catatan</label>
                            <input type="text" wire:model.defer="formNotes" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="save">{{ $logId ? 'Update' : 'Simpan' }}</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
