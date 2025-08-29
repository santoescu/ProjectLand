<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-neutral-900">

<liveware:card class="max-w-md w-full p-8 text-center shadow-2xl rounded-2xl
                     bg-white dark:bg-neutral-800">
    <flux:heading size="4xl" class="font-bold text-red-600 dark:text-red-500">
        403
    </flux:heading>

    <flux:heading size="lg" class="mt-4 text-gray-800 dark:text-gray-100">
        {{ __('Access Denied') }}
    </flux:heading>

    <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
        {{ __('You do not have permission to access this page. If you believe this is an error, please contact the administrator.') }}
    </flux:text>

    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
        <flux:button variant="outline" icon="arrow-left" href="{{ url()->previous() }}">
            {{ __('Return') }}
        </flux:button>

        <flux:button variant="primary" icon="home" href="{{ route('dashboard') }}">
            {{ __('Go Home') }}
        </flux:button>
    </div>
</liveware:card>

</body>
</html>
