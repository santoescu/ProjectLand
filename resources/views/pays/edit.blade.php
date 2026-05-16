<x-layouts.app :title="__('Payable')">

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

        .contract-field{
            display:flex;
            flex-direction:column;
            gap:.5rem;
        }

        .contract-field .contract-label{ order: 1; }

        .contract-field .hs-select{ order: 2; }

        .contract-field .hs-dropdown{ order: 3; }

        .contract-field .contract-select{ order: 4; }

        .contract-field .contract-detail-button{ order: 5; }
    </style>

    <form method="POST" action="{{ route('pays.update', $pay->_id) }}" class="space-y-4 max-w-xl mx-auto">
        @csrf
        @method('PUT')
        @php
            // Mapa liviano para el front (id, name, subprojects)
            $lockedProjectId = (string) ($effectiveProjectId ?? '');
            $selectedProjectId = $lockedProjectId ?: old('project_id', (string) $pay->project_id);
            $projectsForFront = $projects->map(function ($p) {
                return [
                    'id' => (string) $p->_id,
                    'name' => $p->name,
                    'subprojects' => is_array($p->subprojects ?? null) ? $p->subprojects : [],
                ];
            })->values();

            $existingAllocations = $pay->payment_allocations ?? [];
            if (empty($existingAllocations) && filled($pay->chartAccount_id)) {
                $existingAllocations = [[
                    'chartAccount_id' => (string) $pay->chartAccount_id,
                    'amount' => $pay->amount,
                ]];
            }
            $oldAllocations = old('payment_allocations', $existingAllocations);
        @endphp

        <div
            x-data="subprojectSelect({
                projects: @js($projectsForFront),
                initialProjectId: @js($selectedProjectId),
                initialSubproject: @js(old('subproject', (string) ($pay->subproject ?? '')))
              })"
            x-init="init()">


        <div data-flux-field class="relative {{ $errors->has('project_id') ? 'error' : '' }}" wire:ignore>
                <label for="project_id" class="block text-base text-gray-700 dark:text-neutral-200">
                    {{ __('Project') }}
                </label>

                @if($lockedProjectId)
                    <input type="hidden" name="project_id" value="{{ $lockedProjectId }}">
                @endif
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
                    name="{{ $lockedProjectId ? '' : 'project_id' }}"
                    class="hidden"
                    @change="onProjectChange($event.target.value)"
                    {{ $lockedProjectId ? 'disabled' : '' }}>
                    <option value=""></option>
                    @foreach($projects as $project)
                        <option value="{{ $project->_id }}"
                            {{ (string) $selectedProjectId === (string) $project->_id ? 'selected' : '' }}>
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
                      "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
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
        <div x-data="contractSelect({
                contracts: @js($contractsForFront),
                initialContractorId: @js(old('contractor_id', (string) $pay->contractor_id)),
                initialContractId: @js(old('contract_id', (string) ($pay->contract_id ?? '')))
              })"
             x-init="init()">
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
                      }' id="contractor_id" name="contractor_id" @change="onContractorChange($event.target.value)">
                    <option value=""></option>
                    @foreach($contractors as $contractor)
                        <option value="{{ $contractor->_id }}" {{ old('contractor_id', $pay->contractor_id) == $contractor->_id ? 'selected' : '' }}>
                            {{ $contractor->company_name }} - {{ $contractor->contact_name }}
                        </option>
                    @endforeach

                </select>
                @error('contractor_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div x-show="showContract" x-cloak data-flux-field class="mt-4 relative {{ $errors->has('contract_id') ? 'error' : '' }} contract-field" wire:ignore>
                <label for="contract_id" class="block text-base text-gray-700 dark:text-neutral-200 mb-2 contract-label">
                    {{ __('Contract') }}
                </label>
                <div id="contract_toggle" class="w-full contract-toggle"></div>
                <select
                    id="contract_id"
                    name="contract_id"
                    class="hidden"
                    data-hs-select='{
                  "hasSearch": true,
                  "optionAllowEmptyOption": true,
                  "minSearchLength": 3,
                  "searchPlaceholder": "{{__('Search')}}",
                  "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-1.5 sm:py-2 px-3",
                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 dark:bg-neutral-700",
                  "placeholder": "{{__('Select')}}",
                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\" data-hs-select-toggle=\"#contract_toggle\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800 dark:text-neutral-200 \" data-title></span></button>",
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
                @error('contract_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <button type="button"
                        id="viewContractDetailButton"
                        class="contract-detail-button mt-2 inline-flex items-center justify-center gap-x-2 self-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700"
                        onclick="openSelectedContractDetailModal()"
                        disabled>
                    {{ __('View contract') }}
                </button>
            </div>
        </div>
        <div id="contractBudgetAllocations" class="hidden space-y-3">
            <label class="block text-base text-gray-700 dark:text-neutral-200">
                {{ __('Budget Codes') }}
            </label>
            <div id="contractBudgetRows" class="space-y-3"></div>
            @error('payment_allocations')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div id="legacyChartAccountField" data-flux-field class="relative {{ $errors->has('chartAccount_id') ? 'error' : '' }}">
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
                      }' id="chartAccount_id" name="chartAccount_id">
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

        <div data-flux-field>
            <label for="amount" class="block text-base text-gray-700 dark:text-neutral-200">{{ __('Amount') }}</label>
            <div class="relative">
                <input type="text" id="amount" name="amount" class="w-full rounded-lg border border-gray-200 bg-white py-3 ps-9 pe-3 text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" value="{{ old('amount', $pay->amount) }}" placeholder="0.00">
                <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                    <span class="text-gray-500 dark:text-neutral-400">$</span>
                </div>
            </div>
        </div>
        <flux:input label="{{__('Notes')}}" id="description" name="description" :value="old('description', $pay->description)"  />
        <flux:input label="{{__('Attachment Link')}}" id="attachment_link" name="attachment_link" :value="old('attachment_link', $pay->attachment_link)" />
        @error('attachment_link')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror





        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('pays.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
    @include('pays.partials.contract-detail-modal')
    <script>
        window.payContractsForFront = @js($contractsForFront);
        window.oldPaymentAllocations = @js($oldAllocations);

        document.addEventListener('alpine:init', () => {
            Alpine.data('contractSelect', ({ contracts, initialContractorId, initialContractId }) => ({
                contracts,
                showContract: false,
                currentContractorId: '',

                init() {
                    document.getElementById('project_id')?.addEventListener('change', () => {
                        this.onContractorChange(this.currentContractorId);
                    });
                    document.getElementById('subproject')?.addEventListener('change', () => {
                        this.onContractorChange(this.currentContractorId);
                    });

                    if (initialContractorId) {
                        this.onContractorChange(initialContractorId, initialContractId);
                    }
                },

                onContractorChange(contractorId, contractToSelect = null) {
                    this.currentContractorId = contractorId;
                    const filteredContracts = this.filteredContracts(contractorId);
                    this.showContract = filteredContracts.length > 0;

                    this.$nextTick(() => {
                        if (!this.showContract) {
                            this.clearContract();
                            return;
                        }

                        this.fillContractOptions(filteredContracts);
                        this.reInitHSSelect('#contract_id');
                        this.setHSSelectValue('#contract_id', contractToSelect ?? '');
                    });
                },

                filteredContracts(contractorId) {
                    const projectId = document.getElementById('project_id')?.value ?? '';
                    const subproject = document.getElementById('subproject')?.value ?? '';

                    return this.contracts.filter(contract =>
                        contract.contractor_id === contractorId &&
                        (!projectId || contract.project_id === projectId) &&
                        (!subproject || contract.subproject === subproject)
                    );
                },

                fillContractOptions(contracts) {
                    const el = document.querySelector('#contract_id');
                    if (!el) return;

                    el.innerHTML = '<option value=""></option>' + contracts
                        .map(contract => `<option value="${this.escapeHtml(contract.id)}">${this.escapeHtml(contract.name)}</option>`)
                        .join('');
                },

                clearContract() {
                    const el = document.querySelector('#contract_id');
                    if (el) el.value = '';
                    this.setHSSelectValue('#contract_id', '');
                    renderContractBudgetAllocations('');
                },

                reInitHSSelect(selector) {
                    if (!window.HSSelect) return;

                    const el = document.querySelector(selector);
                    if (!el) return;

                    const inst = window.HSSelect.getInstance(el);
                    if (inst && typeof inst.destroy === 'function') inst.destroy();

                    const toggleHolder = document.querySelector('#contract_toggle');
                    if (toggleHolder) toggleHolder.innerHTML = '';

                    new window.HSSelect(el);
                },

                setHSSelectValue(selector, value) {
                    const el = document.querySelector(selector);
                    if (!el) return;

                    if (!window.HSSelect) {
                        el.value = value;
                        return;
                    }

                    const inst = window.HSSelect.getInstance(el);
                    if (inst && typeof inst.setValue === 'function') {
                        inst.setValue(value);
                    } else {
                        el.value = value;
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    renderContractBudgetAllocations(value);
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

            document.getElementById('contract_id')?.addEventListener('change', (event) => {
                renderContractBudgetAllocations(event.target.value);
            });

            const input = document.getElementById('amount');

            input.addEventListener('input', function() {
                this.value = sanitizePayMoneyInput(this.value);
            });
            input.closest('form').addEventListener('submit', function() {
                input.value = normalizePayMoneyInput(input.value);
                document.querySelectorAll('.allocation-amount').forEach((allocationInput) => {
                    allocationInput.value = normalizePayMoneyInput(allocationInput.value);
                });
            });
        });

    </script>
    <script>
        document.addEventListener('load.hs.select', function() {
            const select = document.getElementById('project_id');
            if (select && !select.value) {
                select.value = "{{ $selectedProjectId }}";
            }
        });

        function renderContractBudgetAllocations(contractId) {
            setContractDetailButtonEnabled(contractId);

            const section = document.getElementById('contractBudgetAllocations');
            const rows = document.getElementById('contractBudgetRows');
            const legacy = document.getElementById('legacyChartAccountField');
            const chartSelect = document.getElementById('chartAccount_id');
            const amountInput = document.getElementById('amount');
            const contract = window.payContractsForFront.find(item => item.id === contractId);

            rows.innerHTML = '';

            if (!contract || !Array.isArray(contract.budgets) || contract.budgets.length === 0) {
                section.classList.add('hidden');
                legacy.classList.remove('hidden');
                legacy.style.display = '';
                chartSelect.disabled = false;
                amountInput.readOnly = false;
                return;
            }

            section.classList.remove('hidden');
            legacy.classList.add('hidden');
            legacy.style.display = 'none';
            chartSelect.disabled = true;
            chartSelect.value = '';
            if (window.HSSelect) {
                window.HSSelect.getInstance('#chartAccount_id')?.setValue('');
            }
            amountInput.readOnly = true;

            const oldByBudgetKey = Object.fromEntries((window.oldPaymentAllocations || []).filter(item => item.budget_key).map(item => [item.budget_key, item.amount]));
            const oldByAccount = Object.fromEntries((window.oldPaymentAllocations || []).map(item => [item.chartAccount_id, item.amount]));
            const budgetCountByAccount = contract.budgets.reduce((counts, budget) => {
                counts[budget.chartAccount_id] = (counts[budget.chartAccount_id] || 0) + 1;
                return counts;
            }, {});

            contract.budgets.forEach((budget, index) => {
                const value = oldByBudgetKey[budget.budget_key] ?? (budgetCountByAccount[budget.chartAccount_id] === 1 ? (oldByAccount[budget.chartAccount_id] ?? '') : '');
                rows.insertAdjacentHTML('beforeend', `
                    <div class="contract-budget-row grid grid-cols-1 items-start sm:grid-cols-[46px_minmax(0,1fr)_240px]">
                        <label for="payment-allocation-${index}" class="flex h-[46px] items-center justify-center rounded-s-lg border border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <input id="payment-allocation-${index}" type="checkbox" class="budget-check shrink-0 size-4 bg-transparent border-gray-300 rounded-sm shadow-2xs text-blue-600 focus:ring-0 focus:ring-offset-0 checked:bg-blue-600 checked:border-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-600" aria-describedby="payment-allocation-${index}-description" ${value ? 'checked' : ''}>
                        </label>
                        <div>
                            <input type="text" class="h-[46px] w-full rounded-none border border-t-0 border-gray-200 bg-white px-4 text-sm font-semibold text-gray-800 disabled:pointer-events-none disabled:bg-white disabled:text-gray-800 sm:border-s-0 sm:border-t dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-100 dark:disabled:bg-neutral-700 dark:disabled:text-neutral-100" value="${escapePayHtml(budget.name)}" disabled>
                            <span id="payment-allocation-${index}-description" class="budget-remaining mt-1 block text-sm text-gray-500 dark:text-neutral-400" data-remaining="${budget.remaining}">
                                {{ __('Available budget') }}: ${formatMoney(budget.remaining)}
                            </span>
                        </div>
                        <div class="relative h-[46px]">
                            <input type="text" inputmode="decimal" class="allocation-amount h-[46px] w-full rounded-b-lg border border-t-0 border-gray-200 bg-white ps-9 pe-3 text-sm text-gray-800 sm:rounded-s-none sm:rounded-e-lg sm:border-s-0 sm:border-t dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-200" name="payment_allocations[${index}][amount]" value="${value}" placeholder="0.00">
                            <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-3">
                                <span class="text-gray-500 dark:text-neutral-400">$</span>
                            </div>
                        </div>
                        <input type="hidden" name="payment_allocations[${index}][chartAccount_id]" value="${escapePayHtml(budget.chartAccount_id)}">
                        <input type="hidden" name="payment_allocations[${index}][budget_key]" value="${escapePayHtml(budget.budget_key)}">
                        <input type="hidden" name="payment_allocations[${index}][concept]" value="${escapePayHtml(budget.concept ?? '')}">
                    </div>
                `);
            });

            rows.querySelectorAll('.budget-check, .allocation-amount').forEach(input => {
                input.addEventListener('input', updatePaymentAllocationTotal);
                input.addEventListener('change', updatePaymentAllocationTotal);
            });

            updatePaymentAllocationTotal();
        }

        function updatePaymentAllocationTotal() {
            let total = 0;

            document.querySelectorAll('#contractBudgetRows .contract-budget-row').forEach(row => {
                const checked = row.querySelector('.budget-check').checked;
                const input = row.querySelector('.allocation-amount');
                const remainingEl = row.querySelector('.budget-remaining');
                const remaining = parseFloat(remainingEl.dataset.remaining || '0');
                input.value = sanitizePayMoneyInput(input.value);
                const value = checked ? parsePayMoneyInput(input.value) : 0;

                input.disabled = !checked;
                if (!checked) input.value = '';

                total += value;

                if (value > remaining) {
                    remainingEl.classList.remove('text-gray-500', 'dark:text-neutral-400');
                    remainingEl.classList.add('text-red-600', 'dark:text-red-400');
                    remainingEl.textContent = `{{ __('Available budget') }}: ${formatMoney(remaining)} - {{ __('Exceeded') }}`;
                } else {
                    remainingEl.classList.remove('text-red-600', 'dark:text-red-400');
                    remainingEl.classList.add('text-gray-500', 'dark:text-neutral-400');
                    remainingEl.textContent = `{{ __('Available budget') }}: ${formatMoney(remaining)}`;
                }
            });

            document.getElementById('amount').value = total.toFixed(2);
        }

        function formatMoney(value) {
            return '$' + Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function parsePayMoneyInput(value) {
            return Number(normalizePayMoneyInput(value)) || 0;
        }

        function sanitizePayMoneyInput(value) {
            value = String(value || '').replace(/[^0-9,.]/g, '');
            const separatorIndex = value.search(/[,.]/);

            if (separatorIndex === -1) {
                return value;
            }

            const integerPart = value.slice(0, separatorIndex).replace(/[,.]/g, '');
            const separator = value[separatorIndex];
            const decimalPart = value.slice(separatorIndex + 1).replace(/[,.]/g, '').substring(0, 2);

            return `${integerPart}${separator}${decimalPart}`;
        }

        function normalizePayMoneyInput(value) {
            value = sanitizePayMoneyInput(value);
            const lastComma = value.lastIndexOf(',');
            const lastDot = value.lastIndexOf('.');

            if (lastComma !== -1 && lastDot !== -1) {
                const decimalSeparator = lastComma > lastDot ? ',' : '.';
                const thousandsSeparator = decimalSeparator === ',' ? '.' : ',';
                return value.replaceAll(thousandsSeparator, '').replace(decimalSeparator, '.');
            }

            if (lastComma !== -1) {
                return normalizePaySingleSeparatorMoney(value, ',');
            }

            if (lastDot !== -1) {
                return normalizePaySingleSeparatorMoney(value, '.');
            }

            return value;
        }

        function normalizePaySingleSeparatorMoney(value, separator) {
            const parts = value.split(separator);

            if (parts.length > 2) {
                return value.replaceAll(separator, '');
            }

            const [integerPart, fractionPart] = parts;
            return `${integerPart}.${fractionPart.substring(0, 2)}`;
        }

        function escapePayHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
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
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('subprojectSelect', ({ projects, initialProjectId, initialSubproject }) => ({
                    projects,
                    showSubproject: false,

                    init() {
                        // En edit: siempre precargar el proyecto guardado (o el old si venía de validación)
                        if (initialProjectId) {
                            this.onProjectChange(initialProjectId, initialSubproject);
                            // además setea el project visualmente
                            this.$nextTick(() => this.setHSSelectValue('#project_id', initialProjectId));
                        }
                    },

                    onProjectChange(projectId, subprojectToSelect = null) {
                        const p = this.projects.find(x => x.id === projectId);
                        const subs = (p && Array.isArray(p.subprojects)) ? p.subprojects : [];

                        this.showSubproject = subs.length > 0;

                        this.$nextTick(() => {
                            if (!this.showSubproject) {
                                this.clearSubproject();
                                return;
                            }

                            this.fillSubprojectOptions(subs);
                            this.reInitHSSelect('#subproject');

                            // Si hay un subproject guardado, selecciónalo
                            this.setHSSelectValue('#subproject', subprojectToSelect ?? '');
                        });
                    },

                    fillSubprojectOptions(subs) {
                        const el = document.querySelector('#subproject');
                        if (!el) return;

                        el.innerHTML =
                            '<option value=""></option>' +
                            subs.map(sp => `<option value="${this.escapeHtml(sp)}">${this.escapeHtml(sp)}</option>`).join('');
                    },

                    clearSubproject() {
                        const el = document.querySelector('#subproject');
                        if (el) el.value = '';
                        this.setHSSelectValue('#subproject', '');
                    },

                    reInitHSSelect(selector) {
                        if (!window.HSSelect) return;

                        const el = document.querySelector(selector);
                        if (!el) return;

                        const inst = window.HSSelect.getInstance(el);
                        if (inst && typeof inst.destroy === 'function') inst.destroy();

                        // Limpia toggle viejo si quedó algo (por seguridad)
                        const toggleHolder = document.querySelector('#subproject_toggle');
                        if (toggleHolder) toggleHolder.innerHTML = '';

                        new window.HSSelect(el);
                    },

                    setHSSelectValue(selector, value) {
                        const el = document.querySelector(selector);
                        if (!el) return;

                        if (!window.HSSelect) {
                            el.value = value;
                            return;
                        }

                        const inst = window.HSSelect.getInstance(el);
                        if (inst && typeof inst.setValue === 'function') {
                            inst.setValue(value);
                        } else {
                            el.value = value;
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
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


</x-layouts.app>
