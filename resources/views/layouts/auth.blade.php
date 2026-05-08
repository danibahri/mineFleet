<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 antialiased">
    {{ $slot }}
    <flux:toast />
    @livewireScripts
    @fluxScripts
</body>
</html>
