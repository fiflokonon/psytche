<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\TypePackageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/userlist', [UserController::class, 'index'])->name('userlist');
Route::get('connected-userlist', [UserController::class, 'connectedUsers'])->name('connected-userlist');
Route::get('/packagelist', [PackageController::class, 'index'])->name('packagelist');
Route::get('/type-packagelist', [TypePackageController::class, 'index'])->name('type-packagelist');
