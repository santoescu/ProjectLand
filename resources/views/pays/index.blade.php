<x-layouts.app :title="__('Pays')">
    @include('partials.tittle', [
        'title' => __('Pays'),
        'subheading' => __('Management of registered :name',['name'=> __('pays')])
    ])
    @php
        $userRole = Auth::user()->role;
        $userId = Auth::id();
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


                            <form method="GET" action="{{ route('pays.index') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">

                                <flux:select name="project_id" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                    <option value="">{{ __("Project-All") }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </flux:select>
                            </form>
                            <form method="GET" action="{{ route('pays.index') }}">
                                <input type="hidden" name="project_id" value="{{ request('project_id') }}">

                                <flux:select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                    <option value="">{{ __("Status-All") }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __("Pending") }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __("Rejected") }}</option>
                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>{{ __("Paid") }}</option>
                                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>{{ __("Approved") }}</option>
                                </flux:select>
                            </form>

                            <a href="{{ route('pays.create') }}">
                                <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                            </a>
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="projectsTable">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->created_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->project->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->subproject }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contractor->contact_name }} - {{ $contractor->company_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->chartAccount->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $pay->amount_formatted}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"><flux:badge  :color="$pay->status_color" inset="top bottom">{{ $pay->status_label }}</flux:badge></td>
                                    <td class="px-6 py-4 flex justify-center gap-2">
                                        @if (!in_array($pay->status, [ 1,2]))
                                            <a href="{{ route('pays.edit', $pay->_id) }}">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="pencil-square">
                                                </flux:button>
                                            </a>
                                        @endif

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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        {{ __('There are no registered :name.', ['name'=>__('projects')]) }}
                                    </td>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="py-1 px-4">
                        {{ $pays->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

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
                        <tr >
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

    <!-- Modal de confirmación -->
    <flux:modal name="confirm-delete" class="md:w-96" :dismissible="true">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('Project')]) }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this :name.", ['name' => __('project')]) }}</p>
                    <p>{{ __('This action cannot be reversed.') }}</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <!-- Botón cancelar -->
                <flux:modal.close >
                    <flux:button variant="ghost" x-on:click="$flux.modal('confirm-delete').close()">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <!-- Botón confirmar -->
                <form id="deleteProjectForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger" icon="archive-box-x-mark">
                        {{ __('Delete') }}
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:modal>


    <!-- Script para llenar modal dinámico -->
    <script>

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
                <td class="border px-2 py-1">${history.user_name}</td>
                <td class="border px-2 py-1">${history.action}</td>
                <td class="border px-2 py-1">${new Date(history.created_at).toLocaleString()}</td>
            `;
                    tbody.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="4" class="border px-2 py-1 text-center">No hay historial</td>`;
                tbody.appendChild(tr);
            }
        }
    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#projectsTable').DataTable({
                    dom: '',
                    language: {
                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
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

