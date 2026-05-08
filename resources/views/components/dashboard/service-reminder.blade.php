<flux:card
    class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/80">
    <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
        <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Service Reminder</p>
        <p class="text-xs text-amber-600 dark:text-amber-200">Kendaraan dengan jadwal service mendekati 30 hari ke depan.
        </p>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-left text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                <tr>
                    <th class="pb-3">Vehicle</th>
                    <th class="pb-3">Last Service</th>
                    <th class="pb-3">Next Service</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($this->reminders as $reminder)
                    @php
                        $nextDate = optional($reminder->next_service_date);
                        $status = 'Upcoming';
                        $statusClasses = 'bg-slate-100 text-slate-600';

                        if ($nextDate && $nextDate->isPast()) {
                            $status = 'Overdue';
                            $statusClasses = 'bg-rose-50 text-rose-600';
                        } elseif ($nextDate && $nextDate->diffInDays(now()) <= 14) {
                            $status = 'Due Soon';
                            $statusClasses = 'bg-amber-50 text-amber-600';
                        }
                    @endphp
                    <tr class="text-slate-700 dark:text-slate-200">
                        <td class="py-3 font-medium text-slate-900 dark:text-white">
                            {{ $reminder->vehicle->code ?? '-' }} - {{ $reminder->vehicle->model ?? '' }}
                        </td>
                        <td class="py-3">{{ optional($reminder->service_date)->format('d M Y') }}</td>
                        <td class="py-3">{{ optional($reminder->next_service_date)->format('d M Y') }}</td>
                        <td class="py-3">
                            <span class="{{ $statusClasses }} rounded-full px-2.5 py-1 text-xs font-semibold">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-sm text-slate-400 dark:text-slate-500">Tidak ada
                            jadwal service
                            mendekati.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</flux:card>
