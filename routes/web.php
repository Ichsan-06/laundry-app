<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OwnerRegistrationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BillingPaymentController;
use App\Http\Controllers\BillingWijayaPayCallbackController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantSubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WijayaPayCallbackController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return app(LoginController::class)->redirect();
    }

    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register-owner', [OwnerRegistrationController::class, 'create'])->name('register.owner');
    Route::post('/register-owner', [OwnerRegistrationController::class, 'store'])->name('register.owner.store');
});

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['rbac:permission,dashboard.view'])
        ->name('dashboard');

    Route::get('/dashboard/export', [DashboardController::class, 'export'])
        ->middleware(['subscription', 'rbac:permission,reports.export', 'plan.permission:reports.export'])
        ->name('dashboard.export');

    Route::get('/billing', [BillingController::class, 'index'])
        ->middleware(['rbac:permission,billing.view'])
        ->name('billing.index');
    Route::post('/billing/purchase', [BillingPaymentController::class, 'create'])
        ->middleware(['rbac:permission,billing.view'])
        ->name('billing.purchase');
    Route::get('/billing/purchase/{purchase}/status', [BillingPaymentController::class, 'status'])
        ->middleware(['rbac:permission,billing.view'])
        ->name('billing.purchase.status');

    Route::middleware('subscription')->group(function () {
        Route::middleware(['rbac:permission,cashier.access', 'plan.permission:cashier.access'])->group(function () {
            Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
            Route::get('/kasir/receipt/{id}', [KasirController::class, 'printReceipt'])->name('kasir.receipt');
            Route::post('/kasir/transaction', [KasirController::class, 'store'])->name('kasir.store');
            Route::post('/kasir/qris', [KasirController::class, 'createQrisPayment'])->name('kasir.qris.create');
            Route::post('/kasir/qris/{transaction}/status', [KasirController::class, 'checkQrisStatus'])->name('kasir.qris.status');
            Route::post('/kasir/qris/{transaction}/cancel', [KasirController::class, 'cancelQrisPayment'])->name('kasir.qris.cancel');
            Route::post('/kasir/member', [KasirController::class, 'storeMember'])->name('kasir.member.store');
        });

        Route::resource('members', MemberController::class)
            ->except(['create', 'show', 'edit'])
            ->middleware(['rbac:permission,customers.view,customers.create,customers.update,customers.delete', 'plan.permission:customers.view,customers.create,customers.update,customers.delete']);

        Route::resource('transactions', TransactionController::class)
            ->except(['create', 'edit'])
            ->middleware(['rbac:permission,transactions.view,transactions.create,transactions.update,transactions.delete', 'plan.permission:transactions.view,transactions.create,transactions.update,transactions.delete']);

        Route::patch('/transactions/{transaction}/advance-process', [TransactionController::class, 'advanceProcess'])
            ->middleware(['rbac:permission,transactions.update', 'plan.permission:transactions.update'])
            ->name('transactions.advance-process');

        Route::get('/calendar', [CalendarController::class, 'index'])
            ->middleware(['rbac:permission,calendar.view', 'plan.permission:calendar.view'])
            ->name('calendar.index');
        Route::get('/calendar/show/{date}', [CalendarController::class, 'show'])
            ->middleware(['rbac:permission,calendar.view', 'plan.permission:calendar.view'])
            ->name('calendar.show');

        Route::resource('machines', MachineController::class)
            ->except('show')
            ->middleware(['rbac:permission,machines.view,machines.create,machines.update,machines.delete', 'plan.permission:machines.view,machines.create,machines.update,machines.delete']);

        Route::resource('addons', AddonController::class)
            ->except('show')
            ->middleware(['rbac:permission,addons.view,addons.create,addons.update,addons.delete', 'plan.permission:addons.view,addons.create,addons.update,addons.delete']);

        Route::resource('inventories', InventoryController::class)
            ->except(['show', 'create', 'edit'])
            ->middleware(['rbac:permission,inventories.view,inventories.create,inventories.update,inventories.delete']);
        Route::post('/inventories/{inventory}/restock', [InventoryController::class, 'restock'])
            ->middleware(['rbac:permission,inventories.update'])
            ->name('inventories.restock');
        Route::post('/inventories/{inventory}/use', [InventoryController::class, 'use'])
            ->middleware(['rbac:permission,inventories.update'])
            ->name('inventories.use');

        Route::resource('services', ServiceController::class)
            ->except('show')
            ->middleware(['rbac:permission,services.view,services.create,services.update,services.delete', 'plan.permission:services.view,services.create,services.update,services.delete']);

        Route::resource('outcomes', OutcomeController::class)
            ->except(['show', 'create', 'edit'])
            ->middleware(['rbac:permission,outcomes.view,outcomes.create,outcomes.update,outcomes.delete']);

        Route::resource('outlets', OutletController::class)
            ->except('show')
            ->middleware(['rbac:permission,outlets.view,outlets.create,outlets.update,outlets.delete', 'plan.permission:outlets.view,outlets.create,outlets.update,outlets.delete']);

        Route::resource('users', UserController::class)
            ->except('show')
            ->middleware(['rbac:permission,staff.view,staff.create,staff.update,staff.delete', 'plan.permission:staff.view,staff.create,staff.update,staff.delete']);

        Route::resource('roles', RoleController::class)
            ->except('show')
            ->middleware(['rbac:permission,roles.view,roles.create,roles.update,roles.delete', 'plan.permission:roles.view,roles.create,roles.update,roles.delete']);

        Route::resource('permissions', PermissionController::class)
            ->except('show')
            ->middleware(['rbac:permission,permissions.view,permissions.create,permissions.update,permissions.delete']);

        Route::resource('subscription-plans', SubscriptionPlanController::class)
            ->except('show')
            ->middleware(['rbac:permission,plans.manage']);

        Route::resource('tenants', TenantController::class)
            ->except('show', 'destroy')
            ->middleware(['rbac:permission,tenants.manage']);

        Route::put('/tenants/{tenant}/subscription', [TenantSubscriptionController::class, 'update'])
            ->middleware(['rbac:permission,subscription.manage'])
            ->name('tenants.subscription.update');

        Route::get('/settings', [SettingController::class, 'index'])
            ->middleware(['rbac:permission,settings.manage', 'plan.permission:settings.manage'])
            ->name('settings.index');

        Route::put('/settings/outlet', [SettingController::class, 'updateOutlet'])
            ->middleware(['rbac:permission,settings.manage', 'plan.permission:settings.manage'])
            ->name('settings.outlet.update');

        Route::get('/reports', [ReportController::class, 'index'])
            ->middleware(['rbac:permission,reports.view', 'plan.permission:reports.view'])
            ->name('reports.index');

        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])
            ->middleware(['rbac:permission,reports.view', 'plan.permission:reports.view'])
            ->name('reports.export-pdf');

        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])
            ->middleware(['rbac:permission,reports.view', 'plan.permission:reports.view'])
            ->name('reports.export-excel');

        Route::get('/api/notifications/due-transactions', [NotificationController::class, 'getDueTransactions'])
            ->name('api.notifications.due-transactions');
    });
});

Route::post('/callback/wijayapay', WijayaPayCallbackController::class)->name('callback.wijayapay');
Route::post('/callback/wijayapay/billing', BillingWijayaPayCallbackController::class)->name('callback.wijayapay.billing');
