<x-layouts.app :title="__('Contract')">
    @include('partials.tittle', [
        'title' => __('Contracts'),
        'subheading' => __('Management of registered :name',['name'=> __('contracts')])
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

                        <a href="{{ route('contracts.create') }}">
                            <flux:button variant="filled" icon="plus">{{__('New')}}</flux:button>
                        </a>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700"  id="contractsTable">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>

                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Name')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Vendor')}}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Compensation')}}</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @forelse ($contracts as $contract)
                                <tr >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contract->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contract->contractor->company_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $contract->compensation_formatted }}</td>
                                    <td class="px-6 py-4 flex justify-center gap-2">


                                        <flux:button
                                            size="sm"
                                            variant="primary"
                                            icon="pencil-square"
                                            onclick='openEditModal({{$contract}})'>

                                        </flux:button>
                                        <!--
                                        @if (in_array($userRole, ['accounting_assistant','director','admin']) )
                                            <a href="">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="document-text">
                                                </flux:button>
                                            </a>
                                            <a href="">
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    icon="document-plus">
                                                </flux:button>
                                            </a>
                                        @endif
                                        -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td ></td>

                                    <td  class="px-6 py-4 text-center text-gray-500">
                                        {{__('There are no registered :name.',['name'=>__('Contracts')])}}
                                    </td>
                                    <td ></td>
                                    <td ></td>
                                </tr>
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="py-1 px-4">
                        {{ $contracts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div id="edit-contract" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-sm w-full z-80 bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" tabindex="-1" aria-labelledby="edit-contract-label">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="edit-contract-label" class="font-bold text-gray-800 dark:text-white">
                {{ __("Edit :name", ['name' => __('Contract')]) }}
            </h3>
            <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#edit-contract">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="editContractForm" method="POST" action="" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:input label="{{__('name')}}" id="name" name="name" :value="old('name')" />


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
                <flux:input label="{{__('Compensation')}}"  id="compensation" name="compensation" :value="old('compensation')" />
                <div id="formErrors" class="text-red-500 text-sm"></div>
                <div class="flex gap-3">

                    <flux:spacer />
                    <flux:button type="submit" icon="archive-box-arrow-down" variant="primary"></flux:button>

                    <flux:button
                        type="button"
                        icon="archive-box-x-mark"
                        variant="danger"
                        onclick="HSOverlay.open('#confirm-delete-contract-modal')">
                    </flux:button>

                </div>
            </form>
        </div>
    </div>


    <div id="confirm-delete-contract-modal"
         class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
         role="dialog"
         tabindex="-1"
         aria-labelledby="confirm-delete-contract-label"
         wire:ignore>

        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">

                <!-- Header -->
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="confirm-delete-contract-label" class="font-bold text-gray-800 dark:text-white">
                        {{ __("Delete :name?", ['name' => __('Contract')]) }}
                    </h3>

                    <button type="button"
                            class="size-8 inline-flex justify-center items-center rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400"
                            aria-label="Close"
                            data-hs-overlay="#confirm-delete-contract-modal">
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
                        {{ __("You're about to delete this :name.", ['name' => __('Contract')]) }}
                    </p>

                    <p class="mt-2 text-sm sm:text-base text-gray-500 dark:text-neutral-300">
                        {{ __('This action cannot be reversed.') }}
                    </p>
                </div>

                <!-- Footer -->
                <div class="flex justify-end items-center gap-x-3 py-4 px-5 border-t border-gray-200 dark:border-neutral-700">

                    <button type="button"
                            data-hs-overlay="#confirm-delete-contract-modal"
                            class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">

                        {{ __('Close') }}
                    </button>

                    <form id="deleteContractForm" action="" method="POST">
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




    @push('scripts')
        <script>
            function openEditModal(contract) {
                console.log(contract);
                if (window.HSOverlay) {
                    HSOverlay.autoInit();
                    HSOverlay.open('#edit-contract');
                }
                if (window.HSSelect) {
                    HSSelect.autoInit();
                }
                document.getElementById('name').value = contract.name;
                document.getElementById('compensation').value = contract.compensation;


                HSSelect.getInstance('#contractor_id').setValue(contract.contractor_id);
                document.getElementById('editContractForm').action = `/contracts/${contract.id}`;
                document.getElementById('deleteContractForm').action = `/contracts/${contract.id}`;
            }



            $(document).ready(function () {
                // Inicializamos DataTable
                let table = $('#contractsTable').DataTable({
                    dom: '',
                    language: {

                        zeroRecords: "{{__("No matching records found")}}",

                    }

                });

                // Conectar tu input Preline al DataTable
                $('#hs-table-with-pagination-search').on('keyup', function () {
                    table.search(this.value).draw();
                });
                $("#editContractForm").on("submit", function (e) {
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
                            HSOverlay.close('#edit-contract');
                            location.reload();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'success',
                                    message: "{{ __("Updated :name", ['name' => __('Contract')])}}"
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
                                HSOverlay.open('#edit-contract');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

</x-layouts.app>
