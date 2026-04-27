<x-layouts.app :title="__('Project')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('Project')]) ,
    'subheading' => ""
    ])

    <form method="POST" action="{{ route('projects.store') }}" class="space-y-4 max-w-xl mx-auto">
        @csrf

        <flux:input label="{{__('Name')}}" name="name" :value="old('name')" />

        <label   class="block text-base">
            {{ __('Subprojects') }}
        </label>
        <!-- Input Group -->
        <div id="hs-destroy-and-reinitialize-wrapper-for-copy" class="space-y-3">
            <div id="hs-destroy-and-reinitialize-content-for-copy">
                <div class="relative">

                    <flux:input placeholder="{{__('Subproject')}}" name="subprojects[]">
                        <x-slot name="iconTrailing">
                            <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" data-hs-copy-markup-delete-item="" />
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





        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('projects.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>

    <script>
        window.addEventListener('load', () => {
            (function() {
                const copyMarkup = document.querySelector('#hs-copy-markup-to-destroy');
                const destroy = document.querySelector('#hs-destroy-copy-markup');
                const autoInit = document.querySelector('#hs-auto-init-copy-markup');

                destroy.addEventListener('click', () => {
                    const {element} = HSCopyMarkup.getInstance(copyMarkup, true);

                    element.destroy();

                    destroy.setAttribute('disabled', 'disabled');
                    autoInit.removeAttribute('disabled');
                });

                autoInit.addEventListener('click', () => {
                    HSCopyMarkup.autoInit();

                    autoInit.setAttribute('disabled', 'disabled');
                    destroy.removeAttribute('disabled');
                });
            })();
        });
    </script>
</x-layouts.app>
