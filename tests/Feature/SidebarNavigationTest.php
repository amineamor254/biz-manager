<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('sidebar highlights the active route in desktop and mobile navigation', function () {
    auth()->setUser(User::factory()->make());

    $routeCases = [
        ['dashboard', 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30'],
        ['clients.index', 'bg-white/10 text-white'],
        ['clients.create', 'bg-white/10 text-white'],
        ['products.index', 'bg-white/10 text-white'],
        ['products.edit', 'bg-white/10 text-white'],
        ['orders.index', 'bg-white/10 text-white'],
        ['orders.create', 'bg-white/10 text-white'],
        ['invoices.index', 'bg-white/10 text-white'],
        ['invoices.edit', 'bg-white/10 text-white'],
        ['expenses.index', 'bg-white/10 text-white'],
        ['expenses.create', 'bg-white/10 text-white'],
        ['reports.index', 'bg-white/10 text-white'],
        ['roles-permissions.index', 'bg-white/10 text-white'],
        ['roles-permissions.roles.update', 'bg-white/10 text-white'],
        ['profile.edit', 'bg-white/10 text-white'],
    ];

    foreach ($routeCases as [$routeName, $activeClass]) {
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->not->toBeNull();

        request()->setRouteResolver(fn () => $route);

        $sidebar = view('layouts.sidebar')->render();

        expect(substr_count($sidebar, $activeClass))->toBe(2);
    }
});

test('authenticated shell exposes the mobile drawer and scrollable navigation', function () {
    auth()->setUser(User::factory()->make());

    $header = view('layouts.header')->render();
    $sidebar = view('layouts.sidebar')->render();

    expect($header)->toContain('aria-controls="mobile-sidebar"')
        ->and($header)->toContain('md:hidden')
        ->and($sidebar)->toContain('hidden shrink-0 w-64 h-screen sticky top-0 z-50')
        ->and($sidebar)->toContain('flex-col md:flex')
        ->and($sidebar)->toContain('id="mobile-sidebar"')
        ->and($sidebar)->toContain('flex flex-col md:hidden')
        ->and(substr_count($sidebar, 'x-data="{ darkMode: window.currentTheme === \'dark\' }"'))->toBe(2)
        ->and(substr_count($sidebar, 'min-h-0 flex-1 overflow-x-hidden overflow-y-auto'))->toBe(2);
});