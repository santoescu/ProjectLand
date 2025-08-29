<x-layouts.app :title="__('Contractor')">
    @include('partials.tittle', [
        'title' => __('Contractors'),
        'subheading' => __('Management of registered contractors')
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

                        <a href="{{ route('contractors.create') }}">
                            <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                        </a>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="contractorsTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>

                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Company')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Contact')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Phone')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Payment method')}}</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse ($contractors as $contractor)
                                <tr >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contractor->company_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contractor->contact_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contractor->contact_phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contractor->payment_method }}</td>
                                    <td class="px-6 py-4 flex justify-center gap-2">
                                        <!-- Botón Editar -->
                                        <flux:modal.trigger name="edit-contractor">
                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                icon="pencil-square"
                                                onclick='openEditModal({{$contractor}})'>
                                            </flux:button>

                                        </flux:modal.trigger>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        {{__('There are no registered contractors.')}}
                                    </td>
                                </tr>
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="py-1 px-4">
                        {{ $contractors->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- Modal Único para Editar -->
    <flux:modal name="edit-contractor" variant="flyout">
        <form id="editContractorForm" method="POST" action="" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <flux:heading size="lg">{{ __("Edit :name", ['name' => __('Contractor')]) }}</flux:heading>
                <flux:text class="mt-2">{{__('Update this contractor\'s details.')}}</flux:text>
            </div>

            <flux:input id="company_name" label="{{__('Company')}}" name="company_name" required />
            <flux:input id="contact_name" label="{{__('Contact')}}" name="contact_name" required />
            <flux:input id="contact_phone" label="{{__('Phone')}}" name="contact_phone" required />

            <flux:select id="payment_method" label="{{__('Payment method')}}" name="payment_method" required>
                <option value="Zelle">Zelle</option>
                <option value="ACH">ACH</option>
                <option value="Wire">Wire</option>
            </flux:select>

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
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('Contractor')]) }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this :name.", ['name' => __('contractor')]) }}</p>
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
            <form id="deleteContractorForm" action="{{ route('contractors.destroy', $contractor->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <flux:button type="submit" variant="danger" icon="archive-box-x-mark">
                    {{ __('Delete') }}
                </flux:button>
            </form>
        </div>
    </flux:modal>
    <!-- Formulario de eliminar oculto -->
    <form id="deleteContractorForm" method="POST" style="display:none">
        @csrf
        @method('DELETE')
    </form>

    <!-- Script para llenar modal dinámico -->
    <script>
        function openEditModal(contractor) {


            document.getElementById('company_name').value = contractor.company_name;
            document.getElementById('contact_name').value = contractor.contact_name;
            document.getElementById('contact_phone').value = contractor.contact_phone;
            document.getElementById('payment_method').value = contractor.payment_method;
            document.getElementById('editContractorForm').action = `/contractors/${contractor.id}`;
            document.getElementById('deleteContractorForm').action = `/contractors/${contractor.id}` ;
        }

    </script>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#contractorsTable').DataTable({
                    dom: '',
                    language: {

                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
            });
        </script>
    @endpush

</x-layouts.app>

