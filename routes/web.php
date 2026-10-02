<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\ReportsController;



Route::get('/', function () {
    return view('welcome');
});

// Development only: email verification remains implemented but is not enforced until real SMTP delivery is configured.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'workspace', 'workspace.permission:dashboard'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'workspace'])->group(function () {
    Route::get('/reports', [ReportsController::class, 'index'])
        ->middleware('workspace.permission:reports')
        ->name('reports.index');
    Route::get('/roles-permissions', [RolesPermissionsController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles-permissions/roles', [RolesPermissionsController::class, 'storeRole'])->name('roles-permissions.roles.store');
    Route::put('/roles-permissions/roles/{roleId}', [RolesPermissionsController::class, 'updateRole'])->whereNumber('roleId')->name('roles-permissions.roles.update');
    Route::delete('/roles-permissions/roles/{roleId}', [RolesPermissionsController::class, 'destroyRole'])->whereNumber('roleId')->name('roles-permissions.roles.destroy');
    Route::put('/roles-permissions/members/{membershipId}/role', [RolesPermissionsController::class, 'updateMemberRole'])->whereNumber('membershipId')->name('roles-permissions.members.update');
    Route::resource('clients', ClientController::class)->middleware('workspace.permission:clients');
    Route::resource('products', ProductController::class)->middleware('workspace.permission:products');
    Route::resource('invoices', InvoiceController::class)->middleware('workspace.permission:invoices');
    Route::resource('expenses', ExpenseController::class)->middleware('workspace.permission:expenses');
    Route::resource('orders', OrderController::class)->middleware('workspace.permission:orders');

});

require __DIR__.'/auth.php';
