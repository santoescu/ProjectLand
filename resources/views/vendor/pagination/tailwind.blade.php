@if ($paginator->hasPages())
    <nav class="flex justify-between items-center gap-x-1" aria-label="Pagination">

        {{-- Botón Previous --}}
        @if ($paginator->onFirstPage())
            <span class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-400 dark:text-neutral-600 cursor-not-allowed">
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
                <span aria-hidden="true" class="hidden sm:block">{{__('Previous')}}</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-white/10">
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
                <span aria-hidden="true" class="hidden sm:block">Previous</span>
            </a>
        @endif

        <div class="flex items-center gap-x-1">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-gray-500 dark:text-neutral-500">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 py-2 px-3 text-sm rounded-lg dark:bg-neutral-600 dark:text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="min-h-9.5 min-w-9.5 flex justify-center items-center text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm rounded-lg dark:text-white dark:hover:bg-white/10">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Botón Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-white/10">
                <span aria-hidden="true" class="hidden sm:block">{{__('Next')}}</span>
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        @else
            <span class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-2 text-sm rounded-lg text-gray-400 dark:text-neutral-600 cursor-not-allowed">
                <span aria-hidden="true" class="hidden sm:block">Next</span>
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </span>
        @endif

    </nav>
@endif
