<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WijayaPayCallbackController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KasirController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/', [LoginController::class, 'redirect'])->name('home');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('rbac:permission,view dashboard')
        ->name('dashboard');
    Route::get('/dashboard/export', [\App\Http\Controllers\DashboardController::class, 'export'])
        ->middleware('rbac:permission,export reports')
        ->name('dashboard.export');

    Route::middleware('rbac:permission,access cashier')->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/kasir/receipt/{id}', [KasirController::class, 'printReceipt'])->name('kasir.receipt');
        Route::post('/kasir/transaction', [KasirController::class, 'store'])->name('kasir.store');
        Route::post('/kasir/qris', [KasirController::class, 'createQrisPayment'])->name('kasir.qris.create');
        Route::get('/kasir/qris/{transaction}/status', [KasirController::class, 'checkQrisStatus'])->name('kasir.qris.status');
        Route::post('/kasir/member', [KasirController::class, 'storeMember'])->name('kasir.member.store');
    });

    Route::resource('members', MemberController::class)
        ->except(['create', 'show', 'edit'])
        ->middleware('rbac:permission,manage members');
    Route::resource('transactions', TransactionController::class)
        ->except(['create', 'edit'])
        ->middleware('rbac:permission,manage transactions');
    Route::resource('machines', MachineController::class)
        ->except('show')
        ->middleware('rbac:permission,manage machines');
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->except('show')
        ->middleware('rbac:permission,manage users');
    Route::resource('addons', \App\Http\Controllers\AddonController::class)
        ->except('show')
        ->middleware('rbac:permission,manage addons');
    Route::resource('services', \App\Http\Controllers\ServiceController::class)
        ->except('show')
        ->middleware('rbac:permission,manage services');

    Route::resource('roles', RoleController::class)
        ->except('show')
        ->middleware('rbac:permission,manage roles');
    Route::resource('permissions', PermissionController::class)
        ->except('show')
        ->middleware('rbac:permission,manage permissions');

    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])
        ->middleware('rbac:permission,manage settings')
        ->name('settings.index');
    Route::put('/settings/outlet', [\App\Http\Controllers\SettingController::class, 'updateOutlet'])
        ->middleware('rbac:permission,manage settings')
        ->name('settings.outlet.update');

    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])
        ->middleware('rbac:permission,view reports')
        ->name('reports.index');
});

Route::post('/callback/wijayapay', WijayaPayCallbackController::class)->name('callback.wijayapay');
