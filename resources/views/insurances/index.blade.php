<x-layouts.app :title="__('Insurances')">
    @include('partials.tittle', [
        'title' => __('Insurances'),
        'subheading' => __('Management of registered :name',['name'=> __('insurances')])
    ])

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div>
                <p class="text-sm text-gray-500 dark:text-neutral-400">{{ __('Active') }}</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $counts['active'] }}</p>
            </div>
            <flux:badge color="green" inset="top bottom">{{ __('Active') }}</flux:badge>
        </div>
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div>
                <p class="text-sm text-gray-500 dark:text-neutral-400">{{ __('Expiring Soon') }}</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $counts['expiring_soon'] }}</p>
            </div>
            <flux:badge color="yellow" inset="top bottom">{{ __('Expiring Soon') }}</flux:badge>
        </div>
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div>
                <p class="text-sm text-gray-500 dark:text-neutral-400">{{ __('Expired') }}</p>
                <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $counts['expired'] }}</p>
            </div>
            <flux:badge color="red" inset="top bottom">{{ __('Expired') }}</flux:badge>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                    <div class="py-3 px-4 flex justify-between items-center gap-4">
                        <div class="relative max-w-xs">
                            <label class="sr-only">{{__('Search')}}</label>
                            <flux:input name="hs-table-with-pagination-search" id="hs-table-with-pagination-search"  icon="magnifying-glass" placeholder="{{__('Search')}}"/>
                        </div>

                        <div class="flex items-center gap-5">
                            <form method="GET" action="{{ route('insurances.index') }}" id="insurance-status-filter-form">
                                <input type="hidden" name="filter_applied" value="1">

                                <div class="w-52">
                                    <select id="insurance-status-select" name="statuses[]" multiple
                                            data-hs-select='{
                                              "placeholder": "{{ __('Status') }}...",
                                              "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                              "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative h-10 pl-3 pr-9 flex items-center text-nowrap w-full cursor-pointer bg-white dark:bg-white/10 border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 shadow-xs text-zinc-700 dark:text-zinc-300 rounded-lg text-start text-sm hover:bg-zinc-50 dark:hover:bg-white/[15%] focus:outline-hidden",
                                              "toggleSeparators": { "betweenItemsAndCounter": " &" },
                                              "toggleCountText": "+",
                                              "toggleCountTextPlacement": "prefix-no-space",
                                              "toggleCountTextMinItems": 3,
                                              "toggleCountTextMode": "nItemsAndCount",
                                              "dropdownClasses": "absolute z-50 w-full max-h-60 p-1 space-y-0.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 rounded-lg shadow-lg overflow-y-auto",
                                              "optionClasses": "py-2 px-3 w-full text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer hover:bg-zinc-100 dark:hover:bg-white/10 rounded-lg",
                                              "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-zinc-700 dark:text-zinc-300\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                                              "extraMarkup": "<div class=\"absolute top-1/2 right-3 -translate-y-1/2 pointer-events-none\"><svg class=\"shrink-0 size-3.5 text-zinc-400\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                            }'
                                            class="hidden">
                                        <option value="active" {{ in_array('active', $activeStatuses) ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="expiring_soon" {{ in_array('expiring_soon', $activeStatuses) ? 'selected' : '' }}>{{ __('Expiring Soon') }}</option>
                                        <option value="expired" {{ in_array('expired', $activeStatuses) ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                    </select>
                                </div>
                            </form>

                            <a href="{{ route('insurances.create') }}">
                                <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                            </a>
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-neutral-700"  id="insurancesTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Vendor')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Effective Date')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Expiration Date')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Status')}}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse ($insurances as $insurance)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ optional($insurance->contractor)->company_name }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200">{{ optional($insurance->effective_date)->format('m/d/Y') }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200">{{ optional($insurance->expiration_date)->format('m/d/Y') }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200"><flux:badge :color="$insurance->status_color" inset="top bottom">{{ $insurance->status_label }}</flux:badge></td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap justify-center gap-2">
                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                icon="pencil-square"
                                                data-id="{{ $insurance->_id }}"
                                                data-contractor-id="{{ $insurance->contractor_id }}"
                                                data-effective-date="{{ optional($insurance->effective_date)->format('Y-m-d') }}"
                                                data-expiration-date="{{ optional($insurance->expiration_date)->format('Y-m-d') }}"
                                                data-link="{{ $insurance->link }}"
                                                onclick="openEditModal(this)">
                                            </flux:button>

                                            @if (!empty($insurance->link))
                                                <a href="{{ $insurance->link }}" target="_blank" rel="noopener noreferrer">
                                                    <flux:button
                                                        size="sm"
                                                        variant="primary"
                                                        icon="paper-clip">
                                                    </flux:button>
                                                </a>
                                            @endif

                                            <flux:modal.trigger name="confirm-delete-insurance">
                                                <flux:button
                                                    size="sm"
                                                    type="button"
                                                    icon="archive-box-x-mark"
                                                    variant="danger"
                                                    data-id="{{ $insurance->_id }}"
                                                    onclick="openDeleteModal(this)">
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-insurance" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-sm w-full z-80 bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="edit-insurance-label">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="edit-insurance-label" class="font-bold text-gray-800 dark:text-white">
                {{ __("Edit :name", ['name' => __('Insurance')]) }}
            </h3>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#edit-insurance">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="editInsuranceForm" method="POST" action="" class="space-y-6">
                @csrf
                @method('PUT')

                <div data-flux-field class="relative">
                    <label for="edit_contractor_id" class="block text-base text-gray-700 dark:text-neutral-200">
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
                              }' id="edit_contractor_id" name="contractor_id">
                        <option value=""></option>
                        @foreach($contractors as $contractor)
                            <option value="{{ $contractor->_id }}">{{ $contractor->company_name }} - {{ $contractor->contact_name }}</option>
                        @endforeach
                    </select>
                </div>

                @include('insurances.partials.date-range-picker', [
                    'fieldPrefix' => 'edit_insurance',
                    'initialEffective' => '',
                    'initialExpiration' => '',
                ])

                <flux:input id="edit_link" label="{{__('Link')}}" name="link" />

                <div id="formErrors" class="text-red-500 text-sm"></div>
                <div class="flex gap-3">
                    <flux:spacer />
                    <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>
                </div>
            </form>
        </div>
    </div>

    <div id="confirm-delete-insurance-modal"
         class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
         role="dialog"
         tabindex="-1"
         aria-labelledby="confirm-delete-insurance-label"
         wire:ignore>

        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="confirm-delete-insurance-label" class="font-bold text-gray-800 dark:text-white">
                        {{ __("Delete :name?", ['name' => __('Insurance')]) }}
                    </h3>

                    <button type="button"
                            class="size-8 inline-flex justify-center items-center rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400"
                            aria-label="Close"
                            data-hs-overlay="#confirm-delete-insurance-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-8 sm:px-8 sm:py-10 text-center">
                    <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <flux:icon.trash />
                    </div>

                    <p class="text-base sm:text-lg font-medium text-gray-800 dark:text-white">
                        {{ __("You're about to delete this :name.", ['name' => __('Insurance')]) }}
                    </p>

                    <p class="mt-2 text-sm sm:text-base text-gray-500 dark:text-neutral-300">
                        {{ __('This action cannot be reversed.') }}
                    </p>
                </div>

                <div class="flex justify-end items-center gap-x-3 py-4 px-5 border-t border-gray-200 dark:border-neutral-700">
                    <button type="button"
                            data-hs-overlay="#confirm-delete-insurance-modal"
                            class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">
                        {{ __('Close') }}
                    </button>

                    <form id="deleteInsuranceForm" action="" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-red-600 bg-red-600 text-white hover:bg-red-700 transition dark:bg-red-500 dark:border-red-500 dark:hover:bg-red-600">
                            <flux:icon.archive-box-x-mark />
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(button) {
            if (window.HSOverlay) {
                HSOverlay.autoInit();
                HSOverlay.open('#edit-insurance');
            }

            const data = button.dataset;
            document.getElementById('editInsuranceForm').action = `/insurances/${data.id}`;
            document.getElementById('edit_link').value = data.link || '';

            setHSSelectValue('#edit_contractor_id', data.contractorId || '');

            const pickerRoot = document.getElementById('edit_insurance_date_range_root');
            if (pickerRoot && window.Alpine) {
                Alpine.$data(pickerRoot).setInitialDates(data.effectiveDate || '', data.expirationDate || '');
            }
        }

        function openDeleteModal(button) {
            if (window.HSOverlay) {
                HSOverlay.autoInit();
                HSOverlay.open('#confirm-delete-insurance-modal');
            }

            document.getElementById('deleteInsuranceForm').action = `/insurances/${button.dataset.id}`;
        }

        function setHSSelectValue(selector, value) {
            if (!window.HSSelect) return;

            const el = document.querySelector(selector);
            if (!el) return;

            const inst = window.HSSelect.getInstance(el);
            if (inst && typeof inst.setValue === 'function') {
                inst.setValue(value);
            } else {
                el.value = value;
            }
        }
    </script>

    <script>
        function applyInsuranceStatusFilter() {
            const form = document.getElementById('insurance-status-filter-form');
            const select = document.getElementById('insurance-status-select');
            if (!form || !select) return;

            const url = new URL(form.action, window.location.origin);
            url.searchParams.set('filter_applied', '1');
            Array.from(select.selectedOptions).forEach(function (o) {
                url.searchParams.append('statuses[]', o.value);
            });
            window.location.href = url.toString();
        }

        (function () {
            let pollInterval = null;

            function initInsuranceStatusFilter() {
                clearInterval(pollInterval);

                setTimeout(function () {
                    const select = document.getElementById('insurance-status-select');
                    if (!select) return;

                    function getSelected() {
                        return Array.from(select.selectedOptions).map(function (o) { return o.value; }).sort().join(',');
                    }

                    let lastValues = getSelected();
                    let applyTimer;

                    pollInterval = setInterval(function () {
                        const current = getSelected();
                        if (current !== lastValues) {
                            lastValues = current;
                            clearTimeout(applyTimer);
                            applyTimer = setTimeout(applyInsuranceStatusFilter, 600);
                        }
                    }, 300);
                }, 400);
            }

            document.addEventListener('DOMContentLoaded', initInsuranceStatusFilter);
            document.addEventListener('livewire:navigated', initInsuranceStatusFilter);
        })();
    </script>

    @include('partials.datatable-pagination')

    @push('scripts')
        <script>
            $(document).ready(function () {
                initWorkflowDataTable('#insurancesTable', '#hs-table-with-pagination-search');

                $("#editInsuranceForm").on("submit", function (e) {
                    e.preventDefault();

                    let form = $(this);
                    let action = form.attr("action");
                    let data = form.serialize();

                    $("#formErrors").html("");
                    $.ajax({
                        url: action,
                        method: "POST",
                        data: data + "&_method=PUT",
                        success: function (response) {
                            HSOverlay.close('#edit-insurance');
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Insurance')])}}"
                                }
                            }));
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                let errorMessages = Object.values(errors)
                                    .map(e => e.join("<br>"))
                                    .join("<br>");
                                $("#formErrors").html(errorMessages);
                                HSOverlay.open('#edit-insurance');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>
