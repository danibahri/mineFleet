@props(['name' => 'squares-2x2', 'class' => 'h-5 w-5'])

@switch($name)
    @case('truck')
        <flux:icon.truck class="{{ $class }}" />
    @break

    @case('check-circle')
        <flux:icon.check-circle class="{{ $class }}" />
    @break

    @case('map-pin')
        <flux:icon.map-pin class="{{ $class }}" />
    @break

    @case('wrench-screwdriver')
        <flux:icon.wrench-screwdriver class="{{ $class }}" />
    @break

    @case('calendar-days')
        <flux:icon.calendar-days class="{{ $class }}" />
    @break

    @case('clock')
        <flux:icon.clock class="{{ $class }}" />
    @break

    @case('clipboard-document-check')
        <flux:icon.clipboard-document-check class="{{ $class }}" />
    @break

    @case('x-circle')
        <flux:icon.x-circle class="{{ $class }}" />
    @break

    @case('identification')
        <flux:icon.identification class="{{ $class }}" />
    @break

    @case('beaker')
        <flux:icon.beaker class="{{ $class }}" />
    @break

    @case('building-office-2')
        <flux:icon.building-office-2 class="{{ $class }}" />
    @break

    @case('key')
        <flux:icon.key class="{{ $class }}" />
    @break

    @case('exclamation-triangle')
        <flux:icon.exclamation-triangle class="{{ $class }}" />
    @break

    @case('arrow-down-tray')
        <flux:icon.arrow-down-tray class="{{ $class }}" />
    @break

    @case('document-arrow-down')
        <flux:icon.document-arrow-down class="{{ $class }}" />
    @break

    @case('printer')
        <flux:icon.printer class="{{ $class }}" />
    @break

    @case('plus')
        <flux:icon.plus class="{{ $class }}" />
    @break

    @case('eye')
        <flux:icon.eye class="{{ $class }}" />
    @break

    @case('chart-bar-square')
        <flux:icon.chart-bar-square class="{{ $class }}" />
    @break

    @default
        <flux:icon.squares-2x2 class="{{ $class }}" />
@endswitch
