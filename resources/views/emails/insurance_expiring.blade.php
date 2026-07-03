<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __(':name Expiring Soon', ['name' => __('Insurance')]) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #000000;
            color: white;
            text-align: left;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: white;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #000000;
            margin-bottom: 10px;
        }
        .content p {
            margin: 5px 0;
            color: #000000;
        }
        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #000000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header" style="text-align: center;">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="content">
        <h2>{{ __(':name Expiring Soon', ['name' => __('Insurance')]) }}</h2>

        <p><strong>{{__('Vendor')}}</strong> {{ $insurance->contractor->company_name ?? '' }}</p>
        <p><strong>{{__('Effective Date')}}</strong> {{ optional($insurance->effective_date)->format('m/d/Y') }}</p>
        <p><strong>{{__('Expiration Date')}}</strong> {{ optional($insurance->expiration_date)->format('m/d/Y') }}</p>

        @if (!empty($insurance->link))
            <a href="{{ $insurance->link }}" class="button">{{ __('View') }}</a>
        @endif
    </div>
</div>
</body>
</html>
