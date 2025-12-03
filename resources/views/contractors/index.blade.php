<x-layouts.app :title="__('Vendor')">
    @include('partials.tittle', [
        'title' => __('Vendors'),
        'subheading' => __('Management of registered :name',['name'=> __('vendors')])
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


                                        <flux:button
                                            aria-haspopup="dialog"
                                            aria-expanded="false"
                                            aria-controls="edit-contractor"
                                            data-hs-overlay="#edit-contractor"
                                            size="sm"
                                            variant="primary"
                                            icon="pencil-square"
                                            onclick='openEditModal({{$contractor}})'>

                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td  class="px-6 py-4 text-center text-gray-500">
                                        {{__('There are no registered :name.',['name'=>__('vendors')])}}
                                    </td>
                                    <td></td>
                                    <td></td>
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


    <div id="edit-contractor" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-sm w-full z-80 bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="edit-contractor-label">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="edit-contractor-label" class="font-bold text-gray-800 dark:text-white">
                {{ __("Edit :name", ['name' => __('Vendor')]) }}
            </h3>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#edit-contractor">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="editContractorForm" method="POST" action="" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:input id="company_name" label="{{__('Company')}}" name="company_name"  />
                <flux:input id="contact_name" label="{{__('Contact')}}" name="contact_name"  />
                <flux:input id="contact_phone" label="{{__('Phone')}}" name="contact_phone"  />

                <flux:select id="payment_method" label="{{__('Payment method')}}" name="payment_method" >
                    <option value="Zelle">Zelle</option>
                    <option value="ACH">ACH</option>
                    <option value="Wire">Wire</option>
                </flux:select>
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
        </div>
    </div>




    <!-- Modal de confirmación -->
    <flux:modal name="confirm-delete" class="md:w-96" :dismissible="true">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __("Delete :name?", ['name' => __('Vendor')]) }}</flux:heading>
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
                <form id="deleteContractorForm" action="" method="POST">
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
        function openEditModal(contractor) {
            if (window.HSOverlay) {
                HSOverlay.autoInit();
                HSOverlay.open('#edit-contractor');
            }

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
                $("#editContractorForm").on("submit", function (e) {
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
                            HSOverlay.close('#edit-contractor');
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Vendor')])}}"
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
                                HSOverlay.open('#edit-contractor');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>

