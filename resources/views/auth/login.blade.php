<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in | {{ config('app.name', 'Business Manager') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 dark:bg-slate-950 dark:text-slate-100">
    <main class="min-h-screen">
     
        <section class="flex min-h-screen items-center justify-center bg-white px-5 py-10 dark:bg-slate-950 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between gap-3">
                    <a href="/" class="flex items-center gap-2.5 lg:hidden" aria-label="BizManager home">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.75 18.25V5.75h7.5a3.25 3.25 0 0 1 1.86 5.92 3.25 3.25 0 0 1-1.86 6.58h-7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" /><path d="M9 9h3.1M9 14.5h3.1M16.75 7.25v9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg></span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">BizManager</span>
                    </a>
                    <a href="/" class="ml-auto rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">Back to home <span aria-hidden="true">↗</span></a>
                </div>

                <div class="mb-8">
                    <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:ring-blue-900"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.8" /><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.6 2.77-.09-.03a1.7 1.7 0 0 0-1.81.35l-.07.06h-3.2l-.02-.09a1.7 1.7 0 0 0-1.2-1.37l-.09-.03-1.6-2.77.06-.07a1.7 1.7 0 0 0 .34-1.87l-.03-.09 1.6-2.77.09.03a1.7 1.7 0 0 0 1.81-.35l.07-.06h3.2l.02.09a1.7 1.7 0 0 0 1.2 1.37l.09.03 1.6 2.77-.06.09ZM12 3v1m0 16v1M4.22 7.5l.87.5m13.82 8 .87.5M4.22 16.5l.87-.5m13.82-8 .87-.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl dark:text-white">Sign in to your account</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Welcome back. Enter your details to continue.</p>
                </div>

                <x-auth-session-status class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                        <x-text-input id="email" class="mt-2 block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-400/10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-700 dark:text-slate-300" />
                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-blue-700 transition hover:text-blue-800 focus:outline-none focus:underline dark:text-blue-400 dark:hover:text-blue-300" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                            @endif
                        </div>
                        <x-text-input id="password" class="mt-2 block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-blue-400 dark:focus:ring-blue-400/10" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-1">
                        <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5">
                            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:focus:ring-offset-slate-950" name="remember">
                            <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <button type="submit" class=" mt-2 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
                        {{ __('Log in') }} <span aria-hidden="true">→</span>
                    </button>
                </form>

                <p class="mt-10 text-center text-sm text-slate-600 dark:text-slate-400">Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-blue-700 transition hover:text-blue-800 focus:outline-none focus:underline dark:text-blue-400 dark:hover:text-blue-300">Sign up</a></p>
                <p class="mt-10 text-center text-xs text-slate-400 lg:hidden">© {{ date('Y') }} BizManager</p>
            </div>
        </section>
    </main>
</body>
</html>