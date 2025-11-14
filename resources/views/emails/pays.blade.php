<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{__("New :name", ['name' => __('Pay')])}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #000000; /* texto negro por defecto */
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
            background-color: #000000; /* banner negro */
            color: white;
            text-align: left; /* alineado a la izquierda */
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: white;
        }
        .content {
            padding: 20px;
            text-align: center; /* solo el content centrado */
        }
        .content h2 {
            color: #000000; /* título negro */
            margin-bottom: 10px;
        }
        .content p {
            margin: 5px 0;
            color: #000000; /* textos negros */
        }
        .history ul {
            list-style-type: disc;
            padding-left: 20px;
            text-align: left; /* historial alineado a la izquierda */
        }

        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #000000; /* negro */
            color: white; /* texto blanco */
            text-decoration: none; /* quitar subrayado */
            border-radius: 6px;
            cursor: pointer; /* opcional: cambia el cursor al pasar */
        }

    </style>
</head>
<body>
<div class="container">

    <!-- Header / Banner -->
    <div class="header" style="text-align: center;">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <!-- Contenido centrado -->
    <div class="content">
        <h2>{{__("New :name", ['name' => __('Pay')])}}</h2>

        <p><strong>{{__('Project')}}</strong> {{ $pay->project->name ?? '' }}</p>
        <p><strong>{{__('Amount')}}</strong> ${{ number_format($pay->amount, 2) }}</p>
        <p><strong>{{__('Contractors')}}</strong> {{ $pay->contractor->company_name ?? '' }}</p>
        <p><strong>{{__('Chart of Account')}}:</strong> {{ $pay->chartAccount->name ?? '' }}</p>
        <a href="{{ route('pays.updatePay', ['id' => $pay->id, 'user_id' => $user->id]) }}" class="button">{{ __("View") }}</a>



        <div class="history mt-6">
            <h3>{{__('Histories')}}</h3>
            <ul>
                @foreach($pay->histories as $history)
                    <li>
                        {{ $history['user_name'] ?? $history['user_id'] }} -
                        {{ $history['action'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
</body>
</html>
