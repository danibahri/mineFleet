<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('partials.head')
    </head>

    <body>
        <div class="min-h-screen bg-white antialiased dark:bg-zinc-800">
            @include('partials.sidebar')

            @include('partials.header')

            <flux:main class="p-0 lg:p-0">
                {{ $slot }}
            </flux:main>
        </div>

        @livewireScripts
        @fluxScripts
        @stack('scripts')
    </body>

</html>
