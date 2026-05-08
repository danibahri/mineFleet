<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Vehicle Usage</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Kendaraan paling sering digunakan</p>
        </div>
        <span class="text-xs text-slate-400 dark:text-slate-500">Top 6</span>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                <tr>
                    <th class="pb-3">Vehicle</th>
                    <th class="pb-3">Plate Number</th>
                    <th class="pb-3">Total Usage</th>
                    <th class="pb-3">Last Used</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($this->rows as $row)
                    <tr class="text-slate-700 dark:text-slate-200">
                        <td class="py-3 font-medium text-slate-900 dark:text-white">{{ $row->code }} -
                            {{ $row->model }}</td>
                        <td class="py-3">{{ $row->plate_number }}</td>
                        <td class="py-3">{{ number_format($row->total_usage) }}</td>
                        <td class="py-3 text-slate-500 dark:text-slate-400">
                            {{ $row->last_used ? \Illuminate\Support\Carbon::parse($row->last_used)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada
                            data penggunaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</flux:card>
