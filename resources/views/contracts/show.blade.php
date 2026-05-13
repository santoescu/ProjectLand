<x-layouts.app :title="__('Contract')">
    @include('partials.tittle', [
        'title' => $contract->name,
        'subheading' => __('Contract payment detail')
    ])

    <div class="mb-4 flex items-center justify-between gap-3">
        <div class="text-sm text-gray-600 dark:text-neutral-300">
            <div><span class="font-semibold">{{ __('Vendor') }}:</span> {{ $contract->contractor->company_name ?? '' }}</div>
            <div><span class="font-semibold">{{ __('Project') }}:</span> {{ $contract->project->name ?? '' }}</div>
            @if(filled($contract->subproject ?? null))
                <div><span class="font-semibold">{{ __('Subproject') }}:</span> {{ $contract->subproject }}</div>
            @endif
            <div><span class="font-semibold">{{ __('Compensation') }}:</span> {{ $contract->compensation_formatted }}</div>
        </div>

        <a href="{{ route('contracts.index') }}">
            <flux:button variant="ghost">{{ __('Back') }}</flux:button>
        </a>
    </div>

    @include('contracts.partials.payment-detail-table')
</x-layouts.app>
