<x-layouts.app :title="__('Chart of Account')">
    @include('partials.tittle', [
        'title' => __('Charts of Accounts'),
        'subheading' => __('Management of registered :name',['name'=> __('charts of accounts')]),
        'button' => [
        'label' => __('Table'),
        'route' => route('chartAccounts.index')
    ]
    ])
    <main class="flex-1 p-6 overflow-y-auto">
            <ul class="space-y-2">
                @foreach($rootAccounts as $account)
                    @include('partials.node', ['account' => $account])
                @endforeach
            </ul>
    </main>
</x-layouts.app>

