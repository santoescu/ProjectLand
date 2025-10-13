<div class="relative mb-6 w-full flex items-center justify-between">
    <div>
        <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ $subheading }}</flux:subheading>
    </div>

    @isset($button)
        <a href="{{ $button['route'] }}">
            <flux:button variant="filled">
                {{ $button['label'] }}
            </flux:button>
        </a>
    @endisset
</div>
<flux:separator variant="subtle" class="mb-6" />
