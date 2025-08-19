<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Language')"
        :subheading=" __('Choose your preferred language for the application.')">

        <form wire:submit.prevent="updateLocale">
            <flux:radio.group wire:model="locale" variant="segmented">
                <flux:radio value="en" icon="language">{{ __('English') }}</flux:radio>
                <flux:radio value="es" icon="language">{{ __('Spanish') }}</flux:radio>
            </flux:radio.group>

            <div class="flex items-center gap-4 mt-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="update-locale">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

        </form>
    </x-settings.layout>
</section>
