<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Business Manager') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100" style="font-family: 'Inter', sans-serif;">

    <div class="min-h-screen">

        {{-- Navigation --}}
        @include('layouts.navigation')

        {{-- Page Content --}}
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

    </div>

</body>
</html>
