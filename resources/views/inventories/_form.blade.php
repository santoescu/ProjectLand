@php
    $inventory = $inventory ?? null;
    $downtimeLogs = old('downtime_logs', $inventory->downtime_logs ?? [[]]);
    $maintenanceEvents = old('maintenance_events', $inventory->maintenance_events ?? [[]]);
    $invoices = old('invoices', $inventory->invoices ?? [[]]);
@endphp

<div class="space-y-8">
    <section class="grid gap-4 md:grid-cols-2">
        <flux:input label="{{ __('Equipment name') }}" name="name" :value="old('name', $inventory->name ?? '')" required />
        <flux:input type="number" min="1" label="{{ __('Quantity') }}" name="quantity" :value="old('quantity', $inventory->quantity ?? 1)" required />
        <flux:input label="{{ __('Type') }}" name="type" :value="old('type', $inventory->type ?? '')" required />
        <flux:input label="{{ __('Make') }}" name="make" :value="old('make', $inventory->make ?? '')" />
        <flux:input label="{{ __('Model') }}" name="equipment_model" :value="old('equipment_model', $inventory->equipment_model ?? '')" />
        <flux:input label="{{ __('Asset tag') }}" name="asset_tag" :value="old('asset_tag', $inventory->asset_tag ?? '')" />
        <flux:input label="{{ __('Serial number') }}" name="serial_number" :value="old('serial_number', $inventory->serial_number ?? '')" />
        <flux:input label="{{ __('Location') }}" name="location" :value="old('location', $inventory->location ?? '')" required />

        <flux:select label="{{ __('Status') }}" name="status">
            @foreach(['active' => __('Active'), 'inactive' => __('Inactive'), 'maintenance' => __('Maintenance'), 'retired' => __('Retired')] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $inventory->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </flux:select>

        <flux:textarea label="{{ __('Notes') }}" name="notes">{{ old('notes', $inventory->notes ?? '') }}</flux:textarea>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Downtime logs') }}</h2>
            <button type="button" class="js-add-row rounded-md border border-gray-200 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" data-target="downtime-rows">
                {{ __('Add') }}
            </button>
        </div>

        <div id="downtime-rows" class="space-y-3">
            @foreach($downtimeLogs as $index => $log)
                <div class="js-row grid gap-3 rounded-lg border border-gray-200 p-3 dark:border-neutral-700 md:grid-cols-2">
                    <flux:input type="datetime-local" label="{{ __('Start') }}" name="downtime_logs[{{ $index }}][started_at]" value="{{ $log['started_at'] ?? '' }}" />
                    <flux:input type="datetime-local" label="{{ __('End') }}" name="downtime_logs[{{ $index }}][ended_at]" value="{{ $log['ended_at'] ?? '' }}" />
                    <flux:input label="{{ __('Reason') }}" name="downtime_logs[{{ $index }}][reason]" value="{{ $log['reason'] ?? '' }}" />
                    <flux:input label="{{ __('Notes') }}" name="downtime_logs[{{ $index }}][notes]" value="{{ $log['notes'] ?? '' }}" />
                    <button type="button" class="js-remove-row text-start text-sm text-red-600 dark:text-red-400">{{ __('Delete') }}</button>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Maintenance calendar') }}</h2>
            <button type="button" class="js-add-row rounded-md border border-gray-200 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" data-target="maintenance-rows">
                {{ __('Add') }}
            </button>
        </div>

        <div id="maintenance-rows" class="space-y-3">
            @foreach($maintenanceEvents as $index => $event)
                <div class="js-row grid gap-3 rounded-lg border border-gray-200 p-3 dark:border-neutral-700 md:grid-cols-2">
                    <flux:input type="datetime-local" label="{{ __('Scheduled date') }}" name="maintenance_events[{{ $index }}][scheduled_at]" value="{{ $event['scheduled_at'] ?? '' }}" />
                    <flux:input type="datetime-local" label="{{ __('Completed date') }}" name="maintenance_events[{{ $index }}][completed_at]" value="{{ $event['completed_at'] ?? '' }}" />
                    <flux:input label="{{ __('Description') }}" name="maintenance_events[{{ $index }}][description]" value="{{ $event['description'] ?? '' }}" />
                    <flux:input label="{{ __('Technician') }}" name="maintenance_events[{{ $index }}][technician]" value="{{ $event['technician'] ?? '' }}" />
                    <flux:select label="{{ __('Status') }}" name="maintenance_events[{{ $index }}][status]">
                        @foreach(['scheduled' => __('Scheduled'), 'in_progress' => __('In progress'), 'completed' => __('Completed'), 'cancelled' => __('Cancelled')] as $value => $label)
                            <option value="{{ $value }}" @selected(($event['status'] ?? 'scheduled') === $value)>{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <button type="button" class="js-remove-row text-start text-sm text-red-600 dark:text-red-400">{{ __('Delete') }}</button>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Invoices') }}</h2>
            <button type="button" class="js-add-row rounded-md border border-gray-200 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" data-target="invoice-rows">
                {{ __('Add') }}
            </button>
        </div>

        <div id="invoice-rows" class="space-y-3">
            @foreach($invoices as $index => $invoice)
                <div class="js-row grid gap-3 rounded-lg border border-gray-200 p-3 dark:border-neutral-700 md:grid-cols-2">
                    <flux:input label="{{ __('Invoice number') }}" name="invoices[{{ $index }}][number]" value="{{ $invoice['number'] ?? '' }}" />
                    <flux:input type="date" label="{{ __('Invoice date') }}" name="invoices[{{ $index }}][date]" value="{{ $invoice['date'] ?? '' }}" />
                    <flux:input label="{{ __('Customer') }}" name="invoices[{{ $index }}][customer]" value="{{ $invoice['customer'] ?? '' }}" />
                    <flux:input type="number" step="0.01" min="0" label="{{ __('Amount') }}" name="invoices[{{ $index }}][amount]" value="{{ $invoice['amount'] ?? '' }}" />
                    <flux:input label="{{ __('Description') }}" name="invoices[{{ $index }}][description]" value="{{ $invoice['description'] ?? '' }}" />
                    <button type="button" class="js-remove-row text-start text-sm text-red-600 dark:text-red-400">{{ __('Delete') }}</button>
                </div>
            @endforeach
        </div>
    </section>

    <template id="row-template">
        <div class="js-row grid gap-3 rounded-lg border border-gray-200 p-3 dark:border-neutral-700 md:grid-cols-2"></div>
    </template>
</div>

@push('scripts')
    <script>
        document.addEventListener('click', (event) => {
            const addButton = event.target.closest('.js-add-row');
            const removeButton = event.target.closest('.js-remove-row');

            if (removeButton) {
                removeButton.closest('.js-row')?.remove();
            }

            if (!addButton) return;

            const target = document.getElementById(addButton.dataset.target);
            const firstRow = target?.querySelector('.js-row');
            if (!target || !firstRow) return;

            const clone = firstRow.cloneNode(true);
            const index = target.querySelectorAll('.js-row').length;

            clone.querySelectorAll('input, textarea, select').forEach((input) => {
                input.name = input.name.replace(/\[\d+]/, `[${index}]`);
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = '';
                }
            });

            target.appendChild(clone);
        });
    </script>
@endpush
