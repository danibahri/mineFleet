<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Settings</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Konfigurasi sistem, perusahaan, dan master data.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-1 rounded-xl border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-800 w-fit">
        @foreach (['company' => 'Company Profile', 'approval' => 'Approval Config', 'regions' => 'Region', 'vehicle_types' => 'Vehicle Categories'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                class="rounded-lg px-4 py-2 text-xs font-semibold transition
                    {{ $tab === $key
                        ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-900 dark:text-white'
                        : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── COMPANY PROFILE ────────────────────────────────────────────────── --}}
    @if ($tab === 'company')
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <p class="mb-5 text-sm font-semibold text-slate-900 dark:text-white">Company Profile</p>
            <div class="max-w-xl space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama Perusahaan</label>
                    <input type="text" wire:model.defer="companyName" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    @error('companyName') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Alamat</label>
                    <textarea wire:model.defer="companyAddress" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"></textarea>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Telepon</label>
                        <input type="text" wire:model.defer="companyPhone" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Email</label>
                        <input type="email" wire:model.defer="companyEmail" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        @error('companyEmail') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Website</label>
                    <input type="url" wire:model.defer="companyWebsite" placeholder="https://…" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                </div>
                <div class="pt-2">
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="saveCompany">Simpan Company Profile</flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- ── APPROVAL CONFIG ────────────────────────────────────────────────── --}}
    @if ($tab === 'approval')
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <p class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Konfigurasi Approval</p>
            <p class="mb-5 text-xs text-slate-500 dark:text-slate-400">Tentukan role yang berwenang di setiap level approval booking.</p>
            <div class="max-w-xl space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Role Approver Level 1</label>
                    <select wire:model.defer="approvalLevel1RoleId" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                        <option value="">— Tidak Dikonfigurasi —</option>
                        @foreach ($this->roles as $r)
                            <option value="{{ $r->id }}">{{ ucfirst(str_replace('_', ' ', $r->name)) }} — {{ $r->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Role Approver Level 2</label>
                    <select wire:model.defer="approvalLevel2RoleId" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                        <option value="">— Tidak Dikonfigurasi —</option>
                        @foreach ($this->roles as $r)
                            <option value="{{ $r->id }}">{{ ucfirst(str_replace('_', ' ', $r->name)) }} — {{ $r->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" wire:model.defer="requireLevel2" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600" />
                        <span>Wajib 2 level approval untuk semua booking</span>
                    </label>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    <strong>Catatan:</strong> Perubahan konfigurasi ini hanya berlaku untuk booking yang dibuat setelah perubahan disimpan.
                </div>
                <div class="pt-2">
                    <flux:button size="sm" variant="primary" color="emerald" wire:click="saveApprovalConfig">Simpan Konfigurasi</flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- ── REGIONS ────────────────────────────────────────────────────────── --}}
    @if ($tab === 'regions')
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Manajemen Region</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kelola wilayah operasional tambang.</p>
                </div>
                <flux:button size="sm" variant="primary" color="emerald" wire:click="openRegionForm()">+ Tambah Region</flux:button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Kode</th>
                            <th class="pb-3">Kendaraan</th>
                            <th class="pb-3">Users</th>
                            <th class="pb-3">Deskripsi</th>
                            <th class="pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->regions as $region)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3 font-medium text-slate-900 dark:text-white">{{ $region->name }}</td>
                                <td class="py-3 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $region->code }}</td>
                                <td class="py-3">{{ $region->vehicles_count }}</td>
                                <td class="py-3">{{ $region->users_count }}</td>
                                <td class="py-3 text-xs text-slate-500 dark:text-slate-400">{{ $region->description ?? '-' }}</td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openRegionForm({{ $region->id }})">Edit</flux:button>
                                        <x-confirm-delete name="del-region-{{ $region->id }}" title="Hapus region?" message="Hapus region {{ $region->name }}." confirm-label="Hapus" button-label="Delete" wire:click="deleteRegion({{ $region->id }})" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada region.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>

        @if ($activeRegionModal === 'form')
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="$set('activeRegionModal','')">
                <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $regionId ? 'Edit Region' : 'Tambah Region' }}</p>
                        <button wire:click="$set('activeRegionModal','')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama Region</label>
                            <input type="text" wire:model.defer="regionName" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('regionName') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Kode</label>
                            <input type="text" wire:model.defer="regionCode" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('regionCode') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Deskripsi</label>
                            <input type="text" wire:model.defer="regionDesc" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <flux:button size="sm" variant="ghost" wire:click="$set('activeRegionModal','')">Batal</flux:button>
                        <flux:button size="sm" variant="primary" color="emerald" wire:click="saveRegion">{{ $regionId ? 'Update' : 'Simpan' }}</flux:button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ── VEHICLE TYPES ──────────────────────────────────────────────────── --}}
    @if ($tab === 'vehicle_types')
        <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Kategori Kendaraan</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kelola jenis/tipe kendaraan operasional.</p>
                </div>
                <flux:button size="sm" variant="primary" color="emerald" wire:click="openVehicleTypeForm()">+ Tambah Kategori</flux:button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[440px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Deskripsi</th>
                            <th class="pb-3">Jml Kendaraan</th>
                            <th class="pb-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->vehicleTypes as $vt)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3 font-medium text-slate-900 dark:text-white">{{ $vt->name }}</td>
                                <td class="py-3 text-xs text-slate-500 dark:text-slate-400">{{ $vt->description ?? '-' }}</td>
                                <td class="py-3">{{ $vt->vehicles_count }}</td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openVehicleTypeForm({{ $vt->id }})">Edit</flux:button>
                                        <x-confirm-delete name="del-vt-{{ $vt->id }}" title="Hapus kategori?" message="Hapus {{ $vt->name }}." confirm-label="Hapus" button-label="Delete" wire:click="deleteVehicleType({{ $vt->id }})" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada kategori kendaraan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>

        @if ($activeVehicleTypeModal === 'form')
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="$set('activeVehicleTypeModal','')">
                <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $vehicleTypeId ? 'Edit Kategori' : 'Tambah Kategori' }}</p>
                        <button wire:click="$set('activeVehicleTypeModal','')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nama Kategori</label>
                            <input type="text" wire:model.defer="vehicleTypeName" placeholder="Dump Truck, Light Vehicle…" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            @error('vehicleTypeName') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Deskripsi</label>
                            <input type="text" wire:model.defer="vehicleTypeDesc" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <flux:button size="sm" variant="ghost" wire:click="$set('activeVehicleTypeModal','')">Batal</flux:button>
                        <flux:button size="sm" variant="primary" color="emerald" wire:click="saveVehicleType">{{ $vehicleTypeId ? 'Update' : 'Simpan' }}</flux:button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
