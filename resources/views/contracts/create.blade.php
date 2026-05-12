<x-layouts.app :title="__('Contracts')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('Contracts')]) ,
    'subheading' => ""
    ])

    <form method="POST" action="{{ route('contracts.store') }}" class="space-y-4 max-w-3xl mx-auto">
        @csrf
        @php
            $oldBudgets = old('contract_budgets', [['chartAccount_id' => '', 'budget' => '']]);
            $lockedProjectId = (string) ($effectiveProjectId ?? '');
            $selectedProjectId = $lockedProjectId ?: old('project_id');
            $contractSelectConfig = [
                'hasSearch' => true,
                'optionAllowEmptyOption' => true,
                'minSearchLength' => 3,
                'searchPlaceholder' => __('Search'),
                'searchClasses' => 'block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3',
                'searchWrapperClasses' => 'bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-700',
                'placeholder' => __('Select'),
                'toggleTag' => '<button type="button" aria-expanded="false"><span class="me-2" data-icon></span><span class="text-gray-800 dark:text-neutral-200 " data-title></span></button>',
                'toggleClasses' => 'hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600',
                'dropdownClasses' => 'mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-700 dark:border-neutral-700',
                'optionClasses' => 'py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800',
                'optionTemplate' => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>',
                'extraMarkup' => [
                    '<div class="hidden hs-error:block absolute top-1/2 end-8 -translate-y-1/2"><svg class="shrink-0 size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg></div>',
                    '<div class="absolute top-1/2 end-3 -translate-y-1/2"><svg class="shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg></div>',
                ],
            ];
        @endphp

        <flux:input label="{{__('name')}}" name="name" :value="old('name')" />


        <div data-flux-field class="relative {{ $errors->has('contractor_id') ? 'error' : '' }}">
            <label for="contractor_id"  class="block text-base text-gray-700 dark:text-neutral-200">
                {{ __('Vendor') }}
            </label>
            <select data-hs-select='{
                      "hasSearch": true,
                      "optionAllowEmptyOption": true,
                      "minSearchLength": 3,
                      "searchPlaceholder": "{{__('Search')}}",
                      "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                      "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-700",
                      "placeholder": "{{__('Select')}}",
                      "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
                      "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                      "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-700 dark:border-neutral-700",
                      "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                      "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-black dark:text-white\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                      "extraMarkup": [
                        "<div class=\"hidden hs-error:block absolute top-1/2 end-8 -translate-y-1/2\"><svg class=\"shrink-0 size-4 text-red-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" x2=\"12\" y1=\"8\" y2=\"12\"/><line x1=\"12\" x2=\"12.01\" y1=\"16\" y2=\"16\"/></svg></div>",
                        "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                        ]
                      }' id="contractor_id" name="contractor_id" :value="old('contractor_id')">
                <option value=""></option>
                @foreach($contractors as $contractor)
                    <option value="{{ $contractor->_id }}" {{ old('contractor_id') == $contractor->_id ? 'selected' : '' }}>{{ $contractor->company_name }} - {{ $contractor->contact_name }}</option>
                @endforeach

            </select>
            @error('contractor_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div data-flux-field class="relative {{ $errors->has('project_id') ? 'error' : '' }}">
            <label for="project_id" class="block text-base text-gray-700 dark:text-neutral-200">
                {{ __('Project') }}
            </label>
            @if($lockedProjectId)
                <input type="hidden" name="project_id" value="{{ $lockedProjectId }}">
            @endif
            <select id="project_id" name="{{ $lockedProjectId ? '' : 'project_id' }}" class="hidden" data-hs-select='@json($contractSelectConfig)' {{ $lockedProjectId ? 'disabled' : '' }}>
                <option value=""></option>
                @foreach($projects as $project)
                    <option value="{{ $project->_id }}" {{ (string) $selectedProjectId === (string) $project->_id ? 'selected' : '' }}>{{ $project->name }}</option>
                @endforeach
            </select>
            @error('project_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <flux:input label="{{__('Compensation')}}"  id="compensation" name="compensation" :value="old('compensation')" readonly />

        <div class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <label class="block text-base text-gray-700 dark:text-neutral-200">
                    {{ __('Budget Code') }} / {{ __('Budget') }}
                </label>
                <flux:button type="button" variant="filled" icon="plus" onclick="addBudgetRow()">{{ __('Add') }}</flux:button>
            </div>

            <div id="budgetRows" class="space-y-3">
                @foreach($oldBudgets as $index => $budget)
                    <div class="budget-row grid grid-cols-1 gap-3 sm:grid-cols-[1fr_180px_44px]">
                        <select name="contract_budgets[{{ $index }}][chartAccount_id]" class="budget-account hidden" data-hs-select='@json($contractSelectConfig)'>
                            <option value=""></option>
                            @foreach($chartAccounts as $chartAccount)
                                <option value="{{ $chartAccount->_id }}" {{ ($budget['chartAccount_id'] ?? '') == $chartAccount->_id ? 'selected' : '' }}>{{ $chartAccount->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" inputmode="decimal" name="contract_budgets[{{ $index }}][budget]" value="{{ $budget['budget'] ?? '' }}" class="budget-amount w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" placeholder="{{ __('Budget') }}">
                        <button type="button" class="rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700" onclick="removeBudgetRow(this)">X</button>
                    </div>
                @endforeach
            </div>
            @error('contract_budgets')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            @error('contract_budgets.*.chartAccount_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            @error('contract_budgets.*.budget')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('contracts.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
    <script>
        const chartAccountOptions = @json($chartAccounts->map(fn ($chartAccount) => [
            'id' => (string) $chartAccount->_id,
            'name' => $chartAccount->name,
        ])->values());
        const contractBudgetSelectConfig = @json($contractSelectConfig);

        function addBudgetRow() {
            const rows = document.getElementById('budgetRows');
            const index = rows.querySelectorAll('.budget-row').length;
            const options = chartAccountOptions
                .map(account => `<option value="${escapeHtml(account.id)}">${escapeHtml(account.name)}</option>`)
                .join('');

            rows.insertAdjacentHTML('beforeend', `
                <div class="budget-row grid grid-cols-1 gap-3 sm:grid-cols-[1fr_180px_44px]">
                    <select name="contract_budgets[${index}][chartAccount_id]" class="budget-account hidden">
                        <option value=""></option>${options}
                    </select>
                    <input type="text" inputmode="decimal" name="contract_budgets[${index}][budget]" class="budget-amount w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" placeholder="{{ __('Budget') }}">
                    <button type="button" class="rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700" onclick="removeBudgetRow(this)">X</button>
                </div>
            `);

            initBudgetSelect(rows.lastElementChild.querySelector('.budget-account'));
            updateCompensationFromBudgets();
        }

        function removeBudgetRow(button) {
            const rows = document.querySelectorAll('#budgetRows .budget-row');
            if (rows.length === 1) {
                rows[0].querySelector('.budget-account').value = '';
                rows[0].querySelector('.budget-amount').value = '';
                updateCompensationFromBudgets();
                return;
            }

            button.closest('.budget-row').remove();
            reindexBudgetRows();
            updateCompensationFromBudgets();
        }

        function initBudgetSelect(select) {
            if (!select || !window.HSSelect) return;

            select.setAttribute('data-hs-select', JSON.stringify(contractBudgetSelectConfig));
            new window.HSSelect(select);
        }

        function reindexBudgetRows() {
            document.querySelectorAll('#budgetRows .budget-row').forEach((row, index) => {
                row.querySelector('.budget-account').name = `contract_budgets[${index}][chartAccount_id]`;
                row.querySelector('.budget-amount').name = `contract_budgets[${index}][budget]`;
            });
        }

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatMoneyForInput(value) {
            return '$' + Number(value || 0).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function parseMoneyInput(value) {
            return Number(normalizeMoneyInput(value)) || 0;
        }

        function normalizeMoneyInput(value) {
            value = String(value || '').replace(/[^0-9,.]/g, '');
            const lastComma = value.lastIndexOf(',');
            const lastDot = value.lastIndexOf('.');

            if (lastComma !== -1 && lastDot !== -1) {
                const decimalSeparator = lastComma > lastDot ? ',' : '.';
                const thousandsSeparator = decimalSeparator === ',' ? '.' : ',';
                return value.replaceAll(thousandsSeparator, '').replace(decimalSeparator, '.');
            }

            if (lastComma !== -1) {
                return normalizeSingleSeparatorMoney(value, ',');
            }

            if (lastDot !== -1) {
                return normalizeSingleSeparatorMoney(value, '.');
            }

            return value;
        }

        function normalizeSingleSeparatorMoney(value, separator) {
            const parts = value.split(separator);

            if (parts.length > 2) {
                return value.replaceAll(separator, '');
            }

            const [integerPart, fractionPart] = parts;

            if (fractionPart.length === 3 && integerPart.length <= 3) {
                return value.replaceAll(separator, '');
            }

            return value.replace(separator, '.');
        }

        function updateCompensationFromBudgets() {
            const total = Array.from(document.querySelectorAll('#budgetRows .budget-amount'))
                .reduce((sum, budgetInput) => sum + parseMoneyInput(budgetInput.value), 0);

            document.getElementById('compensation').value = formatMoneyForInput(total);
        }

        document.addEventListener('alpine:init', () => {
            const input = document.getElementById('compensation');

            document.getElementById('budgetRows')?.addEventListener('input', function(event) {
                if (event.target.classList.contains('budget-amount')) {
                    updateCompensationFromBudgets();
                }
            });
            updateCompensationFromBudgets();

            input.addEventListener('input', function() {
                let value = this.value;

                // Quitar todo excepto números y punto
                value = value.replace(/[^0-9,.]/g, '');

                // La coma es decimal; el punto siempre se trata como miles.
                const hasDecimal = value.includes(',');
                const parts = value.split(',');
                let integerPart = parts[0].replace(/[.,]/g, '').replace(/^0+(?=\d)/, '') || '0';
                let decimalPart = parts.slice(1).join('').replace(/[.,]/g, '');

                // Limitar decimales a 2
                decimalPart = decimalPart.substring(0, 2);

                // Formatear la parte entera con separadores de miles
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Unir entero y decimal solo si hay decimal
                if (hasDecimal) {
                    this.value = `$${integerPart},${decimalPart}`;
                } else {
                    this.value = `$${integerPart}`;
                }
            });
            // Limpiar formato antes de enviar el formulario
            input.closest('form').addEventListener('submit', function() {
                input.value = normalizeMoneyInput(input.value);
                document.querySelectorAll('.budget-amount').forEach((budgetInput) => {
                    budgetInput.value = normalizeMoneyInput(budgetInput.value);
                });
            });
        });
    </script>
</x-layouts.app>
