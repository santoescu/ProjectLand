<x-layouts.app :title="__('Contractor')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('Contractor')]) ,
    'subheading' => ""
    ])

    <form method="POST" action="{{ route('contractors.store') }}" class="space-y-4 max-w-xl mx-auto">
        @csrf

        <flux:input label="{{__('Company')}}" name="company_name" :value="old('company_name')" />

        <flux:input label="{{__('Contact')}}" name="contact_name" :value="old('contact_name')" />

        <flux:input label="{{__('Phone')}}" name="contact_phone" :value="old('contact_phone')" />

        <flux:select label="{{__('Payment method')}}" name="payment_method" >
            <option value="Zelle">Zelle</option>
            <option value="ACH">ACH</option>
            <option value="Wire">Wire</option>
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('contractors.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
</x-layouts.app>
