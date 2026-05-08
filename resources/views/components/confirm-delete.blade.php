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

<flux:modal name="{{ $name }}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $title }}</flux:heading>

            <flux:text class="mt-2">
                {!! nl2br(e($message)) !!}
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">{{ $cancelLabel }}</flux:button>
            </flux:modal.close>

            <flux:button type="button" variant="danger" {{ $attributes }}>{{ $confirmLabel }}</flux:button>
        </div>
    </div>
</flux:modal>
