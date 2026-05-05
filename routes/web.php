<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MachineController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
Route::get('/kasir/receipt/{id}', [KasirController::class, 'printReceipt'])->name('kasir.receipt');
Route::post('/kasir/transaction', [KasirController::class, 'store'])->name('kasir.store');
Route::post('/kasir/member', [KasirController::class, 'storeMember'])->name('kasir.member.store');

Route::resource('members', MemberController::class);
Route::resource('transactions', TransactionController::class);
Route::resource('machines', MachineController::class);
Route::resource('users', \App\Http\Controllers\UserController::class);
Route::resource('addons', \App\Http\Controllers\AddonController::class);
Route::resource('services', \App\Http\Controllers\ServiceController::class);
