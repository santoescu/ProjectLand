@php
    $userRole = Auth::user()->role;
    $userId = Auth::id();
    $user = auth()->user();

    $groups = [
        [
            'name' => __('Inventory'),
            'icon' => 'archive-box',
            'url' => route('inventories.index'),
            'current' => request()->routeIs('inventories.*'),
        ],
    ];

    if (in_array($userRole, ['admin'], true)) {
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

        <flux:header container class="border-b border-gray-200 bg-white dark:border-neutral-700 dark:bg-neutral-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                @foreach ($groups as $link)
                    <flux:navbar.item
                        :icon="$link['icon']"
                        :href="$link['url']"
                        :current="$link['current']"
                        wire:navigate
                    >
                        {{ $link['name'] }}
                    </flux:navbar.item>
                @endforeach
            </flux:navbar>

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <button
                    type="button"
                    class="inline-flex max-w-56 items-center gap-2 rounded-md p-2 text-start text-sm text-gray-800 hover:bg-gray-100 focus:bg-gray-100 focus:outline-hidden dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                >
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-200 font-semibold text-gray-800 dark:bg-neutral-700 dark:text-neutral-100">
                        {{ $user->initials() ?? strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </span>

                    <span class="hidden min-w-0 sm:block">
                        <span class="block truncate font-medium">{{ $user->name }}</span>
                        <span class="block truncate text-xs text-gray-500 dark:text-neutral-400">{{ $user->email }}</span>
                    </span>
                </button>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal text-gray-800 dark:text-neutral-100">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ $user->initials() ?? strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold text-gray-800 dark:text-neutral-100">{{ $user->name }}</span>
                                    <span class="truncate text-xs text-gray-500 dark:text-neutral-400">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')">
                    @foreach ($groups as $link)
                        <flux:navlist.item
                            :icon="$link['icon']"
                            :href="$link['url']"
                            :current="$link['current']"
                            wire:navigate
                        >
                            {{ $link['name'] }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
