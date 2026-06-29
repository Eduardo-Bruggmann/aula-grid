<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex flex-col justify-center items-center min-h-screen bg-blue-500">
        <h1 class="text-6xl font-semibold text-white">Hello, World!</h1>
    </body>
</html>
