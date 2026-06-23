@php
    $money = function ($value) {
        $v = (float) $value;
        return ($v < 0 ? '-$' : '$') . number_format(abs($v), 2, '.', ',');
    };
    $percent = fn ($spent, $budget) => (abs((float) $budget) > 0 ? number_format((abs((float) $spent) / abs((float) $budget)) * 100, 2, '.', ',') : '0.00') . '%';

    $budgetTotal    = $budgetRows->sum('budget');
    $spentTotal     = $budgetRows->sum('spent');
    $remainingTotal = $budgetRows->sum('remaining');
    $hasCORows      = isset($coBudgetRows) && $coBudgetRows->isNotEmpty();

    $coTotal  = $hasCORows ? $coBudgetRows->sum('budget') : 0;
    $grandTotal = $budgetTotal + $coTotal;

    $grandColSums = [];
    foreach ($payments as $__p) {
        $__s = 0;
        foreach ($budgetRows as $__r)                      { $__s += (float) ($__r['payments'][(string) $__p->_id] ?? 0); }
        if ($hasCORows) { foreach ($coBudgetRows as $__r) { $__s += (float) ($__r['payments'][(string) $__p->_id] ?? 0); } }
        // For legacy payments with no matching allocations, fall back to the payment's own amount
        $grandColSums[(string) $__p->_id] = $__s > 0 ? $__s : (float) ($__p->amount ?? 0);
    }

    // Use actual paid amounts for accuracy (covers legacy payments without proper allocations)
    $grandSpentTotal     = $payments->sum(fn ($p) => (float) ($p->amount ?? 0));
    $grandRemainingTotal = $grandTotal - $grandSpentTotal;
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
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">{{ __('Spent') }}</th>
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">{{ __('Balance') }}</th>
            <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-neutral-200">{{ __('Paid percentage') }}</th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white dark:divide-neutral-700 dark:bg-neutral-900">

        {{-- Budget rows --}}
        @forelse($budgetRows as $row)
            <tr class="bg-white dark:bg-neutral-900">
                <td class="sticky left-0 z-10 bg-white px-4 py-3 font-medium text-gray-800 dark:bg-neutral-900 dark:text-neutral-100">
                    <span class="block">{{ $row['chartAccount_name'] }}</span>
                    @if(filled($row['concept'] ?? null))
                        <span class="block text-xs font-normal text-gray-500 dark:text-neutral-400">{{ $row['concept'] }}</span>
                    @endif
                </td>
                <td class="border-r-4 border-gray-300 px-4 py-3 text-right text-gray-700 dark:border-neutral-600 dark:text-neutral-200">
                    {{ $money($row['budget']) }}
                </td>
                @forelse($payments as $payment)
                    @php
                        $amount = (float) ($row['payments'][(string) $payment->_id] ?? 0);
                    @endphp
                    <td class="px-4 py-3 text-right {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} {{ $amount > 0 ? 'font-semibold text-gray-800 dark:text-neutral-100' : 'text-gray-400 dark:text-neutral-500' }}">
                        {{ $amount > 0 ? $money($amount) : '-' }}
                    </td>
                @empty
                    <td class="border-r-4 border-gray-300 px-4 py-3 text-center text-gray-400 dark:border-neutral-600 dark:text-neutral-500">-</td>
                @endforelse
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($row['spent']) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($row['remaining']) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $percent($row['spent'], $row['budget']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 6 + max($payments->count(), 1) }}" class="px-4 py-8 text-center text-gray-500 dark:text-neutral-400">
                    {{ __('No records found') }}
                </td>
            </tr>
        @endforelse


        {{-- Change Orders section --}}
        @if($hasCORows)
            <tr class="bg-gray-200 dark:bg-neutral-700">
                <td colspan="{{ 6 + max($payments->count(), 1) }}" class="sticky left-0 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-300">
                    {{ __('Change Orders') }}
                </td>
            </tr>

            @foreach($coBudgetRows as $row)
                <tr class="bg-white dark:bg-neutral-900">
                    <td class="sticky left-0 z-10 bg-white px-4 py-3 font-medium text-gray-800 dark:bg-neutral-900 dark:text-neutral-100">
                        <span class="block">{{ $row['chartAccount_name'] }}</span>
                        @if(filled($row['concept'] ?? null))
                            <span class="block text-xs font-normal text-gray-500 dark:text-neutral-400">{{ $row['concept'] }}</span>
                        @endif
                    </td>
                    <td class="border-r-4 border-gray-300 px-4 py-3 text-right {{ (float)$row['budget'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-neutral-200' }} dark:border-neutral-600">
                        {{ $money($row['budget']) }}
                    </td>
                    @forelse($payments as $payment)
                        @php
                            $coAmount = (float) ($row['payments'][(string) $payment->_id] ?? 0);
                        @endphp
                        <td class="px-4 py-3 text-right {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} {{ $coAmount != 0 ? 'font-semibold text-gray-800 dark:text-neutral-100' : 'text-gray-400 dark:text-neutral-500' }}">
                            {{ $coAmount != 0 ? $money($coAmount) : '-' }}
                        </td>
                    @empty
                        <td class="border-r-4 border-gray-300 px-4 py-3 text-center text-gray-400 dark:border-neutral-600 dark:text-neutral-500">-</td>
                    @endforelse
                    <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($row['spent']) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($row['remaining']) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $percent($row['spent'], $row['budget']) }}</td>
                </tr>
            @endforeach

        @endif

        </tbody>

        {{-- Grand total (budget + CO) --}}
        @if($budgetRows->isNotEmpty())
            <tfoot class="bg-gray-50 dark:bg-neutral-800">
            <tr>
                <th scope="row" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-800 dark:bg-neutral-800 dark:text-neutral-100">
                    {{ __('Total') }}
                </th>
                <td class="border-r-4 border-gray-300 px-4 py-3 text-right font-semibold text-gray-800 dark:border-neutral-600 dark:text-neutral-100">
                    {{ $money($grandTotal) }}
                </td>
                @forelse($payments as $payment)
                    <td class="px-4 py-3 text-right font-semibold text-gray-800 {{ $loop->last ? 'border-r-4 border-gray-300 dark:border-neutral-600' : '' }} dark:text-neutral-100">
                        {{ $money($grandColSums[(string) $payment->_id] ?? 0) }}
                    </td>
                @empty
                    <td class="border-r-4 border-gray-300 px-4 py-3 text-center font-semibold text-gray-400 dark:border-neutral-600 dark:text-neutral-500">-</td>
                @endforelse
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($grandSpentTotal) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $money($grandRemainingTotal) }}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-neutral-100">{{ $percent($grandSpentTotal, $grandTotal) }}</td>
            </tr>
            </tfoot>
        @endif
    </table>
</div>
