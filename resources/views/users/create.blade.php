<x-layouts.app :title="__('Users')">

    @include('partials.tittle', [
    'title' => __("Create :name", ['name' => __('User')]) ,
    'subheading' => ""
    ])

    <form method="POST" action="{{ route('users.store') }}" class="space-y-4 max-w-xl mx-auto">
        @csrf

        <flux:input label="{{__('Name')}}" name="name" :value="old('name')" />
        <flux:input label="{{__('Email')}}" name="email" :value="old('email')" />
        <flux:select label="{{__('Role')}}" name="role" >
            <option value="accounting_assistant">{{ __('Accounting Assistant') }}</option>
            <option value="project_manager">{{ __('Project Manager') }}</option>
            <option value="director">{{ __('Director') }}</option>
        </flux:select>


        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">{{__('Save')}}</flux:button>

            <a href="{{ route('users.index') }}">
                <flux:button variant="ghost">{{__('Cancel')}}</flux:button>
            </a>
        </div>

    </form>
</x-layouts.app>
