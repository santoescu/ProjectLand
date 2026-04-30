<x-layouts.app :title="__('Inventory')">
    @include('partials.tittle', [
        'title' => __("Create :name", ['name' => __('Equipment')]),
        'subheading' => __('Register equipment location, downtime, maintenance and invoices.')
    ])

    <form id="create-inventory-form" method="POST" action="{{ route('inventories.store') }}" class="mx-auto max-w-5xl space-y-6">
        @csrf

        @include('inventories._form')

        <div class="flex justify-end gap-2">
            <a href="{{ route('inventories.index') }}">
                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
            </a>
            <flux:button type="button" variant="primary" onclick="openCreateInventoryModal()">{{ __('Save') }}</flux:button>
        </div>
    </form>

    <div id="confirm-create-inventory-modal"
         class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
         role="dialog"
         tabindex="-1"
         aria-labelledby="confirm-create-inventory-label"
         wire:ignore>

        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <h3 id="confirm-create-inventory-label" class="font-bold text-gray-800 dark:text-white">
                        {{ __("Create :name?", ['name' => __('Equipment')]) }}
                    </h3>

                    <button type="button"
                            class="size-8 inline-flex justify-center items-center rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400"
                            aria-label="Close"
                            data-hs-overlay="#confirm-create-inventory-modal">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-8 sm:px-8 sm:py-10 text-center">
                    <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <flux:icon.archive-box />
                    </div>

                    <p class="text-base sm:text-lg font-medium text-gray-800 dark:text-white">
                        {{ __("Do you want to confirm the creation of this :name?", ['name' => __('equipment')]) }}
                    </p>

                    <p class="mt-2 text-sm sm:text-base text-gray-500 dark:text-neutral-300">
                        {{ __('The equipment will be created after confirmation.') }}
                    </p>
                </div>

                <div class="flex justify-end items-center gap-x-3 py-4 px-5 border-t border-gray-200 dark:border-neutral-700">
                    <button type="button"
                            data-hs-overlay="#confirm-create-inventory-modal"
                            class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">
                        {{ __('Close') }}
                    </button>

                    <flux:button
                        type="button"
                        variant="primary"
                        onclick="submitCreateInventoryForm()">
                        {{ __('Create') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openCreateInventoryModal() {
            const form = document.getElementById('create-inventory-form');

            if (!form.reportValidity()) {
                return;
            }

            if (window.HSOverlay) {
                HSOverlay.autoInit();
                HSOverlay.open('#confirm-create-inventory-modal');
            }
        }

        function submitCreateInventoryForm() {
            document.getElementById('create-inventory-form').submit();
        }
    </script>
</x-layouts.app>
