<x-layouts.app :title="__('Inventory')">
    @php
        $userRole = Auth::user()->role;
        $userId = Auth::id();
    @endphp

    @include('partials.tittle', [
        'title' => __('Inventory'),
        'subheading' => __('Control equipment location, downtime, maintenance calendar and invoice revenue.'),
        'button' => in_array($userRole, ['admin', 'manager'], true) ? [
            'route' => route('inventories.create'),
            'label' => __('New'),
        ] : null,
    ])

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-neutral-700">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-neutral-700">
            <div class="relative max-w-xs">
                <label class="sr-only">{{ __('Search') }}</label>
                <flux:input
                    name="inventory-table-search"
                    id="inventory-table-search"
                    icon="magnifying-glass"
                    placeholder="{{ __('Search') }}"
                />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="inventoriesTable" class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Equipment') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Quantity') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Make') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Model') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Location') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Downtime') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Next maintenance') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Revenue') }}</th>
                        @if (in_array($userRole, ['admin', 'manager'], true))
                            <th class="px-6 py-3 text-end text-xs font-medium uppercase text-gray-500 dark:text-neutral-300">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse($inventories as $inventory)
                        <tr class="bg-white dark:bg-neutral-800">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                <div>{{ $inventory->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-neutral-400">{{ $inventory->asset_tag ?: $inventory->serial_number }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ $inventory->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ $inventory->type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ $inventory->make }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ $inventory->equipment_model }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ $inventory->location }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ __(ucfirst($inventory->status)) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">{{ trans_choice(':count record|:count records', $inventory->downtime_count, ['count' => $inventory->downtime_count]) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-neutral-200">
                                {{ data_get($inventory->next_maintenance, 'scheduled_at', __('Not scheduled')) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $inventory->revenue_total_formatted }}</td>
                            @if (in_array($userRole, ['admin', 'manager'], true))
                                <td class="px-6 py-4 text-end">
                                    <a href="{{ route('inventories.edit', $inventory) }}">
                                        <flux:button size="sm" variant="primary" icon="pencil-square" />
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-neutral-800">
                            <td colspan="{{ in_array($userRole, ['admin', 'manager'], true) ? 11 : 10 }}" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-neutral-400">
                                {{ __('There are no registered :name.', ['name' => __('equipment')]) }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-2 dark:border-neutral-700">
            {{ $inventories->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#inventoriesTable').DataTable({
                    dom: '',
                    ordering: true,
                    language: {
                        zeroRecords: "{{ __('No matching records found') }}",
                    },
                    columnDefs: [
                        @if (in_array($userRole, ['admin', 'manager'], true))
                            { orderable: false, targets: -1 },
                        @endif
                    ],
                });

                $('#inventory-table-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
            });
        </script>
    @endpush
</x-layouts.app>
