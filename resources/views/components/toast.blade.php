@props([
    'duration' => 3000,
])

<div x-data="{
    show: false,
    message: '',
    type: 'success',
    timeout: null,
}"
    @toast.window="
        clearTimeout(timeout);
        message = $event.detail.message || 'Berhasil';
        type = $event.detail.type || 'success';
        show = true;
        timeout = setTimeout(() => show = false, {{ $duration }});
    "
    x-show="show" x-transition.opacity.duration.300ms class="fixed right-6 top-6 z-50">
    <div class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg"
        :class="{
            'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200': type === 'success',
            'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200': type === 'error',
            'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200': type === 'warning',
            'border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200': type === 'info',
        }">
        <span class="font-semibold" x-text="message"></span>
    </div>
</div>
