<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@livewireStyles

{{-- CSS y JS compilados por Vite, servidos directamente --}}
<link rel="stylesheet" href="{{ asset('build/assets/app-B7yxMB0h.css') }}">
<script src="{{ asset('build/assets/app-Bbdxlwnq.js') }}"></script>

@fluxAppearance
