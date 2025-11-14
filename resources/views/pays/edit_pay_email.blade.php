<x-layouts.appEmail :title="__('Pay')">

    @include('partials.tittle', [
    'title' => __("Edit :name", ['name' => __('Pay')]) ,
    'subheading' => ""
    ])

    @php
        $userRole = $user->role ?? null;
    @endphp


    <form method="POST" action="{{ route('pays.updateEmail', ['id' => $pay->_id, 'user_id' => $user->_id]) }}" class="space-y-4 max-w-xl mx-auto">
        @csrf
        @method('PUT')
        <div data-flux-field class="relative {{ $errors->has('project_id') ? 'error' : '' }}">
            <label for="project_id"  class="block text-base">
                {{ __('Project') }}
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
                    }' id="project_id" name="project_id" :value="old('project_id')" class="hidden" {{ in_array($pay->status, [1, 2, 3]) ? 'disabled' : '' }}>
                <option value=""></option>
                @foreach($projects as $project)
                    <option value="{{ $project->_id }}" {{ old('project_id', $pay->project_id) == $project->_id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                    @endforeach

            </select>
            @error('project_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div data-flux-field class="relative {{ $errors->has('contractor_id') ? 'error' : '' }}">
            <label for="contractor_id"  class="block text-base">
                {{ __('Contractor') }}
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
                      }' id="contractor_id" name="contractor_id" :value="old('contractor_id')" {{ in_array($pay->status, [1, 2, 3]) ? 'disabled' : '' }}>
                <option value=""></option>
                @foreach($contractors as $contractor)
                    <option value="{{ $contractor->_id }}" {{ old('contractor_id', $pay->contractor_id) == $contractor->_id ? 'selected' : '' }}>
                        {{ $contractor->contact_name }}
                    </option>
                @endforeach

            </select>
            @error('contractor_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div data-flux-field class="relative {{ $errors->has('chartAccount_id') ? 'error' : '' }}">
            <label for="chartAccount_id"  class="block text-base">
                {{ __('Chart of Account') }}
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
                      }' id="chartAccount_id" name="chartAccount_id" :value="old('chartAccount_id')" {{ in_array($pay->status, [1, 2, 3]) ? 'disabled' : '' }}>
                <option value=""></option>
                @foreach($chartAccounts as $chartAccount)
                    <option value="{{ $chartAccount->_id }}" {{ old('chartAccount_id', $pay->chartAccount_id) == $chartAccount->_id ? 'selected' : '' }}>
                        {{ $chartAccount->name }}
                    </option>
                @endforeach

            </select>
            @error('chartAccount_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <flux:input label="{{__('Amount')}}"  id="amount" name="amount" :value="old('amount', $pay->amount)" :disabled="in_array($pay->status, [1, 2, 3])"  />
        <flux:input label="{{__('Description')}}" id="description" name="description" :value="old('description', $pay->description)" :disabled="in_array($pay->status, [1, 2, 3])"  />





        <div class="flex justify-end gap-2">
            <flux:modal.trigger name="histories-pay">
                <flux:button
                    variant="primary"
                    icon="numbered-list"
                    onclick='openHistoriesModal({{$pay}})'>
                </flux:button>
            </flux:modal.trigger>
            @if (in_array($userRole, [ 'director']) && !in_array($pay->status, [1, 3, 2]))

                <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 3,'user_id' => $user->_id]) }}">
                    <flux:button
                        variant="primary"
                        icon="hand-thumb-up">
                    </flux:button>
                </a>
                <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 1,'user_id' => $user->_id]) }}">
                    <flux:button
                        variant="primary"
                        icon="hand-thumb-down">
                    </flux:button>
                </a>
            @endif
            @if (in_array($userRole, ['accounting_assistant','director']) && in_array($pay->status, [3]))
                <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 2,'user_id' => $user->_id]) }}">
                    <flux:button
                        variant="primary"
                        icon="banknotes">
                    </flux:button>
                </a>
            @endif
            @if (in_array($userRole, [ 'director']) && !in_array($pay->status, [1, 2]))
                <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>
            @endif
        </div>

    </form>
    <flux:modal name="histories-pay" class="md:w-300">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{__('Histories')}}</flux:heading>
            </div>
            <table id="histories-table" class="w-full table-auto border border-gray-200">
                <thead>
                <tr class="bg-gray-100">
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
    </flux:modal>
    <script>
        document.addEventListener('alpine:init', () => {
            const input = document.getElementById('amount');

            input.addEventListener('input', function() {
                let value = this.value;

                // Quitar todo excepto números y punto
                value = value.replace(/[^0-9.]/g, '');

                // Separar entero y decimal
                const parts = value.split('.');
                let integerPart = parts[0] || '0';
                let decimalPart = parts[1] || '';

                // Limitar decimales a 2
                decimalPart = decimalPart.substring(0, 2);

                // Formatear la parte entera con separadores de miles
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                // Unir entero y decimal solo si hay decimal
                if (parts.length > 1) {
                    this.value = `$${integerPart}.${decimalPart}`;
                } else {
                    this.value = `$${integerPart}`;
                }
            });
            // Limpiar formato antes de enviar el formulario
            input.closest('form').addEventListener('submit', function() {
                input.value = input.value.replace(/[$,]/g, '');
            });
        });
    </script>
    <script>

        function openHistoriesModal(pay) {

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
    <script>
        document.addEventListener('load.hs.select', function() {
            const select = document.getElementById('project_id');
            if (select && !select.value) {
                select.value = "{{ old('project_id', $pay->project_id) }}";
            }
        });
    </script>

@push('scripts')
        <script>
            $(document).ready(function () {
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


</x-layouts.appEmail>
