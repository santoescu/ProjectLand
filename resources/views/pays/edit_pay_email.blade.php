<x-layouts.appEmail :title="__('Payable')">

    @include('partials.tittle', [
    'title' => __("Edit :name", ['name' => __('Payable')]) ,
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
    </style>
    @php
        $userRole = $user->role ?? null;
    @endphp


    <form method="POST" action="{{ route('pays.updateEmail', ['id' => $pay->_id, 'user_id' => $user->_id]) }}" class="space-y-4 max-w-xl mx-auto">
        @csrf
        @method('PUT')
        @php
            // Mapa liviano para el front (id, name, subprojects)
            $projectsForFront = $projects->map(function ($p) {
                return [
                    'id' => (string) $p->_id,
                    'name' => $p->name,
                    'subprojects' => is_array($p->subprojects ?? null) ? $p->subprojects : [],
                ];
            })->values();
        @endphp
        @php
            $initialProjectId = (string) old('project_id', $pay->project_id ?? '');
            $initialSubproject = (string) old('subproject', $pay->subproject ?? '');
        @endphp



        <div x-data="subprojectSelect({
                projects: @js($projectsForFront),
                initialProjectId: @js($initialProjectId),
                initialSubproject: @js($initialSubproject)
              })"
            x-init="init()">

        <div data-flux-field class="relative {{ $errors->has('project_id') ? 'error' : '' }}" wire:ignore>
                <label for="project_id" class="block text-base text-gray-700 dark:text-neutral-200">
                    {{ __('Project') }}
                </label>

                <select
                    data-hs-select='{
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
                    }'
                    id="project_id"
                    name="project_id"
                    class="hidden"
                    @change="onProjectChange($event.target.value)">
                    <option value=""></option>
                    @foreach($projects as $project)
                        <option value="{{ $project->_id }}"
                            {{ old('project_id', $pay->project_id) == $project->_id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>

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

                <select
                    id="subproject"
                    name="subproject"
                    class="hidden sp-select"
                    data-hs-select='{
                      "hasSearch": true,
                      "optionAllowEmptyOption": true,
                      "minSearchLength": 3,
                      "searchPlaceholder": "{{__('Search')}}",
                      "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                      "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-700",
                      "placeholder": "{{__('Select')}}",
                      "toggleTag": "<button type=\"button\" aria-expanded=\"false\" data-hs-select-toggle=\"#subproject_toggle\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200\" data-title></span></button>",
                      "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:focus:outline-hidden dark:focus:ring-1 dark:focus:ring-neutral-600",
                      "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-700 dark:border-neutral-700",
                      "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                      "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-3.5 text-black dark:text-white\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"20 6 9 17 4 12\"/></svg></span></div>",
                      "extraMarkup": [
                            "<div class=\"hidden hs-error:block absolute top-1/2 end-8 -translate-y-1/2\"><svg class=\"shrink-0 size-4 text-red-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" x2=\"12\" y1=\"8\" y2=\"12\"/><line x1=\"12\" x2=\"12.01\" y1=\"16\" y2=\"16\"/></svg></div>",
                            "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500 dark:text-neutral-500 \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                        ]
                      }'>
                    <option value=""></option>
                </select>
                @error('subproject')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

        </div>
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
                      }' id="contractor_id" name="contractor_id"  {{ in_array($pay->status, [1, 2, 3]) ? 'disabled' : '' }}>
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
            <label for="chartAccount_id"  class="block text-base text-gray-700 dark:text-neutral-200">
                {{ __('Budget Code') }}
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
                      }' id="chartAccount_id" name="chartAccount_id"  {{ in_array($pay->status, [1, 2, 3]) ? 'disabled' : '' }}>
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
        <flux:input label="{{__('Notes')}}" id="description" name="description" :value="old('description', $pay->description)" :disabled="in_array($pay->status, [1, 2, 3])"  />





        <div class="flex justify-end gap-2">
            <flux:button
                variant="primary"
                icon="numbered-list"
                onclick="openHistoriesModal({{ $pay }})">

            </flux:button>
            @if (in_array($userRole, [ 'director','admin']) && !in_array($pay->status, [1, 3, 2]))

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
            @if (in_array($userRole, ['accounting_assistant','director','admin']) && in_array($pay->status, [3]))
                <a href="{{ route('pays.updateStatus', ['id' => $pay->_id, 'status' => 2,'user_id' => $user->_id]) }}">
                    <flux:button
                        variant="primary"
                        icon="banknotes">
                    </flux:button>
                </a>
            @endif
            @if (in_array($userRole, [ 'director','admin']) && !in_array($pay->status, [1, 2]))
                <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>
            @endif
        </div>

    </form>
    <div id="hs-vertically-centered-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-vertically-centered-modal-label">
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
    <script>
        document.addEventListener('load.hs.select', function() {
            const select = document.getElementById('project_id');
            if (!select) return;

            const value = "{{ old('project_id', $pay->project_id) }}";

            // Setear valor
            select.value = value;

            // Disparar change para que Alpine ejecute onProjectChange
            select.dispatchEvent(new Event('change', { bubbles: true }));
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
                            }
                        }
                    });
                });
            });
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('subprojectSelect', ({ projects, initialProjectId, initialSubproject }) => ({
                    projects,
                    showSubproject: false,

                    init() {
                        // 1) setea project nativo
                        if (initialProjectId) {
                            this.setNative('#project_id', initialProjectId);

                            // 2) carga subs y selecciona subproject guardado
                            this.onProjectChange(initialProjectId, initialSubproject || '');
                        }
                    },

                    onProjectChange(projectId, subprojectToSelect = '') {
                        const p = this.projects.find(x => x.id === projectId);
                        const subs = (p && Array.isArray(p.subprojects)) ? p.subprojects : [];

                        this.showSubproject = subs.length > 0;

                        this.$nextTick(() => {
                            if (!this.showSubproject) {
                                this.destroyHS('#subproject');
                                this.setNative('#subproject', '');
                                return;
                            }

                            // 1) llena options
                            this.fillSubprojectOptions(subs);

                            // 2) pone el value ANTES de inicializar HSSelect
                            this.setNative('#subproject', subprojectToSelect || '');

                            // 3) destruye instancia previa y reconstruye SOLO ese select
                            requestAnimationFrame(() => {
                                this.rebuildHS('#subproject');

                                // 4) refuerza el valor luego de que HSSelect ya montó UI
                                requestAnimationFrame(() => {
                                    this.setNative('#subproject', subprojectToSelect || '');
                                });
                            });
                        });
                    },

                    fillSubprojectOptions(subs) {
                        const el = document.querySelector('#subproject');
                        if (!el) return;

                        el.innerHTML =
                            '<option value=""></option>' +
                            subs.map(sp => `<option value="${this.escapeHtml(sp)}">${this.escapeHtml(sp)}</option>`).join('');
                    },

                    // ✅ seteo nativo y dispara change (sin HSSelect.setValue)
                    setNative(selector, value) {
                        const el = document.querySelector(selector);
                        if (!el) return;
                        el.value = value ?? '';
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    },

                    // ✅ destruye de forma segura sin depender de getInstance(selector)
                    destroyHS(selector) {
                        if (!window.HSSelect) return;
                        const el = document.querySelector(selector);
                        if (!el) return;

                        const inst = window.HSSelect.getInstance(el);
                        if (inst && typeof inst.destroy === 'function') inst.destroy();

                        // limpia el holder del toggle (si aplica)
                        const toggleHolder = document.querySelector('#subproject_toggle');
                        if (toggleHolder) toggleHolder.innerHTML = '';
                    },

                    // ✅ reconstruye SOLO el subproject (no autoInit global)
                    rebuildHS(selector) {
                        if (!window.HSSelect) return;

                        const el = document.querySelector(selector);
                        if (!el) return;

                        // destruye lo anterior
                        const inst = window.HSSelect.getInstance(el);
                        if (inst && typeof inst.destroy === 'function') inst.destroy();

                        const toggleHolder = document.querySelector('#subproject_toggle');
                        if (toggleHolder) toggleHolder.innerHTML = '';

                        // crea instancia SOLO para este select
                        new window.HSSelect(el);
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
        </script>


    @endpush


</x-layouts.appEmail>
