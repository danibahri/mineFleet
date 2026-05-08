<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('partials.head')
    </head>

    <body>
        <div class="min-h-screen bg-white antialiased dark:bg-zinc-800">
            @include('partials.sidebar')

            @include('partials.header')

            <flux:main class="bg-slate-50/70 dark:bg-slate-950/70">
                {{ $slot }}
            </flux:main>
        </div>

        <flux:toast />

        @livewireScripts
        @fluxScripts
        @stack('scripts')
    </body>

</html>
