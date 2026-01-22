<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Invoice System') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gradient-to-br from-gray-100 to-gray-200" style="font-family: 'Inter', sans-serif;">

    <div class="min-h-screen flex flex-col justify-center items-center px-4">

        <!-- Logo / App Name -->
        <a href="/" class="text-3xl font-bold text-blue-600 mb-6">
            Business Manager
        </a>

        <!-- Card -->
        <div class="w-full sm:max-w-md bg-white shadow-xl rounded-2xl px-6 py-8">

            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">
                Welcome Back
            </h1>

            {{ $slot }}

        </div>
    </div>

</body>
</html>
