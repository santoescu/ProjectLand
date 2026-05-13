@php
    $money = fn ($value) => '$' . number_format((float) $value, 2, '.', ',');
    $percent = fn ($spent, $budget) => ((float) $budget > 0 ? number_format(((float) $spent / (float) $budget) * 100, 2, '.', ',') : '0.00') . '%';
    $budgetTotal = $budgetRows->sum('budget');
    $spentTotal = $budgetRows->sum('spent');
    $remainingTotal = $budgetRows->sum('remaining');
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-700">
    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-neutral-700">
        <thead class="bg-gray-50 dark:bg-neutral-800">
        <tr>
            <th scope="colgroup" colspan="2" class="sticky left-0 z-10 border-r-4 border-gray-300 bg-gray-50 px-4 py-2 text-left text-xs font-semibold uppercase text-gray-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                {{ __('Information') }}
            </th>
            <th scope="colgroup" colspan="{{ max($payments->count(), 1) }}" class="border-r-4 border-gray-300 px-4 py-2 text-center text-xs font-semibold uppercase text-gray-500 dark:border-neutral-600 dark:text-neutral-400">
                {{ __('Payments') }}
            </th>
            <th scope="colgroup" colspan="3" class="px-4 py-2 text-center text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400">
                {{ __('Totals') }}
            </th>
        </tr>
        <tr>
            <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-700 dark:bg-neutral-800 dark:text-neutral-200">
                {{ __('Budget Code') }}
            </th>
            <th scope="col" class="border-r-4 border-gray-300 px-4 py-3 text-right font-semibold text-gray-700 dark:border-neutral-600 dark:text-neutral-200">
                {{ __('Budget') }}
            </th>
            @forelse($payments as $payment)
                <th scope="col" class="min-w-[150px] px-4 py-3 text-right font-semibold text-gray-700 {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} dark:text-neutral-200">
                    <span class="block">{{ optional($payment->paid_at)->format('m/d/Y') }}</span>
                    <span class="block text-xs font-normal text-gray-500 dark:text-neutral-400">{{ __('Paid') }}</span>
                </th>
            @empty
                <th scope="col" class="min-w-[150px] border-r-4 border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 dark:border-neutral-600 dark:text-neutral-200">
                    {{ __('Payments') }}
                </th>
            @endforelse
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">
                {{ __('Spent') }}
            </th>
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">
                {{ __('Balance') }}
            </th>
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">
                {{ __('Paid percentage') }}
            </th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">
        @forelse($budgetRows as $row)
            <tr>
                <td class="sticky left-0 z-10 bg-white px-4 py-3 font-medium text-gray-800 dark:bg-neutral-900 dark:text-neutral-100">
                    {{ $row['chartAccount_name'] }}
                </td>
                <td class="border-r-4 border-gray-300 px-4 py-3 text-right text-gray-700 dark:border-neutral-600 dark:text-neutral-200">
                    {{ $money($row['budget']) }}
                </td>
                @forelse($payments as $payment)
                    @php($amount = (float) ($row['payments'][(string) $payment->_id] ?? 0))
                    <td class="px-4 py-3 text-right {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} {{ $amount > 0 ? 'font-semibold text-gray-800 dark:text-neutral-100' : 'text-gray-400 dark:text-neutral-500' }}">
                        {{ $amount > 0 ? $money($amount) : '-' }}
                    </td>
                @empty
                    <td class="border-r-4 border-gray-300 px-4 py-3 text-center text-gray-400 dark:border-neutral-600 dark:text-neutral-500">-</td>
                @endforelse
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $money($row['spent']) }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $money($row['remaining']) }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $percent($row['spent'], $row['budget']) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 6 + max($payments->count(), 1) }}" class="px-4 py-8 text-center text-gray-500 dark:text-neutral-400">
                    {{ __('No records found') }}
                </td>
            </tr>
        @endforelse
        </tbody>
        @if($budgetRows->isNotEmpty())
            <tfoot class="bg-gray-50 dark:bg-neutral-800">
            <tr>
                <th scope="row" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-800 dark:bg-neutral-800 dark:text-neutral-100">
                    {{ __('Total') }}
                </th>
                <td class="border-r-4 border-gray-300 px-4 py-3 text-right font-semibold text-gray-800 dark:border-neutral-600 dark:text-neutral-100">
                    {{ $money($budgetTotal) }}
                </td>
                @forelse($payments as $payment)
                    <td class="px-4 py-3 text-right font-semibold text-gray-800 {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} dark:text-neutral-100">
                        {{ $money($budgetRows->sum(fn ($row) => (float) ($row['payments'][(string) $payment->_id] ?? 0))) }}
                    </td>
                @empty
                    <td class="border-r-4 border-gray-300 px-4 py-3 text-center font-semibold text-gray-400 dark:border-neutral-600 dark:text-neutral-500">-</td>
                @endforelse
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $money($spentTotal) }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $money($remainingTotal) }}
                </td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">
                    {{ $percent($spentTotal, $budgetTotal) }}
                </td>
            </tr>
            </tfoot>
        @endif
    </table>
</div>
