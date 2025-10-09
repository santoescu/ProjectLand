<x-layouts.app :title="__('Chart of Account')">
    @include('partials.tittle', [
        'title' => __('Charts of Accounts'),
        'subheading' => __('Management of registered :name',['name'=> __('charts of accounts')]),
        'button' => [
        'label' => __('Tree'),
        'route' => route('chartAccounts.tree')
    ]
    ])
    <!-- Contenido principal -->
    <main class="flex-1 p-6 overflow-y-auto">
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
                        <div class="py-3 px-4 flex justify-between items-center gap-4">
                            <div class="relative max-w-xs">
                                <label class="sr-only">{{__('Search')}}</label>
                                <flux:input name="hs-table-with-pagination-search" id="hs-table-with-pagination-search"  icon="magnifying-glass" placeholder="{{__('Search')}}"/>
                            </div>

                            <a href="{{ route('chartAccounts.create') }}">
                                <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                            </a>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="chartAccountsTable">
                                <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>

                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Name')}}</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Parent')}}</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($chartAccounts as $chartAccount)
                                    <tr >
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $chartAccount->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $chartAccount->name_parent }}</td>
                                        <td class="px-6 py-4 flex justify-center gap-2">
                                            <!-- Botón Editar -->
                                            <flux:modal.trigger name="edit-chartAccount">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="pencil-square"
                                                    onclick='openEditModal({{$chartAccount}})'>
                                                </flux:button>

                                            </flux:modal.trigger>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td></td>
                                        <td class="px-6 py-4 text-center text-gray-500">
                                            {{ __('There are no registered :name.', ['name'=>__('charts of accounts')]) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforelse


                                </tbody>
                            </table>
                        </div>
                        <div class="py-1 px-4">
                            {{ $chartAccounts->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </main>

    <!-- Modal Único para Editar -->
    <flux:modal name="edit-chartAccount" variant="flyout">
        <form id="editchartAccountForm" method="POST" action="" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <flux:heading size="lg">{{ __("Edit :name", ['name' => __('Chart of Account')]) }}</flux:heading>
                <flux:text class="mt-2">{{__('Update this :name\'s details.',['name'=>__('chart of account')])}}</flux:text>
            </div>

            <flux:input id="name" label="{{__('Name')}}" name="name"  />
            <div data-flux-field>
                <label for="parent_id"  class="block text-base">
                    {{ __('Parent') }}
                </label>
                <select data-hs-select='{
                      "hasSearch": true,
                      "optionAllowEmptyOption": true,
                      "isSelectedOptionOnTop": true,
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
                      "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                    }' id="parent_id" name="parent_id">
                    <option value=""></option>
                    @foreach($chartAccounts as $chartAccount)
                        <option value="{{ $chartAccount->_id }}">{{ $chartAccount->name }}</option>
                    @endforeach

                </select>
            </div>



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
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('Chart of Account')]) }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this :name.", ['name' => __('chart of account')]) }}</p>
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
                <form id="deletechartAccountForm" action="" method="POST">
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
        function openEditModal(chartAccount) {
            HSSelect.getInstance('#parent_id').setValue(chartAccount.parent_id);
            document.getElementById('name').value = chartAccount.name;
            document.getElementById('editchartAccountForm').action = `/chartAccounts/${chartAccount.id}`;
            document.getElementById('deletechartAccountForm').action = `/chartAccounts/${chartAccount.id}` ;
        }

    </script>

    <script>
        function showView(view) {
            const table = document.getElementById('tableView');
            const tree  = document.getElementById('treeView');

            const navTree = document.getElementById('navTree');
            const navTable = document.getElementById('navTable');

            if (view === 'table') {
                table.classList.remove('hidden');
                tree.classList.add('hidden');

                navTable.classList.add('bg-gray-100', 'dark:bg-neutral-700'); // activo
                navTree.classList.remove('bg-gray-100', 'dark:bg-neutral-700');
            } else {
                table.classList.add('hidden');
                tree.classList.remove('hidden');

                navTree.classList.add('bg-gray-100', 'dark:bg-neutral-700'); // activo
                navTable.classList.remove('bg-gray-100', 'dark:bg-neutral-700');
            }
        }

    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#chartAccountsTable').DataTable({
                    dom: '',
                    pageLength: 100,
                    language: {
                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
                $("#editchartAccountForm").on("submit", function (e) {
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
                            Flux.modal('edit-chartAccount').close();
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Chart of Account')])}}"
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
                                Flux.modal('edit-chartAccount').show();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>

