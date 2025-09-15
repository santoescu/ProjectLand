<x-layouts.app :title="__('Chart of Account')">
    @include('partials.tittle', [
        'title' => __('Charts of Accounts'),
        'subheading' => __('Management of registered :name',['name'=> __('charts of accounts')])
    ])

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
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Type')}}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse($chartAccounts as $chartAccount)
                                <tr >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $chartAccount->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $chartAccount->type }}</td>
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
            <flux:input id="type" label="{{__('Type')}}" name="type"  />



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

            document.getElementById('name').value = chartAccount.name;
            document.getElementById('type').value = chartAccount.type;
            document.getElementById('editchartAccountForm').action = `/chartAccounts/${chartAccount.id}`;
            document.getElementById('deletechartAccountForm').action = `/chartAccounts/${chartAccount.id}` ;
        }

    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#chartAccountsTable').DataTable({
                    dom: '',
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

