<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __("New User") }}</title>
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
            text-align: center;
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
            margin: 8px 0;
            color: #000000;
        }
        .credentials {
            margin-top: 20px;
            padding: 15px;
            border: 1px dashed #000;
            border-radius: 6px;
            text-align: left;
            display: inline-block;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #000000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .warning {
            margin-top: 15px;
            color: #000;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <!-- Contenido -->
    <div class="content">
        <h2>{{ $user->name }}</h2>

        <p>Your user account has been created</p>

        <p>These are your access credentials:</p>

        <div class="credentials">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary password:</strong> {{ $temporaryPassword }}</p>
        </div>

        <p class="warning">
            {{ __("For security reasons, please change this password when you log in.") }}
        </p>

        <a href="{{ url('/login') }}" class="button">
            {{ __("Login") }}
        </a>
    </div>
</div>
</body>
</html>
