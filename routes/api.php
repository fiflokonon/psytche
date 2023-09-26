<?php

use App\Http\Controllers\Api\Call\CallController;
use App\Http\Controllers\Api\Package\PackageController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/init-user', [UserController::class, 'initUser']);
Route::get('/users/{call_id}/active-package', [UserController::class, 'active_package']);
Route::get('/users/{call_id}/infos', [UserController::class, 'infos']);
Route::get('/users/{call_id}/called', [CallController::class, 'getCalled']);
Route::get('/users/{call_id}/calls', [UserController::class, 'calls']);
Route::post('/users/{call_id}/make-call', [CallController::class, 'newCall']);
Route::put('/calls/{id}/duration', [CallController::class, 'updateDuration']);
Route::get('/packages', [PackageController::class, 'packages']);
Route::get('/users/{call_id}/packages', [UserController::class, 'userPackages']);
Route::post('/users/{call_id}/packages/{package_id}', [PackageController::class, 'initPackage']);
Route::get('/users/{call_id}/transactions', [UserController::class, 'transactions']);
Route::put('/transactions/checking', [PackageController::class, 'checking']);
Route::put('/users/{call_id}/online', [UserController::class, 'updateConnection']);
Route::post('/callback', [PackageController::class, 'verifyPaiementCallback']);

