<x-layouts.app :title="__('Contracts')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('Contracts')]) ,
    'subheading' => ""
    ])

    <style>
        .subproject-field{
            display:flex;
            flex-direction:column;
            gap:.5rem;
        }

        .subproject-field .sp-label{ order: 1; }

        .subproject-field .hs-select{ order: 2; }

        .subproject-field .hs-dropdown{ order: 3; }

        .subproject-field .sp-select{ order: 4; }

        .budget-row {
            grid-template-columns: 45% 20% 30% 5%;
        }

        @media (min-width: 640px) {
            .budget-row > .hs-select button,
            .budget-row > [data-hs-select-toggle] {
                border-top-right-radius: 0 !important;
                border-bottom-right-radius: 0 !important;
            }
        }
    </style>

    <form method="POST" action="{{ route('contracts.store') }}" class="space-y-4 max-w-3xl mx-auto">
        @csrf
        @php
            $oldBudgets = old('contract_budgets', [['chartAccount_id' => '', 'budget' => '', 'concept' => '']]);
            $lockedProjectId = (string) ($effectiveProjectId ?? '');
            $selectedProjectId = $lockedProjectId ?: old('project_id');
            $projectsForFront = $projects->map(function ($project) {
                return [
                    'id' => (string) $project->_id,
                    'name' => $project->name,
                    'subprojects' => is_array($project->subprojects ?? null) ? $project->subprojects : [],
                ];
            })->values();
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
            $budgetSelectConfig = $contractSelectConfig;
            $budgetSelectConfig['toggleClasses'] = str_replace('rounded-lg', 'rounded-s-lg rounded-e-none', $budgetSelectConfig['toggleClasses']);
        @endphp

        <flux:input label="{{__('Name')}}" name="name" :value="old('name')" />


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
        <div x-data="subprojectSelect({
                projects: @js($projectsForFront),
                oldProjectId: @js($selectedProjectId),
                oldSubproject: @js(old('subproject'))
                })"
             x-init="init()">

            <div data-flux-field class="relative {{ $errors->has('project_id') ? 'error' : '' }}" wire:ignore>
                <label for="project_id" class="block text-base text-gray-700 dark:text-neutral-200">
                    {{ __('Project') }}
                </label>
                @if($lockedProjectId)
                    <input type="hidden" name="project_id" value="{{ $lockedProjectId }}">
                @endif
                <select id="project_id" name="{{ $lockedProjectId ? '' : 'project_id' }}" class="hidden" data-hs-select='@json($contractSelectConfig)' @change="onProjectChange($event.target.value)" {{ $lockedProjectId ? 'disabled' : '' }}>
                    <option value=""></option>
                    @foreach($projects as $project)
                        <option value="{{ $project->_id }}" {{ (string) $selectedProjectId === (string) $project->_id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                @error('project_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div x-show="showSubproject" x-cloak class="mt-4 subproject-field" wire:ignore>
                <label class="block text-base text-gray-700 dark:text-neutral-200 mb-2 sp-label">
                    {{ __('Subproject') }}
                </label>

                <div id="subproject_toggle" class="w-full sp-toggle"></div>

                <select id="subproject" name="subproject" class="hidden sp-select" data-hs-select='@json($contractSelectConfig)'>
                    <option value=""></option>
                </select>
                @error('subproject')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div data-flux-field>
            <label for="compensation" class="block text-base text-gray-700 dark:text-neutral-200">{{ __('Compensation') }}</label>
            <div class="relative">
                <input type="text" id="compensation" name="compensation" class="w-full rounded-lg border border-gray-200 bg-white py-3 ps-9 pe-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" value="{{ str_replace('$', '', old('compensation', '')) }}" placeholder="0.00" readonly>
                <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                    <span class="text-gray-500 dark:text-neutral-400">$</span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <label class="block text-base text-gray-700 dark:text-neutral-200">
                    {{ __('Budget Code') }} / {{ __('Concept') }} / {{ __('Budget') }}
                </label>
                <button type="button"
                        onclick="addBudgetRow()"
                        class="py-1.5 px-2 inline-flex items-center gap-x-1 text-xs font-medium rounded-full border border-dashed border-gray-200 bg-white text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>

            <div id="budgetRows" class="space-y-3">
                @foreach($oldBudgets as $index => $budget)
                    <div class="budget-row grid grid-cols-1 items-start">
                        <select name="contract_budgets[{{ $index }}][chartAccount_id]" class="budget-account hidden" data-hs-select='@json($budgetSelectConfig)'>
                            <option value=""></option>
                            @foreach($chartAccounts as $chartAccount)
                                <option value="{{ $chartAccount->_id }}" {{ ($budget['chartAccount_id'] ?? '') == $chartAccount->_id ? 'selected' : '' }}>{{ $chartAccount->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="contract_budgets[{{ $index }}][concept]" value="{{ $budget['concept'] ?? '' }}" class="budget-concept h-[46px] w-full rounded-none border border-t-0 border-gray-200 bg-white px-3 text-sm text-gray-800 focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:bg-white disabled:text-gray-800 sm:border-s-0 sm:border-t sm:border-e-0 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-200" disabled>
                        <div class="budget-amount-wrap relative h-[46px]">
                            <input type="text" inputmode="decimal" name="contract_budgets[{{ $index }}][budget]" value="{{ $budget['budget'] ?? '' }}" class="budget-amount h-[46px] w-full rounded-none border border-t-0 border-gray-200 bg-white ps-9 pe-3 text-sm text-gray-800 focus:z-10 focus:border-blue-500 focus:ring-blue-500 sm:border-t dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" placeholder="0.00">
                            <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                                <span class="text-gray-500 dark:text-neutral-400">$</span>
                            </div>
                        </div>
                        <button type="button" class="h-[46px] rounded-b-lg border border-t-0 border-gray-200 text-gray-700 hover:bg-gray-50 sm:rounded-s-none sm:rounded-e-lg sm:border-t sm:border-s-0 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700" onclick="removeBudgetRow(this)">X</button>
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
        const contractBudgetSelectConfig = @json($budgetSelectConfig);

        document.addEventListener('alpine:init', () => {
            Alpine.data('subprojectSelect', ({ projects, oldProjectId, oldSubproject }) => ({
                projects,
                showSubproject: false,

                init() {
                    if (oldProjectId) {
                        this.onProjectChange(oldProjectId, oldSubproject);
                    }
                },

                onProjectChange(projectId, subprojectToSelect = null) {
                    const p = this.projects.find(x => x.id === projectId);
                    const subs = (p && Array.isArray(p.subprojects)) ? p.subprojects : [];

                    this.showSubproject = subs.length > 0;

                    this.$nextTick(() => {
                        if (!this.showSubproject) {
                            this.clearSubproject();
                            return;
                        }

                        this.fillSubprojectOptions(subs);
                        this.reInitHSSelect('#subproject');

                        if (subprojectToSelect) {
                            this.setHSSelectValue('#subproject', subprojectToSelect);
                        } else {
                            this.setHSSelectValue('#subproject', '');
                        }
                    });
                },

                fillSubprojectOptions(subs) {
                    const el = document.querySelector('#subproject');
                    if (!el) return;

                    el.innerHTML = '<option value=""></option>' + subs
                        .map(sp => `<option value="${this.escapeHtml(sp)}">${this.escapeHtml(sp)}</option>`)
                        .join('');
                },

                clearSubproject() {
                    const el = document.querySelector('#subproject');
                    if (el) el.value = '';
                    this.setHSSelectValue('#subproject', '');
                },

                reInitHSSelect(selector) {
                    if (!window.HSSelect) return;

                    const el = document.querySelector(selector);
                    if (!el) return;

                    const inst = window.HSSelect.getInstance(el);
                    if (inst && typeof inst.destroy === 'function') inst.destroy();

                    const toggleHolder = document.querySelector('#subproject_toggle');
                    if (toggleHolder) toggleHolder.innerHTML = '';

                    new window.HSSelect(el);
                },

                setHSSelectValue(selector, value) {
                    if (!window.HSSelect) {
                        const el = document.querySelector(selector);
                        if (el) el.value = value;
                        return;
                    }

                    const el = document.querySelector(selector);
                    if (!el) return;

                    const inst = window.HSSelect.getInstance(el);
                    if (inst && typeof inst.setValue === 'function') {
                        inst.setValue(value);
                    } else {
                        el.value = value;
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                },

                escapeHtml(str) {
                    return String(str)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }
            }));
        });

        function addBudgetRow() {
            const rows = document.getElementById('budgetRows');
            const index = rows.querySelectorAll('.budget-row').length;
            const options = chartAccountOptions
                .map(account => `<option value="${escapeHtml(account.id)}">${escapeHtml(account.name)}</option>`)
                .join('');

            rows.insertAdjacentHTML('beforeend', `
                <div class="budget-row grid grid-cols-1 items-start">
                    <select name="contract_budgets[${index}][chartAccount_id]" class="budget-account hidden">
                        <option value=""></option>${options}
                    </select>
                    <input type="text" name="contract_budgets[${index}][concept]" class="budget-concept h-[46px] w-full rounded-none border border-t-0 border-gray-200 bg-white px-3 text-sm text-gray-800 focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:bg-white disabled:text-gray-800 sm:border-s-0 sm:border-t sm:border-e-0 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-200" disabled>
                    <div class="budget-amount-wrap relative h-[46px]">
                        <input type="text" inputmode="decimal" name="contract_budgets[${index}][budget]" class="budget-amount h-[46px] w-full rounded-none border border-t-0 border-gray-200 bg-white ps-9 pe-3 text-sm text-gray-800 focus:z-10 focus:border-blue-500 focus:ring-blue-500 sm:border-t dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" placeholder="0.00">
                        <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                            <span class="text-gray-500 dark:text-neutral-400">$</span>
                        </div>
                    </div>
                    <button type="button" class="h-[46px] rounded-b-lg border border-t-0 border-gray-200 text-gray-700 hover:bg-gray-50 sm:rounded-s-none sm:rounded-e-lg sm:border-t sm:border-s-0 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700" onclick="removeBudgetRow(this)">X</button>
                </div>
            `);

            initBudgetSelect(rows.lastElementChild.querySelector('.budget-account'));
            updateDuplicateConceptFields('#budgetRows');
            updateCompensationFromBudgets();
        }

        function removeBudgetRow(button) {
            const rows = document.querySelectorAll('#budgetRows .budget-row');
            if (rows.length === 1) {
                rows[0].querySelector('.budget-account').value = '';
                rows[0].querySelector('.budget-amount').value = '';
                rows[0].querySelector('.budget-concept').value = '';
                updateDuplicateConceptFields('#budgetRows');
                updateCompensationFromBudgets();
                return;
            }

            button.closest('.budget-row').remove();
            reindexBudgetRows();
            updateDuplicateConceptFields('#budgetRows');
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
                row.querySelector('.budget-concept').name = `contract_budgets[${index}][concept]`;
            });
        }

        function updateDuplicateConceptFields(containerSelector) {
            const rows = Array.from(document.querySelectorAll(`${containerSelector} .budget-row`));
            const selectedCounts = rows.reduce((counts, row) => {
                const accountId = row.querySelector('.budget-account')?.value || '';
                if (accountId) counts[accountId] = (counts[accountId] || 0) + 1;
                return counts;
            }, {});

            rows.forEach(row => {
                const accountId = row.querySelector('.budget-account')?.value || '';
                const concept = row.querySelector('.budget-concept');
                const isDuplicate = accountId && selectedCounts[accountId] > 1;

                concept.disabled = !isDuplicate;
                concept.placeholder = isDuplicate ? '{{ __('Concept') }}' : '';
                concept.required = Boolean(isDuplicate);

                if (!isDuplicate) {
                    concept.value = '';
                }
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
            return Number(value || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function parseMoneyInput(value) {
            return Number(normalizeMoneyInput(value)) || 0;
        }

        function sanitizeMoneyInput(value) {
            value = String(value || '').replace(/[^0-9,.]/g, '');
            const separatorIndex = value.search(/[,.]/);

            if (separatorIndex === -1) {
                return value;
            }

            const integerPart = value.slice(0, separatorIndex).replace(/[,.]/g, '');
            const separator = value[separatorIndex];
            const decimalPart = value.slice(separatorIndex + 1).replace(/[,.]/g, '').substring(0, 2);

            return `${integerPart}${separator}${decimalPart}`;
        }

        function normalizeMoneyInput(value) {
            value = sanitizeMoneyInput(value);
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
            return `${integerPart}.${fractionPart.substring(0, 2)}`;
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
                    event.target.value = sanitizeMoneyInput(event.target.value);
                    updateCompensationFromBudgets();
                }
            });
            document.getElementById('budgetRows')?.addEventListener('change', function(event) {
                if (event.target.classList.contains('budget-account')) {
                    updateDuplicateConceptFields('#budgetRows');
                }
            });
            updateDuplicateConceptFields('#budgetRows');
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
