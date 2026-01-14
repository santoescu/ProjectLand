@php
    $userRole = Auth::user()->role;
    $user = auth()->user();

    $groups = [
        [
            'name' => __('Payables'),
            'icon' => 'currency-dollar',
            'url' => route('pays.index'),
            'current' => request()->routeIs('pays.index'),
        ],
    ];

    if (in_array($userRole, ['accounting_assistant', 'director', 'admin'])) {
        $groups[] = [
            'name' => __('Vendors'),
            'icon' => 'clipboard-document-list',
            'url' => route('contractors.index'),
            'current' => request()->routeIs('contractors.index'),
        ];

        $groups[] = [
            'name' => __('Projects'),
            'icon' => 'home-modern',
            'url' => route('projects.index'),
            'current' => request()->routeIs('projects.index'),
        ];

        $groups[] = [
            'name' => __('Budget Codes'),
            'icon' => 'presentation-chart-line',
            'url' => route('chartAccounts.index'),
            'current' => request()->routeIs('chartAccounts.index'),
        ];
    }

    if ($userRole === 'admin') {
        $groups[] = [
            'name' => __('Users'),
            'icon' => 'user',
            'url' => route('users.index'),
            'current' => request()->routeIs('users.index'),
        ];
    }

    $appearance = session('appearance', 'light');
    $isDark = $appearance === 'dark';

@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $isDark ? 'dark' : '' }}">
<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-neutral-800">
@include('components.toast')

<!-- Navigation Toggle (Mobile) -->
<div class="lg:hidden p-3">
    <button
        type="button"
        class="py-2 px-3 inline-flex justify-center items-center gap-x-2 text-start bg-gray-800 border border-gray-800 text-white text-sm font-medium rounded-lg shadow-2xs align-middle hover:bg-gray-950 focus:outline-hidden focus:bg-gray-900 dark:bg-white dark:text-neutral-800 dark:hover:bg-neutral-200 dark:focus:bg-neutral-200"
        aria-haspopup="dialog"
        aria-expanded="false"
        aria-controls="hs-sidebar-content-push-to-mini-sidebar"
        aria-label="Toggle navigation"
        data-hs-overlay="#hs-sidebar-content-push-to-mini-sidebar"
    >
        {{ __('Open') }}
    </button>
</div>
<!-- End Navigation Toggle -->

<!-- Sidebar -->
<div
    id="hs-sidebar-content-push-to-mini-sidebar"
    class="hs-overlay [--auto-close:lg] hs-overlay-minified:w-13 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-64
               hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform
               h-full hidden overflow-x-hidden fixed top-0 start-0 bottom-0 z-60
               bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700"
    role="dialog"
    tabindex="-1"
    aria-label="Sidebar"
>
    <div class="relative flex flex-col h-full max-h-full">
        <!-- Header -->
        <header class="py-4 px-2 flex justify-between items-center gap-x-2">
            <a
                class="flex items-center gap-x-2 flex-none font-semibold text-xl text-black focus:outline-hidden focus:opacity-80 dark:text-white hs-overlay-minified:hidden"
                href="{{ route('dashboard') }}"
                aria-label="Brand"
            >
                <x-app-logo />
            </a>

            <div class="lg:hidden">
                <!-- Close Button -->
                <button
                    type="button"
                    class="flex justify-center items-center gap-x-3 size-6 bg-white border border-gray-200 text-sm text-gray-600 hover:bg-gray-100 rounded-full disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                    data-hs-overlay="#hs-sidebar-content-push-to-mini-sidebar"
                >
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
                <!-- End Close Button -->
            </div>

            <div class="hidden lg:block">
                <!-- Minify Toggle Button -->
                <button
                    type="button"
                    class="flex justify-center items-center flex-none gap-x-3 size-9 text-sm text-gray-600 hover:bg-gray-100 rounded-full disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                    aria-haspopup="dialog"
                    aria-expanded="false"
                    aria-controls="hs-sidebar-content-push-to-mini-sidebar"
                    aria-label="Minify navigation"
                    data-hs-overlay-minifier="#hs-sidebar-content-push-to-mini-sidebar"
                >
                    <svg class="hidden hs-overlay-minified:block shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/><path d="m8 9 3 3-3 3"/>
                    </svg>
                    <svg class="hs-overlay-minified:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M15 3v18"/><path d="m10 15-3-3 3-3"/>
                    </svg>
                    <span class="sr-only">Navigation Toggle</span>
                </button>
                <!-- End Toggle Button -->
            </div>
        </header>
        <!-- End Header -->

        <!-- Body -->
        <nav class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <div class="px-2 w-full flex flex-col">
                <ul class="space-y-1">

                    {{-- Main links --}}
                    @foreach($groups as $link)
                        <li>
                            <a
                                href="{{ $link['url'] }}"
                                wire:navigate
                                @class([
                                    'min-h-[36px] w-full flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden',
                                    'bg-gray-100 text-gray-800 hover:bg-gray-100 focus:bg-gray-100 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700' => $link['current'],
                                    'text-gray-800 hover:bg-gray-100 focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700' => !$link['current'],
                                ])
                            >
                                {{-- Icon --}}
                                @if(!empty($link['icon']))
                                    <x-dynamic-component :component="'heroicon-o-'.$link['icon']" class="size-4 shrink-0" />
                                @endif

                                <span class="hs-overlay-minified:hidden">
                                        {{ $link['name'] }}
                                    </span>
                            </a>
                        </li>
                    @endforeach

                    <li class="my-2 border-t border-gray-200 dark:border-neutral-700"></li>



                </ul>
            </div>
        </nav>
        <!-- End Body -->

        <!-- Footer: Profile + dropdown -->
        <div class="mt-auto p-2 border-t border-gray-200 dark:border-neutral-700">

            <div class="hs-dropdown [--strategy:absolute] [--auto-close:inside] relative w-full inline-flex"  wire:ignore>
                <button id="hs-sidebar-account"
                        type="button"
                        class="w-full inline-flex shrink-0 items-center gap-x-2 p-2 text-start text-sm text-gray-800 rounded-md hover:bg-gray-100
                       focus:outline-hidden focus:bg-gray-100
                       dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                        aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">

                    <div class="shrink-0 size-8 rounded-full bg-gray-200 text-gray-800 flex items-center justify-center font-semibold
                      dark:bg-neutral-700 dark:text-neutral-100">
                        {{ $user->initials() ?? strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1 hs-overlay-minified:hidden">
                        <div class="truncate font-medium">{{ $user->name }}</div>
                        <div class="truncate text-xs text-gray-500 dark:text-neutral-400">{{ $user->email }}</div>
                    </div>

                    <svg class="shrink-0 size-3.5 ms-auto hs-overlay-minified:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 w-60 transition-[opacity,margin] duration opacity-0 hidden z-20
                    bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700"
                     role="menu" aria-orientation="vertical" aria-labelledby="hs-sidebar-account">
                    <div class="p-1">
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100
                      focus:outline-hidden focus:bg-gray-100
                      dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                           href="{{ route('settings.profile') }}">
                            <x-dynamic-component component="heroicon-o-cog-6-tooth" class="size-4 shrink-0" />
                            {{ __('Settings') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 text-start
                             focus:outline-hidden focus:bg-gray-100
                             dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                <x-dynamic-component component="heroicon-o-arrow-right-start-on-rectangle" class="size-4 shrink-0" />
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
                <!-- End Dropdown -->
            </div>

        </div>
        <!-- End Footer -->

    </div>
</div>
<script>
    function initPreline() {
        // Preline core auto init (lo más importante)
        if (window.HSStaticMethods && typeof window.HSStaticMethods.autoInit === 'function') {
            window.HSStaticMethods.autoInit();
        }

        // Fallbacks (por si tu build expone módulos individuales)
        if (window.HSDropdown && typeof window.HSDropdown.autoInit === 'function') window.HSDropdown.autoInit();
        if (window.HSOverlay && typeof window.HSOverlay.autoInit === 'function') window.HSOverlay.autoInit();
        if (window.HSAccordion && typeof window.HSAccordion.autoInit === 'function') window.HSAccordion.autoInit();
    }

    // 1ra carga
    document.addEventListener('DOMContentLoaded', initPreline);

    // Livewire v3: cada vez que navegas con wire:navigate
    document.addEventListener('livewire:navigated', () => {
        initPreline();

        // opcional: cerrar sidebar en mobile al navegar
        try {
            if (window.HSOverlay && typeof window.HSOverlay.close === 'function') {
                window.HSOverlay.close('#hs-sidebar-content-push-to-mini-sidebar');
            }
        } catch (e) {}
    });
</script>

<!-- End Sidebar -->

<!-- Main content wrapper (deja espacio para el sidebar en desktop) -->
<div class="lg:ps-64">
    {{ $slot }}
</div>

@fluxScripts
</body>
</html>
