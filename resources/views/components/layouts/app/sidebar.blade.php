@php
    $userRole = Auth::user()->role;

    $groups = [
        [
            'name' => __('Pays'),
            'icon' => 'currency-dollar',
            'url' => route('pays.index'),
            'current' => request()->routeIs('pays.index'),
        ],
    ];

    if (in_array($userRole, ['accounting_assistant', 'director'])) {
        $groups[] = [
            'name' => __('Contractors'),
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
            'name' => __('Charts of Accounts'),
            'icon' => 'presentation-chart-line',
            'url' => route('chartAccounts.index'),
            'current' => request()->routeIs('chartAccounts.index'),
        ];
    }

    if ($userRole === 'director') {
        $groups[] = [
            'name' => __('Users'),
            'icon' => 'user',
            'url' => route('users.index'),
            'current' => request()->routeIs('users.index'),
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
    @include('components.toast')
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo/>
            </a>
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"/>
        </flux:sidebar.header>

        <flux:sidebar.nav>

            @foreach($groups as $link)
                <flux:sidebar.item :icon="$link['icon']" :href="$link['url']" :current="$link['current']"
                                   wire:navigate>{{ $link['name'] }}</flux:sidebar.item>
            @endforeach


        </flux:sidebar.nav>
        <flux:sidebar.spacer/>
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth"
                               href="{{route('settings.profile')}}">{{ __('Settings') }}</flux:sidebar.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:sidebar.item as="button" type="submit"
                                   icon="arrow-right-start-on-rectangle">{{ __('Log Out') }}</flux:sidebar.item>
            </form>
        </flux:sidebar.nav>


        <flux:sidebar.profile :initials="auth()->user()->initials()" name="{{ auth()->user()->name }}"/>


    </flux:sidebar>
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" inset="left"/>
        <flux:spacer/>

        <flux:profile :initials="auth()->user()->initials()" name="{{ auth()->user()->name }}"/>

    </flux:header>

    {{ $slot }}
    @fluxScripts
    </body>

</html>
