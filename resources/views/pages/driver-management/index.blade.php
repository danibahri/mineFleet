<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Driver Management</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data driver untuk kebutuhan booking kendaraan.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                {{ now()->format('d M Y') }}
            </div>
            <flux:button variant="primary" color="emerald" size="sm" wire:click="openCreateForm">
                + Tambah Driver
            </flux:button>
        </div>
    </div>

    {{-- Table Card --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        {{-- Toolbar --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Daftar Driver</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Status, SIM, dan availability driver.</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                <input type="search" wire:model.debounce.400ms="search" placeholder="Cari nama, SIM, phone…"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-52 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                <select wire:model="status"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua status</option>
                    @foreach ($this->statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model="availability"
                    class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                    <option value="">Semua availability</option>
                    @foreach ($this->availabilityOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <select wire:model="regionId"
                class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-56 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                <option value="">Semua region</option>
                @foreach ($this->regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->code }})</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="mt-5 w-full overflow-x-auto">
            <table class="w-full min-w-[720px] table-auto text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                    <tr>
                        <th class="pb-3">Driver</th>
                        <th class="pb-3">Region</th>
                        <th class="pb-3">SIM</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Availability</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($this->drivers as $driver)
                        @php
                            $statusClass = $driver->status === 'active'
                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'bg-slate-100 text-slate-600 dark:bg-slate-500/10 dark:text-slate-300';
                            $availability = $driver->active_bookings_count > 0 ? 'In Use' : 'Available';
                            $availabilityClass = $driver->active_bookings_count > 0
                                ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'
                                : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300';
                        @endphp
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="py-3">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $driver->name }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $driver->phone ?? '-' }}</p>
                            </td>
                            <td class="py-3">{{ $driver->region?->name ?? '-' }}</td>
                            <td class="py-3">{{ $driver->license_number }}</td>
                            <td class="py-3">
                                <span class="{{ $statusClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ ucfirst($driver->status) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="{{ $availabilityClass }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                    {{ $availability }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="openEditForm({{ $driver->id }})">
                                        Edit
                                    </flux:button>
                                    <x-confirm-delete name="delete-driver-{{ $driver->id }}"
                                        title="Hapus driver?"
                                        message="Anda akan menghapus {{ $driver->name }}. Tindakan ini tidak dapat dibatalkan."
                                        confirm-label="Hapus" button-label="Delete"
                                        wire:click="delete({{ $driver->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">
                                Belum ada data driver.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->drivers->links() }}
        </div>
    </flux:card>

    {{-- ── MODAL: Form Create / Edit ─────────────────────────────────────── --}}
    @if ($activeModal === 'form')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ $driverId ? 'Edit Driver' : 'Tambah Driver Baru' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $driverId ? 'Perbarui data driver.' : 'Isi detail driver baru.' }}
                        </p>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama</label>
                        <input type="text" wire:model.defer="name"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nomor HP</label>
                            <input type="text" wire:model.defer="phone"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('phone') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">No. SIM</label>
                            <input type="text" wire:model.defer="licenseNumber"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('licenseNumber') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
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
                            @error('formRegionId') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Status</label>
                            <select wire:model.defer="statusValue"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                                @foreach ($this->statusOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('statusValue') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <flux:button size="sm" variant="ghost" wire:click="closeModal">Batal</flux:button>
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="save">
                        {{ $driverId ? 'Update' : 'Simpan' }}
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
