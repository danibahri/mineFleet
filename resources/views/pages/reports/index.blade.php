<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-white">Reports & Export</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Laporan operasional kendaraan dengan ekspor ke CSV/Excel.</p>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    {{-- Filters --}}
    <flux:card class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <p class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Filter Laporan</p>
        <div class="flex flex-wrap gap-3">
            {{-- Report type tabs --}}
            <div class="flex rounded-xl border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-800">
                @foreach (['booking' => 'Booking', 'fuel' => 'BBM', 'service' => 'Service', 'usage' => 'Usage'] as $val => $label)
                    <button wire:click="$set('reportType', '{{ $val }}')"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition
                            {{ $reportType === $val
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <select wire:model="filterYear" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                <option value="">Semua tahun</option>
                @foreach (range(now()->year, now()->year - 4) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <select wire:model="filterMonth" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                <option value="">Semua bulan</option>
                @foreach (range(1,12) as $m)
                    <option value="{{ $m }}">{{ now()->month($m)->format('F') }}</option>
                @endforeach
            </select>
            <select wire:model="filterVehicleId" class="h-9 rounded-lg border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white sm:w-44 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900">
                <option value="">Semua kendaraan</option>
                @foreach ($this->vehicles as $v)
                    <option value="{{ $v->id }}">{{ $v->code }} — {{ $v->plate_number }}</option>
                @endforeach
            </select>
            <button wire:click="exportCsv"
                class="flex h-9 items-center gap-2 rounded-lg bg-emerald-600 px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 8l-3-3m3 3l3-3"/>
                </svg>
                Export CSV
            </button>
        </div>
    </flux:card>

    {{-- Report table --}}
    <flux:card class="w-full min-w-0 rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                    @switch($reportType)
                        @case('booking') Laporan Booking @break
                        @case('fuel') Laporan Konsumsi BBM @break
                        @case('service') Laporan Service & Maintenance @break
                        @case('usage') Laporan Vehicle Usage @break
                    @endswitch
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $this->reportData->count() }} data ditemukan</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            {{-- BOOKING --}}
            @if ($reportType === 'booking')
                <table class="w-full min-w-[860px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Booking Code</th>
                            <th class="pb-3">Pemohon</th>
                            <th class="pb-3">Kendaraan</th>
                            <th class="pb-3">Driver</th>
                            <th class="pb-3">Keperluan</th>
                            <th class="pb-3">Tgl Berangkat</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->reportData as $b)
                            @php
                                $sc = match ($b->status) {
                                    'pending'   => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300',
                                    'approved'  => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300',
                                    'rejected'  => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300',
                                    'completed' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-300',
                                    default     => 'bg-slate-100 text-slate-500',
                                };
                            @endphp
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3 font-mono text-xs">{{ $b->booking_code }}</td>
                                <td class="py-3">{{ $b->requester?->name ?? '-' }}</td>
                                <td class="py-3">{{ $b->vehicle?->code ?? '-' }} · {{ $b->vehicle?->plate_number ?? '' }}</td>
                                <td class="py-3">{{ $b->driver?->name ?? '-' }}</td>
                                <td class="py-3">{{ $b->purpose }}</td>
                                <td class="py-3">{{ $b->departure_date?->format('d M Y') }}</td>
                                <td class="py-3"><span class="{{ $sc }} rounded-full px-2.5 py-1 text-xs font-semibold">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- FUEL --}}
            @if ($reportType === 'fuel')
                <table class="w-full min-w-[760px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Kendaraan</th>
                            <th class="pb-3">Liter</th>
                            <th class="pb-3">Harga/L</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Odometer</th>
                            <th class="pb-3">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->reportData as $f)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3">{{ $f->fuel_date?->format('d M Y') }}</td>
                                <td class="py-3">{{ $f->vehicle?->code ?? '-' }}</td>
                                <td class="py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format((float)$f->liter, 2) }} L</td>
                                <td class="py-3">Rp {{ number_format((float)$f->price_per_liter, 0, ',', '.') }}</td>
                                <td class="py-3 font-semibold">Rp {{ number_format((float)$f->total_cost, 0, ',', '.') }}</td>
                                <td class="py-3">{{ $f->odometer ? number_format($f->odometer) . ' km' : '-' }}</td>
                                <td class="py-3">{{ $f->filledBy?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- SERVICE --}}
            @if ($reportType === 'service')
                <table class="w-full min-w-[780px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Kendaraan</th>
                            <th class="pb-3">Jenis</th>
                            <th class="pb-3">Bengkel</th>
                            <th class="pb-3">Biaya</th>
                            <th class="pb-3">Next Service</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->reportData as $s)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3">{{ $s->service_date?->format('d M Y') }}</td>
                                <td class="py-3">{{ $s->vehicle?->code ?? '-' }}</td>
                                <td class="py-3 font-medium">{{ $s->service_type }}</td>
                                <td class="py-3">{{ $s->workshop_name ?? '-' }}</td>
                                <td class="py-3 font-semibold">Rp {{ number_format((float)$s->cost, 0, ',', '.') }}</td>
                                <td class="py-3">{{ $s->next_service_date?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

            {{-- USAGE --}}
            @if ($reportType === 'usage')
                <table class="w-full min-w-[820px] table-auto text-sm">
                    <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        <tr>
                            <th class="pb-3">Booking</th>
                            <th class="pb-3">Kendaraan</th>
                            <th class="pb-3">Driver</th>
                            <th class="pb-3">Km Awal</th>
                            <th class="pb-3">Km Akhir</th>
                            <th class="pb-3">Total Jarak</th>
                            <th class="pb-3">Berangkat</th>
                            <th class="pb-3">Kembali</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->reportData as $u)
                            <tr class="text-slate-700 dark:text-slate-200">
                                <td class="py-3 font-mono text-xs">{{ $u->booking?->booking_code ?? '-' }}</td>
                                <td class="py-3">{{ $u->vehicle?->code ?? '-' }}</td>
                                <td class="py-3">{{ $u->driver?->name ?? '-' }}</td>
                                <td class="py-3">{{ $u->start_odometer ? number_format($u->start_odometer) . ' km' : '-' }}</td>
                                <td class="py-3">{{ $u->end_odometer ? number_format($u->end_odometer) . ' km' : '-' }}</td>
                                <td class="py-3 font-semibold text-emerald-600 dark:text-emerald-400">{{ $u->total_distance ? number_format($u->total_distance) . ' km' : '-' }}</td>
                                <td class="py-3">{{ $u->actual_departure?->format('d M Y') ?? '-' }}</td>
                                <td class="py-3">{{ $u->actual_return?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </flux:card>
</div>
