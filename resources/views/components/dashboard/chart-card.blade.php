@props([
    'title',
    'subtitle',
    'height' => 'h-64 sm:h-72',
])

<section
    {{ $attributes->merge(['class' => 'rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 sm:p-5']) }}>
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
        </div>
        {{ $action ?? '' }}
    </div>
    <div class="{{ $height }}">
        {{ $slot }}
    </div>
</section>
