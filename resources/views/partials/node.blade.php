<li>
    <details class="group">
        <summary class="cursor-pointer flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700">
            <span class="text-gray-900 dark:text-gray-100">{{ $account->name }}</span>
        </summary>

        @if($account->children()->count())
            <ul class="ms-4 mt-2 space-y-2 border-l border-gray-300 dark:border-neutral-600 ps-4">
                @foreach($account->children as $child)
                    @include('partials.node', ['account' => $child])
                @endforeach
            </ul>
        @endif
    </details>
</li>
