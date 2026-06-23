<x-layouts.app :title="__('Pays')">
    @include('partials.tittle', [
        'title' => __('Pays'),
        'subheading' => __('Management of registered :name',['name'=> __('pays')])
    ])
    @php
        $userRole = Auth::user()->role;
        $userId = Auth::id();
        $selectedProjectIsInactive = filled(data_get($selectedProject ?? null, 'id')) && data_get($selectedProject ?? null, 'status') === 'inactive';
    @endphp

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

                            @php
                                $activeStatuses = request()->has('filter_applied')
                                    ? array_map('strval', request('statuses', []))
                                    : ['0', '1', '3'];
                            @endphp

                            @if (!filled(data_get($selectedProject ?? null, 'id')))
                                <form method="GET" action="{{ route('pays.index') }}">
                                    <input type="hidden" name="filter_applied" value="1">
                                    @foreach($activeStatuses as $s)
                                        <input type="hidden" name="statuses[]" value="{{ $s }}">
                                    @endforeach

                                    <flux:select name="project_id" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                        <option value="">{{ __("Project-All") }}</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ (string) request('project_id') === (string) $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </form>
                            @endif

                            <form method="GET" action="{{ route('pays.index') }}" id="status-filter-form">
                                <input type="hidden" name="project_id" value="{{ $effectiveProjectId }}">
                                <input type="hidden" name="filter_applied" value="1">

                                <div class="flex items-center gap-2">
                                <div class="w-52">
                                    <select id="pay-status-select" name="statuses[]" multiple
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
                                        <option value="0" {{ in_array('0', $activeStatuses) ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                        <option value="1" {{ in_array('1', $activeStatuses) ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                        <option value="2" {{ in_array('2', $activeStatuses) ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                        <option value="3" {{ in_array('3', $activeStatuses) ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    </select>
                                </div>
                                </div>
                            </form>

                            @unless($selectedProjectIsInactive)
                                <a href="{{ route('pays.create') }}">
                                    <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                                </a>
                            @endunless
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-neutral-700"  id="projectsTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Date')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Project')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Subproject')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Vendor')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Budget Code')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Description')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Amount')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Status')}}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($pays as $pay)
                                <tr >
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200">{{ optional($pay->created_at)->format('m/d/Y') }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ $pay->project->name }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ $pay->subproject }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ $pay->contractor->company_name }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ $pay->chartAccount->name }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 break-words dark:text-neutral-200">{{ $pay->description }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->amount_formatted}}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200"><flux:badge  :color="$pay->status_color" inset="top bottom">{{ $pay->status_label }}</flux:badge></td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap justify-center gap-2">
                                        <a href="{{ route('pays.edit', $pay->_id) }}">
                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                icon="pencil-square">
                                            </flux:button>
                                        </a>

                                        <flux:button
                                            size="sm"
                                            variant="primary"
                                            icon="numbered-list"
                                            onclick="openHistoriesModal({{ $pay }})">

                                        </flux:button>

                                        @if (in_array($userRole, [ 'director','admin']) && !in_array($pay->status, [1, 3, 2]))

                                            <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 3,'user_id' => $userId]) }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="hand-thumb-up">
                                                </flux:button>
                                            </a>
                                            <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 1,'user_id' => $userId]) }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="hand-thumb-down">
                                                </flux:button>
                                            </a>
                                        @endif
                                        @if (in_array($userRole, ['accounting_assistant','director','admin']) && in_array($pay->status, [3]))
                                            <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 2,'user_id' => $userId]) }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="banknotes">
                                                </flux:button>
                                            </a>
                                        @endif

                                        @if (!empty($pay->attachment_link))
                                            <a href="{{ $pay->attachment_link }}" target="_blank" rel="noopener noreferrer">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="paper-clip">
                                                </flux:button>
                                            </a>
                                        @endif
                                        @if (in_array($userRole, ['accounting_assistant','director','admin']) && in_array($pay->status, [ 0,]))

                                            <flux:modal.trigger name="confirm-delete-pay">
                                                <flux:button
                                                    size="sm"
                                                    type="button"
                                                    icon="archive-box-x-mark"
                                                    variant="danger"
                                                    onclick='openDeleteModal({{$pay}})'>
                                                </flux:button>
                                            </flux:modal.trigger>
                                        @endif
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

    @include('partials.datatable-pagination')

    <div id="hs-vertically-centered-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-modal-label" wire:ignore>
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="hs-vertically-centered-modal-label" class="font-bold text-gray-800 dark:text-white">
                        {{__('Histories')}}
                    </h3>
                    <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#hs-vertically-centered-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>
                <div>
                    <table id="histories-table" class="w-full table-auto border ">
                        <thead>
                        <tr class="border px-2 py-1  text-base  text-gray-700 dark:text-neutral-200">
                            <th class="border px-2 py-1">{{__('Users')}}</th>
                            <th class="border px-2 py-1">{{__('Actions')}}</th>
                            <th class="border px-2 py-1">{{__('Date')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Aquí se llenará el historial -->
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-hs-overlay="#hs-vertically-centered-modal">
                        {{__('Close')}}
                    </button>
                </div>


            </div>
        </div>
    </div>
    <div id="confirm-delete-pay-modal"
         class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
         role="dialog"
         tabindex="-1"
         aria-labelledby="confirm-delete-pay-label"
         wire:ignore>

        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">

                <!-- Header -->
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="confirm-delete-pay-label" class="font-bold text-gray-800 dark:text-white">
                        {{ __("Delete :name?", ['name' => __('Pay')]) }}
                    </h3>

                    <button type="button"
                            class="size-8 inline-flex justify-center items-center rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400"
                            aria-label="Close"
                            data-hs-overlay="#confirm-delete-pay-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-8 sm:px-8 sm:py-10 text-center">
                    <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <flux:icon.trash />
                    </div>

                    <p class="text-base sm:text-lg font-medium text-gray-800 dark:text-white">
                        {{ __("You're about to delete this :name.", ['name' => __('Pay')]) }}
                    </p>

                    <p class="mt-2 text-sm sm:text-base text-gray-500 dark:text-neutral-300">
                        {{ __('This action cannot be reversed.') }}
                    </p>
                </div>

                <!-- Footer -->
                <div class="flex justify-end items-center gap-x-3 py-4 px-5 border-t border-gray-200 dark:border-neutral-700">

                    <button type="button"
                            data-hs-overlay="#confirm-delete-pay-modal"
                            class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">

                        {{ __('Close') }}
                    </button>

                    <form id="deletePayForm" action="" method="POST">
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





    <!-- Modal Único para Editar -->
    <flux:modal name="edit-project" variant="flyout">
        <form id="editProjectForm" method="POST" action="" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <flux:heading size="lg">{{ __("Edit :name", ['name' => __('Project')]) }}</flux:heading>
                <flux:text class="mt-2">{{__('Update this :name\'s details.',['name'=>__('project')])}}</flux:text>
            </div>

            <flux:input id="name" label="{{__('Name')}}" name="name"  />
            <label   class="block text-base">
                {{ __('Subprojects') }}
            </label>
            <!-- Input Group -->
            <div id="hs-destroy-and-reinitialize-wrapper-for-copy" class="space-y-3">
                <div id="hs-destroy-and-reinitialize-content-for-copy">
                    <div class="relative">

                        <flux:input placeholder="{{__('Subproject')}}" name="subprojects[]">
                            <x-slot name="iconTrailing">
                                <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1 remove-subproject"  data-hs-copy-markup-delete-item="" />
                            </x-slot>
                        </flux:input>

                    </div>
                </div>
            </div>

            <p class="mt-3 text-end">
                <button id="hs-copy-markup-to-destroy" type="button" data-hs-copy-markup='{
                    "targetSelector": "#hs-destroy-and-reinitialize-content-for-copy",
                    "wrapperSelector": "#hs-destroy-and-reinitialize-wrapper-for-copy"
                    }'
                        class="py-1.5 px-2 inline-flex items-center gap-x-1 text-xs font-medium rounded-full border border-dashed border-gray-200 bg-white text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    {{__('Add')}}
                </button>
            </p>
            <!-- End Input Group -->


            <div id="formErrors" class="text-red-500 text-sm"></div>
            <div class="flex gap-3">

                <flux:spacer />
                <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>

                <flux:modal.trigger name="confirm-delete">
                    <flux:button
                        type="button"
                        icon="archive-box-x-mark"
                        variant="danger">
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </form>
    </flux:modal>




    @push('scripts')
        <script>
            function applyStatusFilter() {
                const form   = document.getElementById('status-filter-form');
                const select = document.getElementById('pay-status-select');
                if (!form || !select) { console.warn('applyStatusFilter: form or select not found'); return; }

                const selected = Array.from(select.selectedOptions).map(function (o) { return o.value; });
                console.log('applyStatusFilter selected values:', selected);

                const url = new URL(form.action, window.location.origin);
                url.searchParams.set('filter_applied', '1');
                const projectInput = form.querySelector('input[name="project_id"]');
                if (projectInput && projectInput.value) {
                    url.searchParams.set('project_id', projectInput.value);
                }
                selected.forEach(function (v) { url.searchParams.append('statuses[]', v); });

                console.log('navigating to:', url.toString());
                window.location.href = url.toString();
            }

            (function () {
                let pollInterval = null;

                function initStatusFilter() {
                    clearInterval(pollInterval);

                    setTimeout(function () {
                        const form    = document.getElementById('status-filter-form');
                        const select  = document.getElementById('pay-status-select');
                        const wrapper = form && form.querySelector('.w-52');
                        const btn     = wrapper && wrapper.querySelector('button');

                        if (!btn || !select || !wrapper) return;

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
                                applyTimer = setTimeout(applyStatusFilter, 600);
                            }
                        }, 300);

                        // Reposicionar dropdown con position:fixed al abrir
                        const dropdown = btn.nextElementSibling;
                        function reposition() {
                            if (!dropdown || dropdown.tagName !== 'DIV') return;
                            const rect = btn.getBoundingClientRect();
                            dropdown.style.position = 'fixed';
                            dropdown.style.width    = rect.width + 'px';
                            dropdown.style.left     = rect.left + 'px';
                            dropdown.style.top      = (rect.bottom + 4) + 'px';
                            dropdown.style.bottom   = 'auto';
                            dropdown.style.margin   = '0';
                        }

                        btn.addEventListener('click', function () { setTimeout(reposition, 10); });
                    }, 400);
                }

                // Carga inicial (recarga completa)
                document.addEventListener('DOMContentLoaded', initStatusFilter);
                // Navegación SPA de Livewire (wire:navigate) — DOMContentLoaded no vuelve a disparar
                document.addEventListener('livewire:navigated', initStatusFilter);
            })();

            function openDeleteModal(pay) {
                if (window.HSOverlay) {
                    HSOverlay.autoInit();
                    HSOverlay.open('#confirm-delete-pay-modal');
                }


                document.getElementById('deletePayForm').action = `/pays/${pay.id}` ;
            }
            function openHistoriesModal(pay) {

                if (window.HSOverlay) {
                    HSOverlay.autoInit();
                    HSOverlay.open('#hs-vertically-centered-modal');
                }

                // Seleccionamos el tbody de la tabla
                const tbody = document.querySelector('#histories-table tbody');
                tbody.innerHTML = '';

                if (pay.histories && pay.histories.length > 0) {
                    pay.histories.forEach(history => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                <td class="border px-2 py-1 text-base  text-gray-700 dark:text-neutral-200">${history.user_name}</td>
                <td class="border px-2 py-1 text-base  text-gray-700 dark:text-neutral-200">${history.action}</td>
                <td class="border px-2 py-1 text-base  text-gray-700 dark:text-neutral-200">${formatUsDateTime(history.created_at)}</td>
            `;
                        tbody.appendChild(tr);
                    });
                } else {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td colspan="4" class="border px-2 py-1 text-center">No hay historial</td>`;
                    tbody.appendChild(tr);
                }
            }

            function formatUsDateTime(value) {
                if (!value) return '';

                return new Date(value).toLocaleString('en-US', {
                    month: '2-digit',
                    day: '2-digit',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                });
            }


            $(document).ready(function () {
                let table = initWorkflowDataTable('#projectsTable', '#hs-table-with-pagination-search');
                $("#editProjectForm").on("submit", function (e) {
                    e.preventDefault(); // evita reload

                    let form = $(this);
                    let action = form.attr("action");
                    let data = form.serialize();

                    $("#formErrors").html("");
                    $.ajax({
                        url: action,
                        method: "POST", // 👈 en vez de PUT
                        data: data + "&_method=PUT",
                        success: function (response) {
                            Flux.modal('edit-project').close();
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Project')])}}"
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
                                Flux.modal('edit-project').show();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>
