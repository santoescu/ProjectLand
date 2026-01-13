<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background:#f9fafb; color:#000; margin:0; padding:0; }
        .container { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,.1); }
        .header { background:#000; color:#fff; text-align:center; padding:20px; }
        .header h1 { margin:0; font-size:24px; color:#fff; }
        .content { padding:20px; text-align:center; }
        .content h2 { margin:0 0 10px; color:#000; }
        .content p { margin:8px 0; color:#000; }
        .subcopy { margin-top:16px; font-size:12px; color:#111; text-align:left; }
        .btn { display:inline-block; margin-top:20px; padding:10px 20px; background:#000; color:#fff !important; text-decoration:none; border-radius:6px; }
    </style>
</head>
<body>
<div class="container">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#000;">
        <tr>
            <td align="center" style="padding:20px;">
            <span style="font-family: Arial, sans-serif; font-size:24px; font-weight:bold; color:#ffffff;">
                {{ config('app.name') }}
            </span>
            </td>
        </tr>
    </table>


    <div class="content">
        {{-- Aquí entra TODO lo que tú ya produces con x-mail::message --}}
        {{ Illuminate\Mail\Markdown::parse($slot) }}

        @isset($subcopy)
            <div class="subcopy">
                {{ Illuminate\Mail\Markdown::parse($subcopy) }}
            </div>
        @endisset
    </div>
</div>
</body>
</html>
