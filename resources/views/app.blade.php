<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="UKM Aktiviteter - Påmelding for aktiviteter">
        
        <!-- Favicon -->
        <link rel="icon" href="https://ico.ukm.no/wp-admin_favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="https://ico.ukm.no/wp-admin_favicon.ico" type="image/x-icon">

        <title inertia>{{ config('app.name', 'UKM Aktiviteter') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:300,400,500,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
