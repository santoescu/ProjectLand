<x-layouts.app :title="__('Chart of Account')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('Chart of Account')]) ,
    'subheading' => ""
    ])

    <form method="POST" action="{{ route('chartAccounts.store') }}" class="space-y-4 max-w-xl mx-auto">
        @csrf

        <flux:input label="{{__('Name')}}" name="name" :value="old('name')" />
        <flux:input label="{{__('Type')}}" name="type" :value="old('type')" />



        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('chartAccounts.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
</x-layouts.app>
