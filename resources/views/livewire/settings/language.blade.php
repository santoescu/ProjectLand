<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Language')"
        :subheading=" __('Choose your preferred language for the application.')">

        <form wire:submit.prevent="updateLocale">

            <div class="inline-flex items-center rounded-lg bg-neutral-800/40 p-1 w-full max-w-xl">
                <!-- English -->
                <div class="relative flex-1">
                    <input
                        id="locale-en"
                        type="radio"
                        name="locale"
                        value="en"
                        wire:model="locale"
                        class="peer sr-only"
                    />

                    <label
                        for="locale-en"
                        class="flex items-center justify-center gap-2 w-full px-5 py-2.5
                        text-sm font-medium rounded-md cursor-pointer select-none transition
                        text-neutral-300 hover:text-white peer-checked:bg-neutral-700/70 peer-checked:text-white">
                        <flux:icon.language />

                        {{ __('English') }}
                    </label>
                </div>

                <!-- Spanish -->
                <div class="relative flex-1">
                    <input
                        id="locale-es"
                        type="radio"
                        name="locale"
                        value="es"
                        wire:model="locale"
                        class="peer sr-only"
                    />

                    <label
                        for="locale-es"
                        class="flex items-center justify-center gap-2 w-full px-5 py-2.5
                        text-sm font-medium rounded-md cursor-pointer select-none transition
                        text-neutral-300 hover:text-white peer-checked:bg-neutral-700/70 peer-checked:text-white">
                        <flux:icon.language />

                        {{ __('Spanish') }}
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Save') }}
                </flux:button>

                <x-action-message class="me-3" on="update-locale">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

        </form>


    </x-settings.layout>
</section>
