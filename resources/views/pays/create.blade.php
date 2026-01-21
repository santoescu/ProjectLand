<x-layouts.app :title="__('Payable')">

    @include('partials.tittle', [
    'title' => __("Add :name", ['name' => __('Payable')]) ,
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


    <form method="POST" action="{{ route('pays.store') }}" class="space-y-4 max-w-xl mx-auto">
        @csrf

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

        <div
            x-data="subprojectSelect({
                projects: @js($projectsForFront),
                oldProjectId: @js(old('project_id')),
                oldSubproject: @js(old('subproject'))
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
                        <option value="{{ $project->_id }}" {{ old('project_id') == $project->_id ? 'selected' : '' }}>
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
                    <option value="{{ $contractor->_id }}" {{ old('contractor_id') == $contractor->_id ? 'selected' : '' }}>{{ $contractor->contact_name }}</option>
                @endforeach

            </select>
            @error('contractor_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div data-flux-field class="relative {{ $errors->has('chartAccount_id') ? 'error' : '' }}">
            <label for="chartAccount_id"  class="block text-base  text-gray-700 dark:text-neutral-200">
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
                      }' id="chartAccount_id" name="chartAccount_id" :value="old('chartAccount_id')">
                <option value=""></option>
                @foreach($chartAccounts as $chartAccount)
                    <option value="{{ $chartAccount->_id }}" {{ old('chartAccount_id') == $chartAccount->_id ? 'selected' : '' }}>{{ $chartAccount->name }}</option>
                @endforeach

            </select>
            @error('chartAccount_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <flux:input label="{{__('Amount')}}"  id="amount" name="amount" :value="old('amount')" />
        <flux:input label="{{__('Notes')}}" id="description" name="description" :value="old('description')"  />





        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('pays.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('subprojectSelect', ({ projects, oldProjectId, oldSubproject }) => ({
                projects,
                showSubproject: false,

                init() {
                    if (oldProjectId) {
                        this.onProjectChange(oldProjectId, oldSubproject);
                    }
                },

                onProjectChange(projectId, subprojectToSelect = null) {
                    const p = this.projects.find(x => x.id === projectId);
                    const subs = (p && Array.isArray(p.subprojects)) ? p.subprojects : [];

                    this.showSubproject = subs.length > 0;

                    this.$nextTick(() => {
                        // Si no hay subprojects, limpia y ya
                        if (!this.showSubproject) {
                            this.clearSubproject();
                            return;
                        }

                        // 1) reconstruir options (sin x-for)
                        this.fillSubprojectOptions(subs);

                        // 2) reiniciar HSSelect del subproject
                        this.reInitHSSelect('#subproject');

                        // 3) setear valor si aplica (old o lo que quieras)
                        if (subprojectToSelect) {
                            this.setHSSelectValue('#subproject', subprojectToSelect);
                        } else {
                            this.setHSSelectValue('#subproject', '');
                        }
                    });
                },

                fillSubprojectOptions(subs) {
                    const el = document.querySelector('#subproject');
                    if (!el) return;

                    el.innerHTML = '<option value=""></option>' + subs
                        .map(sp => `<option value="${this.escapeHtml(sp)}">${this.escapeHtml(sp)}</option>`)
                        .join('');
                },

                clearSubproject() {
                    const el = document.querySelector('#subproject');
                    if (el) el.value = '';
                    // si el HSSelect ya estaba creado, también lo resetea visualmente
                    this.setHSSelectValue('#subproject', '');
                },

                reInitHSSelect(selector) {
                    if (!window.HSSelect) return;

                    const el = document.querySelector(selector);
                    if (!el) return;

                    const inst = window.HSSelect.getInstance(el);
                    if (inst && typeof inst.destroy === 'function') inst.destroy();

                    // Limpia el contenedor donde montamos el toggle
                    const toggleHolder = document.querySelector('#subproject_toggle');
                    if (toggleHolder) toggleHolder.innerHTML = '';

                    // Inicializa SOLO este select
                    new window.HSSelect(el);
                },



                setHSSelectValue(selector, value) {
                    if (!window.HSSelect) {
                        const el = document.querySelector(selector);
                        if (el) el.value = value;
                        return;
                    }

                    const el = document.querySelector(selector);
                    if (!el) return;

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



</x-layouts.app>
