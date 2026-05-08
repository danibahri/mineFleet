@props([
    'name' => 'delete-confirmation',
    'title' => 'Hapus data?',
    'message' => 'Tindakan ini tidak dapat dibatalkan.',
    'confirmLabel' => 'Delete',
    'cancelLabel' => 'Cancel',
    'buttonLabel' => 'Delete',
    'variant' => 'danger',
    'size' => 'sm',
])

<flux:modal.trigger name="{{ $name }}">
    <flux:button variant="{{ $variant }}" size="{{ $size }}">{{ $buttonLabel }}</flux:button>
</flux:modal.trigger>

<flux:modal name="{{ $name }}" class="min-w-[22rem] text-left">
    <div class="space-y-6">
        <div class="mt-6">
            <flux:heading size="lg" class="text-left">{{ $title }}</flux:heading>

            <flux:text class="mt-2 text-left">
                {!! nl2br(e($message)) !!}
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost" size="{{ $size }}">{{ $cancelLabel }}</flux:button>
            </flux:modal.close>

            <flux:button type="button" variant="{{ $variant }}" size="{{ $size }}" {{ $attributes }}>
                {{ $confirmLabel }}</flux:button>
        </div>
    </div>
</flux:modal>
