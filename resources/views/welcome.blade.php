<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Daniel</title>

    </head>
    <body>

        <h1> Bem-vindo ao Laravel 11 </h1>

        <p>Data atual: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>

    </body>
</html>
