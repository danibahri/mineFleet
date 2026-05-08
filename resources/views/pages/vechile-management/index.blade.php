<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Vehicle Management</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Master data kendaraan untuk operasional tambang.</p>
        </div>
        <div
            class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <flux:card
                class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Daftar Kendaraan</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola data armada aktif dan sewa.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="search" wire:model.debounce.400ms="search" placeholder="Cari kode, plat, brand"
                            class="h-9 w-52 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        <select wire:model="status"
                            class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            <option value="">Semua status</option>
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <select wire:model="ownership"
                            class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            <option value="">Semua ownership</option>
                            <option value="company">Company</option>
                            <option value="rental">Rental</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <select wire:model="regionId"
                        class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                        <option value="">Semua region</option>
                        @foreach ($this->regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->code }})</option>
                        @endforeach
                    </select>
                    <select wire:model="vehicleTypeId"
                        class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                        <option value="">Semua jenis</option>
                        @foreach ($this->vehicleTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                            <tr>
                                <th class="pb-3">Vehicle</th>
                                <th class="pb-3">Region</th>
                                <th class="pb-3">Type</th>
                                <th class="pb-3">Ownership</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($this->vehicles as $vehicle)
                                @php
                                    $statusLabel =
                                        $this->statusOptions()[$vehicle->status] ?? ucfirst($vehicle->status);
                                    $statusClass = match ($vehicle->status) {
                                        'available'
                                            => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                        'booked' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
                                        'service'
                                            => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                        default
                                            => 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-300',
                                    };
                                @endphp
                                <tr class="text-slate-700 dark:text-slate-200">
                                    <td class="py-3">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $vehicle->code }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">
                                            {{ $vehicle->plate_number }}
                                            - {{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                    </td>
                                    <td class="py-3">{{ $vehicle->region->name ?? '-' }}</td>
                                    <td class="py-3">{{ $vehicle->vehicleType->name ?? '-' }}</td>
                                    <td class="py-3">{{ ucfirst($vehicle->ownership_type) }}</td>
                                    <td class="py-3">
                                        <span
                                            class="{{ $statusClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <flux:button type="button" wire:click="edit({{ $vehicle->id }})"
                                            variant="primary" color="emerald" size="sm">
                                            Edit
                                        </flux:button>

                                        <x-confirm-delete name="delete-vehicle-{{ $vehicle->id }}"
                                            title="Hapus kendaraan?"
                                            message="Anda akan menghapus {{ $vehicle->code }} - {{ $vehicle->plate_number }}. Tindakan ini tidak dapat dibatalkan."
                                            confirm-label="Hapus" button-label="Delete"
                                            wire:click="delete({{ $vehicle->id }})" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">
                                        Belum ada data kendaraan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $this->vehicles->links() }}
                </div>
            </flux:card>
        </div>

        <div class="space-y-6">
            <flux:card
                class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Form Kendaraan</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tambah atau perbarui data.</p>
                    </div>
                    <button type="button" wire:click="createForm"
                        class="text-xs font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-300">
                        Tambah Baru
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Kode</label>
                            <input type="text" wire:model.defer="code"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('code')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Plate Number</label>
                            <input type="text" wire:model.defer="plateNumber"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('plateNumber')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Brand</label>
                            <input type="text" wire:model.defer="brand"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('brand')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Model</label>
                            <input type="text" wire:model.defer="model"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('model')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tahun</label>
                            <input type="number" wire:model.defer="year"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('year')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Ownership</label>
                            <select wire:model.defer="ownershipType"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="company">Company</option>
                                <option value="rental">Rental</option>
                            </select>
                            @error('ownershipType')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Region</label>
                            <select wire:model.defer="formRegionId"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Pilih region</option>
                                @foreach ($this->regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                            @error('formRegionId')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Jenis</label>
                            <select wire:model.defer="formVehicleTypeId"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                <option value="">Pilih jenis</option>
                                @foreach ($this->vehicleTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('formVehicleTypeId')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Status</label>
                        <select wire:model.defer="statusValue"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                            @foreach ($this->statusOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statusValue')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Fuel Type</label>
                            <input type="text" wire:model.defer="fuelType"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('fuelType')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Fuel
                                Consumption</label>
                            <input type="number" step="0.01" wire:model.defer="fuelConsumption"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('fuelConsumption')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Odometer</label>
                            <input type="number" wire:model.defer="odometer"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('odometer')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Notes</label>
                            <input type="text" wire:model.defer="notes"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('notes')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" wire:click="save"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        {{ $vehicleId ? 'Update' : 'Save' }}
                    </button>
                </div>
            </flux:card>
        </div>
    </div>
</div>
