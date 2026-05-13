<div id="contract-detail-modal"
     class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none"
     role="dialog"
     tabindex="-1"
     aria-labelledby="contract-detail-modal-label"
     wire:ignore>
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all max-w-[95rem] w-[calc(100%-1.5rem)] m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 id="contract-detail-modal-label" class="font-bold text-gray-800 dark:text-white">
                    {{ __('Contract payment detail') }}
                </h3>

                <button type="button"
                        class="size-8 inline-flex justify-center items-center rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400"
                        aria-label="Close"
                        data-hs-overlay="#contract-detail-modal">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div id="contract-detail-table-container" class="p-4 overflow-x-auto">
                <p class="text-sm text-gray-500 dark:text-neutral-400">{{ __('Select a contract') }}</p>
            </div>

            <div class="flex justify-end items-center gap-x-3 py-4 px-5 border-t border-gray-200 dark:border-neutral-700">
                <button type="button"
                        data-hs-overlay="#contract-detail-modal"
                        class="inline-flex items-center justify-center gap-x-2 min-w-[110px] py-2.5 px-4 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-700">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.contractDetailTableUrlTemplate = @js(route('contracts.paymentDetailTable', ['id' => '__CONTRACT_ID__']));

    function setContractDetailButtonEnabled(contractId) {
        const button = document.getElementById('viewContractDetailButton');
        if (!button) return;

        button.disabled = !contractId;
        button.classList.toggle('opacity-50', !contractId);
        button.classList.toggle('pointer-events-none', !contractId);
    }

    async function openSelectedContractDetailModal() {
        const contractId = document.getElementById('contract_id')?.value;
        if (!contractId) return;

        const container = document.getElementById('contract-detail-table-container');
        if (!container) return;

        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-neutral-400">{{ __('Loading') }}...</p>';

        if (window.HSOverlay) {
            HSOverlay.autoInit();
            HSOverlay.open('#contract-detail-modal');
        }

        try {
            const url = window.contractDetailTableUrlTemplate.replace('__CONTRACT_ID__', encodeURIComponent(contractId));
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Unable to load contract detail.');
            }

            container.innerHTML = await response.text();
        } catch (error) {
            container.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400">{{ __('Unable to load the contract detail') }}</p>';
        }
    }
</script>
