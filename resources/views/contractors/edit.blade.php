<flux:modal name="edit-contractor" variant="flyout">
    <form wire:submit.prevent="update" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __("Edit :name", ['name' => $contractor->company_name]) }}</flux:heading>
            <flux:text class="mt-2">Actualiza los datos de este contratista.</flux:text>
        </div>

        <flux:input label="Nombre de la compañía" wire:model="company_name" required />
        <flux:input label="Nombre de contacto" wire:model="contact_name" required />
        <flux:input label="Teléfono de contacto" wire:model="contact_phone" required />

        <flux:select label="Método de pago" wire:model="payment_method" required>
            <option value="Zelle">Zelle</option>
            <option value="ACH">ACH</option>
            <option value="Wire">Wire</option>
        </flux:select>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Guardar cambios</flux:button>
        </div>
    </form>
</flux:modal>
